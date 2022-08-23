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
                <thead class="thead-dark">
                    <tr class="text-center">
                        <th class="text-center">Action</th>
                        <th style="width: 5%">No</th>
                        <th>Bahan Makanan</th>
                        <th>Kategori</th>
                        <th>Ukuran Rumah Tangga</th>
                        <!-- <th>URT Unit</th> -->
                        <th>Berat</th>
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
<div class="modal fade" id="modal-foods" data-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tambah Bahan Makanan</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-makanan">
                <input type="hidden" id="action">
                <input type="hidden" id="id" name="id">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="category">Kategori <?= $requiredLabel ?></label>
                        <?php if (!empty($food_categories)) : ?>
                            <select name="category_id" id="category_id" class="custom-select select2">
                                <?php foreach ($food_categories as $i => $category) : ?>
                                    <option value="<?= $category['id'] ?>"><?= $category['category'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label for="food">Bahan Makanan <?= $requiredLabel ?></label>
                        <input type="text" name="food" class="form-control" id="food" required placeholder="Bahan makanan">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label for="urt">Ukuran Rumah Tangga <?= $requiredLabel ?></label>
                            <input type="text" name="urt" class="form-control" id="urt" required placeholder="Ukuran rumah tangga">
                        </div>
                        <div class="col-md-6 mb-2">
                            <div class="form-group">
                                <label for="urt_unit">URT Unit <?= $requiredLabel ?></label>
                                <input type="text" name="urt_unit" class="form-control" id="urt_unit" required placeholder="URT unit">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="berat">Berat (gram) <?= $requiredLabel ?></label>
                        <input type="number" name="berat" class="form-control" id="berat" required placeholder="Berat (gram)">
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
<div class="modal fade" id="modal-detail" data-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Deatil Bahan Makanan</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="category">Kategori</label>
                    <input type="text" name="category" class="form-control" id="category_detail" required placeholder="Bahan makanan" readonly>
                </div>
                <div class="form-group">
                    <label for="food">Bahan Makanan</label>
                    <input type="text" name="food" class="form-control" id="food_detail" required placeholder="Bahan makanan" readonly>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <label for="urt">Ukuran Rumah Tangga</label>
                        <input type="text" name="urt" class="form-control" id="urt_detail" required placeholder="Ukuran rumah tangga" readonly>
                    </div>
                    <div class="col-md-6 mb-2">
                        <div class="form-group">
                            <label for="urt_unit">URT Unit</label>
                            <input type="text" name="urt_unit" class="form-control" id="urt_unit_detail" required placeholder="URT unit" readonly>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="berat">Berat (gram)</label>
                    <input type="number" name="berat" class="form-control" id="berat_detail" required placeholder="Berat (gram)" readonly>
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