<div class="modal fade" id="modal8" tabindex="-1" aria-labelledby="modal8Label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-between">
                <h5 class="modal-title">Tambah {{ $title }}</h5>
                <button type="button" class="btn btn-sm btn-label-danger btn-icon" data-bs-dismiss="modal">
                    <i class="mdi mdi-close"></i>
                </button>
            </div>
            <!-- Form untuk pengiriman data -->
            <form id="addEmployeeForm" action="" method="POST" enctype="multipart/form-data">
                @csrf <!-- CSRF Token untuk keamanan -->
                <div class="modal-body">
                    <div id="errorMessages" class="alert alert-danger d-none" role="alert"></div>

                    <!-- Department Select -->
                    <div class="mb-3">
                        <label class="form-label w-100" for="department_id">Departemen</label>
                        <select name="department_id" id="department_id" class="form-control select2">
                            <option value="">Pilih Departemen</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Position Select -->
                    <div class="mb-3">
                        <label class="form-label w-100" for="position_id">Jabatan</label>
                        <select name="position_id" id="position_id" class="form-control select2">
                            <option value="">Pilih Jabatan</option>
                            @foreach ($positions as $position)
                                <option value="{{ $position->id }}">{{ $position->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Name Input -->
                    <div class="mb-3">
                        <label class="form-label" for="name">Nama Karyawan</label>
                        <input class="form-control" type="text" name="name" id="name"
                            placeholder="Masukkan nama karyawan">
                    </div>

                    <!-- Address Input -->
                    <div class="mb-3">
                        <label class="form-label" for="address">Alamat</label>
                        <textarea class="form-control" name="address" id="address" rows="3" placeholder="Masukkan alamat"></textarea>
                    </div>

                    <!-- Phone Input -->
                    <div class="mb-3">
                        <label class="form-label" for="phone">Nomor Telepon</label>
                        <input class="form-control" type="text" name="phone" id="phone"
                            placeholder="Masukkan nomor telepon">
                    </div>

                    <!-- Email Input -->
                    <div class="mb-3">
                        <label class="form-label" for="email">Email</label>
                        <input class="form-control" type="email" name="email" id="email"
                            placeholder="Masukkan email">
                    </div>

                    <!-- Date of Birth Input -->
                    <div class="mb-3">
                        <label class="form-label" for="date_of_birth">Tanggal Lahir</label>
                        <input class="form-control" type="date" name="date_of_birth" id="date_of_birth">
                    </div>

                    <!-- Gender Select -->
                    <div class="mb-3">
                        <label class="form-label w-100" for="gender">Jenis Kelamin</label>
                        <select name="gender" id="gender" class="form-control select2">
                            <option value="">Pilih Jenis Kelamin</option>
                            <option value="male">Laki-laki</option>
                            <option value="female">Perempuan</option>
                        </select>
                    </div>

                    <!-- NIK Input -->
                    <div class="mb-3">
                        <label class="form-label" for="nik">Nomor Induk Kependudukan (NIK)</label>
                        <input class="form-control" type="text" name="nik" id="nik"
                            placeholder="Masukkan NIK">
                    </div>

                    <!-- Identity Card File Upload -->
                    <div class="mb-3">
                        <label class="form-label" for="identity_card">Kartu Identitas</label>
                        <input class="form-control" type="file" name="identity_card" id="identity_card">
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary btn-sm">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
