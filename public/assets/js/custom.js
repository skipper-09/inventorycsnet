"use strict";

//sweet alert delete button
$(function () {
    var n = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-label-info btn-wide mx-1",
            denyButton: "btn btn-label-secondary btn-wide mx-1",
            cancelButton: "btn btn-label-danger btn-wide mx-1",
        },
        buttonsStyling: !1,
    });
    $("#scroll-sidebar-datatable").on("click", ".action", function () {
        let data = $(this).data();
        let id = data.id;
        let type = data.type;
        var route = data.route;

        if (type == "delete") {
            n.fire({
                title: 'Anda yakin ingin menghapus data ini?',
                text: "Setelah dihapus, Anda tidak akan bisa mengembalikannya.",
                icon: "warning",
                showCancelButton: !0,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!",
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: route,
                        method: "DELETE",
                        type: "DELETE",
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                                "content"
                            ),
                        },
                        success: function (res) {
                            //reload table
                            $("#scroll-sidebar-datatable").DataTable().ajax.reload();
                            // Do something with the result
                            if (res.status === "success") {
                                n.fire("Deleted!", res.message,
                                     "success"
                                );
                            } else {
                                n.fire("Error!", res.message,
                                    "error"
                                );
                            }
                        },
                    });
                } else if (result.isDenied) {
                    console.log("User cancelled the action.");
                }
            });
        }
    });
});
