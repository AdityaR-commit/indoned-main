<div class="card">
    <div class="card-header pb-0">
        <div class="row justify-content-between">
            <div class="col-sm-12 col-md-6 col-lg-6">
                <div class="input-group mb-3">
                    <select class="custom-select mx-0 me-2" name="dt_len" id="dt_len">
                        <option selected value="30">30 Baris</option>
                        <option value="50">50 Baris</option>
                        <option value="100">100 Baris</option>
                    </select>

                    <input id="search" type="text" style="width:100%;" class="form-control w-50" placeholder="Pencarian...">
                    <span id="btnSearch" class="input-group-text btn bg-success w-auto btn-sm d-flex align-items-center">
                        <i class="fas fa-search fa-fw"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table" style="width: 100%;" id="table">
                <thead class="thead-dark">
                    <tr>
                        <th style="width: 5%">No</th>
                        <th>Nama</th>
                        <th>Jenis Kelamin</th>
                        <th>Alamat</th>
                        <th>BBI</th>
                        <th>Energi (kkal)</th>
                        <th>Karbohidrat (gram)</th>
                        <th>Protein (gram)</th>
                        <th>Lemak (gram)</th>
                    </tr>
                </thead>
                <tbody class="align-middle">
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-hitung" data-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Perhitungan Estimasi Kebutuhan <span id="jenis-text"></span> Pasien</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-hitung" action="<?= site_url('estimasi/hitungEstimasi') ?>">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="pasien_id">Pasien <?= $requiredLabel ?></label>
                        <input type="text" id="nama_pasien" class="form-control" readonly>
                        <input type="hidden" name="pasien_id" id="pasien_id" class="form-control" readonly>
                        <input type="hidden" name="jenis" id="jenis" class="form-control" readonly>
                    </div>
                    <div id="container-bbi" class="form-group d-none">
                        <label for="bbi">BBI <?= $requiredLabel ?></label>
                        <input type="text" name="bbi" id="bbi" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label for="konstanta">Acuan Perhitungan <?= $requiredLabel ?> <span id="acuan-text"></span></label>
                        <input type="number" name="konstanta" id="konstanta" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Tutup</button>
                    <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Proses</button>
                </div>
            </form>
        </div>
    </div>
</div>