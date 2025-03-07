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

    // Function to initialize DataTable
    function initDataTable(tableId, filterInputId) {
        var route = $("#" + tableId).data("route");
        var hasActionPermission = $("#" + tableId).data("has-action-permission") === 'true';

        var table = $("#" + tableId).DataTable({
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
                data: function(d) {
                    if ($('#' + filterInputId).val()) {
                        d.assign_date = $('#' + filterInputId).val();
                    }
                }
            },
            columns: [
                {
                    data: "DT_RowIndex",
                    name: "DT_RowIndex",
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
                    data: "tugas",
                    name: "tugas",
                },
                {
                    data: "location",
                    name: "location",
                },
                {
                    data: "action",
                    name: "action",
                    orderable: false,
                    searchable: false,
                }
            ],
        });

        // Apply filter when date changes
        $('#' + filterInputId).change(function() {
            table.ajax.reload();
        });

        return table;
    }

    // Initialize Today's DataTable
    var todayDatatable = initDataTable('today-datatable', 'FilterAssignment');

    // Initialize All Data DataTable
    var allDatatable = initDataTable('all-datatable', 'FilterAssignmentAll');

    // Reload tables when tabs are clicked to ensure proper rendering
    $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
    });
});
