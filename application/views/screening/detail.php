<div class="card">
    <div class="card-header pb-0">
        <div class="form-group">
            <label>Pasien</label>
            <input type="text" class="form-control" value="<?= $pasien['nama'] ?>" readonly>
            <input type="hidden" name="pasien_id" class="form-control" value="<?= $pasien['id'] ?>">
        </div>
    </div>
    <div class="card-body">
        <div class="card-body">
            <h4 class="mt-0">Slot Makan</h4>
            <div class="row">
                <div class="col-5 col-sm-3">
                    <div class="nav flex-column nav-tabs h-100" id="vert-tabs-tab" role="tablist" aria-orientation="vertical">
                        <input type="hidden" id="sum_slot" value="<?= count($slot_makan) ?>">
                        <?php foreach ($slot_makan as $key => $slot) : ?>
                            <a class="nav-link <?= $key == 0 ? 'active' : null ?>" id="vert-tabs-<?= $slot['id'] ?>-tab" data-toggle="pill" href="#vert-tabs-<?= $slot['id'] ?>" role="tab" aria-controls="vert-tabs-<?= $slot['id'] ?>" aria-selected="true"><?= $slot['slot_makan'] ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="col-7 col-sm-9">
                    <div class="tab-content" id="vert-tabs-tabContent">
                        <?php foreach ($slot_makan as $key => $slot) : ?>
                            <div class="tab-pane <?= $key == 0 ? 'active' : null ?> text-left fade show" id="vert-tabs-<?= $slot['id'] ?>" role="tabpanel" aria-labelledby="vert-tabs-<?= $slot['id'] ?>-tab">
                                <form id="form-slot-<?= $key ?>" action="<?= site_url('screening/prosesscreening') ?>" method="POST">
                                    <b><?= $slot['slot_makan'] ?></b><button type="button" class="btn btn-success btn-xs float-right" onclick="addrow('<?= $slot['id'] ?>')"><i class="fa fa-plus"></i> Tambah</button>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover" id="slot_<?= $slot['id'] ?>" style="width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th style="width:40%;">Makanan <?= $requiredLabel ?></th>
                                                    <th style="width:20%;">Berat (gram) <?= $requiredLabel ?></th>
                                                    <th style="width:40%;">Keterangan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <input type="hidden" name="slot_makan_id" value="<?= $slot['id'] ?>">
                                                <input type="hidden" name="pasien_id" value="<?= $pasien['id'] ?>">
                                                <?php if (!empty($pasien['screening'])) : ?>
                                                    <?php foreach ($pasien['screening'] as $i => $screening) :
                                                        if ($screening['slot_makan_id'] == $slot['id']) :
                                                            foreach ($screening['detail'] as $key => $detail) : ?>
                                                                <tr>
                                                                    <td>
                                                                        <select name="food[]" class="custom-select select2 form-control form-control-sm foods" required>
                                                                            <option value="" disabled selected>Pilih Makanan</option>
                                                                            <?php if (!empty($foods)) : foreach ($foods as $key => $food) : ?>
                                                                                    <option value="<?= $food['id'] ?>" <?= $detail['food_id'] == $food['id'] ? 'selected' : null ?>><?= $food['food'] ?></option>
                                                                            <?php endforeach;
                                                                            endif; ?>
                                                                        </select>
                                                                    </td>
                                                                    <td>
                                                                        <input type="number" name="berat[]" class="form-control" placeholder="Berat (gram)" value="<?= $detail['berat'] ?>" required>
                                                                    </td>
                                                                    <td>
                                                                        <div class="input-group">
                                                                            <input type="text" name="keterangan[]" class="form-control" placeholder="Keterangan" value="<?= $detail['keterangan'] ?>">
                                                                            <button type="button" id="remove-row" class="input-group-text btn bg-danger w-auto btn-sm d-flex align-items-center">
                                                                                <i class="fas fa-times fa-fw"></i>
                                                                            </button>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            <?php endforeach;
                                                        else : ?>
                                                            <!-- <tr>
                                                                <td>
                                                                    <select name="food[]" class="custom-select select2 form-control form-control-sm foods" required>
                                                                        <option value="" disabled selected>Pilih Makanan</option>
                                                                        <?php if (!empty($foods)) : foreach ($foods as $key => $food) : ?>
                                                                                <option value="<?= $food['id'] ?>"><?= $food['food'] ?></option>
                                                                        <?php endforeach;
                                                                        endif; ?>
                                                                    </select>
                                                                </td>
                                                                <td>
                                                                    <input type="number" name="berat[]" class="form-control" placeholder="Berat (gram)" required>
                                                                </td>
                                                                <td>
                                                                    <div class="input-group">
                                                                        <input type="text" name="keterangan[]" class="form-control" placeholder="Keterangan">
                                                                        <button type="button" id="remove-row" class="input-group-text btn bg-danger w-auto btn-sm d-flex align-items-center">
                                                                            <i class="fas fa-times fa-fw"></i>
                                                                        </button>
                                                                    </div>
                                                                </td>
                                                            </tr> -->
                                                    <?php endif;
                                                    endforeach;
                                                else : ?>
                                                    <!-- <tr>
                                                        <td>
                                                            <select name="food[]" class="custom-select select2 form-control form-control-sm foods" required>
                                                                <option value="" disabled selected>Pilih Makanan</option>
                                                                <?php if (!empty($foods)) : foreach ($foods as $key => $food) : ?>
                                                                        <option value="<?= $food['id'] ?>"><?= $food['food'] ?></option>
                                                                <?php endforeach;
                                                                endif; ?>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="number" name="berat[]" class="form-control" placeholder="Berat (gram)" required>
                                                        </td>
                                                        <td>
                                                            <div class="input-group">
                                                                <input type="text" name="keterangan[]" class="form-control" placeholder="Keterangan">
                                                                <button type="button" id="remove-row" class="input-group-text btn bg-danger w-auto btn-sm d-flex align-items-center">
                                                                    <i class="fas fa-times fa-fw"></i>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr> -->
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Simpan</button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    let foods = <?= json_encode($foods) ?>
</script>