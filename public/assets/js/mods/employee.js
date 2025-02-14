$(document).ready(function () {
    // Modal title dynamic setup
    var n = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-label-info btn-wide mx-1",
            denyButton: "btn btn-label-secondary btn-wide mx-1",
            cancelButton: "btn btn-label-danger btn-wide mx-1",
        },
        buttonsStyling: false,
    });

    $("#modal8").on("show.bs.modal", function (event) {
        var button = $(event.relatedTarget);
        var action = button.data("action");
        var title = button.data("title");
        var modalTitle = $("#modal8 .modal-header .modal-title");
        var route = button.data("route");
        var proses = button.data("proses");
        var form = $("#addEmployeeForm");

        // Reset form errors
        $(".is-invalid").removeClass("is-invalid");
        $(".invalid-feedback").remove();
        $("#errorMessages").addClass("d-none");

        if (action === "create") {
            modalTitle.text("Tambah " + title);
            form[0].reset();
            form.attr("action", proses);
            form.attr("method", "POST");
            $("#addEmployeeForm #department_id").val("").trigger("change");
            $("#addEmployeeForm #position_id").val("").trigger("change");
            $("#addEmployeeForm #gender").val("").trigger("change");
        } else if (action === "edit") {
            modalTitle.text("Edit " + title);
            form.attr("action", proses);
            form.attr("method", "PUT");

            // Get data via AJAX
            $.ajax({
                url: route,
                type: "GET",
                success: function (response) {
                    if (response.employee) {
                        // Populate fields with employee data
                        $("#addEmployeeForm #department_id")
                            .val(response.employee.department_id)
                            .trigger("change");
                        $("#addEmployeeForm #position_id")
                            .val(response.employee.position_id)
                            .trigger("change");
                        $("#addEmployeeForm #name").val(response.employee.name);
                        $("#addEmployeeForm #address").val(response.employee.address);
                        $("#addEmployeeForm #phone").val(response.employee.phone);
                        $("#addEmployeeForm #email").val(response.employee.email);
                        $("#addEmployeeForm #date_of_birth").val(response.employee.date_of_birth);
                        $("#addEmployeeForm #gender")
                            .val(response.employee.gender)
                            .trigger("change");
                        $("#addEmployeeForm #nik").val(response.employee.nik);
                        
                        // If the identity card is available (optional)
                        if (response.employee.identity_card) {
                            // You might want to show the file name or other details related to the file
                            $("#addEmployeeForm #identity_card").data('identity_card', response.employee.identity_card);
                        }
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
            data: "position_name",
            name: "position_name",
        },
        {
            data: "department_name",
            name: "department_name",
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

    $("#addEmployeeForm").on("submit", function (e) {
        e.preventDefault();
        var formData = new FormData(this); // Use FormData for file uploads

        $.ajax({
            url: $(this).attr("action"),
            type: $(this).attr("method"),
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                if (response.success) {
                    $("#modal8").modal("hide");
                    $("#addEmployeeForm")[0].reset();
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