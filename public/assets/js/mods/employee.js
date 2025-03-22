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
            data: "name",
            name: "name",
        },
        {
            data: "position_name",
            name: "position_name",
        },
        {
            data: "department_name",
            name: "department_name",
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
    
    // Add reset button aligned with the filters
    // $(".row.mb-3.d-flex.align-items-center").append(
    //     '<div class="col-md-4">' +
    //     '<label class="form-label" style="visibility: hidden;">Hidden Label</label>' +
    //     '<div><button id="reset-filters" class="btn btn-primary">Reset Filter</button></div>' +
    //     '</div>'
    // );

    // Initialize Select2 before DataTable
    // $("#position_id, #department_id").select2();
    $("#department_id").select2();

    // Initialize datatable with variable assignment
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
                // d.position_id = $("#position_id").val(); // Filter position
                d.department_id = $("#department_id").val(); // Filter department
            },
        },
        columns: columns,
    });

    // Reload table data when position filter changes
    // $("#position_id").on("change", function () {
    //     table.ajax.reload();
    // });
    
    // Reload table data when department filter changes
    $("#department_id").on("change", function () {
        table.ajax.reload();
    });
    
    // Reset filters on button click
    // $(document).on("click", "#reset-filters", function() {
    //     // Clear Select2 values properly
    //     $("#position_id, #department_id").each(function() {
    //         $(this).val("").trigger("change");
    //     });
        
    //     // Reload the table
    //     table.ajax.reload();
        
    //     return false; // Prevent default button behavior
    // });
});