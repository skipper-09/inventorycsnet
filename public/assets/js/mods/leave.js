$(document).ready(function () {
    // Modal title dynamic
    var n = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-label-info btn-wide mx-1",
            denyButton: "btn btn-label-secondary btn-wide mx-1",
            cancelButton: "btn btn-label-danger btn-wide mx-1",
        },
        buttonsStyling: false,
    });

    // Show modal for create or edit leave
    $("#modal8").on("show.bs.modal", function (event) {
        var button = $(event.relatedTarget);
        var action = button.data("action");
        var title = button.data("title");
        var modalTitle = $("#modal8 .modal-header .modal-title");
        var route = button.data("route");
        var proses = button.data("proses");
        var form = $("#addLeaveForm");

        // Reset form errors
        $(".is-invalid").removeClass("is-invalid");
        $(".invalid-feedback").remove();
        $("#errorMessages").addClass("d-none");

        if (action === "create") {
            modalTitle.text("Tambah " + title);
            form[0].reset();
            form.attr("action", proses);
            form.attr("method", "POST");
            $("#addLeaveForm #employee_id").val("").trigger("change");
            $("#addLeaveForm #status").val("").trigger("change");
        } else if (action === "edit") {
            modalTitle.text("Edit " + title);
            form.attr("action", proses);
            form.attr("method", "PUT");

            // Get data via AJAX for edit
            $.ajax({
                url: route,
                type: "GET",
                success: function (response) {
                    if (response.leave) {
                        $("#addLeaveForm #employee_id")
                            .val(response.leave.employee_id)
                            .trigger("change");
                        $("#addLeaveForm #start_date").val(
                            response.leave.start_date
                        );
                        $("#addLeaveForm #end_date").val(
                            response.leave.end_date
                        );
                        $("#addLeaveForm #reason").val(response.leave.reason);
                        $("#addLeaveForm #status")
                            .val(response.leave.status)
                            .trigger("change");
                        $("#addLeaveForm #year").val(response.leave.year);
                    }
                },
                error: function (xhr, status, error) {
                    console.log(xhr, error);
                },
            });
        }
    });

    var route = $("#scroll-sidebar-datatable").data("route");
    var hasActionPermission = $("#scroll-sidebar-datatable").data(
        "has-action-permission"
    );

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
            data: "position",
            name: "position",
        },
        {
            data: "status",
            name: "status",
        },
        {
            data: "start_date",
            name: "start_date",
        },
        {
            data: "end_date",
            name: "end_date",
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

    // Handle form submission for add/edit leave
    $("#addLeaveForm").on("submit", function (e) {
        e.preventDefault();
        var formData = $(this).serialize();

        $.ajax({
            url: $(this).attr("action"),
            type: $(this).attr("method"),
            data: formData,
            processData: false,
            success: function (response) {
                if (response.success) {
                    $("#modal8").modal("hide");
                    $("#addLeaveForm")[0].reset();
                    table.ajax.reload();
                    n.fire({
                        position: "center",
                        icon: "success",
                        title: response.status,
                        text: response.message,
                        showConfirmButton: false,
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
});