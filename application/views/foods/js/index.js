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
            "url": "<?= site_url('foods/datatable') ?>",
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

    function isDateFiltered() {
        let dateData = $('#activationDatePicker').data('daterangepicker');
        let defaultFormat = 'DD/MM/YYYY';

        return !(
            dateData.startDate.format(defaultFormat) === getDefaultStartDate().format(defaultFormat) &&
            dateData.endDate.format(defaultFormat) === getDefaultEndDate().format(defaultFormat)
        )
    }

    function toggleHapusFilter(isFiltered) {
        if (isFiltered) {
            $("#btn-hapus-filter").removeClass("d-none");
        } else {
            $("#btn-hapus-filter").addClass("d-none");
        }
    }

    function toggleFilterStatus(selector, status) {
        if (status) {
            $(selector).addClass("btn-primary");
            $(selector).removeClass("btn-outline-primary");
        } else {
            $(selector).removeClass("btn-primary");
            $(selector).addClass("btn-outline-primary");
        }

    }

});
function tambah() {
    $('#form-makanan')[0].reset();
    $('#form-makanan').attr('action', site_url + '/foods/ajaxtambah');
    $('#action').val('add');
    $('#modal-foods').modal('show');
}

function ubah(id) {
    $('#form-makanan')[0].reset();
    $('#form-makanan').attr('action', site_url + '/foods/ajaxUbah');
    $('#action').val('edit');
    $.ajax({
        type: "post",
        dataType: 'JSON',
        url: site_url + '/foods/ajaxDetail',
        data: {
            id: id
        },
        beforeSend: function () {
            showLoading();
        },
        success: function (response) {
            console.log(response)
            hideLoading();
            $('#id').val(response.id);
            $('#category_id').val(response.category_id);
            $('#food').val(response.food);
            $('#urt').val(response.urt);
            $('#berat').val(response.berat);
            $('#urt_unit').val(response.urt_unit);
            $('#keterangan').val(response.keterangan);
            $('#modal-foods').modal('show');
        },
        error: function (request, status, error) {
            hideLoading()
            Swal.fire('Galat', 'Internal server error', 'error');
        },
    });
}

$('#form-makanan').submit(function (e) {
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
        }
    });

})

function hapus(id) {
    let url = site_url + '/foods/ajaxHapus';
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
        } else {
            console.log('batal')
        }
    });
}

function detail(id) {
    $.ajax({
        type: "post",
        dataType: 'JSON',
        url: site_url + '/foods/ajaxDetail',
        data: {
            id: id
        },
        beforeSend: function () {
            showLoading();
        },
        success: function (response) {
            hideLoading();
            $('#category_detail').val(response.category);
            $('#food_detail').val(response.food);
            $('#urt_detail').val(response.urt);
            $('#urt_unit_detail').val(response.urt_unit);
            $('#berat_detail').val(response.berat);
            $('#keterangan_detail').val(response.keterangan);
            $('#modal-detail').modal('show');
        },
        error: function (request, status, error) {
            hideLoading()
            Swal.fire('Galat', 'Internal server error', 'error');
        },
    });
}
