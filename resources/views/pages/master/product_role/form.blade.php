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
            <form id="addUnitForm" action="" method="POST">
                @csrf
                <!-- CSRF Token untuk keamanan -->
                <div class="modal-body">
                    <div id="errorMessages" class="alert alert-danger d-none" role="alert"></div>
                    <div class="form-group col-12 col-md-12">
                        <div class="mb-3">
                            <div class="d-flex align-content-start justify-content-start mb-2">
                                <button id="select-all-btn" type="button"
                                    class="btn btn-sm btn-primary mr-2" onclick="toggleSelectAll()">Select
                                    All</button>
                            </div>
                            <label class="form-label" for="permissions">
                                Permissions
                            </label>
                            <div class="row mx-4">
                                @foreach ($product as $perm)
                                    <div class="col-4 col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input permission-checkbox"
                                                type="checkbox" name="product_id[]"
                                                id="permission_{{ $perm->id }}"
                                                value="{{ $perm->name }}">
                                            <label class="form-check-label"
                                                for="permission_{{ $perm->id }}">
                                                {{ $perm->name }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
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