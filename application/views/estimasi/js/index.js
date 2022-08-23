
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
            "url": "<?= site_url('estimasi/datatable') ?>",
            "type": "POST"
        },
        "columnDefs": [{
            "targets": [0, 5, 6, 7, 8],
            "orderable": false,
            "searchable": false,
        }],
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

    $('#pasien_id').change(function () {
        $.ajax({
            type: "post",
            dataType: 'JSON',
            url: site_url + '/energi/pasienData',
            data: {
                id: $(this).val()
            },
            beforeSend: function () {
                showLoading();
            },
            success: function (response) {
                hideLoading();
                $('#container-bbi').removeClass('d-none');
                $('#bbi').val(response.data.data.berat_badan_ideal);
            },
            error: function (jqXHR, status, error) {
                hideLoading();
                $('#container-bbi').addClass('d-none');
                $('#bbi').val('');
                let msg = jqXHR.responseJSON.message;
                Swal.fire({
                    title: msg.title,
                    icon: "error",
                    text: msg.body,
                });
            },
        });
    })

    $('#form-hitung').submit(function (e) {
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
                            // location.reload();
                            $('#modal-hitung').modal('hide');
                            table.draw(false);
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

});

function hitung(pasien_id, jenis) {
    $('#form-hitung')[0].reset();
    $('#jenis-text').text(jenis);
    $('#jenis').val(jenis);
    let acuan = null;
    if (jenis == 'ENERGI') {
        acuan = '(25 - 30 kkal)';
    } else if (jenis == 'KARBOHIDRAT') {
        acuan = '(45 - 65%)'
    } else if (jenis == 'PROTEIN') {
        acuan = '(1,2 - 2 Gram)';
    } else if (jenis == 'LEMAK') {
        acuan = '(20 - 30%)';
    }
    $.ajax({
        type: "post",
        dataType: 'JSON',
        url: site_url + '/estimasi/pasienData',
        data: {
            id: pasien_id
        },
        beforeSend: function () {
            showLoading();
        },
        success: function (response) {
            hideLoading();
            $('#pasien_id').val(pasien_id);
            $('#acuan-text').text(acuan);
            $('#nama_pasien').val(response.data.data.nama);
            $('#container-bbi').removeClass('d-none');
            $('#bbi').val(response.data.data.berat_badan_ideal);
        },
        error: function (jqXHR, status, error) {
            hideLoading();
            $('#pasien_id').val('');
            $('#acuan-text').text('');
            $('#nama_pasien').val('');
            $('#container-bbi').addClass('d-none');
            $('#bbi').val('');
            let msg = jqXHR.responseJSON.message;
            Swal.fire({
                title: msg.title,
                icon: "error",
                text: msg.body,
            });
        },
    });
    $('#modal-hitung').modal('show')
}
