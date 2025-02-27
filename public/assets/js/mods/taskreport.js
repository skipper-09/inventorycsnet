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
    var hasActionPermission = $("#scroll-sidebar-datatable").data("has-action-permission");

    // Define columns for DataTables
    var columns = [
        {
            data: "DT_RowIndex",
            searchable: false,
            width: "10px",
            class: "text-center",
        },
        {
            data: "assignment_date",
            name: "assignment_date",
        },
        {
            data: "employee_name",
            name: "employee_name",
        },
        {
            data: "location",
            name: "location",
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
                console.log(d); // Log data being sent
                d.assign_date = $("#FilterAssignment").val();  // Capture the filter value for assignment
                d.status = $("#FilterStatus").val();  // Capture the filter value for status
            },
        },
        columns: columns,
    });

    // Initialize Select2 for the status filter
    $("#FilterStatus").select2();

    // Flag to avoid recursive filter reloads
    var filterChanged = false;

    // Handle assignment filter change
    $("#FilterAssignment").on("change", function () {
        if (!filterChanged) {
            filterChanged = true;
            // Reset the Status filter when Assignment filter changes
            $("#FilterStatus").val("").trigger("change");
            table.ajax.reload(null, false);  // Reload table with new filters
            filterChanged = false;
        }
    });

    // Handle status filter change
    $("#FilterStatus").on("change", function () {
        if (!filterChanged) {
            filterChanged = true;
            // Reset the Assignment filter when Status filter changes
            $("#FilterAssignment").val("").trigger("change");
            table.ajax.reload(null, false);  // Reload table with new filters
            filterChanged = false;
        }
    });
});
