<div class="modal fade" id="modal8">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-between">
                <h5 class="modal-title">Tambah {{ $title }}</h5>
                <button type="button" class="btn btn-sm btn-label-danger btn-icon" data-bs-dismiss="modal">
                    <i class="mdi mdi-close"></i>
                </button>
            </div>
            <!-- Form untuk pengiriman data -->
            <form id="addOfficeForm" action="" method="POST">
                @csrf <!-- CSRF Token untuk keamanan -->
                <div class="modal-body">
                    <div id="errorMessages" class="alert alert-danger d-none" role="alert"></div>

                    <!-- Company ID -->
                    <div class="mb-3">
                        <label class="form-label w-100" for="company_id">Perusahaan</label>
                        <select name="company_id" id="company_id" class="form-control select2">
                            <option value="">Pilih Perusahaan</option>
                            @foreach ($companies as $company)
                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Name -->
                    <div class="mb-3">
                        <label class="form-label w-100" for="name">Nama</label>
                        <input class="form-control" type="text" name="name" id="name" placeholder="Masukkan Nama Perusahaan">
                    </div>

                    <!-- Latitude -->
                    <div class="mb-3">
                        <label class="form-label" for="lat">Latitude</label>
                        <input class="form-control" type="text" name="lat" id="lat" placeholder="Masukkan Latitude Perusahaan">
                    </div>

                    <!-- Longitude -->
                    <div class="mb-3">
                        <label class="form-label" for="long">Longitude</label>
                        <input class="form-control" type="text" name="long" id="long" placeholder="Masukkan Longitude Perusahaan">
                    </div>

                    <!-- Radius -->
                    <div class="mb-3">
                        <label class="form-label" for="radius">Radius</label>
                        <input class="form-control" type="number" name="radius" id="radius" placeholder="Masukkan Radius Perusahaan">
                    </div>

                    <!-- Address -->
                    <div class="mb-3">
                        <label class="form-label" for="address">Alamat</label>
                        <textarea class="form-control" name="address" id="address" rows="3" placeholder="Masukkan Alamat Perusahaan"></textarea>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary btn-sm">Kirim</button>
                </div>
            </form>
        </div>
    </div>
</div>
