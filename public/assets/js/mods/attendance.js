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
            data: null,
            searchable: false,
            orderable: false,
            width: "10px",
            class: "text-center",
            render: function (data, type, row, meta) {
                return meta.row + 1; // Use meta.row instead of DT_RowIndex
            }
        },
        {
            data: "employee_name",
            name: "employee_name",
        },
        {
            data: "date",
            name: "date",
        },
        {
            data: "schedule",
            name: "schedule",
        },
        {
            data: "clock_in_time",
            name: "clock_in_time",
        },
        {
            data: "clock_out_time",
            name: "clock_out_time",
        },
        {
            data: "status",
            name: "status",
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

    // Store the DataTable instance in a variable
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
                d.filter_date = $("#filter_date").val();
                d.filter_employee = $("#filter_employee").val();
                d.filter_status = $("#filter_status").val();
            },
        },
        columns: columns,
    });

    // Initialize Select2
    $(".select2").select2();
    
    // Auto-filter on change of any filter input
    $("#filter_date, #filter_employee, #filter_status").on("change", function() {
        table.ajax.reload();
    });
});