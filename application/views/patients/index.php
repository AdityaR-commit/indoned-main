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
            <div class="col-sm-12 col-md-6 col-lg-6">
                <button type="button" onclick="tambah()" class="btn btn-success float-right"><i class="fa fa-plus"></i> Tambah</button>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table" style="width: 100%;" id="table">
                <thead class="thead-dark">
                    <tr>
                        <th class="text-center">Action</th>
                        <th style="width: 5%">No</th>
                        <th>Nama</th>
                        <th>Jenis Kelamin</th>
                        <th>Alamat</th>
                        <th>No HP</th>
                        <th>Umur (Thn)</th>
                        <th>Tinggi Lutut (Cm)</th>
                        <th>Tinggi Badan (Cm)</th>
                        <th>BBI</th>
                    </tr>
                </thead>
                <tbody class="align-middle">
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-pasien" data-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tambah Pasien</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-pasien">
                <input type="hidden" id="action">
                <input type="hidden" id="id" name="id">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="nama">Nama Lengkap <?= $requiredLabel ?></label>
                        <input type="text" name="nama" class="form-control" id="nama" required placeholder="Nama lengkap">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <div class="form-group">
                                <label for="no_hp">No HP <?= $requiredLabel ?></label>
                                <input type="number" name="no_hp" class="form-control" id="no_hp" required placeholder="No HP">
                            </div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <div class="form-group">
                                <label for="jenis_kelamin">Jenis Kelamin <?= $requiredLabel ?></label>
                                <select name="jenis_kelamin" id="jenis_kelamin" class="custom-select select2">
                                    <option value="Laki-laki">Laki-laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="alamat">Alamat <?= $requiredLabel ?></label>
                        <textarea name="alamat" class="form-control" id="alamat" required placeholder="Alamat"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <div class="form-group">
                                <label for="estimasi_opsi">Estimasi Perhitungan Tinggi Badan <?= $requiredLabel ?></label>
                                <select name="estimasi_opsi" id="estimasi_opsi" class="custom-select select2">
                                    <option value="" selected disabled>Pilih Estimasi</option>
                                    <option value="tinggi_lutut">Tinggi Lutut</option>
                                    <option value="panjang_badan">Panjang Badan</option>
                                </select>
                                <!-- <div>
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" id="customRadio1" name="estimasi_opsi" class="custom-control-input" value="tinggi_lutut">
                                        <label class="custom-control-label" for="customRadio1">Tinggi Lutut</label>
                                    </div>
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" id="customRadio2" name="estimasi_opsi" class="custom-control-input" value="panjang_badan">
                                        <label class="custom-control-label" for="customRadio2">Panjang Badan</label>
                                    </div>
                                </div> -->
                            </div>
                        </div>
                        <div class="col-md-6 d-none" id="container-lutut">
                            <div class="form-group">
                                <label for="tinggi_lutut">Tinggi Lutut (Cm) <?= $requiredLabel ?></label>
                                <input type="number" name="tinggi_lutut" class="form-control" id="tinggi_lutut" required placeholder="Tinggi Lutut">
                            </div>
                        </div>
                        <div class="col-md-6 d-none" id="container-badan">
                            <div class="form-group">
                                <label for="panjang_badan">Panjang Badan (Cm) <?= $requiredLabel ?></label>
                                <input type="number" name="panjang_badan" class="form-control" id="panjang_badan" required placeholder="Panjang badan">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="rumus_opsi">Rumus <?= $requiredLabel ?></label>
                                <select name="rumus_opsi" id="rumus_opsi" class="custom-select select2 form-control" required>
                                    <option value="" selected disabled>Pilih Rumus</option>
                                    <option value="CHUMLEA">CHUMLEA I</option>
                                    <option value="OKTAVIANUS">OKTAVIANUS</option>
                                    <option value="FATMAH">FATMAH</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="umur">Umur (Tahun) <?= $requiredLabel ?></label>
                                <input type="number" name="umur" class="form-control" id="umur" required placeholder="Umur">
                            </div>
                        </div>
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
<div class="modal fade" id="modal-detail">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Detail Kategori Bahan Makanan</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="category">Kategori</label>
                    <input type="text" name="category" class="form-control" id="category_detail" required placeholder="Kategori" readonly>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <div class="form-group">
                            <label for="energi">Energi (kkal)</label>
                            <input type="number" name="energi" class="form-control" id="energi_detail" required placeholder="Energi (kkal)" readonly>
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <div class="form-group">
                            <label for="protein">Protein (gram)</label>
                            <input type="number" name="protein" class="form-control" id="protein_detail" required placeholder="Protein (gram)" readonly>
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <div class="form-group">
                            <label for="lemak">Lemak (gram)</label>
                            <input type="number" name="lemak" class="form-control" id="lemak_detail" required placeholder="Lemak (gram)" readonly>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="keterangan">Keterangan</label>
                    <textarea name="keterangan" class="form-control" id="keterangan_detail" required placeholder="Ketarangan" readonly></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Tutup</button>
            </div>
        </div>
    </div>
</div>