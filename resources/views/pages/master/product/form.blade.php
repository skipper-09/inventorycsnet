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
            <form id="addProductForm" action="{{ route('produk.store') }}" method="POST">
                @csrf <!-- CSRF Token untuk keamanan -->
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group mb-3">
                            <label class="form-label" for="name">Nama Produk</label>
                            <input class="form-control" type="text" name="name" id="name"
                                placeholder="Nama Produk" required>
                        </div>
                        <div class="form-group mb-3 ">
                            <label class="form-label w-100" for="unit_id">Unit Product</label>
                            <select name="unit_id" class="form-control select2">
                                <option value="">PIlih Unit</option>
                                @foreach ($unit as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>

                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label" for="description">Deskripsi</label>
                            <textarea class="form-control" name="description" id="description" placeholder="Deskripsi" required></textarea>
                        </div>
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
