<?php require_once __DIR__ . '/layouts/header.php'; ?>

<div class="container mt-5">
    <h2 class="text-center mb-5">Dashboard de Monitoramento</h2>

    <div class="d-flex justify-content-end mb-4">
        <form method="GET" action="<?= BASE_URL ?>/dashboard" class="d-flex align-items-center">
            <label for="period" class="me-2">Período:</label>
            <select name="period" id="period" class="form-select me-2" onchange="this.form.submit()">
                <option value="1h" <?= ($period ?? '') === '1h' ? 'selected' : '' ?>>Última Hora</option>
                <option value="6h" <?= ($period ?? '') === '6h' ? 'selected' : '' ?>>Últimas 6 Horas</option>
                <option value="24h" <?= ($period ?? '') === '24h' ? 'selected' : '' ?>>Últimas 24 Horas</option>
                <option value="7d" <?= ($period ?? '') === '7d' ? 'selected' : '' ?>>Últimos 7 Dias</option>
                <option value="month" <?= ($period ?? '') === 'month' ? 'selected' : '' ?>>Mês Atual</option>
                <option value="all" <?= ($period ?? '') === 'all' ? 'selected' : '' ?>>Todos</option>
            </select>
        </form>
    </div>

    <!-- Cards -->
    <div class="row text-center mb-5">

        <div class="col-md-4 mb-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Visitantes Online</h5>
                    <p id="visitorsOnline" class="card-text display-6"><?= htmlspecialchars($onlineUsers) ?></p>
                </div>
            </div>
        </div>


        <div class="col-md-4 mb-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Total de Visitas</h5>
                    <p class="card-text display-6"><?= htmlspecialchars($totalVisits) ?></p>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Páginas Monitoradas</h5>
                    <p class="card-text display-6"><?= htmlspecialchars($totalPages) ?></p>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Países Detectados</h5>
                    <p class="card-text display-6"><?= htmlspecialchars($totalCountries) ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Exibindo dados do visitante -->
    <div class="container my-4">
    <h2 class="text-center mb-4">Relatório de Visitas</h2>

    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-light">
                <tr class="text-center">
                    <th>Data da Visita</th>
                    <th>IP do Visitante</th>
                    <th>Dispositivo</th>
                    <th>Sistema Operacional</th>
                    <th>Estado</th>
                    <th>Página de Referência</th>
                    <th>Página Visitada</th>
                </tr>
            </thead>
            <tbody id="visitsTableBody">

            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const rowsPerPage = 15;
                    const tableBody = document.getElementById('visitsTableBody');
                    const pagination = document.getElementById('pagination');
                    let currentPage = 1;
                    const period = '<?= htmlspecialchars($period ?? 'all') ?>'; // Captura o valor do período do PHP

                    // Função para carregar visitas via AJAX
                    function loadVisits(page) {
                        fetch(`<?= BASE_URL ?>/statistics/getPaginatedVisits?page=${page}&period=${period}`)
                            .then(response => response.json())
                            .then(data => {
                                tableBody.innerHTML = ''; // Limpa a tabela antes de adicionar novos dados
                                data.visits.forEach(visit => {
                                    const row = document.createElement('tr');
                                    row.classList.add('text-center');
                                    row.innerHTML = `
                                        <td>${new Date(visit.visit_date).toLocaleString('pt-BR')}</td>
                                        <td>${visit.ip_address}</td>
                                        <td>${getDeviceIcon(visit.device_type)}</td>
                                        <td>${visit.os}</td>
                                        <td>${visit.state}</td>
                                        <td>${visit.referrer === 'Acesso Direto' ? visit.referrer : `<a href="${visit.referrer}" target="_blank">${new URL(visit.referrer).hostname}</a>`}</td>
                                        <td>${visit.page_url}</td>
                                    `;
                                    tableBody.appendChild(row);
                                });

                                // Atualiza a paginação
                                updatePagination(page, data.totalPages);
                            })
                            .catch(error => console.error('Erro ao carregar visitas:', error));
                    }

                    // Função para mostrar ícone de dispositivo
                    function getDeviceIcon(deviceType) {
                        switch (deviceType) {
                            case 'mobile':
                                return '<i class="bi bi-phone"></i>';
                            case 'tablet':
                                return '<i class="bi bi-tablet"></i>';
                            default:
                                return '<i class="bi bi-laptop"></i>';
                        }
                    }

                    // Função para atualizar a paginação
                    function updatePagination(currentPage, totalPages) {
                        pagination.innerHTML = '';

                        const maxVisiblePages = 4;
                        let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
                        let endPage = startPage + maxVisiblePages - 1;

                        if (endPage > totalPages) {
                            endPage = totalPages;
                            startPage = Math.max(1, endPage - maxVisiblePages + 1);
                        }

                        // Botão "«"
                        if (currentPage > 1) {
                            const prevLi = document.createElement('li');
                            prevLi.className = 'page-item';
                            prevLi.innerHTML = '<a class="page-link" href="#">«</a>';
                            prevLi.addEventListener('click', function (e) {
                                e.preventDefault();
                                loadVisits(currentPage - 1);
                            });
                            pagination.appendChild(prevLi);
                        }

                        // Botões de páginas visíveis
                        for (let i = startPage; i <= endPage; i++) {
                            const li = document.createElement('li');
                            li.className = 'page-item' + (i === currentPage ? ' active' : '');
                            li.innerHTML = `<a class="page-link" href="#">${i}</a>`;
                            li.addEventListener('click', function (e) {
                                e.preventDefault();
                                loadVisits(i);
                            });
                            pagination.appendChild(li);
                        }

                        // Botão "»"
                        if (currentPage < totalPages) {
                            const nextLi = document.createElement('li');
                            nextLi.className = 'page-item';
                            nextLi.innerHTML = '<a class="page-link" href="#">»</a>';
                            nextLi.addEventListener('click', function (e) {
                                e.preventDefault();
                                loadVisits(currentPage + 1);
                            });
                            pagination.appendChild(nextLi);
                        }
                    }


                    // Carregar as visitas da primeira página ao carregar
                    loadVisits(currentPage);
                });
            </script>


            </tbody>
        </table>
    </div>

        <nav class="mt-3">
            <ul class="pagination justify-content-center" id="pagination"></ul>
        </nav>
    </div>
    <br>

    <!-- Mapa-múndi para localização dos visitantes -->
    <div class="col-12 my-4">
        <div id="worldMap" style="height: 400px;"></div>
    </div>


    <!-- Gráficos -->
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title text-center">Páginas Mais Acessadas</h5>
                    <canvas id="pagesChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title text-center">Navegadores Mais Usados</h5>
                    <canvas id="browsersChart"></canvas>
                </div>
            </div>
        </div>
    </div>

</div>

<div class="row">
    <!-- Gráfico de Países -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title text-center">Visitas por País</h5>
                <canvas id="countriesChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Gráfico de Cidades -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title text-center">Visitas por Cidade</h5>
                <canvas id="citiesChart"></canvas>
            </div>
        </div>
    </div>
</div>


<?php require_once __DIR__ . '/layouts/footer.php'; ?>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


<script>
    const pagesData = <?= json_encode($pagesData) ?>;
    const browsersData = <?= json_encode($browsersData) ?>;
    const countriesData = <?= json_encode($countriesData) ?>;
    const citiesData = <?= json_encode($citiesData) ?>;

    // Gráfico de Páginas
    const ctxPages = document.getElementById('pagesChart').getContext('2d');
    new Chart(ctxPages, {
        type: 'bar',
        data: {
            labels: pagesData.labels,
            datasets: [{
                label: 'Acessos',
                data: pagesData.data,
                backgroundColor: 'rgba(13, 110, 253, 0.7)'
            }]
        },
        options: { responsive: true, scales: { y: { beginAtZero: true } } }
    });

    // Gráfico de Navegadores
    const ctxBrowsers = document.getElementById('browsersChart').getContext('2d');
    new Chart(ctxBrowsers, {
        type: 'pie',
        data: {
            labels: browsersData.labels,
            datasets: [{
                data: browsersData.data,
                backgroundColor: [
                    'rgba(13, 110, 253, 0.7)',
                    'rgba(220, 53, 69, 0.7)',
                    'rgba(255, 193, 7, 0.7)',
                    'rgba(40, 167, 69, 0.7)',
                    'rgba(108, 117, 125, 0.7)'
                ]
            }]
        },
        options: { responsive: true }
    });

    // Gráfico de Países
    const ctxCountries = document.getElementById('countriesChart').getContext('2d');
    new Chart(ctxCountries, {
        type: 'doughnut',
        data: {
            labels: countriesData.labels,
            datasets: [{
                data: countriesData.data,
                backgroundColor: [
                    'rgba(13, 110, 253, 0.7)',
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(255, 206, 86, 0.7)',
                    'rgba(75, 192, 192, 0.7)',
                    'rgba(153, 102, 255, 0.7)'
                ]
            }]
        },
        options: { responsive: true }
    });

    // Gráfico de Cidades
    const ctxCities = document.getElementById('citiesChart').getContext('2d');
    new Chart(ctxCities, {
        type: 'doughnut',
        data: {
            labels: citiesData.labels,
            datasets: [{
                data: citiesData.data,
                backgroundColor: [
                    'rgba(255, 159, 64, 0.7)',
                    'rgba(255, 205, 86, 0.7)',
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(153, 102, 255, 0.7)'
                ]
            }]
        },
        options: { responsive: true }
    });
</script>

<script>
    function updateVisitorsOnline() {
        fetch('<?= BASE_URL ?>/visitors-online')
            .then(response => response.json())
            .then(data => {
                const visitorsOnlineElement = document.getElementById('visitorsOnline');
                if (visitorsOnlineElement && data.visitors_online !== undefined) {
                    visitorsOnlineElement.textContent = data.visitors_online;
                }
            })
            .catch(error => {
                console.error('Erro ao atualizar visitantes online:', error);
            });
    }

    // Atualiza imediatamente ao carregar a página
    updateVisitorsOnline();

    // Atualiza a cada 10 segundos
    setInterval(updateVisitorsOnline, 10000);
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const map = L.map('worldMap').setView([20, 0], 2); // Centraliza o mapa no meio do mundo

        // Adiciona o tile layer (camada de mapa)
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Função para carregar visitantes no mapa
        function loadVisitorLocations() {
            const period = '<?= htmlspecialchars($period ?? 'all') ?>'; // Captura o valor do período do PHP
            fetch(`<?= BASE_URL ?>/statistics/getVisitorLocations?period=${period}`)
                .then(response => response.json())
                .then(data => {
                    data.visits.forEach(visit => {
                        const lat = visit.latitude;
                        const lng = visit.longitude;

                        // Verifica se a localização é válida
                        if (lat && lng) {
                            L.marker([lat, lng]).addTo(map)
                                .bindPopup(`<b>Visitante</b><br>${visit.city}, ${visit.state}, ${visit.country}`)
                                .openPopup();
                        }
                    });
                })
                .catch(error => {
                    console.error('Erro ao carregar as localizações dos visitantes:', error);
                });
        }

        // Carregar as localizações dos visitantes
        loadVisitorLocations();
    });
</script>

