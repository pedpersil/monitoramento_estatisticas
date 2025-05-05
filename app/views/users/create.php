<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-5">
    <h2 class="text-center mb-4">Cadastrar Novo Usuário</h2>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success text-center">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger text-center">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form action="<?= BASE_URL ?>/users/store" method="POST" class="bg-light p-4 rounded shadow-sm">
        <div class="mb-3">
            <label for="name" class="form-label">Nome</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">E-mail</label>
            <input type="email" name="email" id="email" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Senha</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="role" class="form-label">Tipo de Usuário</label>
            <select name="role" id="role" class="form-select" required>
                <option value="user">Usuário</option>
                <option value="admin">Administrador</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success w-100">Cadastrar</button>
    </form>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
