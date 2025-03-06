<!-- Review Task Modal -->
<div class="modal fade" id="reviewTaskModal" tabindex="-1" aria-labelledby="reviewTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reviewTaskModalLabel">
                    <i class="fas fa-clipboard-check me-2"></i>Review Task
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="reviewTaskForm" method="POST">
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

@push('js')

<script>
    $('#reviewTaskModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget); 
    var route = button.data('route');

    var modal = $(this);
    modal.find('form').attr('data-route', route);
});

$('#reviewTaskForm').on('submit', function (e) {
    var n = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-label-info btn-wide mx-1",
            denyButton: "btn btn-label-secondary btn-wide mx-1",
            cancelButton: "btn btn-label-danger btn-wide mx-1",
        },
        buttonsStyling: !1,
    });

    e.preventDefault();
    var form = $(this);
    var route = form.data('route');
    var formData = new FormData(form[0]);

    // Make the AJAX request
    $.ajax({
    url: route,
    type: 'POST',
    data: formData,
    processData: false,
    contentType: false,
    success: function (response) {
        console.log(response);
        
        if (response.success) {
            n.fire({
                position: "center",
                icon: "success",
                title: "Success",
                text: response.message,
                showConfirmButton: !1,
                timer: 1500,
            });
            window.location.href = response.redirect_url;
        }
        $('#reviewTaskModal').modal('hide'); 
    },
    error: function (xhr, status, error) {
        console.error('AJAX error: ', status, error);
        console.error('Response Text: ', xhr.responseText);
        // alert('An error occurred while submitting the review.');
        n.fire({
                position: "center",
                icon: "error",
                title: "Gagal",
                text: "An error occurred while submitting the review.",
                showConfirmButton: !1,
                timer: 1500,
            });
        $('#reviewTaskModal').modal('hide');
    }
});
});
</script>
@endpush