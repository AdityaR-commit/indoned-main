<section>
    <div style="height: 80px;"></div>
    <div class="container">
        <div class="section-title">
            <h2>Login</h2>
        </div>
        <div class="row d-flex justify-content-center mx-auto">
            <div class="col-lg-8 mt-0">
                <div class="card m-3 p-3 shadow">
                    <h5 class="text-center mt-0 mb-0">Silahkan masukkan akun Anda</h5>
                    <form action="<?= site_url('auth') ?>" id="login-form" method="post">
                        <div class="form-group mt-3">
                            <label for="username">Username</label>
                            <input type="text" name="username" class="form-control" id="username" placeholder="Username" required>
                        </div>
                        <div class="form-group mt-3">
                            <label for="password">Password</label>
                            <div class="input-group">
                                <input type="password" id="password" name="password" class="form-control" required placeholder="Password">
                                <div class="input-group-append">
                                    <button type="button" name="show_password" class="btn bg-success text-light input-group-text show-password"><i class="fa fa-eye-slash"></i></button>
                                    <button type="button" name="hide_password" class="btn bg-success text-light input-group-text d-none hide-password"><i class="fa fa-eye"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mt-3">
                            <button type="submit" name="login-button" class="btn pull-right btn-success btn-block" style="width:100%;">
                                <i class="fas fa-sign-in-alt"></i> Login
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    $(document).ready(function() {
        $('.show-password').click(function(e) {
            e.preventDefault();
            $('[name=password]').attr('type', 'text');
            $('.show-password').addClass('d-none');
            $('.hide-password').removeClass('d-none');
        })

        $('.hide-password').click(function(e) {
            e.preventDefault();
            $('[name=password]').attr('type', 'password');
            $('.show-password').removeClass('d-none');
            $('.hide-password').addClass('d-none');
        })
    });
</script>