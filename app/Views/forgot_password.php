<?= $this->include('layouts/head') ?>

<body class="bg-primary">
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container-xl px-4">
                    <div class="row justify-content-center">
                        <div class="col-lg-5">
                            <!-- Basic forgot password form-->
                            <div class="card shadow-lg border-0 rounded-lg mt-5">
                                <div class="card-header justify-content-center">
                                    <h3 class="fw-light my-4">Password Recovery</h3>
                                </div>
                                <div class="card-body">
                                    <div class="small mb-3 text-muted">Enter your email address and we will send you a link to reset your password.</div>

                                    <!-- Menampilkan pesan sukses atau error -->
                                    <?php if (session()->getFlashdata('success')) : ?>
                                        <div id="success-message" class="alert alert-success">
                                            <?= session()->getFlashdata('success') ?>
                                        </div>
                                    <?php elseif (session()->getFlashdata('error')) : ?>
                                        <div id="error-message" class="alert alert-danger">
                                            <?= session()->getFlashdata('error') ?>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Form hanya ditampilkan jika tidak ada pesan sukses -->
                                    <?php if (!session()->getFlashdata('success')) : ?>
                                        <form action="<?= base_url('auth/sendResetLink') ?>" method="post">
                                            <?= csrf_field() ?>
                                            <div class="mb-3">
                                                <label class="small mb-1" for="email">Email</label>
                                                <input class="form-control" id="email" type="email" aria-describedby="emailHelp" name="email" placeholder="Enter email address" value="<?= old('email') ?>" required />
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                                <a class="small" href="/login">Return to login</a>
                                                <button type="submit" class="btn btn-primary">Send Reset Link</button>
                                            </div>
                                        </form>
                                    <?php endif; ?>
                                </div>
                                <div class="card-footer text-center">
                                    <div class="small"><a href="/register">Need an account? Sign up!</a></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
        <div id="layoutAuthentication_footer">
            <?= $this->include('layouts/footer') ?>
        </div>
    </div>
    <script src="<?php echo base_url('assets/js/bootstrap/bootstrap.bundle.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/scripts.js'); ?>"></script>

    <script>
        // Menghapus pesan error ketika input email di-fokuskan
        document.getElementById('email').addEventListener('focus', function() {
            var errorMessage = document.getElementById('error-message');
            if (errorMessage) {
                errorMessage.style.display = 'none';
            }
        });
    </script>

</body>

</html>