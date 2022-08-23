
var table;
$(document).ready(function () {
    //datatables
    table = $('#table').DataTable({
        "pageLength": 30,
        "scrollX": true,
        "processing": true,
        "serverSide": true,
        "order": [],
        "ajax": {
            "url": "<?= site_url('patients/datatable') ?>",
            "type": "POST"
        },
        // "columnDefs": [{
        //     "targets": [0, 1, 7, 8],
        //     "orderable": false,
        // }],
        'dom': 'rtip',

    });

    table.on('processing.dt', function (e, settings, processing) {
        if (processing) {
            showLoading()
        } else {
            hideLoading()
        }
    })

    $('#search').keyup(debounce(function () {
        table.search(this.value).draw();

        toggleHapusFilter(isFiltered())
    }, 200));


    $('#dt_len').on('change', function (e) {
        var len_rows = $("#dt_len").val();
        table.page.len(len_rows).draw();
    });

    function isFiltered() {
        return (
            statusFilter.length > 0 ||
            $("#search").val() ||
            $('#filter-layanan').val() !== 'SEMUA' ||
            isDateFiltered()
        )
    }

    function toggleHapusFilter(isFiltered) {
        if (isFiltered) {
            $("#btn-hapus-filter").removeClass("d-none");
        } else {
            $("#btn-hapus-filter").addClass("d-none");
        }
    }

    $('[name=estimasi_opsi]').change(function () {
        if ($(this).val() == 'tinggi_lutut') {
            $('#tinggi_lutut').prop("disabled", false);
            $('#panjang_badan').prop("disabled", true);
            $('#container-lutut').removeClass('d-none');
            $('#container-badan').addClass('d-none')
        } else {
            $('#panjang_badan').prop("disabled", false);
            $('#tinggi_lutut').prop("disabled", true);
            $('#container-badan').removeClass('d-none');
            $('#container-lutut').addClass('d-none');
        }
    })

});
function tambah() {
    $('#form-pasien')[0].reset();
    $('#form-pasien').attr('action', site_url + '/patients/ajaxtambah');
    $('#action').val('add');
    $('#modal-pasien').modal('show');
}

function ubah(id) {
    $('#form-pasien')[0].reset();
    $('#form-pasien').attr('action', site_url + '/patients/ajaxUbah');
    $('#action').val('edit');
    $.ajax({
        type: "post",
        dataType: 'JSON',
        url: site_url + '/patients/ajaxDetail',
        data: {
            id: id
        },
        beforeSend: function () {
            showLoading();
        },
        success: function (response) {
            hideLoading();
            $('#id').val(response.id);
            $('#nama').val(response.nama);
            $('#no_hp').val(response.no_hp);
            $('#jenis_kelamin').val(response.jenis_kelamin).trigger('change');
            $('#alamat').val(response.alamat);
            $('#tinggi_lutut').val(response.tinggi_lutut);
            $('#panjang_badan').val(response.panjang_badan);
            $('#estimasi_opsi').val(response.estimasi_opsi).trigger('change');
            $('#rumus_opsi').val(response.rumus_opsi).trigger('change');
            $('#umur').val(response.umur);
            // $('#keterangan').val(response.keterangan);
            $('#modal-pasien').modal('show');
        },
        error: function (request, status, error) {
            hideLoading()
            Swal.fire('Galat', 'Internal server error', 'error');
        },
    });
}

$('#form-pasien').submit(function (e) {
    e.preventDefault();
    let url = $(this).attr('action');
    Swal.fire({
        icon: "question",
        title: 'Informasi',
        text: "Apakah Anda yakin untuk melanjutkan?",
        showCancelButton: true,
        cancelButtonText: "Batal",
        confirmButtonText: "Simpan",
        reverseButtons: true,
    }).then((result) => {
        if (result.value) {
            $.ajax({
                type: "post",
                dataType: 'JSON',
                url: url,
                data: $(this).serialize(),
                beforeSend: function () {
                    showLoading();
                },
                success: function (response) {
                    hideLoading()
                    Swal.fire({
                        confirmButtonColor: "#3ab50d",
                        icon: "success",
                        title: `${response.message.title}`,
                        text: `${response.message.body}`,
                    }).then((result) => {
                        // document.location.href = `${baseUrl}auth/login`;
                        location.reload();
                    });
                },
                error: function (request, status, error) {
                    hideLoading()
                    Swal.fire({
                        confirmButtonColor: "#3ab50d",
                        icon: "error",
                        title: `${request.responseJSON.message.title}`,
                        text: `${request.responseJSON.message.body}`,
                    });
                },
            });
        } else {
            console.log('batal')
        }
    });

})

function hapus(id) {
    let url = site_url + '/patients/ajaxHapus';
    Swal.fire({
        icon: "question",
        title: 'Informasi',
        text: "Apakah Anda yakin untuk menghapus data?",
        showCancelButton: true,
        cancelButtonText: "Batal",
        confirmButtonText: "Hapus",
        reverseButtons: true,
    }).then((result) => {
        if (result.value) {
            $.ajax({
                type: "post",
                dataType: 'JSON',
                url: url,
                data: {
                    id: id
                },
                beforeSend: function () {
                    showLoading();
                },
                success: function (response) {
                    hideLoading()
                    Swal.fire({
                        confirmButtonColor: "#3ab50d",
                        icon: "success",
                        title: `${response.message.title}`,
                        text: `${response.message.body}`,
                    }).then((result) => {
                        // document.location.href = `${baseUrl}auth/login`;
                        location.reload();
                    });
                },
                error: function (request, status, error) {
                    hideLoading()
                    Swal.fire({
                        confirmButtonColor: "#3ab50d",
                        icon: "error",
                        title: `${request.responseJSON.message.title}`,
                        text: `${request.responseJSON.message.body}`,
                    });
                },
            });
        }
    });
}

function detail(id) {
    $.ajax({
        type: "post",
        dataType: 'JSON',
        url: site_url + '/patients/ajaxDetail',
        data: {
            id: id
        },
        beforeSend: function () {
            showLoading();
        },
        success: function (response) {
            hideLoading();
            $('#category_detail').val(response.category);
            $('#energi_detail').val(response.energi);
            $('#protein_detail').val(response.protein);
            $('#lemak_detail').val(response.lemak);
            $('#keterangan_detail').val(response.keterangan);
            $('#modal-detail').modal('show');
        },
        error: function (request, status, error) {
            hideLoading()
            Swal.fire('Galat', 'Internal server error', 'error');
        },
    });
}

function hitung(id) {
    Swal.fire({
        icon: "question",
        title: 'Informasi',
        text: "Apakah Anda yakin untuk menghitung estimasi BBI?",
        showCancelButton: true,
        cancelButtonText: "Batal",
        confirmButtonText: "Lanjut",
        reverseButtons: true,
    }).then((result) => {
        if (result.value) {
            $.ajax({
                type: "post",
                dataType: 'JSON',
                url: site_url + '/patients/ajaxHitung',
                data: {
                    id: id
                },
                beforeSend: function () {
                    showLoading();
                },
                success: function (response) {
                    hideLoading();
                    Swal.fire({
                        confirmButtonColor: "#3ab50d",
                        icon: "success",
                        title: `${response.message.title}`,
                        text: `${response.message.body}`,
                    }).then((result) => {
                        // document.location.href = `${baseUrl}auth/login`;
                        location.reload();
                    });
                },
                error: function (request, status, error) {
                    hideLoading()
                    Swal.fire('Galat', 'Internal server error', 'error');
                },
            });
        }
    });
}
