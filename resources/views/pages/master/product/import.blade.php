<div class="modal fade" id="modalimport">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-between">
                <h5 class="modal-title">Import {{ $title }}</h5>
                <button type="button" class="btn btn-sm btn-label-danger btn-icon" data-bs-dismiss="modal">
                    <i class="mdi mdi-close"></i>
                </button>
            </div>
            <!-- Form for submitting data -->
            <form id="ImportForm" action="" method="POST" enctype="multipart/form-data">
                @csrf
                <!-- CSRF Token for security -->
                <div class="modal-body">
                    <div id="errorMessages" class="alert alert-danger d-none" role="alert"></div>
                    <div class="mb-3">
                        <label class="form-label" for="name">Input File</label>
                        <input class="form-control" type="file" name="file" id="file" placeholder="Import Data Produk">
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