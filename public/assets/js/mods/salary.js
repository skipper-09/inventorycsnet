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
            data: "employee_name",
            name: "employee_name",
        },
        {
            data: "salary_month",
            name: "salary_month",
        },
        {
            data: "basic_salary_amount",
            name: "basic_salary_amount",
        },
        {
            data: "bonus",
            name: "bonus",
        },
        {
            data: "allowance",
            name: "allowance",
        },
        {
            data: "deduction",
            name: "deduction",
        },
        {
            data: "total_salary",
            name: "total_salary",
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
                d.month = $('#filter_month').val();
                d.year = $('#filter_year').val();
            },
        },
        columns: columns,
    });

    // Filter data
    $('#btn-filter').click(function() {
        $("#scroll-sidebar-datatable").DataTable().ajax.reload();
    });

    // Sync filter values to export modal
    $('#export-button').click(function() {
        $('#month').val($('#filter_month').val());
        $('#year').val($('#filter_year').val());
    });
});