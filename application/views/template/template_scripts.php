<script src="<?php echo base_url("public/lib/jquery/jquery.min.js") ?>"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="<?php echo base_url('public/lib') ?>/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url('public/lib') ?>/datatables.net-dt/js/dataTables.dataTables.min.js"></script>
<script src="<?php echo base_url('public/lib') ?>/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
<script src="<?php echo base_url('public/lib') ?>/datatables.net-responsive-dt/js/responsive.dataTables.min.js"></script>
<script src="<?= base_url('public') ?>/lib/popper/popper.min.js"></script>
<script src="<?php echo base_url("public/lib/bootstrap/js/bootstrap.bundle.min.js") ?>"></script>
<script src="<?php echo base_url("public/lib/feather-icons/feather.min.js") ?>"></script>
<script src="<?php echo base_url("public/lib/perfect-scrollbar/perfect-scrollbar.min.js") ?>"></script>
<script src="<?php echo base_url("public/lib/jquery.flot/jquery.flot.js") ?>"></script>
<script src="<?php echo base_url("public/lib/jquery.flot/jquery.flot.stack.js") ?>"></script>
<script src="<?php echo base_url("public/lib/jquery.flot/jquery.flot.resize.js") ?>"></script>
<script src="<?php echo base_url("public/lib/jqvmap/jquery.vmap.min.js") ?>"></script>
<!-- <script src="<?php echo base_url("public/lib/jqvmap/maps/jquery.vmap.usa.js") ?>"></script> -->
<script src="<?php echo base_url("public/assets/js/dashforge.js") ?>"></script>
<script src="<?php echo base_url("public/assets/js/dashforge.aside.js") ?>"></script>
<script src="<?php echo base_url("public/assets/js/dashforge.sampledata.js") ?>"></script>
<script src="<?= base_url('public/lib/select2/js/select2.min.js') ?>"></script>
<script src="<?= base_url('public/lib/sweetalert2/sweetalert2.js') ?>"></script>

<script src="<?= base_url('public/lib/bs-custom-file-input/bs-custom-file-input.min.js') ?>"></script>
<script src="<?= base_url('public') ?>/lib/parsleyjs/parsley.min.js"></script>
<script src="<?= base_url('public') ?>/lib/parsleyjs/i18n/id.js"></script>

<script src="<?= base_url('public') ?>/lib/summernote/summernote-bs4.min.js"></script>
<script src="<?= base_url('public') ?>/lib/moment/moment.min.js"></script>
<script src="<?= base_url('public') ?>/lib/daterangepicker/daterangepicker.js"></script>
<!-- <script src="<?= base_url('public') ?>/lib/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script> -->
<script src="<?= base_url('public') ?>/lib/toastr/toastr.min.js"></script>
<script src="<?= base_url('public') ?>/assets/js/scroll-to-top.js"></script>
<script src="<?= base_url('public') ?>/lib/pagination/pagination.min.js"></script>
<?php $this->load->view('template/asinkron'); ?>
<script>
  var site_url = "<?= site_url() ?>";

  $(document).ready(function() {
    $('[data-toggle="tooltip"]').tooltip();
    bsCustomFileInput.init();
  });
</script>
<script>
  function getStatusJual(param) {
    var state = '';
    if (param == '0') {
      state = 'Belum dicek';
    } else if (param == '1') {
      state = 'Sudah dicek';
    }
    return state;
  }

  function getStatusPO(param) {
    var state = '';
    if (param == '0') {
      state = 'Belum disetujui';
    } else if (param == '1') {
      state = 'Disetujui';
    } else if (param == '2') {
      state = 'Dikirim';
    } else if (param == '3') {
      state = 'Diterima';
    }
    return state;
  }

  function getStatusTerimaPO(is_terima_sesuai, pesan) {
    var diterima = '-';
    if (is_terima_sesuai != null) {
      if (is_terima_sesuai == '1') {
        diterima = 'Sesuai';
      } else {
        diterima = 'Tidak sesuai | ' + pesan;
      }
    }
    return diterima;
  }

  function catatanItem(param) {
    var state = param;
    if (param == "" || param == null) {
      state = "-";
    }

    return state;
  }

  $('.select2').select2({
    theme: 'bootstrap4',
  });

  // $('.datepicker').datepicker({
  //   format: 'yyyy-mm-dd',
  //   language: "id",
  //   daysOfWeekHighlighted: "0",
  //   autoclose: true,
  //   todayHighlight: true,
  //   timePicker:true
  // });

  // $('#datepicker').datepicker({
  //   format: 'yyyy-mm-dd',
  //   language: "id",
  //   daysOfWeekHighlighted: "0",
  //   autoclose: true,
  //   todayHighlight: true
  // });


  function showLoading() {
    document.getElementById("spinner-front").classList.add("show");
    document.getElementById("spinner-back").classList.add("show");
  }

  function hideLoading() {
    document.getElementById("spinner-front").classList.remove("show");
    document.getElementById("spinner-back").classList.remove("show");
  }
</script>
<script>
  $(document).ready(function() {
    var menu = "<?= !empty($menu) ? $menu : ''; ?>";
    var active = menu.split('-');
    $('#' + active[0]).addClass('show');
    $('#' + active[0]).addClass('active');
    $('#' + active[0] + '-' + active[1]).addClass('active');
    $('#' + menu).addClass('active');
    //console.log(active[0]+'-'+active[1]);
    //$('.'+active[0]).css("display", "block");
  });
</script>
<script>
  function hanyaAngka(event) {
    var angka = (event.which) ? event.which : event.keyCode
    if (angka != 46 && angka > 31 && (angka < 48 || angka > 57))
      return false;
    return true;
  }
  $(function() {
    $('.number-only').keyup(function(e) {
      if (/\D/g.test(this.value)) {
        // Filter non-digits from input value.
        this.value = this.value.replace(/\D/g, '');
      }
    });
  })
</script>
<script>
  function showSessionExpiredAlert(jqXHR, redirectTo) {
    let errorText = 'Error';
    let sessionExpiredText = 'Session anda telah berakhir, silahkan login kembali untuk masuk ke dashboard.';

    if (jqXHR['responseText'].includes(errorText) && jqXHR['responseText'].includes(sessionExpiredText)) {
      swal(errorText, sessionExpiredText, 'error').then(function() {
        window.location.href = redirectTo;
      });
    };
  }
</script>
<script>
  $(document).ready(function() {
    window.addEventListener('gcrud.datagrid.ready', () => {
      // $('.gc-actions .btn').removeClass('btn-outline-dark');
      // $(".gc-actions .btn").addClass('btn-xs btn-outline-primary');
      $(".gc-actions .btn").addClass('btn-xs');
      $('.grocery-crud-table').addClass('table-sm');
      $('.gc-data-container-text').css("padding", "0px");

    });

    window.addEventListener('gcrud.datagrid.ready', () => {
      $('[name=email]').removeClass('btn-outline-dark');
      $(".gc-actions .btn").addClass('btn-xs btn-outline-primary');
      $('.grocery-crud-table').addClass('table-sm');
      $('.gc-data-container-text').css("padding", "0px");

    });
  });
</script>