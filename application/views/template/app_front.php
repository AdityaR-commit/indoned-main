<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title><?= APP_NAME ?> &mdash; <?= (empty($title) ? $title : 'index') ?></title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link href="<?= base_url('public') ?>/front/img/favicon.png" rel="icon">
    <link href="<?= base_url('public') ?>/front/img/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
    <link href="<?= base_url('public') ?>/libs/bootstrap/bootstrap.min.css" rel="stylesheet">
    <!-- Vendor CSS Files -->
    <link href="<?= base_url('public') ?>/front/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="<?= base_url('public') ?>/front/vendor/animate.css/animate.min.css" rel="stylesheet">
    <link href="<?= base_url('public') ?>/front/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= base_url('public') ?>/front/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="<?= base_url('public') ?>/front/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="<?= base_url('public') ?>/front/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

    <!-- Template Main CSS File -->
    <link href="<?= base_url('public') ?>/front/css/style.css" rel="stylesheet">
    <!-- for all page use -->
    <script src="<?= base_url('public') ?>/libs/jquery/jquery.min.js"></script>
</head>

<body>
    <!-- ======= Top Bar ======= -->
    <div id="topbar" class="d-flex align-items-center fixed-top">
        <div class="container d-flex justify-content-between">
            <div class="contact-info d-flex align-items-center">
                <i class="bi bi-envelope"></i> <a href="mailto:contact@example.com">contact@example.com</a>
                <i class="bi bi-phone"></i> +1 5589 55488 55
            </div>
            <div class="d-none d-lg-flex social-links align-items-center">
                <a href="#" class="twitter"><i class="bi bi-twitter"></i></a>
                <a href="#" class="facebook"><i class="bi bi-facebook"></i></a>
                <a href="#" class="instagram"><i class="bi bi-instagram"></i></a>
                <a href="#" class="linkedin"><i class="bi bi-linkedin"></i></i></a>
            </div>
        </div>
    </div>

    <!-- ======= Header ======= -->
    <header id="header" class="fixed-top">
        <div class="container d-flex align-items-center">

            <h1 class="logo me-auto">
                <a href="<?= site_url() ?>"><?= APP_NAME ?></a>
            </h1>
            <!-- Uncomment below if you prefer to use an image logo -->
            <!-- <a href="<?= site_url() ?>" class="logo me-auto"><img src="assets/img/logo.png" alt="" class="img-fluid"></a>-->

            <nav id="navbar" class="navbar order-last order-lg-0">
                <ul>
                    <li><a class="nav-link scrollto active" href="#hero">Beranda</a></li>
                    <li><a class="nav-link scrollto" href="#about">Tentang Kami</a></li>
                    <!-- <li><a class="nav-link scrollto" href="#services">Services</a></li>
                    <li><a class="nav-link scrollto" href="#departments">Departments</a></li>
                    <li><a class="nav-link scrollto" href="#doctors">Doctors</a></li> -->
                </ul>
                <i class="bi bi-list mobile-nav-toggle"></i>
            </nav>
            <!-- .navbar -->

            <!-- <a href="#appointment" class="appointment-btn scrollto"><span class="d-none d-md-inline">Login</a> -->
            <?php if (empty(getSession('userId'))) : ?>
                <a href="<?= site_url('auth') ?>" class="appointment-btn scrollto"><span class="d-none d-md-inline">Login</a>
            <?php else : ?>
                <a href="<?= site_url('dashboard') ?>" class="appointment-btn scrollto"><span class="d-none d-md-inline">Dashboard</a>
            <?php endif; ?>

        </div>
    </header>
    <main id="main">
        <?php if ($this->uri->segment(1) != 'auth') : ?>
            <!-- ======= Hero Section ======= -->
            <section id="hero" class="d-flex align-items-center">
                <div class="container">
                    <h1>Selamat Datang di <?= APP_NAME ?></h1>
                    <h2>Perhitungan Berat Badan Ideal dan Kebutuhan Energi</h2>
                    <a href="#about" class="btn-get-started scrollto">Get Started</a>
                </div>
            </section>
        <?php endif; ?>
        <!-- End Hero -->
        {CONTENT}
    </main>

    <!-- ======= Footer ======= -->
    <footer id="footer">

        <div class="footer-top">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12 col-md-12 footer-contact">
                        <h3><?= APP_NAME ?></h3>
                        <p>
                            <!-- A108 Adam Street <br>
                            New York, NY 535022<br>
                            United States <br><br> -->
                            <strong>Phone:</strong> +1 5589 55488 55<br>
                            <strong>Email:</strong> info@example.com<br>
                        </p>
                    </div>

                    <!-- <div class="col-lg-4 col-md-6 footer-links">
                        <h4>Useful Links</h4>
                        <ul>
                            <li><i class="bx bx-chevron-right"></i> <a href="#">Home</a></li>
                            <li><i class="bx bx-chevron-right"></i> <a href="#">About us</a></li>
                            <li><i class="bx bx-chevron-right"></i> <a href="#">Services</a></li>
                            <li><i class="bx bx-chevron-right"></i> <a href="#">Terms of service</a></li>
                            <li><i class="bx bx-chevron-right"></i> <a href="#">Privacy policy</a></li>
                        </ul>
                    </div>

                    <div class="col-lg-4 col-md-6 footer-links">
                        <h4>Our Services</h4>
                        <ul>
                            <li><i class="bx bx-chevron-right"></i> <a href="#">Web Design</a></li>
                            <li><i class="bx bx-chevron-right"></i> <a href="#">Web Development</a></li>
                            <li><i class="bx bx-chevron-right"></i> <a href="#">Product Management</a></li>
                            <li><i class="bx bx-chevron-right"></i> <a href="#">Marketing</a></li>
                            <li><i class="bx bx-chevron-right"></i> <a href="#">Graphic Design</a></li>
                        </ul>
                    </div> -->
                </div>
            </div>
        </div>

        <div class="container d-md-flex py-4">

            <div class="me-md-auto text-center text-md-start">
                <div class="copyright">
                    &copy; Copyright <strong><span><?= APP_NAME ?></span></strong>. All Rights Reserved
                </div>
            </div>
            <!-- <div class="social-links text-center text-md-right pt-3 pt-md-0">
                <a href="#" class="twitter"><i class="bx bxl-twitter"></i></a>
                <a href="#" class="facebook"><i class="bx bxl-facebook"></i></a>
                <a href="#" class="instagram"><i class="bx bxl-instagram"></i></a>
                <a href="#" class="google-plus"><i class="bx bxl-skype"></i></a>
                <a href="#" class="linkedin"><i class="bx bxl-linkedin"></i></a>
            </div> -->
        </div>
    </footer>

    <div id="preloader"></div>
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="fa fa-arrow-up"></i></a>

</body>
<!-- Vendor JS Files -->
<script src="<?= base_url('public') ?>/front/vendor/purecounter/purecounter_vanilla.js"></script>
<script src="<?= base_url('public') ?>/front/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="<?= base_url('public') ?>/front/vendor/glightbox/js/glightbox.min.js"></script>
<script src="<?= base_url('public') ?>/front/vendor/swiper/swiper-bundle.min.js"></script>
<script src="<?= base_url('public') ?>/front/vendor/php-email-form/validate.js"></script>

<!-- Template Main JS File -->
<script src="<?= base_url('public') ?>/front/js/main.js"></script>

</html>