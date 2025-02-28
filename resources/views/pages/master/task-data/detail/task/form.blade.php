<div class="modal fade" id="modal8">
    <div class="modal-dialog modal-lg"> <!-- Changed from modal-dialog to modal-lg -->
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-between">
                <h5 class="modal-title">Tambah {{ $title }}</h5>
                <button type="button" class="btn btn-sm btn-label-danger btn-icon" data-bs-dismiss="modal">
                    <i class="mdi mdi-close"></i>
                </button>
            </div>
            <!-- Form untuk pengiriman data -->
            <form id="addForm" action="" method="POST">
                @csrf <!-- CSRF Token untuk keamanan -->
                <div class="modal-body">
                    <div id="errorMessages" class="alert alert-danger d-none" role="alert"></div>
                    <input hidden name="task_data_id" type="text" id="idtasktemplate">
                    <div class="mb-3">
                        <label class="form-label" for="name">Nama Task</label>
                        <input class="form-control" type="text" name="name" id="name"
                            placeholder="Nama Task">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="description">Deskripsi</label>
                        <textarea class="form-control" name="description" id="description" rows="10" placeholder="Deskripsi Task"></textarea>
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