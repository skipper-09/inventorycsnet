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
            <form id="addLeaveForm" action="" method="POST">
                @csrf <!-- CSRF Token untuk keamanan -->
                <div class="modal-body">
                    <div id="errorMessages" class="alert alert-danger d-none" role="alert"></div>

                    <!-- Field untuk memilih Karyawan, hanya tampil jika role bukan Employee -->
                    @if (Auth::user()->roles->first()?->name !== 'Employee')
                        <div class="mb-3">
                            <label class="form-label w-100" for="employee_id">Karyawan</label>
                            <select name="employee_id" id="employee_id" class="form-control select2">
                                <option value="">Pilih Karyawan</option>
                                @foreach ($employees as $emp)
                                    <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <!-- Field untuk memilih Tanggal Mulai Cuti -->
                    <div class="mb-3">
                        <label class="form-label" for="start_date">Tanggal Mulai Cuti</label>
                        <input type="date" name="start_date" id="start_date" class="form-control">
                    </div>

                    <!-- Field untuk memilih Tanggal Selesai Cuti -->
                    <div class="mb-3">
                        <label class="form-label" for="end_date">Tanggal Selesai Cuti</label>
                        <input type="date" name="end_date" id="end_date" class="form-control">
                    </div>

                    <!-- Field untuk alasan cuti -->
                    <div class="mb-3">
                        <label class="form-label" for="reason">Alasan Cuti</label>
                        <textarea class="form-control" name="reason" id="reason" rows="3" placeholder="Masukkan alasan cuti"></textarea>
                    </div>

                    @if (Auth::user()->roles->first()?->name !== 'Employee')
                        <!-- Field untuk memilih Status Cuti -->
                        <div class="mb-3">
                            <label class="form-label" for="status">Status Cuti</label>
                            <select name="status" id="status" class="form-control select2">
                                <option value="">Pilih Status</option>
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>
                    @else
                        <input type="hidden" name="status" value="pending">
                    @endif

                    <!-- Field untuk memilih Tahun Cuti -->
                    <div class="mb-3">
                        <label class="form-label" for="year">Tahun Cuti</label>
                        <input type="number" name="year" id="year" class="form-control"
                            placeholder="Masukkan tahun cuti">
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
