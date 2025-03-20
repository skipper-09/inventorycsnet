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
            <form id="addProductForm" action="" method="POST">
                @csrf <!-- CSRF Token untuk keamanan -->
                <div class="modal-body">
                    <div id="errorMessages" class="alert alert-danger d-none" role="alert"></div>
                    <div class="mb-3">
                        <label class="form-label" for="name">Nama Produk</label>
                        <input class="form-control" type="text" name="name" id="name"
                            placeholder="Nama Produk">
                    </div>
                    <div class="mb-3">
                        <label class="form-label w-100" for="unit_id">Unit Product</label>
                        <select name="unit_id" id="unit_id" class="form-control select2">
                            <option value="">Pilih Unit</option>
                            @foreach ($unit as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label w-100" for="is_modem">Modem (Ya/Tidak)</label>
                        <select name="is_modem" id="is_modem" class="form-control select2">
                            <option value="">Pilih</option>
                            <option value="1" {{ old('is_modem', $item->is_modem) == 1 ? 'selected' : '' }}>Ya</option>
                            <option value="0" {{ old('is_modem', $item->is_modem) == 0 ? 'selected' : '' }}>Tidak</option>
                        </select>
                    </div>                    
                    <div class="mb-3">
                        <label class="form-label" for="description">Deskripsi</label>
                        <textarea class="form-control" name="description" id="description" placeholder="Deskripsi"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary btn-sm">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
