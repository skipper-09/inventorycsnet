$(document).ready(function () {
    // Sweet Alert configuration
    var n = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-label-info btn-wide mx-1",
            denyButton: "btn btn-label-secondary btn-wide mx-1",
            cancelButton: "btn btn-label-danger btn-wide mx-1",
        },
        buttonsStyling: false,
    });

    var route = $("#scroll-sidebar-datatable").data("route");
    var hasActionPermission = $("#scroll-sidebar-datatable").data(
        "has-action-permission"
    );

    // Define columns for DataTables
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
            data: "completed_tasks",
            name: "completed_tasks",
        },
        {
            data: "inreview_tasks",
            name: "inreview_tasks",
        },
        {
            data: "overdue_tasks",
            name: "overdue_tasks",
        },
        {
            data: "kpi",
            name: "kpi",
        },
        {
            data: "bulan",
            name: "bulan",
        },
    ];

    // Only add action column if user has permission
    // if (hasActionPermission) {
    //     columns.push({
    //         data: "action",
    //         name: "action",
    //         orderable: false,
    //         searchable: false,
    //     });
    // }

    // Initialize DataTable
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
        ajax: {
            url: route,
            data: function (d) {
                d.bulan = $("#filter_month").val();
                d.tahun = $("#filter_year").val();
            },
        },
        columns: columns,
    });

    // Handle assignment filter change
    $("#filter_month").on("change", function () {
        table.ajax.reload();
    });

    // Handle status filter change
    $("#filter_year").on("change", function () {
        table.ajax.reload();
    });

    // $("#btn-filter").click(function () {
    //     $("#filter_month").val("").trigger("change");
    //     $("#filter_year").val("").trigger("change");
    //     table.ajax.reload();
    // });
});
