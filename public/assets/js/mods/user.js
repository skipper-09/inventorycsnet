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
        var form = $("#addUserForm");

        // Reset form errors
        $(".is-invalid").removeClass("is-invalid");
        $(".invalid-feedback").remove();
        $("#errorMessages").addClass("d-none");

        if (action === "create") {
            modalTitle.text("Tambah " + title);
            form[0].reset();
            form.attr("action", proses);
            form.attr("method", "POST");
            $("#imagePreview").addClass("d-none");
            $("#addUserForm #role").val("").trigger("change");
            $("#addUserForm #branch").val("").trigger("change");
            $("#addUserForm #is_block").val("").trigger("change");
        } else if (action === "edit") {
            modalTitle.text("Edit " + title);
            form.attr("action", proses);
            form.attr("method", "PUT");

            $.ajax({
                url: route,
                type: "GET",
                success: function (response) {
                    if (response.user) {
                        $("#addUserForm #username").val(response.user.username);
                        $("#addUserForm #name").val(response.user.name);
                        $("#addUserForm #email").val(response.user.email);
                        $("#addUserForm #role")
                            .val(response.user.roles[0].name)
                            .trigger("change");
                        $("#addUserForm #branch")
                            .val(response.user.branch_id)
                            .trigger("change");

                        $("#addUserForm #is_block")
                            .val(response.user.is_block)
                            .trigger("change");

                        // Set image preview
                        if (response.user.picture) {
                            $("#imagePreview")
                                .attr(
                                    "src",
                                    "/storage/images/user/" +
                                        response.user.picture
                                )
                                .removeClass("d-none");
                        } else {
                            $("#imagePreview")
                                .attr(
                                    "src",
                                    "/assets/images/users/avatar-1.png"
                                )
                                .removeClass("d-none");
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
            data: "picture",
            name: "picture",
            orderable: false,
            searchable: false,
        },
        { data: "username", name: "username" },
        { data: "name", name: "name" },
        { data: "role", name: "role" },
        { data: "status", name: "status" },
        { data: "branch", name: "branch" },
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

    // Form submit handler
    $("#addUserForm").on("submit", function (e) {
        e.preventDefault();
        var form = $(this);
        var formData = new FormData(this);

        // Tambahkan CSRF token
        formData.append("_token", $('meta[name="csrf-token"]').attr("content"));

        // Tambahkan _method field jika methodnya PUT
        if (form.attr("method").toLowerCase() === "put") {
            formData.append("_method", "PUT");
        }

        $.ajax({
            url: form.attr("action"),
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            cache: false,
            success: function (response) {
                if (response.success) {
                    $("#modal8").modal("hide");
                    form[0].reset();
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
            error: function (xhr) {
                $(".is-invalid").removeClass("is-invalid");
                $(".invalid-feedback").remove();
                $("#errorMessages").addClass("d-none");

                if (xhr.status === 419) {
                    location.reload();
                    return;
                }

                if (xhr.responseJSON && xhr.responseJSON.errors) {
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
                    $("#errorMessages").removeClass("d-none");
                    $("#errorMessages").html(
                        "Something went wrong. Please try again."
                    );
                }
            },
        });
    });

    // Preview gambar
    $("#picture").on("change", function (event) {
        var input = this;
        var reader = new FileReader();

        if (input.files && input.files[0]) {
            var file = input.files[0];
            if (file.type.match("image.*")) {
                reader.onload = function (e) {
                    $("#imagePreview")
                        .attr("src", e.target.result)
                        .removeClass("d-none");
                };
                reader.readAsDataURL(file);
            } else {
                $("#imagePreview").addClass("d-none");
                alert("Silakan pilih file gambar.");
            }
        }
    });
});
