<!-- index.php -->
<?= $this->extend('layouts/starter/main') ?>
<?= $this->section('content') ?>

<div class="container mt-5">
    <h2>User List</h2>
    <a href="<?= base_url('/user/create') ?>" class="btn btn-success mb-3">Add New User</a>

    <!-- Table displaying user information -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Username</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Role</th> <!-- Changed from "Role ID" to "Role" -->
                <th>Nick</th>
                <th>Employee ID</th>
                <th>Photo</th>
                <th>Join Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= esc($user['txtUserName']) ?></td>
                    <td><?= esc($user['txtFullName']) ?></td>
                    <td><?= esc($user['txtEmail']) ?></td>
                    <td><?= esc($user['txtRoleName']) ?></td> <!-- Display role name here -->
                    <td><?= esc($user['txtNick']) ?></td>
                    <td><?= esc($user['txtEmpID']) ?></td>
                    <td>
                        <?php if (!empty($user['txtPhoto'])): ?>
                            <div class="mb-3">
                                <img src="<?= base_url('uploads/photos/' . $user['txtPhoto']) ?>" alt="User Photo" class="img-thumbnail" width="150">
                            </div>
                        <?php endif; ?>
                    </td>
                    <td><?= esc($user['dtmJoinDate']) ?></td>
                    <td>
                        <?php if ($user['bitActive']): ?>
                            <span class="badge bg-success">Active</span>
                        <?php else: ?>
                            <span class="badge bg-danger">Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="<?= base_url('/user/view/' . $user['intUserID']) ?>" class="btn btn-info btn-sm">Details</a>
                        <a href="<?= base_url('/user/update/' . $user['intUserID']) ?>" class="btn btn-warning btn-sm">Edit</a>
                        <form action="<?= base_url('/user/delete/' . $user['intUserID']) ?>" method="post" style="display:inline;">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?')">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Pagination (if any) -->
    <?= $pager->links() ?>

</div>

<?= $this->endSection() ?>