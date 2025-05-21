<a href="https://pedrosilva.tech/">
<img src="https://pedrosilva.tech/insighttrack/public/images/insightTrack.png" alt="InsightTrack Logo" style="height: 100px; margin-right: 10px;">
</a>

# InsightTrack - Sistema de Monitoramento e Estatísticas

Sistema web desenvolvido em **PHP 8+**, **MySQL**, **PDO** e **Bootstrap 5** para monitorar, analisar e exibir estatísticas de acesso em tempo real.

O sistema registra informações dos visitantes como IP, navegador, sistema operacional, tipo de dispositivo (Desktop, Mobile, Tablet) e localização (país, estado e cidade). Tudo organizado em um painel administrativo moderno com dashboards.

## Funcionalidades
- Monitoramento de visitantes online em tempo real.
- Registro de visitas com informações completas.
- Filtros de período (1h, 6h, 24h, 7 dias, mês atual).
- Mapa de localização de visitantes.
- Gráficos interativos com Chart.js (páginas, navegadores, países, cidades).
- Identificação do tipo de dispositivo.
- Sistema de login com controle de acesso.
- Layout responsivo com Bootstrap.

## Tecnologias Utilizadas
- PHP 8+
- MySQL
- PDO
- Bootstrap 5
- Chart.js
- JavaScript (fetch API)

---

# Instalação

## 1. Clonar o repositório
```bash
git clone https://github.com/pedpersil/monitoramento_estatisticas.git
```

## 2. Configurar o Banco de Dados

1. Crie um banco de dados MySQL, por exemplo `monitoramento_estatisticas`.
2. Importe o arquivo `/monitoramento_estatisticas.sql` localizado na pasta database do projeto para criar as tabelas necessárias.


> Observação: o arquivo `/monitoramento_estatisticas.sql` já inclui o usuário administrador padrão.

## 3. Configurar o arquivo `config.php`

Abra `config/config.php` e ajuste as seguintes constantes conforme seu ambiente:

```php
// Configuração do banco de dados
define('DB_HOST', 'localhost');
define('DB_NAME', 'monitoramento_estatisticas');
define('DB_USER', 'seu_usuario');
define('DB_PASS', 'sua_senha');

// URL base do projeto
define('BASE_URL', 'https://seusite.com/monitoramento_estatisticas/public');

// URL Path do Sistema
define('BASE_PATH', '/monitoramento_estatisticas/public'); 

// Nome da sessão
define('SESSION_NAME', 'monitoramento_estatisticas');
```

> Certifique-se de apontar `BASE_URL` corretamente para onde o sistema foi hospedado.


## 4. Configurar o `track.js`

O arquivo `public/track.js` é responsável por enviar os dados dos visitantes.

Ajuste o `fetch` para apontar corretamente para sua `BASE_URL`:

```javascript
fetch('https://seusite.com/monitoramento_estatisticas/public/track', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(data)
})
```

> Se você alterou o caminho do projeto, não esqueça de corrigir aqui também.

---

# Como usar

Para começar a monitorar visitas em qualquer página web, basta adicionar o seguinte código logo antes do fechamento da tag `<body>`:

```html
<script src="https://seusite.com/monitoramento_estatisticas/public/track.js"></script>
```

Isso permitirá que o script capture e envie automaticamente informações dos visitantes para o sistema.

---

# Login Padrão

- **Email:** `admin@admin.com`
- **Senha:** `123456`

> Recomenda-se alterar a senha do usuário administrador após o primeiro login para maior segurança.

---


# Requisitos Mínimos
- PHP >= 8.0
- MySQL >= 5.7
- Extensões PHP ativas:
  - pdo
  - pdo_mysql
  - mbstring
  - json
  - session
  - curl

---

# Licença

Este projeto está licenciado sob a Licença MIT. Consulte o arquivo [LICENSE](LICENSE) para obter mais informações.

---

# Autor

Desenvolvido por [pedrosilva.tech](https://pedrosilva.tech)
