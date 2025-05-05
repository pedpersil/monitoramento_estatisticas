<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-5">
    <h2 class="text-center mb-4">Alterar Senha</h2>

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

    <form action="<?= BASE_URL ?>/users/change-password" method="POST" class="bg-light p-4 rounded shadow-sm">
        <div class="mb-3">
            <label for="current_password" class="form-label">Senha Atual</label>
            <input type="password" name="current_password" id="current_password" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="new_password" class="form-label">Nova Senha</label>
            <input type="password" name="new_password" id="new_password" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="confirm_password" class="form-label">Confirme a Nova Senha</label>
            <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary w-100">Alterar Senha</button>
    </form>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
