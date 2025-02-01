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
    var hasActionPermission = $("#scroll-sidebar-datatable").data("has-action-permission");

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
        ajax: route,
        columns: columns,
    });

   
});
