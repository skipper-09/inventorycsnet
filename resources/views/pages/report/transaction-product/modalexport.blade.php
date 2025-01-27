<div class="modal fade" id="modal8">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-between">
                <h5 class="modal-title">Export Data</h5>
                <button type="button" class="btn btn-sm btn-label-danger btn-icon" data-bs-dismiss="modal">
                    <i class="mdi mdi-close"></i>
                </button>
            </div>
            <!-- Form untuk pengiriman data -->
            <form id="addBranchForm" action="{{ route('report.transaction-product.export') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div id="errorMessages" class="alert alert-danger d-none" role="alert"></div>
                    <div class="mb-3">
                        <label class="form-label" for="name">Dari Tanggal</label>
                        <input class="form-control" type="date" name="start_date" id="start_date">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="address">Sampai Tanggal</label>
                        <input class="form-control" name="end_date" id="end_date" type="date"></input>
                    </div>
                    <div class="mb-3">
                        <label class="form-label w-100" for="unit_id">Jenis</label>
                        <select name="type_transaction" id="type_transaction" class="form-control select2">
                            <option value="">Semua Jenis</option>
                            <option value="transfer">Transfer</option>
                            <option value="psb">Pemasangan baru</option>
                            <option value="repair">Perbaikan</option>
                        </select>
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
