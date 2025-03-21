<div class="modal fade" id="modal8">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-between">
                <h5 class="modal-title"></h5>
                <button type="button" class="btn btn-sm btn-label-danger btn-icon" data-bs-dismiss="modal">
                    <i class="mdi mdi-close"></i>
                </button>
            </div>
            <!-- Form untuk pengiriman data -->
            <form id="Form" action="" method="POST">
                @csrf
                <!-- CSRF Token untuk keamanan -->
                <div class="modal-body">
                    <div id="errorMessages" class="alert alert-danger d-none" role="alert"></div>

                    <div class="mb-3">
                        <label class="form-label w-100" for="branch_id">Cabang</label>
                        <select name="branch_id" id="branch_id" class="form-control select2">
                            <option value="">PIlih Cabang</option>
                            @foreach ($branch as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label w-100" for="product_id">Produk</label>
                        <select name="product_id" id="product_id" class="form-control select2">
                            <option value="">PIlih Barang</option>
                            @foreach ($product as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="name">Jumlah</label>
                        <input class="form-control" type="text" inputmode="numeric" name="qty" id="qty" placeholder="Jumlah">
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