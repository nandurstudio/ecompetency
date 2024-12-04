<?= $this->extend('layouts/starter/main') ?>

<?= $this->section('content') ?>
<h1>Add User Job Title</h1>
<form method="post" action="<?= base_url('transactions/user_jobtitle/store') ?>">
    <div class="mb-3">
        <label>User ID</label>
        <input type="number" name="intUserID" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Job Title ID</label>
        <input type="number" name="intJobTitleID" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Achieved</label>
        <select name="bitAchieved" class="form-control">
            <option value="0">No</option>
            <option value="1">Yes</option>
        </select>
    </div>
    <button type="submit" class="btn btn-success">Save</button>
</form>
<?= $this->endSection() ?>