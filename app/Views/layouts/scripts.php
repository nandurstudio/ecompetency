<!-- Tambahkan script utama lainnya -->
<script src="<?= base_url('assets/js/jquery/jquery.min.js'); ?>"></script>
<script src="<?= base_url('assets/js/chartjs/Chart.min.js'); ?>"></script>
<script src="<?= base_url('assets/js/bootstrap/bootstrap.bundle.min.js'); ?>"></script>
<script src="<?= base_url('assets/js/feather-icons/feather.min.js'); ?>"></script>
<script src="<?= base_url('assets/js/datatables/dataTables.js'); ?>"></script>
<script src="<?= base_url('assets/js/datatables/dataTables.bootstrap5.js'); ?>"></script>
<script src="<?= base_url('assets/js/datatables/dataTables.responsive.js'); ?>"></script>
<script src="<?= base_url('assets/js/datatables/responsive.bootstrap5.js'); ?>"></script>
<script src="<?= base_url('assets/js/litepicker/bundle.js'); ?>"></script>
<script src="<?= base_url('assets/js/scripts.js'); ?>"></script>

<!-- Tambahan JS lainnya -->
<script>
    feather.replace(); // Inisialisasi feather icons
</script>

<!-- Menambahkan scripts yang di-push dari controller -->
<?php if (!empty($scripts)) : ?>
    <!-- Pastikan script competencies.js ada di sini -->
    <script src="<?= base_url($scripts); ?>"></script>
<?php else: ?>
    <script>
        console.warn('No additional scripts were provided.');
    </script>
<?php endif; ?>