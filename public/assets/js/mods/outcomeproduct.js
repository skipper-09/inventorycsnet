$(document).ready(function () {
    // Modal title dinamis
    var n = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-label-info btn-wide mx-1",
            denyButton: "btn btn-label-secondary btn-wide mx-1",
            cancelButton: "btn btn-label-danger btn-wide mx-1",
        },
        buttonsStyling: !1,
    });

    $("#modal8").on("show.bs.modal", function (event) {
        var button = $(event.relatedTarget);
        var action = button.data("action");
        var title = button.data("title");
        var modalTitle = $("#modal8 .modal-header .modal-title");
        var route = button.data("route");
        var proses = button.data("proses");
        var form = $("#addOutcomeProductForm");

        // Reset form errors
        $(".is-invalid").removeClass("is-invalid");
        $(".invalid-feedback").remove();
        $("#errorMessages").addClass("d-none");

        if (action === "create") {
            modalTitle.text("Tambah " + title);
            form[0].reset();
            form.attr("action", proses);
            form.attr("method", "POST");
            $("#addOutcomeProductForm #branch_id").val("").trigger("change");
            $("#addOutcomeProductForm #product_id").val("").trigger("change");
        } else if (action === "edit") {
            modalTitle.text("Edit " + title);
            form.attr("action", proses);
            form.attr("method", "PUT");
            // Get data via AJAX
            $.ajax({
                url: route,
                type: "GET",
                success: function (response) {
                    if (response.transactionproduct) {
                        $("#addOutcomeProductForm #qty").val(
                            response.transactionproduct.quantity
                        );
                        $("#addOutcomeProductForm #branch_id")
                            .val(
                                response.transactionproduct.transaksi.branch_id
                            )
                            .trigger("change");
                        $("#addOutcomeProductForm #product_id")
                            .val(response.transactionproduct.product_id)
                            .trigger("change");
                    }
                },
                error: function (xhr, status, error) {
                    console.log(xhr, error);
                },
            });
        }
    });

    // Initialize DataTable
    var route = $("#scroll-sidebar-datatable").data("route");
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
        columns: [
            {
                data: "DT_RowIndex",
                searchable: false,
                width: "10px",
                class: "text-center",
            },
            {
                data: "branch",
                name: "branch",
            },
            {
                data: "product",
                name: "product",
            },
            {
                data: "quantity",
                name: "quantity",
            },
            {
                data: "created_at",
                name: "created_at",
            },
            {
                data: "action",
                name: "action",
                orderable: false,
                searchable: false,
            },
        ],
    });

    // Form submission
    $("#addOutcomeProductForm").on("submit", function (e) {
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
                    $("#addOutcomeProductForm")[0].reset();
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
            error: function (xhr) {
                // Reset error styles
                $(".is-invalid").removeClass("is-invalid");
                $(".invalid-feedback").remove();
                $("#errorMessages").addClass("d-none");

                if (xhr.status === 400 && xhr.responseJSON.message) {
                    // Tampilkan notifikasi stok tidak mencukupi
                    Swal.fire({
                        icon: "error",
                        title: "Stok Tidak Mencukupi",
                        text: xhr.responseJSON.message,
                    });
                } else if (xhr.responseJSON.errors) {
                    // Tampilkan validasi kesalahan pada form
                    $.each(xhr.responseJSON.errors, function (field, messages) {
                        var inputField = $("#" + field);
                        inputField.addClass("is-invalid");
                        inputField.after(
                            '<div class="invalid-feedback">' +
                                messages.join(", ") +
                                "</div>"
                        );
                    });
                } else {
                    $("#errorMessages")
                        .removeClass("d-none")
                        .html("Something went wrong. Please try again.");
                }
            },
        });
    });
});
