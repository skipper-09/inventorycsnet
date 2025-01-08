$(document).ready(function () {
    //modal title dinamis
    $("#modal8").on("show.bs.modal", function (event) {
        var button = $(event.relatedTarget);
        var action = button.data("action");
        var title = button.data("title");
        var modalTitle = $("#modal8 .modal-header .modal-title");

        if (action === "create") {
            modalTitle.text("Tambah " + title);
        } else if (action === "edit") {
            modalTitle.text("Edit " + title);
        }
    });

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
                data: "action",
                name: "action",
                orderable: false,
                searchable: false,
            },
        ],
    });

    $("#addUnitForm").on("submit", function (e) {
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
                    $("#addUnitForm")[0].reset();
                    table.ajax.reload();
                }
            },
            error: function (xhr, status, error) {
                console.log(xhr, error);
            },
        });
    });
});
