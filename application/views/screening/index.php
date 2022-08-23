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
                        <th>Hasil Screening</th>
                    </tr>
                </thead>
                <tbody class="align-middle">
                </tbody>
            </table>
        </div>
    </div>
</div>