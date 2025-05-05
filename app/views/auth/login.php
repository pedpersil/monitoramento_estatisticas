<?php 
require_once __DIR__ . '/../layouts/header.php'; 
?>

<div class="container mt-5">
    <h2 class="text-center mb-4">Login no Sistema</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger text-center">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form action="<?= BASE_URL ?>/login" method="POST" class="bg-light p-4 rounded shadow-sm">
        <div class="mb-3">
            <label for="email" class="form-label">E-mail</label>
            <input type="email" name="email" id="email" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Senha</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>

        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="remember" id="remember" checked>
            <label class="form-check-label" for="remember">
                Lembrar-me nesse dispositivo
            </label>
        </div>

        <button type="submit" class="btn btn-primary w-100">Entrar</button>
        <br>

    </form>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
