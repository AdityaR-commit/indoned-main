<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// ! require jangan ditaruh disini, server belum support 👍
// require_once FCPATH . '/vendor/autoload.php';

/**
 * @see \Mpdf\Output\Destination
 * @see https://mpdf.github.io/reference/mpdf-functions/output.html
 * @param string $idVendor Required
 * @param string $dest Optional. Default \Mpdf\Output\Destination::INLINE
 * @return void|string
 */
function pdf_perjanjianKerjasamaVendor($idVendor, $dest = "I")
{
    require_once FCPATH . '/vendor/autoload.php';
    $ci = &get_instance();
    $ci->load->model('M_vendor');
    $ci->load->model('M_pengaturan_sistem');

    $ci->M_vendor->_buildTemplate();

    $delimiter = [
        0 => '{~', /* Delimiter awal */
        1 => '~}'  /* Delimiter akhir */
    ];

    $vendor = $ci->M_vendor->getVendorUser(NULL, ['vendors.id' => $idVendor], NULL, NULL, 1);

    if (!$vendor) {
        return show_custom_404();
    };

    $fileName_with_ext = "surat-perjanjian-kerjasama-vendor_{$vendor->{'vendor_name'}}.pdf";

    $variable = [
        'VENDOR_NAME'     => $vendor->{'vendor_name'}     ? $vendor->{'vendor_name'}     : '-',
        'VENDOR_NIK'      => $vendor->{'nik'}             ? $vendor->{'nik'}             : '-',
        'VENDOR_PHONE'    => $vendor->{'vendor_phone'}    ? $vendor->{'vendor_phone'}    : '-',
        'VENDOR_ADDRESS'  => $vendor->{'vendor_address'}  ? $vendor->{'vendor_address'}  : '-',
        'VENDOR_DESA'     => $vendor->{'vendor_desa'}     ? $vendor->{'vendor_desa'}     : '-',
        'VENDOR_KEC'      => $vendor->{'vendor_kec'}      ? $vendor->{'vendor_kec'}      : '-',
        'VENDOR_KAB'      => $vendor->{'vendor_kab'}      ? $vendor->{'vendor_kab'}      : '-',
        'VENDOR_PROV'     => $vendor->{'vendor_prov'}     ? $vendor->{'vendor_prov'}     : '-',
        'VENDOR_KODE_POS' => $vendor->{'vendor_kode_pos'} ? $vendor->{'vendor_kode_pos'} : '-',
    ];

    // $html = file_get_contents('application/views/template/pdf/perjanjian_kerjasama_vendor/index.html');
    $html = $ci->M_pengaturan_sistem->_getByName(KEY_TMPLT_VNDR_AGRMNT_CUSTOM)->{'value'};

    // Mengubah variabel template dari $variable
    foreach ($variable as $key => $value) {
        $html = str_replace("{$delimiter[0]}{$key}{$delimiter[1]}", $value, $html);
    };

    $pengaturanSistems = $ci->M_pengaturan_sistem->getAll();
    if ($pengaturanSistems && is_array($pengaturanSistems)) {
        // Mengubah variabel template dari $pengaturanSistems
        foreach ($pengaturanSistems as $pSistem) {
            // Mencegah mereplace Kunci Template itu Sendiri
            if (in_array($pSistem['name'], [KEY_TMPLT_VNDR_AGRMNT_ORIGIN, KEY_TMPLT_VNDR_AGRMNT_CUSTOM])) {
                continue;
            };
            $html = str_replace($delimiter[0] . $pSistem['name'] . $delimiter[1], $pSistem['value'], $html);
        };
    };

    $mpdf = new \Mpdf\Mpdf([
        'format'     => 'A4',
    ]);
    $mpdf->setFooter('{PAGENO} / {nbpg}');
    $mpdf->WriteHTML($html);
    return $mpdf->Output($fileName_with_ext, $dest);
}

/**
 * @see \Mpdf\Output\Destination
 * @see https://mpdf.github.io/reference/mpdf-functions/output.html
 * @param  string      $nomor_invoice           Required
 * @param  string      $registrasi_atau_bulanan Required. You can insert 'BULANAN' for bulanan, whatever string for registrasi
 * @param  string      $dest                    Optional. Default \Mpdf\Output\Destination::INLINE
 * @return void|string
 */
function pdf_tagihan_invoice($nomor_invoice, $registrasi_atau_bulanan, $dest = "I")
{
    require_once FCPATH . '/vendor/autoload.php';
    $ci = &get_instance();
    $ci->load->model('M_invoice');
    $ci->load->model('M_rekening');

    $invoice = (array)$ci->M_invoice->getInvoicesbyNumber($nomor_invoice);
    if (!$invoice) {
        return show_custom_404();
    };

    $invoice['status_color'] = 'dark';
    switch (strtoupper($invoice['status'])) {
        case 'LUNAS':
            $invoice['status_color'] = 'success';
            break;
        case 'BELUM LUNAS':
            $invoice['status_color'] = 'warning';
            break;
        case 'LEWAT':
            $invoice['status_color'] = 'danger';
            break;
    };
    $invoice['pelanggan__nomor_registrasi'] = $ci->db->where(['id' => $invoice['pelanggan__id']])->get('pelanggan')->row_array()['nomor_registrasi'];
    $invoice['tgl_invoice']  = isset($invoice['tgl_invoice'])  ? $ci->tanggalindo->konversi(date('Y-m-d', strtotime($invoice['tgl_invoice'])))  : '-';
    $invoice['tgl_deadline'] = isset($invoice['tgl_deadline']) ? $ci->tanggalindo->konversi(date('Y-m-d', strtotime($invoice['tgl_deadline']))) : '-';

    $tagihan_capel   = NULL;
    $monthly_or_once = ($registrasi_atau_bulanan && strtoupper($registrasi_atau_bulanan) == 'BULANAN') ? 'MONTHLY' : null;
    $tagihan_capel   = $ci->M_invoice->getInvoiceOrderDetailsByRefOrderId($invoice['invoice_order_detail__ref_order_id'], $monthly_or_once);

    $invoice['html_tagihan']  = '';
    $invoice['total_tagihan'] = $invoice['total'];
    // $invoice['sub_total'] = 0;
    $nomor = 1;
    foreach ($tagihan_capel as $no => $tagihan) {
        if (strtolower($tagihan['invoice_order_detail__nama_tagihan']) != 'kode unik') {
            $invoice['html_tagihan'] .= '
                <tr>
                    <td>' . $nomor . '</td>
                    <td>' . $tagihan['invoice_order_detail__nama_tagihan'] . '</td>
                    <td style="text-align: right;">' . currencyIDR($tagihan['invoice_order_detail__jumlah_tagihan']) . '</td>
                </tr>';
            // $invoice['total_tagihan'] += $tagihan['invoice_order_detail__jumlah_tagihan'];
            // $invoice['sub_total'] += $tagihan['invoice_order_detail__jumlah_tagihan'];
            $nomor++;
        }
    };
    $invoice['total_tagihan'] += $invoice['kode_unik'];
    // sub total
    $invoice['html_tagihan'] .= '<tr>
    <td colspan="2" style="text-align: right;" class="fw-bold">Sub Total</td>
    <td style="text-align: right;" class="fw-bold">' . currencyIDR($invoice['sub_total']) . '</td>
    </tr>';
    // diskon
    $invoice['html_tagihan'] .= '<tr>
    <td colspan="2" style="text-align: right;">Diskon (' . getPersentDiscount($invoice['sub_total'], $invoice['diskon']) . '%)</td>
    <td style="text-align: right;">(' . currencyIDR($invoice['diskon']) . ')</td>
    </tr>';
    //kode unik
    $invoice['html_tagihan'] .= '<tr>
    <td colspan="2" style="text-align: right;">Kode unik</td>
    <td style="text-align: right;">' . currencyIDR($invoice['kode_unik']) . '</td>
    </tr>';
    $invoice['total_tagihan'] = currencyIDR($invoice['total_tagihan']);
    // $invoice['html_rek_asnet'] = '';
    // foreach ($ci->M_rekening->rekAsnet() as $rekening) {
    //     $invoice['html_rek_asnet'] .= '<br>' . $rekening['nama_bank'] . ' ' . $rekening['nomor_rekening']
    //         . '<br> a.n ' . $rekening['nama_rekening']
    //         . '<br>';
    // };

    $fileName_with_ext = "invoice-$nomor_invoice.pdf";
    $dest = returnDefault($dest, 'I');

    return generate_pdf(
        'template/pdf/invoice_reg-bul',
        $invoice,
        $fileName_with_ext,
        'A4',
        $dest
    );
}

/**
 *
 * @see \Mpdf\Output\Destination
 * @see https://mpdf.github.io/reference/mpdf-functions/output.html
 * @param string $dest Optional. Default \Mpdf\Output\Destination::INLINE
 * @param string $invoices__number
 *
 * @return string|void pdf
 */
function pdf_invoice_manual(
    $invoices__number,
    $dest = 'I'
    /** , 'MANUAL' */
) {
    error_reporting(0);
    require_once FCPATH . '/vendor/autoload.php';
    $ci = &get_instance();
    $ci->load->model('M_invoice');
    $ci->load->model('M_rekening');
    $ci->load->model('M_invoice_manual_detail');

    $data = $ci->M_invoice->getInvoiceManual([
        generate_all_column_as('invoices', 'invoices__', ''),
    ], [
        'invoices.number' => $invoices__number,
    ], 1);
    /** OBJECT */
    if (!$data) {
        return show_custom_404();
    };

    /** BEGIN setting warna status */
    $classColor = [
        'LUNAS'       => 'success',
        'BELUM LUNAS' => 'warning',
        'LEWAT'       => 'danger',
    ];
    $data->{'status_color'} = array_key_exists($data->{'invoices__status'}, $classColor) ? $classColor[$data->{'invoices__status'}] : 'dark';
    /** END setting warna status */

    $data->{'tagihan'} = $ci->M_invoice_manual_detail->getByRefInvoiceId($data->{'invoices__id'});

    // $data->{'html_rek_asnet'} = '';
    // foreach ($ci->M_rekening->rekAsnet() as $rekening) {
    //     $data->{'html_rek_asnet'} .= '<br>' . $rekening['nama_bank'] . ' ' . $rekening['nomor_rekening']
    //         . '<br> a.n ' . $rekening['nama_rekening']
    //         . '<br>';
    // };

    $fileName_with_ext = "invoice-{$invoices__number}.pdf";
    // $dest = returnDefault($dest, 'D');
    $dest = returnDefault($dest, 'I');

    return generate_pdf(
        'template/pdf/invoice_manual',
        $data,
        $fileName_with_ext,
        'A4',
        $dest
    );
}

/**
 *
 * @see \Mpdf\Output\Destination
 * @see https://mpdf.github.io/reference/mpdf-functions/output.html
 * @param string $id_spk Required. Column spk.id
 * @param string $dest Optional. Default \Mpdf\Output\Destination::DOWNLOAD
 *
 * @return void|string
 */
function pdf_spk_instalasi_baru($id_spk, $dest = "D")
{
    require_once FCPATH . '/vendor/autoload.php';
    $ci = &get_instance();
    $ci->load->model('dashboard/pelanggan/M_capel');
    $ci->load->model('M_invoice_order_detail');
    $ci->load->model('M_invoice');
    $ci->load->model('M_spk_instalasi');
    $ci->load->model('M_pengaturan_sistem');
    $ci->load->model('M_spk');

    $data_pdf = $ci->M_capel->data_pdf($id_spk);

    if (!$data_pdf) {
        return show_custom_404();
    };

    $data_pdf['tagihan_capel'] = $ci->M_invoice_order_detail->by_ref_order_id($data_pdf['ref_order_id']);

    /* MENGATUR NAMA TEKNISI */
    $executor_name = null;
    $executorsList = $ci->M_spk->getExecutorsBySpkId($id_spk);
    if (count($executorsList) > 1) {
        $executor_name = implode(', ', array_map(function ($executor) {
            return explode(' ', $executor['user__real_name'])[0];
        }, $executorsList));
    } elseif (count($executorsList) == 1) {
        $executor_name = $executorsList[0]['user__real_name'];
    };
    $data_pdf['executor_name'] = $executor_name;
    $data_pdf['kode_unik'] = '0';
    /* MENGATUR NAMA TEKNISI */

    $data_pdf['html_tagihan_capel'] = '';
    if ($data_pdf['tagihan_capel']) {
        foreach ($data_pdf['tagihan_capel'] as $tagihan_capel) {

            $tagihan_capel = (array)$tagihan_capel;

            if ($tagihan_capel['nama_tagihan'] === 'Kode unik') {
                $data_pdf['kode_unik'] = ($tagihan_capel['jumlah_tagihan']);
                continue;
            }

            switch (strtoupper($tagihan_capel['jenis_tagihan'])) {
                case 'MONTHLY':
                    $tagihan_capel['jenis_tagihan'] = 'Bulanan';
                    break;
                case 'ONCE':
                    $tagihan_capel['jenis_tagihan'] = 'Sekali';
                    break;
            };

            $data_pdf['html_tagihan_capel'] .= "
                <tr>
                    <td>" . ($tagihan_capel['nama_tagihan'] ? $tagihan_capel['nama_tagihan'] : '-') . " ({$tagihan_capel['jenis_tagihan']})</td>
                    <td>" . currencyIDR($tagihan_capel['jumlah_tagihan'] ? $tagihan_capel['jumlah_tagihan'] : 0) . "</td>
                </tr>
            ";
        };
    };

    $data_pdf['tanggal'] = $ci->tanggalindo->konversi($data_pdf['tanggal']);
    $data_pdf['discount_percentage'] = intval(($data_pdf['discount'] / $data_pdf['amount']) * 100);
    $data_pdf['amount']       = currencyIDR($data_pdf['amount']);
    $data_pdf['discount']     = currencyIDR($data_pdf['discount']);
    $data_pdf['total_amount'] = currencyIDR($data_pdf['total_amount'] + $data_pdf['kode_unik']);

    $data_pdf['kode_unik'] = currencyIDR($data_pdf['kode_unik']);

    $instalasi_perangkat = $ci->M_spk_instalasi->get_perangkat_instalasi($data_pdf['spk_instalasi_id']);
    $data_pdf['html_instalasi_perangkat'] = '';

    $number = 0;

    foreach ($instalasi_perangkat as $perangkat) {
        $perangkat = (array) $perangkat;
        $html_temp = '
                <tr>
                    <td class="text-center">' . ++$number . '</td>
                    <td class="text-start">' . $perangkat['nama'] . '</td>
                    <td></td>
                    <td class="text-center">OK / NOK</td>
                </tr>
            ';
        $data_pdf['html_instalasi_perangkat'] .= $html_temp;
    };

    $data_pdf['html_instalasi_perangkat'] .= '
        <tr><td class="text-center">' . ++$number . '</td><td class="text-start">Kabel FO</td><td class="text-end">' . $data_pdf['panjang_kabel_fo'] . ' meter</td><td class="text-center">OK / NOK</td></tr>
        <tr><td class="text-center">' . ++$number . '</td><td class="text-start">Kabel LAN</td><td class="text-end">' . $data_pdf['panjang_kabel_lan'] . ' meter</td><td class=\"text-center\">OK / NOK</td></tr>
    ';

    $urlQrCode = generate_qr_code(
        urlGoogleMapsByLatLong(
            $data_pdf['latitude'],
            $data_pdf['longitude']
        )
    );
    $data_pdf['qrcode_path'] = $urlQrCode['file_path'];

    $data_pdf['title'] = 'FORMULIR REGISTRASI';

    $syaratDanKetentuanBerlangganan = $ci->M_pengaturan_sistem->getSyaratDanKetentuanBerlangganan();
    $data_pdf['syaratDanKetentuanBerlangganan'] = $syaratDanKetentuanBerlangganan->{'value'};

    $file_name = "spk-instalasi-baru-" . $data_pdf['nomor_registrasi'];

    return generate_pdf(
        'template/pdf/spk_instalasi_baru.php',
        $data_pdf,
        $file_name . '.pdf',
        'A4',
        $dest
    );
}

/**
 * @param string|int $spk__id Required. Form column spk.id
 * @param string $dest Optional. Default 'D'
 * @see \Mpdf\Output\Destination
 * @see https://mpdf.github.io/reference/mpdf-functions/output.html
 * @return void|string pdf
 */
function pdf_spk_migrasi($spk_id, $dest = 'D')
{
    // error_reporting(0);
    require_once FCPATH . '/vendor/autoload.php';
    $ci = &get_instance();
    $ci->load->model('M_spk_migrasi');
    $ci->load->model('M_spk');

    if (!$spk_id) {
        return show_custom_404();
    };

    $ci->load->model('M_pelanggan_update_layanan');

    $data = $ci->M_spk_migrasi->getBySpkId($spk_id);

    if (!$data) {
        return show_custom_404();
    };

    $data = (array) $data;

    $spk_perangkat = array_map(function ($perangkat) {
        return is_object($perangkat) ? (array) $perangkat : $perangkat;
    }, $ci->M_spk_migrasi->getSpkPerangkatBySpkId($spk_id));

    $spk_perangkat_diambil = array_map(function ($perangkat) {
        return is_object($perangkat) ? (array) $perangkat : $perangkat;
    }, $ci->M_spk_migrasi->getSpkPerangkatDiambilBySpkId($spk_id));

    $biaya_perubahan = 0;

    $data['total_biaya']     = intval($biaya_perubahan) + intval($data['pelanggan_update_layanan__biaya_bulanan_baru']);
    $data['biaya_perubahan'] = $biaya_perubahan;

    $data['qrCodeUrl'] = generate_qr_code(urlGoogleMapsByLatLong($data['pelanggan__latitude'], $data['pelanggan__longitude']))['url_path'];

    $data['spk_perangkat']         = $spk_perangkat;
    $data['spk_perangkat_diambil'] = $spk_perangkat_diambil;

    $executors_list = $ci->M_spk->getExecutorsBySpkId($spk_id);
    $data['executor_name'] = null;
    if (count($executors_list) > 1) {
        $data['executor_name'] = implode(', ', array_map(function ($executor) {
            return explode(' ', $executor['user__real_name'])[0];
        }, $executors_list));
    } elseif (count($executors_list) == 1) {
        $data['executor_name'] = $executors_list[0]['user__real_name'];
    };

    $file_name_n_ext = 'spk-migrasi-' . $data['spk__nomor_spk'] . '.pdf';
    $dest = returnDefault($dest, 'D');
    return generate_pdf('template/pdf/spk_migrasi', $data, $file_name_n_ext, 'A4', $dest);
}

/**
 * @param string|int $spk__id Required. Form column spk.id
 * @param string     $dest Optional. Default 'D'
 * @see \Mpdf\Output\Destination
 * @see https://mpdf.github.io/reference/mpdf-functions/output.html
 * @return void|string pdf
 */
function pdf_spk_relokasi($spk__id, $dest = 'D')
{
    // error_reporting(0);
    require_once FCPATH . '/vendor/autoload.php';
    $ci = &get_instance();
    $ci->load->model('M_spk_relokasi');
    $ci->load->model('M_pelanggan_relokasi');
    $ci->load->model('M_spk');

    if (!$spk__id) {
        return show_custom_404();
    };

    $data = $ci->M_spk_relokasi->getBySpkId($spk__id);

    if (!$data) {
        return show_custom_404();
    };

    $data = (array) $data;

    $spk_perangkat = array_map(function ($perangkat) {
        return is_object($perangkat) ? (array) $perangkat : $perangkat;
    }, $ci->M_spk_relokasi->getSpkPerangkatBySpkId($spk__id));

    $spk_perangkat_diambil = array_map(function ($perangkat) {
        return is_object($perangkat) ? (array) $perangkat : $perangkat;
    }, $ci->M_spk_relokasi->getSpkPerangkatDiambilBySpkId($spk__id));

    $data['spk_perangkat']         = $spk_perangkat;
    $data['spk_perangkat_diambil'] = $spk_perangkat_diambil;

    $executors_list = $ci->M_spk->getExecutorsBySpkId($spk__id);
    $data['executor_name'] = null;
    if (count($executors_list) > 1) {
        $data['executor_name'] = implode(', ', array_map(function ($executor) {
            return explode(' ', $executor['user__real_name'])[0];
        }, $executors_list));
    } elseif (count($executors_list) == 1) {
        $data['executor_name'] = $executors_list[0]['user__real_name'];
    };

    $file_name_n_ext = 'spk-relokasi-' . $data['spk__nomor_spk'] . '.pdf';
    $dest = returnDefault($dest, 'D');
    return generate_pdf('template/pdf/spk_relokasi', $data, $file_name_n_ext, 'A4', $dest);
}

/**
 * @param string|int $spk__id Required. Form column spk.id
 * @param string     $dest Optional. Default 'D'
 * @see \Mpdf\Output\Destination
 * @see https://mpdf.github.io/reference/mpdf-functions/output.html
 * @return void|string pdf
 */
function pdf_spk_aktivasi_ulang($spk__id, $dest = 'D')
{
    // error_reporting(0);
    require_once FCPATH . '/vendor/autoload.php';
    $ci = &get_instance();
    $ci->load->model('M_spk_aktivasi_pelanggan_off');
    $ci->load->model('M_spk');

    if (!$spk__id) {
        return show_custom_404();
    };

    $data = $ci->M_spk_aktivasi_pelanggan_off->getDataPdf($spk__id, true);
    if (!$data) {
        return show_custom_404();
    };

    $data['spk_perangkat'] = $ci->M_spk->spkPerangkatBySpkId($spk__id);

    $executors_list = $ci->M_spk->getExecutorsBySpkId($spk__id);
    $data['executor_name'] = null;
    if (count($executors_list) > 1) {
        $data['executor_name'] = implode(', ', array_map(function ($executor) {
            return explode(' ', $executor['user__real_name'])[0];
        }, $executors_list));
    } elseif (count($executors_list) == 1) {
        $data['executor_name'] = $executors_list[0]['user__real_name'];
    };

    $file_name_n_ext = 'spk-aktivasi-ulang-' . $data['spk__nomor_spk'] . '.pdf';
    $dest = returnDefault($dest, 'D');
    return generate_pdf('template/pdf/spk/spk_aktivasi_ulang', $data, $file_name_n_ext, 'A4', $dest);
}

/**
 * @param string|int $spk__id Required. Form column spk.id
 * @param string     $dest Optional. Default 'D'
 * @see \Mpdf\Output\Destination
 * @see https://mpdf.github.io/reference/mpdf-functions/output.html
 * @return void|string pdf
 */
function pdf_spk_pengambilan_perangkat($spk__id, $dest = 'D')
{
    // error_reporting(0);
    require_once FCPATH . '/vendor/autoload.php';
    $ci = &get_instance();
    $ci->load->model('M_spk_pengambilan_perangkat');
    $ci->load->model('M_spk');

    if (!$spk__id) {
        return show_custom_404();
    };

    $data = $ci->M_spk_pengambilan_perangkat->getDataPdf($spk__id, true);
    if (!$data) {
        return show_custom_404();
    };

    $data['spk_perangkat_diambil'] = $ci->M_spk->spkPerangkatDiambilBySpkId($spk__id);

    $executors_list = $ci->M_spk->getExecutorsBySpkId($spk__id);
    $data['executor_name'] = null;
    if (count($executors_list) > 1) {
        $data['executor_name'] = implode(', ', array_map(function ($executor) {
            return explode(' ', $executor['user__real_name'])[0];
        }, $executors_list));
    } elseif (count($executors_list) == 1) {
        $data['executor_name'] = $executors_list[0]['user__real_name'];
    };

    $file_name_n_ext = 'spk-pengambilan-perangkat_' . $data['spk__nomor_spk'] . '.pdf';
    $dest = returnDefault($dest, 'D');
    return generate_pdf('template/pdf/spk/spk_pengambilan_perangkat', $data, $file_name_n_ext, 'A4', $dest);
}

/**
 * untuk template pdf tugas 2022/07/02
 */
function pdf_spk__2022_07_02($dest = 'D')
{
    $data = [];
    $filename_n_ext = 'test-spk.pdf';
    $dest = returnDefault($dest, 'D');
    return generate_pdf('template/pdf/spk/template_spk__2022_07_02', $data, $filename_n_ext, 'A4', $dest);
}

/**
 * untuk template pdf tugas 2022/07/02
 */
function pdf_spk_lainnya($idSpk, $dest = 'D')
{
    $ci = &get_instance();
    $ci->load->model("M_spk_lainnya", "spk_lainnya");
    $spkLainnya = $ci->spk_lainnya->getSpkLainnyaByIdSpk($idSpk);
    $data = $ci->spk_lainnya->detail($spkLainnya->id);
    $jenis = str_replace(' ', '_', $data->jenis);
    $filename_n_ext = "SPK-$jenis-$data->nomor_spk.pdf";
    $dest = returnDefault($dest, 'D');
    return generate_pdf('template/pdf/spk/spk_lainnya', $data, $filename_n_ext, 'A4', $dest);
}

function pdf_spk_mainline($idSpk, $dest = 'D')
{
    $ci = &get_instance();
    $ci->load->model("M_spk_general", "spk");
    $spk = $ci->spk->getSpk($idSpk);
    $spk->executors = $ci->spk->getExecutorsByIdSpk($idSpk);
    $spk->equipments = $ci->spk->getEquipmentsByIdSpk($idSpk);

    $spk->tos = [
        "Pekerjaan harus sesuai dengan peta kerja yang telah disahkan / ditetapkan.",
        "Penerima perintah kerja ini sanggup memberikan jaminan mutu atas pekerjaannya",
        "Untuk menjamin mutu dan kelancaran pekerjaan, mulai dari persiapan sampai pelaksanaan di
        lapangan, pelaksana diharap bersedia dan selalu berkoordinasi dengan PIHAK PERTAMA."
    ];



    $dari = date_create($spk->tanggal_penugasan);
    $sampai = date_create($spk->tanggal_selesai_penugasan);
    $interval = date_diff($dari, $sampai);

    $spk->interval = $interval->format('%a');
    $filename_n_ext = "SPK-MAINLINE-$spk->nomor_spk.pdf";
    $dest = returnDefault($dest, 'D');
    return generate_pdf('template/pdf/spk/spk_mainline2', $spk, $filename_n_ext, 'A4', $dest);
}

/**
 * @see \Mpdf\Output\Destination
 * @see https://mpdf.github.io/reference/mpdf-functions/output.html
 * @param string     $viewPath          Required
 * @param array      $data              Required
 * @param string     $fileName_with_ext Optional. Default ''
 * @param string     $format_file       Optional. Default 'A4'
 * @param string     $dest              Optional. Default \Mpdf\Output\Destination::DOWNLOAD
 * @param bool       $header            Optional. Default false
 * @param bool       $pageNumber        Optional. Default false
 * @param array|null $customConfig      Optional. Default null. This will override $format_file
 * @return void|string
 */
function generate_pdf($viewPath, $data, $fileName_with_ext = '', $format_file = 'A4', $dest = "D", $header = false, $pageNumber = false, $customConfig = null)
{
    require_once FCPATH . '/vendor/autoload.php';
    $ci = &get_instance();

    if (!is_null($customConfig)) {
        $mpdf = new \Mpdf\Mpdf($customConfig);
    } else {
        $mpdf = new \Mpdf\Mpdf(['format' => $format_file]);
    };
    // Create an instance of the class:

    if ($header === TRUE) {
        $mpdf->SetHTMLHeader($ci->load->view('template/pdf/part/header', [], TRUE));
    };

    if ($pageNumber === TRUE) {
        // Set a simple Footer including the page number
        $mpdf->setFooter('{PAGENO} / {nbpg}');
    };

    // Write some HTML code:
    $mpdf->WriteHTML($ci->load->view($viewPath, $data, TRUE));

    // Check $dest is valid
    $validDest = [
        'FILE'          => 'F',
        'DOWNLOAD'      => 'D',
        'STRING_RETURN' => 'S',
        'INLINE'        => 'I',
    ];
    $dest = in_array($dest, $validDest) ? $dest : "D";

    // Output a PDF file directly to the browser
    return $mpdf->Output($fileName_with_ext, $dest);
}

/**
 * @see \Mpdf\Output\Destination
 * @see https://mpdf.github.io/reference/mpdf-functions/output.html
 * @param string $id_perubahan Required
 * @param string $dest         Optional. Default \Mpdf\Output\Destination::DOWNLOAD
 * @return void|string
 */
function pdf_perubahan_layanan($id_perubahan, $dest = "D")
{
    require_once FCPATH . '/vendor/autoload.php';
    $ci = &get_instance();
    $ci->load->model('M_perubahan_layanan'); //Tidak dibuat alias, karena ter-override yang memanggil fungsi ini
    $data = $ci->M_perubahan_layanan->getPerubahanLayanan($id_perubahan);
    if (!$data) {
        return show_custom_404();
    };

    $data['layanan_lama'] = $ci->M_perubahan_layanan->getLayananLama($id_perubahan);
    $data['layanan_baru'] = $ci->M_perubahan_layanan->getLayananBaru($id_perubahan);

    $data['qrCodePathAlamatPemasangan'] = generate_qr_code(urlGoogleMapsByLatLong($data['latitude'], $data['longitude']))['file_path'];

    $nama_file = '';
    $template  = '';
    switch ($data['id_jenis_update_layanan']) {
        case '1':
        case '2':
            $nama_file = 'Form-perubahan-' . $data['nomor_perubahan'];
            $template = 'template/pdf/formulir/perubahan_layanan.php';

            break;
        case '3':
            $nama_file = 'Form-migrasi-' . $data['nomor_perubahan'];
            $template = 'template/pdf/formulir/migrasi_layanan.php';

            break;
    };

    return generate_pdf(
        $template,
        $data,
        $nama_file . '.pdf',
        'A4',
        $dest
    );
}

/**
 * @param string $dest Optional. Default \Mpdf\Output\Destination::INLINE
 * @return void|string
 */
function pdf_spk($dest = "I")
{
    require_once FCPATH . '/vendor/autoload.php';
    return generate_pdf(
        'template/pdf/spk.php',
        [],
        'spk.pdf',
        'A4',
        $dest
    );
}

/**
 * @see \Mpdf\Output\Destination
 * @see https://mpdf.github.io/reference/mpdf-functions/output.html
 * @param string|int $pelanggan_aktivasi_off__id Required. From column pelanggan_aktivasi_off.id
 * @param string $dest Optional. Default 'D'
 * @return void|string pdf
 */
function pdf_formulir_aktivasi_off($pelanggan_aktivasi_off__id, $dest = 'D')
{
    // error_reporting(0);
    require_once FCPATH . '/vendor/autoload.php';
    $ci = &get_instance();

    if (!$pelanggan_aktivasi_off__id) {
        return show_custom_404();
    };

    $ci->load->model('M_pelanggan_aktivasi_off');

    $data = $ci->M_pelanggan_aktivasi_off->getById($pelanggan_aktivasi_off__id, true);

    if (!$data) {
        return show_custom_404();
    };

    $biaya_perubahan = intval(getSystemSetting('biaya_aktivasi_pelanggan_off'));
    $max             = intval(getSystemSetting('max_distance_odp'));
    $basePrice       = intval(getSystemSetting('biaya_kabel_fo_tambahan'));
    $lan             = intval($data['pelanggan_aktivasi_off__panjang_kabel_lan']);
    $fo              = intval($data['pelanggan_aktivasi_off__panjang_kabel_fo']);

    $biayaLan = ($lan > $max) ? (($lan - $max) * $basePrice) : 0;
    $biayaFo  = ($fo  > $max) ? (($fo - $max)  * $basePrice) : 0;

    $data['biaya_perubahan'] = $biaya_perubahan;
    $data['total_biaya']     = intval($biaya_perubahan) + intval($biayaFo) + intval($biayaLan);
    $data['biaya_lan']       = $biayaLan;
    $data['biaya_fo']        = $biayaFo;

    $file_name_n_ext = 'formulir-aktivasi-off_' . $data['pelanggan_aktivasi_off__nomor_aktivasi'] . '.pdf';
    $dest = returnDefault($dest, 'D');
    return generate_pdf('template/pdf/formulir-baru/aktivasi_off', $data, $file_name_n_ext, 'A4', $dest);
}

/**
 * @see \Mpdf\Output\Destination
 * @see https://mpdf.github.io/reference/mpdf-functions/output.html
 * @param string $dest Optional. Default \Mpdf\Output\Destination::INLINE
 * @return void|string
 */
function pdf_formulir_perubahan_layanan($dest = "I")
{
    $nama_file = "formulir_perubahan_layanan";

    return generate_pdf(
        'template/pdf/formulir_perubahan/formulir_perubahan_layanan.php',
        [],
        $nama_file . '.pdf',
        'A4',
        $dest
    );
}

/**
 * @see \Mpdf\Output\Destination
 * @see https://mpdf.github.io/reference/mpdf-functions/output.html
 * @param string $dest Optional. Default \Mpdf\Output\Destination::INLINE
 * @return void|string
 */
function pdf_formulir_perubahan_jaringan($dest = "I")
{
    $nama_file = "formulir_perubahan_jaringan";
    return generate_pdf(
        'template/pdf/formulir_perubahan/formulir_perubahan_jaringan.php',
        [],
        $nama_file . '.pdf',
        'A4',
        $dest
    );
}

/**
 * Generate Formulir Relokasi Jaringan
 *
 * @param string|int $pelanggan_relokasi__id Required.  Column from pelanggan_relokasi.id
 * @param string $dest Optional. Default 'D'
 * @see \Mpdf\Output\Destination
 * @see https://mpdf.github.io/reference/mpdf-functions/output.html
 *
 * @return void|string PDF
 */
function pdf_formulir_relokasi($pelanggan_relokasi__id, $dest = 'D')
{
    // error_reporting(0);
    require_once FCPATH . '/vendor/autoload.php';
    $ci = &get_instance();
    $ci->load->model('M_pelanggan_relokasi');

    $data = $ci->M_pelanggan_relokasi->getById($pelanggan_relokasi__id);
    if (!$data) {
        return show_custom_404();
    };
    $data = (array) $data;

    $biaya_perubahan  = intval(getSystemSetting('biaya_relokasi_pelanggan'));
    $max_distance_odp = intval(getSystemSetting('max_distance_odp'));
    $basePrice        = intval(getSystemSetting('biaya_kabel_fo_tambahan'));

    $panjang_lan = intval($data['pelanggan_relokasi__panjang_kabel_lan']);
    $panjang_fo  = intval($data['pelanggan_relokasi__panjang_kabel_fo']);

    $biayaLan = $panjang_lan > $max_distance_odp ? ($panjang_lan - $max_distance_odp) * $basePrice : 0;
    $biayaFo  = $panjang_fo  > $max_distance_odp ? ($panjang_fo  - $max_distance_odp) * $basePrice : 0;

    $data['total_biaya']     = intval($biaya_perubahan) + intval($biayaFo) + intval($biayaLan);
    $data['biaya_perubahan'] = $biaya_perubahan;
    $data['biaya_lan']       = $biayaLan;
    $data['biaya_fo']        = $biayaFo;

    $file_name_n_ext = 'formulir_relokasi_' . $data['pelanggan_relokasi__nomor_perubahan'] . '.pdf';
    $dest = returnDefault($ci->input->get('dest'), 'D');
    return generate_pdf('template/pdf/formulir-baru/relokasi', $data, $file_name_n_ext, 'A4', $dest);
}

function pdf_formulir_berhentilangganan($pelanggan_berhenti__id, $dest = 'D')
{
    // error_reporting(0);
    require_once FCPATH . '/vendor/autoload.php';
    $ci = &get_instance();
    $ci->load->model('M_berhenti_berlangganan');
    $data = $ci->M_berhenti_berlangganan->getDataFormulir($pelanggan_berhenti__id);
    if (!$data) {
        return show_custom_404();
    };
    $data = (array) $data;

    $file_name_n_ext = 'formulir_berhenti_' . $data['nomor_berhenti'] . '.pdf';
    $dest = returnDefault($ci->input->get('dest'), 'D');
    return generate_pdf('template/pdf/formulir-baru/temp_berhenti', $data, $file_name_n_ext, 'A4', $dest);
}

function gen_pdf_perubahan_layanana($pelanggan_update_layanan_id, $dest = 'D')
{
    // error_reporting(0);
    require_once FCPATH . '/vendor/autoload.php';
    $ci = &get_instance();

    if (!$pelanggan_update_layanan_id) {
        return show_custom_404();
    };

    // $ci->load->model('M_pelanggan');
    // $ci->load->model('M_layanan');
    $ci->load->model('M_pelanggan_update_layanan');

    // $POST_jenis_perubahan      = $ci->input->post('jenis_perubahan');
    // $POST_ref_product_id_baru  = $ci->input->post('ref_product_id_baru');
    // $POST_ref_jaringan_id_baru = $ci->input->post('ref_jaringan_id_baru');
    // $POST_alasan_perubahan     = $ci->input->post('alasan_perubahan');

    // $data = (array) $ci->M_pelanggan->getPelangganDetail($pelanggan_id);
    $data = $ci->M_pelanggan_update_layanan->getById($pelanggan_update_layanan_id);

    if (!$data) {
        return show_custom_404();
    };

    $data = (array) $data;
    // echo'<pre>';
    // var_dump($data);
    // die;
    $title = '';
    $biaya_perubahan = 0;

    switch (strval($data['pelanggan_update_layanan__id_jenis_update_layanan'])) {
        case '1':
        case '2':
            $biaya_perubahan = getSystemSetting('biaya_pindah_layanan');
            $title = 'PERUBAHAN LAYANAN';
            break;
        case '3':
            $biaya_perubahan = getSystemSetting('biaya_migrasi_jaringan');
            $title = 'MIGRASI JARINGAN';
            break;
    };
    $data['title']           = $title;

    $max = intval(getSystemSetting("max_distance_odp"));
    $lan = intval($data['pelanggan_update_layanan__panjang_kabel_lan']);
    $fo = intval($data['pelanggan_update_layanan__panjang_kabel_fo']);

    $basePrice = intval(getSystemSetting("biaya_kabel_fo_tambahan"));

    $biayaLan = $lan > $max ? ($lan - $max) * $basePrice : 0;
    $biayaFo = $fo > $max ? ($fo - $max) * $basePrice : 0;

    $data['total_biaya']     = intval($biaya_perubahan) + intval($biayaFo) + intval($biayaLan);
    $data['biaya_perubahan'] = $biaya_perubahan;
    $data['biaya_lan'] = $biayaLan;
    $data['biaya_fo'] = $biayaFo;

    // $ref_jaringan_baru = (array) $ci->M_layanan->getData('ref_jaringan', '*', ['id' => $POST_ref_jaringan_id_baru], 1);
    // $ref_product_baru  = (array) $ci->M_layanan->getData('ref_products', '*', ['id' => $POST_ref_product_id_baru ], 1);
    // $data['nama_layanan_baru']  = $ref_product_baru['name'];
    // $data['nama_jaringan_baru'] = $ref_jaringan_baru['name'];
    // $data['biaya_bulanan_baru'] = currencyIDR($ref_product_baru['price']);
    // $data['alasan_perubahan']   = $POST_alasan_perubahan;

    $data['qrCodeUrl'] = generate_qr_code(urlGoogleMapsByLatLong($data['pelanggan__latitude'], $data['pelanggan__longitude']))['url_path'];

    $file_name_n_ext = strtolower(preg_replace('/\s/', '-', 'Formulir ' . $title)) . '_' . $data['pelanggan_update_layanan__nomor_perubahan'] . '.pdf';
    $dest = returnDefault($dest, 'D');
    return generate_pdf('template/pdf/formulir-baru/perubahan', $data, $file_name_n_ext, 'A4', $dest);
}

/**
 * untuk menampilkan hasil pdf laporan gangguan
 *
 * @see \Mpdf\Output\Destination
 * @see https://mpdf.github.io/reference/mpdf-functions/output.html
 * @param  string      $spk_id Required.
 * @param  string      $dest   Optional. Default \Mpdf\Output\Destination::INLINE
 * @return void|string
 */
function pdf_penanganan_gangguan($spk_id, $dest = "I")
{

    require_once FCPATH . '/vendor/autoload.php';
    $ci = &get_instance();
    $ci->load->model('M_spk_gangguan');
    $ci->load->model('M_spk');

    $data = $ci->M_spk_gangguan->getDataPdfPenangananGangguan($spk_id);
    if (!$data) {
        return show_custom_404();
    };

    $data['tanggal_penugasan'] = $ci->tanggalindo->konversi($data['tanggal_penugasan']);
    $data['filepath_qrcode'] = generate_qr_code(urlGoogleMapsByLatLong($data['latitude'], $data['longitude']))['file_path'];

    $nama_file = 'penanganan-gangguan_' . $data['nomor_spk'];

    /** Setting Nama Teknisi */
    $executorList = $ci->M_spk->getExecutorsBySpkId($spk_id);
    if (count($executorList) > 1) {
        /** Jika ada lebih dari 1 executor, */
        $data['executor_name'] = implode(', ', array_map(function ($executor) {
            return explode(' ', $executor['user__real_name'])[0];
        }, $executorList));
    } else {
        $data['executor_name'] = $executorList[0]['user__real_name'];
    };
    /** Setting Nama Teknisi */

    return generate_pdf(
        'template/pdf/penanganan_gangguan/index.php',
        $data,
        $nama_file . '.pdf',
        'A4',
        $dest
    );
};

/**
 * @param string $dest Optional. Default \Mpdf\Output\Destination::DOWNLOAD
 * @see \Mpdf\Output\Destination
 * @see https://mpdf.github.io/reference/mpdf-functions/output.html
 * @return void|string
 */
function generatePdfSuratPernyataanAktivasiPelanggan($dest = "D", $file_name = 'surat-pernyataan-aktivasi-pelanggan')
{
    return generatePdf_systemSetingValue_byName(SURATPERNYATAAN_AKTIVASIPELANGGAN_NAME, $dest, $file_name);
}
/**
 * @param string $dest Optional. Default \Mpdf\Output\Destination::DOWNLOAD
 * @see \Mpdf\Output\Destination
 * @see https://mpdf.github.io/reference/mpdf-functions/output.html
 * @return void|string
 */
function generatePdfSuratPernyataanBerhentiBerlangganan($dest = "D", $file_name = 'surat-pernyataan-berhenti-berlangganan')
{
    return generatePdf_systemSetingValue_byName(SURATPERNYATAAN_BERHENTIBERLANGGANAN_NAME, $dest, $file_name);
}
/**
 * @param string $dest Optional. Default \Mpdf\Output\Destination::DOWNLOAD
 * @see \Mpdf\Output\Destination
 * @see https://mpdf.github.io/reference/mpdf-functions/output.html
 * @return void|string
 */
function generatePdfSuratPernyataanPindahLayanan($dest = "D", $file_name = 'surat-pernyataan-pindah-layanan')
{
    return generatePdf_systemSetingValue_byName(SURATPERNYATAAN_PINDAHLAYANAN_NAME, $dest, $file_name);
}
/**
 * @param string $dest Optional. Default \Mpdf\Output\Destination::DOWNLOAD
 * @see \Mpdf\Output\Destination
 * @see https://mpdf.github.io/reference/mpdf-functions/output.html
 * @return void|string
 */
function generatePdfSuratPernyataanRelokasiJaringan($dest = "D", $file_name = 'surat-pernyataan-relokasi-jaringan')
{
    return generatePdf_systemSetingValue_byName(SURATPERNYATAAN_RELOKASIJARINGAN_NAME, $dest, $file_name);
}

/**
 * @param string $systemSettings_name Required. Column system_settings.name
 * @param string $dest Optional. Default 'D'
 * @param string|null $file_name. Optional. Default null. if null, filename will be the same as $systemSettings_name. if string, filename will be like that
 * @see \Mpdf\Output\Destination
 * @see https://mpdf.github.io/reference/mpdf-functions/output.html
 *
 * @return void|string
 */
function generatePdf_systemSetingValue_byName($systemSettings_name, $dest = 'D', $file_name = null)
{
    require_once FCPATH . '/vendor/autoload.php';
    $ci = &get_instance();
    $ci->load->model('M_pengaturan_sistem');

    $validDest = ['F', 'D', 'S', 'I',];
    $dest = in_array($dest, $validDest) ? $dest : "D";

    $data = $ci->M_pengaturan_sistem->createDataFrom_listToInsertIfNotExist($systemSettings_name);
    if (!$data) {
        $data = (object) [
            'value' => '
                <h3>' . $systemSettings_name . ' TIDAK DITEMUKAN DI PENGATURAN SISTEM</h3>
                <p>Anda dapat <a href="' . site_url('dashboard/data_master/pengaturan/tambah') . '">menambahkan</a> ' . $systemSettings_name . ' di Pengaturan Sistem terlebih dahulu.</p>
            ',
        ];
    };

    $mpdf = new \Mpdf\Mpdf([
        'format' => 'A4',
    ]);
    $mpdf->WriteHTML($data->{'value'});

    if (!$file_name) {
        $file_name = $systemSettings_name;
    };

    return $mpdf->Output(strval($file_name) . '.pdf', $dest);
}


function pdf_invoice_bulanan($nomor_invoice, $dest = "I")
{
    require_once FCPATH . '/vendor/autoload.php';
    $ci = &get_instance();
    $ci->load->model('M_invoice_general', 'general');
    $ci->load->model('M_invoice');
    $ci->load->model('M_rekening');

    $invoice = (array)$ci->general->getInvoiceByNumber($nomor_invoice);
    if (!$invoice) {
        return show_custom_404();
    };

    $invoice['status_color'] = 'dark';
    switch (strtoupper($invoice['status'])) {
        case 'LUNAS':
            $invoice['status_color'] = 'success';
            break;
        case 'BELUM LUNAS':
            $invoice['status_color'] = 'warning';
            break;
        case 'LEWAT':
            $invoice['status_color'] = 'danger';
            break;
    };
    $idPelanggan = (array) $ci->general->listInvoiceBulananByIdInvoice($invoice['id'], true);
    $pelanggan = $ci->db->where(['id' => $idPelanggan['id_pelanggan']])->get('pelanggan')->row_array();
    $invoice['tagihan'] = (array) $ci->general->listInvoiceBulananByIdInvoice($invoice['id']);
    $invoice['no_invoice'] = $invoice['number'];
    $invoice['pelanggan__nomor_registrasi'] = $pelanggan['nomor_registrasi'];
    $invoice['orders__nama'] = $pelanggan['nama'];
    $invoice['orders__alamat_penagihan'] = $pelanggan['alamat_penagihan'];
    $invoice['tgl_invoice']  = isset($invoice['invoice_date'])  ? $ci->tanggalindo->konversi($invoice['invoice_date'])  : '-';
    $invoice['tgl_deadline'] = isset($invoice['invoice_deadline']) ? $ci->tanggalindo->konversi($invoice['invoice_deadline']) : '-';

    $invoice['html_tagihan']  = '';
    $invoice['total_tagihan'] = 0;
    $invoice['sub_total'] = 0;
    $nomor = 1;
    foreach ($invoice['tagihan']['tagihan'] as $no => $tagihan) {
        $invoice['html_tagihan'] .= '
            <tr>
                <td>' . $nomor . '</td>
                <td>' . $tagihan->nama_tagihan . '</td>
                <td style="text-align: right;">' . currencyIDR($tagihan->jumlah_tagihan) . '</td>
            </tr>';
        $nomor++;
        $invoice['total_tagihan'] += $tagihan->jumlah_tagihan;
        $invoice['sub_total'] += $tagihan->jumlah_tagihan;
    };
    //sub total
    $invoice['html_tagihan'] .= '<tr>
    <td colspan="2" style="text-align: right;" class="fw-bold">Sub Total</td>
    <td style="text-align: right;" class="fw-bold">' . currencyIDR($invoice['sub_total']) . '</td>
    </tr>';
    //DISKON TETAP
    $invoice['total_tagihan'] -= $invoice['discount'];
    $invoice['html_tagihan'] .= '<tr>
    <td colspan="2" style="text-align: right;">Diskon bulanan (' . $pelanggan['diskon'] . '%) </td>
    <td style="text-align: right;">(' . currencyIDR($invoice['discount']) . ')</td>
    </tr>';
    //DISKON PEMBAYARAN
    $invoice['total_tagihan'] += $invoice['kode_unik'];
    $diskon = getProposesDiscount($invoice['id']);
    $invoice['total_tagihan'] -= $diskon['amount'];
    $invoice['html_tagihan'] .= '<tr>
    <td colspan="2" style="text-align: right;">Diskon pembayaran (' . $diskon['persen'] . '%) </td>
    <td style="text-align: right;">(' . currencyIDR($diskon['amount']) . ')</td>
    </tr>';
    //kode unik
    $invoice['html_tagihan'] .= '<tr>
    <td colspan="2" style="text-align: right;">Kode unik</td>
    <td style="text-align: right;">' . currencyIDR($invoice['kode_unik']) . '</td>
    </tr>';
    $invoice['total_tagihan'] = currencyIDR($invoice['total_tagihan']);
    $invoice['html_rek_asnet'] = '';
    foreach ($ci->M_rekening->rekAsnet() as $rekening) {
        $invoice['html_rek_asnet'] .= '<br>' . $rekening['nama_bank'] . ' ' . $rekening['nomor_rekening']
            . '<br> a.n ' . $rekening['nama_rekening']
            . '<br>';
    };
    $fileName_with_ext = "invoice-$nomor_invoice.pdf";
    $dest = returnDefault($dest, 'I');

    return generate_pdf(
        'template/pdf/invoice_reg-bul',
        $invoice,
        $fileName_with_ext,
        'A4',
        $dest
    );
}


/**
 * getPersentDiscount
 *
 * @param  mixed $amount
 * @param  int (rupiah) $diskon
 * @return void
 */
function getPersentDiscount($amount, $diskon)
{
    $return = $diskon / $amount * 100;
    return $return;
}

/**
 * getProposesDiscount
 *
 * @param  int $idInvoice
 * @return void
 */
function getProposesDiscount($idInvoice)
{
    $ci = &get_instance();
    $ci->db->where(['id' => $idInvoice, 'jenis' => 'BULANAN']);
    $invoice = $ci->db->get('invoices')->row_array();

    $ci->db->where(['id_invoice' => $idInvoice, 'proposed_status' => 'ACCEPTED']);
    $data = $ci->db->get('invoice_proposed_discount')->row_array();
    $diskon['persen'] = 0;
    $diskon['amount'] = 0;
    if (!empty($data)) {
        $diskon['persen'] = $data['accepted_discount'];
        $diskon['amount'] = $data['accepted_discount'] * ($invoice['amount'] - $invoice['discount']) / 100;
    }
    return $diskon;
}
