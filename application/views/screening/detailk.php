<div class="card">
    <form id="form-slot" action="<?= site_url('screening/prosesscreening') ?>" method="POST">
        <div class="card-header pb-0">
            <div class="form-group">
                <label>Pasien</label>
                <input type="text" class="form-control" value="<?= $pasien['nama'] ?>" readonly>
                <input type="hidden" name="pasien_id" class="form-control" value="<?= $pasien['id'] ?>">
            </div>
        </div>
        <div class="card-body">
            <div class="timeline">
                <div class="time-label">
                    <!-- <span class="bg-red">10 Feb. 2014</span> -->
                </div>
                <?php if (!empty($slot_makan)) :
                    foreach ($slot_makan as $key => $slot) : ?>
                        <div>
                            <i class="fa fa-utensils bg-success"></i>
                            <div class="timeline-item">
                                <!-- <span class="time"><i class="fas fa-clock"></i> 12:05</span> -->
                                <h3 class="timeline-header"><b><?= $slot['slot_makan'] ?></b> <button type="button" class="btn btn-success btn-xs float-right" onclick="addrow('<?= $slot['id'] ?>')"><i class="fa fa-plus"></i> Tambah</button></h3>
                                <div class="timeline-body">
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
                                                <tr>
                                                    <td>
                                                        <input type="hidden" name="slot_makan_id[]" value="<?= $slot['id'] ?>">
                                                        <select name="food[]" class="custom-select form-control form-control-sm foods" required>
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
                                                            <input type="text" name="keterangan[]" class="form-control" placeholder="Keterangan" required>
                                                            <button type="button" id="remove-row" class="input-group-text btn bg-danger w-auto btn-sm d-flex align-items-center">
                                                                <i class="fas fa-times fa-fw"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="timeline-footer">
                                    <!-- <a class="btn btn-primary btn-sm">Read more</a>
                                    <a class="btn btn-danger btn-sm">Delete</a> -->
                                </div>
                            </div>
                        </div>
                <?php endforeach;
                endif; ?>
                <div class="form-group">
                    <button type="submit" class="btn btn-success btn-block mt-3"><i class="fa fa-save"></i> Simpan</button>
                </div>
            </div>
        </div>
    </form>
</div>
<script>
    let foods = <?= json_encode($foods) ?>
</script>