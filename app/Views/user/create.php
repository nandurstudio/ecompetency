<!-- create.php -->
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="container mt-5">
    <h2>Tambah User Baru</h2>
    <form action="<?= base_url('/user/create') ?>" method="post">
        <?= csrf_field() ?>
        <div class="mb-3">
            <label for="txtUserName" class="form-label">Username</label>
            <input type="text" class="form-control" id="txtUserName" name="txtUserName" required>
        </div>
        <div class="mb-3">
            <label for="txtFullName" class="form-label">Full Name</label>
            <input type="text" class="form-control" id="txtFullName" name="txtFullName" required>
        </div>
        <!-- Tambahkan input lainnya sesuai kebutuhan -->
        <div class="mb-3">
            <label for="txtEmail" class="form-label">Email</label>
            <input type="email" class="form-control" id="txtEmail" name="txtEmail" required>
        </div>
        <button type="submit" class="btn btn-primary">Tambah User</button>
    </form>
</div>
<?= $this->endSection() ?>
