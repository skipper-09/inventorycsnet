$(document).ready(function () {
    //modal title dinamis
    var n = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-label-info btn-wide mx-1",
            denyButton: "btn btn-label-secondary btn-wide mx-1",
            cancelButton: "btn btn-label-danger btn-wide mx-1",
        },
        buttonsStyling: !1,
    });
    
    // TinyMCE initialization for the modal
    $("#modal8").on("shown.bs.modal", function () {
        // Destroy any existing instance first to prevent duplicates
        if (tinymce.get('description')) {
            tinymce.get('description').remove();
        }
        
        // Initialize TinyMCE
        tinymce.init({
            selector: 'textarea#description',
            height: 300,
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'table', 'help', 'wordcount'
            ],
            toolbar: 'undo redo | blocks | ' +
                'bold italic backcolor | alignleft aligncenter ' +
                'alignright alignjustify | bullist numlist outdent indent | ' +
                'removeformat | help',
            content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:16px }'
        });
    });
    
    // Clean up TinyMCE instance when modal is hidden
    $("#modal8").on("hidden.bs.modal", function () {
        if (tinymce.get('description')) {
            tinymce.get('description').remove();
        }
    });
    
    $("#modal8").on("show.bs.modal", function (event) {
        var button = $(event.relatedTarget);
        var action = button.data("action");
        var title = button.data("title");
        var modalTitle = $("#modal8 .modal-header .modal-title");
        var route = button.data("route");
        var proses = button.data("proses");
        var form = $("#addForm");
        var taskId = button.data('taskid');
        var modal = $(this);
       
        // Reset form errors
        $(".is-invalid").removeClass("is-invalid");
        $(".invalid-feedback").remove();
        $("#errorMessages").addClass("d-none");
        $("#addForm #statustask").val('').trigger('change');
        if (action === "create") {
            modalTitle.text("Tambah " + title);
            form[0].reset();
            modal.find('#idtasktemplate').val(taskId);
            form.attr("action", proses);
            form.attr("method", "POST");
        } else if (action === "edit") {
            modalTitle.text("Edit " + title);
            modal.find('#idtasktemplate').val(taskId);
            form.attr("action", proses);
            form.attr("method", "PUT");
            //get data ajax
            $.ajax({
                url: route,
                type: "GET",
                success: function (response) {
                    if (response.taskdetail) {
                        $("#addForm #name").val(response.taskdetail.name);
                        $("#addForm #description").val(response.taskdetail.description);
                        
                        // Update TinyMCE content when it's initialized
                        setTimeout(function() {
                            if (tinymce.get('description')) {
                                tinymce.get('description').setContent(response.taskdetail.description);
                            }
                        }, 100);
                    }
                },
                error: function (xhr, status, error) {
                    console.log(xhr, error);
                },
            });
        }
    });

    var route = $("#scroll-sidebar-datatable").data("route");
    var hasActionPermission = $("#scroll-sidebar-datatable").data("has-action-permission");

    var columns = [
        {
            data: "DT_RowIndex",
            searchable: false,
            width: "10px",
            class: "text-center",
        },
        {
            data: "name",
            name: "name",
        },
        {
            data: "description",
            name: "description",
        },
    ];

    // Only add action column if user has permission
    if (hasActionPermission) {
        columns.push({
            data: "action",
            name: "action",
            orderable: false,
            searchable: false,
        });
    }

    var table = $("#scroll-sidebar-datatable").DataTable({
        scrollY: "350px",
        scrollCollapse: !0,
        paging: !0,
        language: {
            paginate: {
                previous: "<i class='mdi mdi-chevron-left'>",
                next: "<i class='mdi mdi-chevron-right'>",
            },
        },
        drawCallback: function () {
            $(".dataTables_paginate > .pagination").addClass("pagination");
        },
        processing: true,
        serverSide: true,
        ajax: route,
        columns: columns,
    });

    $("#addForm").on("submit", function (e) {
        e.preventDefault();
        
        // Update textarea content with TinyMCE content before form submission
        if (tinymce.get('description')) {
            tinymce.get('description').save();
        }
        
        var formData = $(this).serialize();
        $.ajax({
            url: $(this).attr("action"),
            type: $(this).attr("method"),
            data: formData,
            processData: false,
            success: function (response) {
                if (response.success) {
                    $("#modal8").modal("hide");
                    $("#addForm")[0].reset();
                    table.ajax.reload();
                    n.fire({
                        position: "center",
                        icon: "success",
                        title: response.status,
                        text: response.message,
                        showConfirmButton: !1,
                        timer: 1500,
                    });
                }
            },
            error: function (response) {
                $(".is-invalid").removeClass("is-invalid");
                $(".invalid-feedback").remove();
                $("#errorMessages").addClass("d-none");

                if (response.responseJSON.errors) {
                    $.each(
                        response.responseJSON.errors,
                        function (field, messages) {
                            var inputField = $("#" + field);
                            inputField.addClass("is-invalid");
                            inputField.after(
                                '<div class="invalid-feedback">' +
                                    messages.join(", ") +
                                    "</div>"
                            );
                        }
                    );
                } else {
                    $("#errorMessages").removeClass("d-none");
                    $("#errorMessages").html(
                        "Something went wrong. Please try again."
                    );
                }
            },
        });
    });

    $(document).on('click', '.show-full-description', function() {
        var description = $(this).data('description');
        $('#fullDescription').html(description);
    });
});