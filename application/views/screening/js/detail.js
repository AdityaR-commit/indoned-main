var table;
$(document).ready(function () {
    // $('#pasien_id').change(function () {
    //     $.ajax({
    //         type: "post",
    //         dataType: 'JSON',
    //         url: site_url + '/screening/pasienData',
    //         data: {
    //             id: $(this).val()
    //         },
    //         beforeSend: function () {
    //             showLoading();
    //         },
    //         success: function (response) {
    //             hideLoading();
    //             $('#container-bbi').removeClass('d-none');
    //             $('#bbi').val(response.data.data.berat_badan_ideal);
    //         },
    //         error: function (jqXHR, status, error) {
    //             hideLoading();
    //             $('#container-bbi').addClass('d-none');
    //             $('#bbi').val('');
    //             let msg = jqXHR.responseJSON.message;
    //             Swal.fire({
    //                 title: msg.title,
    //                 icon: "error",
    //                 text: msg.body,
    //             });
    //         },
    //     });
    // })

    $('body').on('click', '#remove-row', function (e) {
        // $("#remove-row").click(function (e) {
        e.preventDefault();
        $(this).closest('tr').remove();
    });
    for (let index = 0; index < parseInt($('#sum_slot').val()); index++) {
        $('#form-slot-' + index).submit(function (e) {
            e.preventDefault();
            let url = $(this).attr('action');
            let argument = $('.foods').length;
            if (argument > 0) {
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
            } else {
                Swal.fire({
                    confirmButtonColor: "#3ab50d",
                    icon: "warning",
                    title: 'Perhatian',
                    text: 'Silahkan isi data pada formulir',
                });
            }

        })
    }
});



function addrow(id) {
    tableBody = $('#slot_' + id + ' tbody');
    let markup = '<tr><td>' +
        '<select name="food[]" class="custom-select form-control form-control-sm foods" required>' +
        '<option value="" disabled selected>Pilih Makanan</option>';
    for (let index = 0; index < foods.length; index++) {
        markup += '<option value="' + foods[index].id + '">' + foods[index].food + '</option > ';
    }

    markup += '</select></td>' +
        '<td><input type="number" name="berat[]" class="form-control" placeholder="Berat (gram)" required></td>' +
        '<td><div class="input-group"><input type="text" name="keterangan[]" class="form-control" placeholder="Keterangan">' +
        '<button type="button" id="remove-row" class="input-group-text btn bg-danger w-auto btn-sm d-flex align-items-center"><i class="fas fa-times fa-fw"></i></button>' +
        '</div></td></tr>';
    tableBody.append(markup);
}
