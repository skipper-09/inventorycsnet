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
            data: "log",
            name: "log",
        },
    ];

    // // Only add action column if user has permission
    // if (hasActionPermission) {
    //     columns.push({
    //         data: "action",
    //         name: "action",
    //         orderable: false,
    //         searchable: false,
    //     });
    // }

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

    $("#deletelog").on("click", function () {
        var routeclear = $(this).data("route");
        n.fire({
            title: "Apakah Kamu Yakin?",
            text: "Semua Log akan terhapus",
            icon: "warning",
            showCancelButton: 1,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!",
        }).then((willDelete) => {
            if (willDelete.isConfirmed) {
                $.ajax({
                    url: routeclear,
                    method: "DELETE",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    success: function (res) {
                        // Reload table data
                        table.ajax.reload();

                        // Notify the user with SweetAlert
                        if (res.status === "success") {
                            n.fire({
                                position: "center",
                                icon: "success",
                                title: "Success",
                                text: res.message,
                                showConfirmButton: !1,
                                timer: 1500,
                            });
                        } else {
                            n.fire({
                                position: "center",
                                icon: "error",
                                title: "Error!",
                                text: res.message,
                                showConfirmButton: !1,
                                timer: 1500,
                            });
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        n.fire({
                            position: "center",
                            icon: "error",
                            title: "Error!",
                            text: "Something went wrong with the request.",
                            showConfirmButton: !1,
                            timer: 1500,
                        });
                    },
                });
            }else{
                return;
            }
        });
    });
});
