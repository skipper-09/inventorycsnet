<div class="modal fade" id="reviewTaskModal" tabindex="-1" aria-labelledby="reviewTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reviewTaskModalLabel">
                    <i class="fas fa-clipboard-check me-2"></i>Review Task
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('taskreport.review', ['id' => $employeeTask->id]) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label w-100" for="status">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">Pilih Status</option>
                            <option value="complated">Completed</option>
                            <option value="pending">Pending</option>
                            <option value="overdue">Overdue</option>
                            <option value="in_review">In Review</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label w-100" for="log">Review</label>
                        <textarea class="form-control" id="log" name="log" rows="4" required
                            placeholder="Enter your comments about this task..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Submit Review
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
