$(document).ready(function () {
    // Sweet Alert configuration
    var n = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-label-info btn-wide mx-1",
            denyButton: "btn btn-label-secondary btn-wide mx-1",
            cancelButton: "btn btn-label-danger btn-wide mx-1",
        },
        buttonsStyling: false,
    });

    // Form reset helper function
    function resetForm() {
        const form = $("#addEmployeeForm");
        form[0].reset();
        $(".is-invalid").removeClass("is-invalid");
        $(".invalid-feedback").remove();
        $("#errorMessages").addClass("d-none");
        $("#addEmployeeForm #department_id").val("").trigger("change");
        $("#addEmployeeForm #position_id").val("").trigger("change");
        $("#addEmployeeForm #gender").val("").trigger("change");
        $("#current_identity_card_info").remove();
    }

    // Modal show event handler
    $("#modal8").on("show.bs.modal", function (event) {
        var button = $(event.relatedTarget);
        var action = button.data("action");
        var title = button.data("title");
        var modalTitle = $("#modal8 .modal-header .modal-title");
        var route = button.data("route");
        var proses = button.data("proses");
        var form = $("#addEmployeeForm");

        resetForm();

        // Remove any existing method field
        form.find('input[name="_method"]').remove();

        if (action === "create") {
            modalTitle.text("Tambah " + title);
            form.attr("action", proses);
            form.attr("method", "POST");
        } else if (action === "edit") {
            modalTitle.text("Edit " + title);
            form.attr("action", proses);
            form.attr("method", "POST");
            // Add hidden field for PUT method
            form.append('<input type="hidden" name="_method" value="PUT">');

            $.ajax({
                url: route,
                type: "GET",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                success: function (response) {
                    if (response.employee) {
                        const employee = response.employee;

                        // Handle non-file inputs
                        $("#addEmployeeForm #department_id")
                            .val(employee.department_id)
                            .trigger("change");
                        $("#addEmployeeForm #position_id")
                            .val(employee.position_id)
                            .trigger("change");
                        $("#addEmployeeForm #name").val(employee.name);
                        $("#addEmployeeForm #address").val(employee.address);
                        $("#addEmployeeForm #phone").val(employee.phone);
                        $("#addEmployeeForm #email").val(employee.email);
                        $("#addEmployeeForm #date_of_birth").val(
                            employee.date_of_birth
                        );
                        $("#addEmployeeForm #gender")
                            .val(employee.gender)
                            .trigger("change");
                        $("#addEmployeeForm #nik").val(employee.nik);

                        // Handle file input display
                        if (employee.identity_card) {
                            $("#current_identity_card_info").remove();
                            const fileInfoHtml = `
                                <div id="current_identity_card_info" class="mb-2">
                                    <small class="text-muted">Current file: ${employee.identity_card}</small>
                                    <div class="form-text">Upload new file to replace current one</div>
                                </div>
                            `;
                            $("#identity_card").before(fileInfoHtml);
                        }
                    }
                },
                error: function (xhr) {
                    n.fire({
                        icon: "error",
                        title: "Error",
                        text: "Failed to load employee data",
                    });
                },
            });
        }
    });

    // DataTable initialization
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
        { data: "name", name: "name" },
        { data: "position_name", name: "position_name" },
        { data: "department_name", name: "department_name" },
    ];

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

    // Form submission handler
    $("#addEmployeeForm").on("submit", function (e) {
        e.preventDefault();
        var form = $(this);
        var formData = new FormData(this);
        var submitBtn = form.find('button[type="submit"]');

        // Disable submit button
        submitBtn.prop("disabled", true);

        // Clear previous errors
        $(".is-invalid").removeClass("is-invalid");
        $(".invalid-feedback").remove();
        $("#errorMessages").addClass("d-none");

        // Ensure CSRF token is included
        formData.append("_token", $('meta[name="csrf-token"]').attr("content"));

        $.ajax({
            url: form.attr("action"),
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                Accept: "application/json",
            },
            success: function (response) {
                $("#modal8").modal("hide");
                resetForm();
                table.ajax.reload();
                n.fire({
                    position: "center",
                    icon: "success",
                    title: response.status || "Success",
                    text:
                        response.message || "Data has been saved successfully",
                    showConfirmButton: false,
                    timer: 1500,
                });
            },
            error: function (xhr) {
                submitBtn.prop("disabled", false);

                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    Object.keys(errors).forEach((field) => {
                        const inputField = $("#" + field);
                        if (inputField.length) {
                            inputField.addClass("is-invalid");
                            inputField.after(
                                `<div class="invalid-feedback">${errors[
                                    field
                                ].join(", ")}</div>`
                            );
                        }
                    });

                    $("#errorMessages")
                        .removeClass("d-none")
                        .html("Please correct the errors below.");
                } else {
                    n.fire({
                        icon: "error",
                        title: "Error",
                        text:
                            xhr.responseJSON?.message ||
                            "An error occurred while saving the data",
                    });
                }
            },
            complete: function () {
                submitBtn.prop("disabled", false);
            },
        });
    });

    // Modal hidden event handler
    $("#modal8").on("hidden.bs.modal", resetForm);
});
