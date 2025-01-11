// Wait for document ready
$(document).ready(function () {
    // Initialize Select2 first
    $('.select2').select2({
        width: '100%',
        dropdownParent: $('body')
    });

    // Sweet Alert configuration
    var n = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-label-info btn-wide mx-1",
            denyButton: "btn btn-label-secondary btn-wide mx-1",
            cancelButton: "btn btn-label-danger btn-wide mx-1",
        },
        buttonsStyling: !1,
    });

    var route = $("#scroll-sidebar-datatable").data("route");
    
    // Initialize DataTable
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
        ajax: {
            url: route,
            data: function (d) {
                d.filter = $("#FilterBranch").val();
                d.product = $("#FilterProduct").val();
            },
        },
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
                data: "stock",
                name: "stock",
            },
        ],
    });

    // Handle branch filter change
    $("#FilterBranch").on("change", function () {
        table.ajax.reload(null, false);
    });

    // Handle product filter change
    $("#FilterProduct").on("change", function () {
        table.ajax.reload(null, false);
    });
});