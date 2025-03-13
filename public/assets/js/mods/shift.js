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

    // Modal Show Event
    $("#modal8").on("show.bs.modal", function (event) {
        var button = $(event.relatedTarget);
        var action = button.data("action");
        var title = button.data("title");
        var modalTitle = $("#modal8 .modal-header .modal-title");
        var route = button.data("route");
        var proses = button.data("proses");
        var form = $("#addShiftForm");

        // Reset form errors
        $(".is-invalid").removeClass("is-invalid");
        $(".invalid-feedback").remove();
        $("#errorMessages").addClass("d-none");

        if (action === "create") {
            modalTitle.text("Tambah " + title);
            form[0].reset();
            form.attr("action", proses);
            form.attr("method", "POST");
            $("#addShiftForm #status").val("").trigger("change");
        } else if (action === "edit") {
            modalTitle.text("Edit " + title);
            form.attr("action", proses);
            form.attr("method", "PUT");
            // Get data via AJAX
            $.ajax({
                url: route,
                type: "GET",
                success: function (response) {
                    if (response.shift) {
                        $("#addShiftForm #name").val(response.shift.name);
                        $("#addShiftForm #shift_start").val(response.shift.shift_start);
                        $("#addShiftForm #shift_end").val(response.shift.shift_end);
                        $("#addShiftForm #status").val(response.shift.status).trigger("change");
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
            data: "shift_start",
            name: "shift_start",
        },
        {
            data: "shift_end",
            name: "shift_end",
        },
        {
            data: "status",
            name: "status",
            render: function (data) {
                return data ? "Aktif" : "Tidak Aktif";
            }
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
        scrollCollapse: true,
        paging: true,
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

    // Form Submit
    $("#addShiftForm").on("submit", function (e) {
        e.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
            url: $(this).attr("action"),
            type: $(this).attr("method"),
            data: formData,
            success: function (response) {
                if (response.success) {
                    $("#modal8").modal("hide");
                    $("#addShiftForm")[0].reset();
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
                    $.each(response.responseJSON.errors, function (field, messages) {
                        var inputField = $("#" + field);
                        inputField.addClass("is-invalid");
                        inputField.after('<div class="invalid-feedback">' + messages.join(", ") + "</div>");
                    });
                } else {
                    $("#errorMessages").removeClass("d-none");
                    $("#errorMessages").html("Something went wrong. Please try again.");
                }
            },
        });
    });
});
