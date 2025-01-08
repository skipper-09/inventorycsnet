$(document).ready(function () {
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
                data: "name",
                name: "name",
            },
            {
                data: "description",
                name: "description",
            },
            {
                data: "action",
                name: "action",
                orderable: false,
                searchable: false,
            },
        ],
    });

    $("#addProductForm").on("submit", function (e) {
        e.preventDefault();

        // Ambil data form
        var formData = $(this).serialize();

        $.ajax({
            url: $(this).attr("action"),
            type: "POST",
            data: formData,
            success: function (response) {
                if (response.success) {
                    $("#modal8").modal("hide");
                    $("#addProductForm")[0].reset();
                    table.ajax.reload();
                }
            },
            error: function (xhr, status, error) {
                console.log(xhr, error);
            },
        });
    });
});
