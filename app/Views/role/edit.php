<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Role</title>
    <link rel="stylesheet" href="<?php echo base_url('assets/css/bootstrap/bootstrap.min.css'); ?>">
</head>

<body>
    <div class="container mt-5">
        <h2>Edit Role</h2>

        <form action="<?= base_url('role/update/' . $role['intRoleID']) ?>" method="post">
            <div class="mb-3">
                <label for="txtRoleName" class="form-label">Role Name</label>
                <input type="text" class="form-control" id="txtRoleName" name="txtRoleName" value="<?= esc($role['txtRoleName']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="txtRoleDesc" class="form-label">Description</label>
                <textarea class="form-control" id="txtRoleDesc" name="txtRoleDesc"><?= esc($role['txtRoleDesc']) ?></textarea>
            </div>
            <div class="mb-3">
                <label for="txtRoleNote" class="form-label">Note</label>
                <textarea class="form-control" id="txtRoleNote" name="txtRoleNote"><?= esc($role['txtRoleNote']) ?></textarea>
            </div>
            <div class="form-check mb-3">
                <input type="checkbox" class="form-check-input" id="bitActive" name="bitActive" value="1" <?= $role['bitActive'] ? 'checked' : '' ?>>
                <label class="form-check-label" for="bitActive">Active</label>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="<?= base_url('role') ?>" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
    <script src="<?php echo base_url('assets/js/bootstrap/bootstrap.bundle.min.js'); ?>"></script>
</body>

</html>