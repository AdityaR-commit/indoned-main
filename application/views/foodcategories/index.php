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
            <table class="table m-table" style="width: 100%;" id="table">
                <thead>
                    <tr>
                        <th class="text-center">Action</th>
                        <th style="width: 5%">No</th>
                        <th>Kategori</th>
                        <th>Energi (gram)</th>
                        <th>Protein (gram)</th>
                        <th>Lemak (gram)</th>
                        <th>Keterangan</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody class="align-middle">
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-category" data-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tambah Kategori Bahan Makanan</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-kategori">
                <input type="hidden" id="action">
                <input type="hidden" id="id" name="id">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="category">Kategori <?= $requiredLabel ?></label>
                        <input type="text" name="category" class="form-control" id="category" required placeholder="Kategori">
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <div class="form-group">
                                <label for="energi">Energi (kkal) <?= $requiredLabel ?></label>
                                <input type="number" name="energi" class="form-control" id="energi" required placeholder="Energi (kkal)">
                            </div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <div class="form-group">
                                <label for="protein">Protein (gram) <?= $requiredLabel ?></label>
                                <input type="number" name="protein" class="form-control" id="protein" required placeholder="Protein (gram)">
                            </div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <div class="form-group">
                                <label for="lemak">Lemak (gram) <?= $requiredLabel ?></label>
                                <input type="number" name="lemak" class="form-control" id="lemak" required placeholder="Lemak (gram)">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="keterangan">Keterangan <?= $requiredLabel ?></label>
                        <textarea name="keterangan" class="form-control" id="keterangan" required placeholder="Ketarangan"></textarea>
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