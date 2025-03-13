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
            <form id="addShiftForm" action="" method="POST">
                @csrf <!-- CSRF Token untuk keamanan -->
                <div class="modal-body">
                    <div id="errorMessages" class="alert alert-danger d-none" role="alert"></div>

                    <!-- Name -->
                    <div class="mb-3">
                        <label class="form-label w-100" for="name">Nama Jadwal</label>
                        <input class="form-control" type="text" name="name" id="name" placeholder="Masukkan nama Jadwal">
                    </div>

                    <!-- Shift Start Time -->
                    <div class="mb-3">
                        <label class="form-label" for="shift_start">Jam Mulai</label>
                        <input class="form-control" type="time" name="shift_start" id="shift_start">
                    </div>

                    <!-- Shift End Time -->
                    <div class="mb-3">
                        <label class="form-label" for="shift_end">Jam Selesai</label>
                        <input class="form-control" type="time" name="shift_end" id="shift_end">
                    </div>

                    <!-- Status -->
                    <div class="mb-3">
                        <label class="form-label w-100" for="status">Status</label>
                        <select name="status" id="status" class="form-control select2">
                            <option value="">Pilih Status</option>
                            <option value="1">Aktif</option>
                            <option value="0">Tidak Aktif</option>
                        </select>
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
