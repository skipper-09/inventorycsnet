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
            <form id="addUserForm" action="" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div id="errorMessages" class="alert alert-danger d-none" role="alert"></div>
                    <!-- Input Username -->
                    <div class="mb-3">
                        <label class="form-label" for="username">Username</label>
                        <input class="form-control" type="text" name="username" id="username"
                            placeholder="Username">
                    </div>

                    <!-- Input Name -->
                    <div class="mb-3">
                        <label class="form-label" for="name">Nama</label>
                        <input class="form-control" type="text" name="name" id="name" placeholder="Nama">
                    </div>

                    <!-- Input Email -->
                    <div class="mb-3">
                        <label class="form-label" for="email">Email</label>
                        <input class="form-control" type="email" name="email" id="email" placeholder="Email">
                    </div>

                    <!-- Input Password -->
                    <div class="mb-3">
                        <label class="form-label" for="password">Password</label>
                        <input class="form-control" type="password" name="password" id="password"
                            placeholder="Password">
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-3">
                        <label class="form-label" for="password_confirmation">Konfirmasi Password</label>
                        <input class="form-control" type="password" name="password_confirmation"
                            id="password_confirmation" placeholder="Konfirmasi Password">
                    </div>

                    <!-- Role Selection -->
                    <div class="mb-3">
                        <label class="form-label" for="role">Role</label>
                        <select class="form-control select2" name="role" id="role">
                            <option value="">Pilih Role</option>
                            @foreach ($roles as $item)
                                <option value="{{ $item->name }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Block Status -->
                    <div class="mb-3">
                        <label class="form-label" for="is_block">Status Akun</label>
                        <select class="form-control select2" id="is_block" name="is_block">
                            <option value="">Pilih Status</option>
                            <option value="0">Aktifkan Akun</option>
                            <option value="1">Blokir Akun</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="picture">Foto Profil</label>
                        <input class="form-control" type="file" name="picture" id="picture" accept="image/*">
                        <div id="imagePreviewContainer" class="mt-2">
                            <!-- Preview Image akan ditampilkan di sini -->
                            <img id="imagePreview" src="#" alt="Preview" class="img-fluid rounded-circle d-none"
                                style="max-width: 100px; max-height: 100px;">
                        </div>
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
