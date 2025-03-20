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
            <form id="addDepartmentForm" action="" method="POST">
                @csrf
                <!-- CSRF Token untuk keamanan -->
                <div class="modal-body">
                    <div id="errorMessages" class="alert alert-danger d-none" role="alert"></div>
                    <div class="mb-3">
                        <label class="form-label" for="name">Nama</label>
                        <input class="form-control" type="text" name="name" id="name"
                            placeholder="Nama Departemen">
                    </div>
                    {{-- <div class="mb-3">
                        <label class="form-label" for="location">Lokasi</label>
                        <input class="form-control" type="text" name="location" id="location"
                            placeholder="Lokasi">
                    </div> --}}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary btn-sm">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>