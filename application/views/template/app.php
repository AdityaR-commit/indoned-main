<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?= APP_NAME ?> &mdash; <?= (isset($title) ? $title : 'Dashboard') ?></title>

	<!-- Google Font: Source Sans Pro -->
	<!-- <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback"> -->
	<!-- Font Awesome -->
	<link rel="stylesheet" href="<?= base_url('public') ?>/libs/fontawesome-free/css/all.min.css">
	<!-- Tempusdominus Bootstrap 4 -->
	<link rel="stylesheet" href="<?= base_url('public') ?>/libs/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
	<!-- Bootstrap -->
	<link rel="stylesheet" href="<?= base_url('public') ?>/libs/bootstrap/css/bootstrap.min.css">

	<link rel="stylesheet" href="<?= base_url('public') ?>/libs/datatables-bs4/css/dataTables.bootstrap4.min.css">
	<link rel="stylesheet" href="<?= base_url('public') ?>/libs/datatables-responsive/css/responsive.bootstrap4.min.css">
	<link rel="stylesheet" href="<?= base_url('public') ?>/libs/datatables-buttons/css/buttons.bootstrap4.min.css">
	<!-- overlayScrollbars -->
	<link rel="stylesheet" href="<?= base_url('public') ?>/libs/overlayScrollbars/css/OverlayScrollbars.min.css">
	<!-- Daterange picker -->
	<link rel="stylesheet" href="<?= base_url('public') ?>/libs/daterangepicker/daterangepicker.css">
	<!-- summernote -->
	<link rel="stylesheet" href="<?= base_url('public') ?>/libs/summernote/summernote-bs4.min.css">
	<link rel="stylesheet" href="<?= base_url('public') ?>/loading/loading_page.css">

	<link rel="stylesheet" href="<?= base_url('public') ?>/libs/sweetalert2-theme-bootstrap-4/bootstrap-4.css">
	<link rel="stylesheet" href="<?= base_url('public') ?>/libs/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
	<link rel="stylesheet" href="<?= base_url('public') ?>/libs/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">

	<link rel="stylesheet" href="<?= base_url('public') ?>/back/css/adminlte.min.css">
	<!-- jQuery -->
	<script src="<?= base_url('public') ?>/libs/jquery/jquery-3.6.0.min.js"></script>
</head>

<body class="hold-transition sidebar-mini layout-fixed accent-success layout-navbar-fixed text-sm">
	<div id="spinner-front">
		<img src="<?= base_url('public/loading/ajax-loader.gif') ?>" width="100px"><br>
		Loading...
	</div>
	<div id="spinner-back"></div>
	<div class="wrapper">
		<!-- Preloader -->
		<!-- <div class="preloader flex-column justify-content-center align-items-center" id="preloader">
			<img class="animation__shake" src="<?= base_url('public') ?>/back/img/AdminLTELogo.png" alt="AdminLTELogo" height="60" width="60">
		</div> -->
		<nav class="main-header navbar navbar-expand navbar-light">
			<ul class="navbar-nav">
				<li class="nav-item">
					<a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
				</li>
			</ul>

			<ul class="navbar-nav ml-auto">
				<li class="nav-item dropdown user-menu">
					<a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
						<img src="<?= base_url('public') ?>/back/img/user2-160x160.jpg" class="user-image img-circle elevation-2" alt="User Image">
						<span class="d-none d-md-inline"><?= getSession('realName') ?></span>
					</a>
					<ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
						<li class="user-header bg-success">
							<img src="<?= base_url('public') ?>/back/img/user2-160x160.jpg" class="img-circle" alt="User Image">
							<p>
								<?= getSession('realName') ?> - <?= getSession('groupName') ?>
								<small>Terakhir login: </small>
							</p>
						</li>
						<li class="user-footer">
							<!-- <a href="#" class="btn btn-default btn-flat">Profile</a> -->
							<a href="<?= site_url('auth/logout') ?>" class="btn btn-default btn-flat float-right">
								<i class="fa fa-sign-out"></i> Logout</a>
						</li>
					</ul>
				</li>
			</ul>
		</nav>

		<aside class="main-sidebar sidebar-light-success">
			<a href="<?= site_url('dashboard') ?>" class="brand-link">
				<img src="<?= base_url('public') ?>/back/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
				<span class="brand-text font-weight-light"><?= APP_NAME ?></span>
			</a>

			<div class="sidebar">
				<div class="user-panel mt-3 pb-3 mb-3 d-flex">
					<div class="image">
						<img src="<?= base_url('public') ?>/back/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
					</div>
					<div class="info">
						<a href="#" class="d-block"><?= getSession('realName') ?></a>
					</div>
				</div>
				<nav class="mt-2">
					<ul class="nav nav-pills nav-sidebar flex-column nav-flat nav-child-indent" data-widget="treeview" role="menu" data-accordion="false">
						<li class="nav-header">Dashboard</li>
						<li class="nav-item">
							<a href="<?= site_url('dashboard') ?>" class="nav-link">
								<i class="nav-icon fas fa-home"></i>
								<p>Dashboard</p>
							</a>
						</li>
						<?php if (in_array('pasien.access', $userMenus) || in_array('estimasi.access', $userMenus) || in_array('screening.access', $userMenus)) : ?>
							<li class="nav-header">Pasien</li>
							<li class="nav-item">
								<a href="#" class="nav-link">
									<i class="nav-icon fa fa-cheese"></i>
									<p>
										Data Pasien
										<i class="fas fa-angle-left right"></i>
									</p>
								</a>
								<ul class="nav nav-treeview">
									<?php if (in_array('pasien.access', $userMenus)) : ?>
										<li class="nav-item">
											<a href="<?= site_url('patients') ?>" class="nav-link">
												<i class="far fa-circle nav-icon"></i>
												<p>Data Pasien</p>
											</a>
										</li>
									<?php endif; ?>
									<?php if (in_array('estimasi.access', $userMenus)) : ?>
										<li class="nav-item">
											<a href="<?= site_url('estimasi') ?>" class="nav-link">
												<i class="far fa-circle nav-icon"></i>
												<p>Estimasi Kebutuhan</p>
											</a>
										</li>
									<?php endif; ?>
									<?php if (in_array('screening.access', $userMenus)) : ?>
										<li class="nav-item">
											<a href="<?= site_url('screening') ?>" class="nav-link">
												<i class="far fa-circle nav-icon"></i>
												<p>Screening</p>
											</a>
										</li>
									<?php endif; ?>
								</ul>
							</li>
						<?php endif; ?>
						<?php if (in_array('food_categories.access', $userMenus) || in_array('foods.access', $userMenus)) : ?>
							<li class="nav-header">Database Bahan Makanan</li>
							<li class="nav-item">
								<a href="#" class="nav-link">
									<i class="nav-icon fa fa-cheese"></i>
									<p>
										Bahan Makanan
										<i class="fas fa-angle-left right"></i>
									</p>
								</a>
								<ul class="nav nav-treeview">
									<?php if (in_array('food_categories.access', $userMenus)) : ?>
										<li class="nav-item">
											<a href="<?= site_url('foodcategories') ?>" class="nav-link">
												<i class="far fa-circle nav-icon"></i>
												<p>Kategori</p>
											</a>
										</li>
									<?php endif; ?>
									<?php if (in_array('foods.access', $userMenus)) : ?>
										<li class="nav-item">
											<a href="<?= site_url('foods') ?>" class="nav-link">
												<i class="far fa-circle nav-icon"></i>
												<p>Bahan Makanan</p>
											</a>
										</li>
									<?php endif; ?>
								</ul>
							</li>
						<?php endif; ?>
					</ul>
				</nav>
			</div>
		</aside>

		<div class="content-wrapper">
			<div class="content-header">
				<div class="container-fluid">
					<div class="row mb-2">
						<div class="col-sm-6">
							<h1 class="m-0"><?= isset($title) ? $title : null ?></h1>
						</div>
						<div class="col-sm-6">
							<?php if (isset($breadcrumb)) : $akhir = count($breadcrumb) - 1; ?>
								<ol class="breadcrumb float-sm-right">
									<?php foreach ($breadcrumb as $i => $crumb) : ?>
										<?php
										$url = (($akhir != $i) ? (isset($crumb['url']) ? $crumb['url'] : '#') : '#');
										$icon = isset($crumb['icon']) ? '<i class="' . $crumb['icon'] . ' m-r-5"></i>' : null;
										$active = ($akhir == $i) ? 'active' : null;
										?>
										<li class="breadcrumb-item">
											<a href="<?= $url ?>" class="breadcrumb-item <?= $active ?>"><?= $icon ?><?= $crumb['title'] ?></a>
										</li>
									<?php endforeach; ?>
									</nav>
								</ol>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>

			<section class="content">
				<div class="container-fluid">
					{CONTENT}
				</div>
			</section>
		</div>
		<footer class="main-footer">
			<b>Copyright &copy; 2022 - <?= date('Y') ?> <a href="#"><?= APP_NAME ?></a>.</b>
			All rights reserved.
			<div class="float-right d-none d-sm-inline-block">
				<b>Version</b> 1.0
			</div>
		</footer>
	</div>

	<script src="<?= base_url('public') ?>/libs/jquery-ui/jquery-ui.min.js"></script>
	<script>
		$.widget.bridge('uibutton', $.ui.button)
	</script>
	<!-- Bootstrap 4 -->
	<script src="<?= base_url('public') ?>/libs/bootstrap/js/bootstrap.bundle.min.js"></script>

	<!-- daterangepicker -->
	<script src="<?= base_url('public') ?>/libs/moment/moment.min.js"></script>
	<script src="<?= base_url('public') ?>/libs/daterangepicker/daterangepicker.js"></script>
	<!-- Tempusdominus Bootstrap 4 -->
	<script src="<?= base_url('public') ?>/libs/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
	<!-- Summernote -->
	<script src="<?= base_url('public') ?>/libs/summernote/summernote-bs4.min.js"></script>
	<script src="<?= base_url('public') ?>/libs/sweetalert2/sweetalert2.js"></script>
	<!-- overlayScrollbars -->
	<script src="<?= base_url('public') ?>/libs/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
	<script src="<?= base_url('public') ?>/libs/jquery-knob/jquery.knob.min.js"></script>

	<!-- Datatable -->
	<script src="<?= base_url('public') ?>/libs/datatables/jquery.dataTables.min.js"></script>
	<script src="<?= base_url('public') ?>/libs/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
	<script src="<?= base_url('public') ?>/libs/datatables-responsive/js/dataTables.responsive.min.js"></script>
	<script src="<?= base_url('public') ?>/libs/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
	<script src="<?= base_url('public') ?>/libs/datatables-buttons/js/dataTables.buttons.min.js"></script>
	<script src="<?= base_url('public') ?>/libs/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
	<script src="<?= base_url('public') ?>/libs/datatables-buttons/js/buttons.html5.min.js"></script>
	<script src="<?= base_url('public') ?>/libs/datatables-buttons/js/buttons.print.min.js"></script>
	<script src="<?= base_url('public') ?>/libs/datatables-buttons/js/buttons.colVis.min.js"></script>
	<script src="<?= base_url('public') ?>/back/js/menu-dinamis.js"></script>
	<script src="<?= base_url('public') ?>/back/js/menu-din.js"></script>
	<!-- AdminLTE App -->
	<script src="<?= base_url('public') ?>/back/js/adminlte.js"></script>
	<?php echo '<script>';
	if (isset($scripts)) : ?>
		<?php foreach ($scripts as $i => $script) :
			$this->load->view($script);
		endforeach; ?>
	<?php endif;
	echo '</script>';
	?>
	<script>
		let site_url = "<?= site_url() ?>"
		let base_url = "<?= base_url() ?>"

		function debounce(func, wait, immediate) {
			var timeout;
			return function() {
				var context = this,
					args = arguments;
				var later = function() {
					timeout = null;
					if (!immediate) func.apply(context, args);
				};
				var callNow = immediate && !timeout;
				clearTimeout(timeout);
				timeout = setTimeout(later, wait);
				if (callNow) func.apply(context, args);
			};
		}

		function showLoading() {
			document.getElementById("spinner-front").classList.add("show");
			document.getElementById("spinner-back").classList.add("show");
		}

		function hideLoading() {
			document.getElementById("spinner-front").classList.remove("show");
			document.getElementById("spinner-back").classList.remove("show");
		}
	</script>
</body>

</html>