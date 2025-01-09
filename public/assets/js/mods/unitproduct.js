$(document).ready(function () {
    //modal title dinamis
    $("#modal8").on("show.bs.modal", function (event) {
        var button = $(event.relatedTarget);
        var action = button.data("action");
        var title = button.data("title");
        var modalTitle = $("#modal8 .modal-header .modal-title");
        var route = button.data("route");
        var proses = button.data("proses");
        var form = $("#addUnitForm");

        if (action === "create") {
            modalTitle.text("Tambah " + title);
            form[0].reset();
            form.attr("action", proses);
            form.attr("method", "POST");
        } else if (action === "edit") {
            modalTitle.text("Edit " + title);
            form.attr("action", proses);
            form.attr("method", "PUT");
            //get data ajax
            $.ajax({
                url: route,
                type: "GET",
                success: function (response) {
                    if (response.unit) {
                        $("#addUnitForm #name").val(response.unit.name);
                    }
                },
                error: function (xhr, status, error) {
                    console.log(xhr, error);
                },
            });
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

        var formData = $(this).serialize();

        $.ajax({
            url: $(this).attr("action"),
            type: $(this).attr("method"),
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
