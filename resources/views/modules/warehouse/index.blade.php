@extends('layout.main')
@section('pageName', 'Warehouse')
@section('page-content')

<div class="content d-flex flex-column flex-column-fluid" id="kt_content">

    <!--begin::Entry-->
    <div class="d-flex flex-column-fluid">
        <!--begin::Container-->
        <div class="container">
            <div class="card card-custom">
                <div class="card-header flex-wrap border-0 pt-6 pb-0">
                    <div class="card-title">
                        <h3 class="card-label">Warehouse
                        </h3>
                    </div>
                    <div class="card-toolbar">

                        <!--begin::Button-->
                        <a data-toggle="modal" data-target="#add-warehouse-modal"
                            class="btn btn-primary font-weight-bolder">
                            Add Warehouse</a>
                        <!--end::Button-->
                    </div>
                </div>

                <div class="card-body">

                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home">
                                <span class="nav-text">All Warehouse</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="dis-tab" data-toggle="tab" href="#dis">
                                <span class="nav-text">Dispatch Cylinder</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="ret-tab" data-toggle="tab" href="#ret">
                                <span class="nav-text">Returned Cylinders</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="out-tab" data-toggle="tab" href="#out">
                                <span class="nav-text">Outstanding Cylinders</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="wd-tab" data-toggle="tab" href="#wd">
                                <span class="nav-text">Warehouse Dispatch</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="ex-tab" data-toggle="tab" href="#ex">
                                <span class="nav-text">Exchanged Cylinders</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="prod-tab" data-toggle="tab" href="#prod">
                                <span class="nav-text">Production</span>
                            </a>
                        </li>

                    </ul>

                    <div class="tab-content mt-5" id="myTabContent">

                        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                            <div class="table-responsive mt-3">
                                <table width="100%"
                                    class="table table-bordered table-sm table-hover dataTable js-exportable"
                                    id="warehouse-table">
                                    <thead>
                                        <tr>
                                            <th>Warehouse Code</th>
                                            <th>Warehouse Name</th>
                                            <th>Town</th>
                                            <th>Region</th>
                                            <th>Street Name</th>
                                            <th>Phone</th>
                                            <th>Email</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!--Data is fetched here using ajax-->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane fade show" id="dis" role="tabpanel" aria-labelledby="dis-tab">
                            <div class="table-responsive mt-3">
                                <table width="100%"
                                    class="table table-bordered table-sm table-hover dataTable js-exportable"
                                    id="dispatch-table">
                                    <thead>
                                        <tr>
                                            <th>Date/Time</th>
                                            <th>Cylinder Code</th>
                                            <th>Size</th>
                                            <th>Operator Name</th>
                                            <th>Phone</th>
                                            <th>Location</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!--Data is fetched here using ajax-->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane fade show" id="ret" role="tabpanel" aria-labelledby="ret-tab">
                            <div class="table-responsive mt-3">
                                <table width="100%"
                                    class="table table-bordered table-sm table-hover dataTable js-exportable"
                                    id="return-table">
                                    <thead>
                                        <tr>
                                            <th>Date/Time</th>
                                            <th>Cylinder Code</th>
                                            <th>Size</th>
                                            <th>Empty/Full</th>
                                            <th>Operator</th>
                                            <th>Returned To/Warehouse</th>
                                            {{-- <th>Action</th> --}}
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!--Data is fetched here using ajax-->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane fade show" id="out" role="tabpanel" aria-labelledby="out-tab">
                            <div class="table-responsive mt-3">
                                <table width="100%"
                                    class="table table-bordered table-sm table-hover dataTable js-exportable"
                                    id="outstanding-table">
                                    <thead>
                                        <tr>
                                            <th>Date/Time</th>
                                            <th>Cylinder Code</th>
                                            <th>Size</th>
                                            <th>Operator Name</th>
                                            <th>Phone</th>
                                            <th>Location</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!--Data is fetched here using ajax-->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane fade show" id="wd" role="tabpanel" aria-labelledby="wd-tab">
                            <div class="table-responsive mt-3">
                                <table width="100%"
                                    class="table table-bordered table-sm table-hover dataTable js-exportable"
                                    id="wd-table">
                                    <thead>
                                        <tr>
                                            <th>Date/Time</th>
                                            <th>Cylinder Code</th>
                                            <th>Size</th>
                                            <th>Warehouse User</th>
                                            <th>From Warehouse</th>
                                            <th>To Warehouse</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!--Data is fetched here using ajax-->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane fade show" id="ex" role="tabpanel" aria-labelledby="ex-tab">
                            <div class="table-responsive mt-3">
                                <table width="100%"
                                    class="table table-bordered table-sm table-hover dataTable js-exportable"
                                    id="exchange-table">
                                    <thead>
                                        <tr>
                                            <th>Date/Time</th>
                                            <th>Customer</th>
                                            <th>New Cylinder</th>
                                            <th>New Cyl. Size</th>
                                            <th>Phone</th>
                                            <th>Operator</th>
                                            <th>Old Cylinder</th>
                                            <th>Old Cyl. Size</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!--Data is fetched here using ajax-->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane fade show" id="prod" role="tabpanel" aria-labelledby="prod-tab">
                            <div class="table-responsive mt-3">
                                <table width="100%"
                                    class="table table-bordered table-sm table-hover dataTable js-exportable"
                                    id="production-table">
                                    <thead>
                                        <tr>
                                            <th>New Cylinder</th>
                                            <th>Old Cylinder</th>
                                            <th>Weight Empty</th>
                                            <th>Weight Filled</th>
                                            <th>Total Weight</th>
                                            <th>Filled By</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!--Data is fetched here using ajax-->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Container-->
    </div>
    <!--end::Entry-->
</div>
@include('modules.warehouse.modals.add_warehouse')
@include('modules.warehouse.modals.add_dispatch')
@include('modules.warehouse.modals.print_dispatch')
@include('modules.warehouse.modals.vendor_dispatch')
@include('modules.warehouse.modals.print_return')
@include('modules.warehouse.modals.vendor_return')
@include('modules.warehouse.modals.return_cylinder')
@include('modules.warehouse.modals.update_warehouse')
@include('modules.warehouse.modals.exchange')

<script>
    var warehouseTable = $('#warehouse-table').DataTable({
        dom: 'Bfrtip',
        ajax: {
            url: `${APP_URL}/api/warehouse`,
            type: "GET"

        },
        ordering: false,
        order: [],
        processing: true,
        columns: [{
                data: "wcode"
            },
            {
                data: "wname"
            },
            {
                data: "town"
            },
            {
                data: "region"
            },
            {
                data: "streetname"
            },
            {
                data: "phone"
            },
            {
                data: "email"
            },
            {
                data: null,
                defaultContent: `

                <button type='button' data-row-transid='$this->transid'
                rel='tooltip' class='btn btn-success btn-sm edit-btn'>
                   <i class='fas fa-edit'></i>
                </button>

                <button type='button' data-row-transid='$this->transid'
                rel='tooltip' class='btn btn-danger btn-sm delete-btn'>
                   <i class='fas fa-trash'></i>
                </button>
                
                `
            },
        ],
        responsive: true,
        buttons: [{
                extend: 'print',
                attr: {
                    class: "btn btn-sm btn-info rounded-right"
                },
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6]
                }
            },
            {
                extend: 'copy',
                attr: {
                    class: "btn btn-sm btn-info rounded-right"
                },
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6]
                }
            },
            {
                extend: 'excel',
                attr: {
                    class: "btn btn-sm btn-info rounded-right"
                },
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6]
                }
            },
            {
                extend: 'pdf',
                attr: {
                    class: "btn btn-sm btn-info rounded-right"
                },
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6]
                }
            },
            {
                text: "Refresh",
                attr: {
                    class: "ml-2 btn-warning btn btn-sm rounded"
                },
                action: function (e, dt, node, config) {
                    dt.ajax.reload(false, null);
                }
            },
        ]
    });

    var dispatchTable = $('#dispatch-table').DataTable({
        dom: 'Bfrtip',
        ajax: {
            url: `${APP_URL}/api/warehouse/fetch_dispatch`,
            type: "GET"

        },
        pageLength: 100,
        ordering: false,
        order: [],
        processing: true,
        columns: [{
                data: "date"
            },
            {
                data: "cylcode"
            },
            {
                data: "size"
            },
            {
                data: "name"
            },
            {
                data: "phone"
            },
            {
                data: "location"
            },
            {
                data: "action"
            },
        ],
        responsive: true,
        buttons: [{
                extend: 'copy',
                attr: {
                    class: "btn btn-sm btn-info rounded-right"
                },
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5]
                }
            },
            {
                extend: 'excel',
                attr: {
                    class: "btn btn-sm btn-info rounded-right"
                },
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5]
                }
            },
            {
                extend: 'pdf',
                attr: {
                    class: "btn btn-sm btn-info rounded-right"
                },
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5]
                }
            },
            {
                text: "Refresh",
                attr: {
                    class: "ml-2 btn-warning btn btn-sm rounded"
                },
                action: function (e, dt, node, config) {
                    dt.ajax.reload(false, null);
                }
            },
            {
                text: "Dispatch Cylinder",
                attr: {
                    class: "ml-2 btn-success btn btn-sm rounded"
                },
                action: function (e, dt, node, config) {
                    $('#add-dispatch-modal').modal('show')
                }
            },
            {
                text: "Print Dispatch",
                attr: {
                    class: "ml-2 btn-danger btn btn-sm rounded"
                },
                action: function (e, dt, node, config) {
                    $('#print-dispatch-modal').modal('show')
                }
            },
        ]
    });

    var returnTable = $('#return-table').DataTable({
        dom: 'Bfrtip',
        ajax: {
            url: `${APP_URL}/api/warehouse/fetch_return`,
            type: "GET"

        },
        pageLength: 100,
        ordering: false,
        order: [],
        processing: true,
        columns: [{
                data: "date"
            },
            {
                data: "cylcode"
            },
            {
                data: "size"
            },
            {
                data: "empty_full"
            },
            {
                data: "name"
            },
            {
                data: "wname"
            },
        ],
        responsive: true,
        buttons: [{
                extend: 'copy',
                attr: {
                    class: "btn btn-sm btn-info rounded-right"
                },
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5]
                }
            },
            {
                extend: 'excel',
                attr: {
                    class: "btn btn-sm btn-info rounded-right"
                },
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5]
                }
            },
            {
                extend: 'pdf',
                attr: {
                    class: "btn btn-sm btn-info rounded-right"
                },
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5]
                }
            },
            {
                text: "Refresh",
                attr: {
                    class: "ml-2 btn-warning btn btn-sm rounded"
                },
                action: function (e, dt, node, config) {
                    dt.ajax.reload(false, null);
                }
            },
        ]
    });

    var outstandingTable = $('#outstanding-table').DataTable({
        dom: 'Bfrtip',
        ajax: {
            url: `${APP_URL}/api/warehouse/fetch_outstanding_cylinders`,
            type: "GET"

        },
        pageLength: 100,
        ordering: false,
        order: [],
        processing: true,
        columns: [{
                data: "date"
            },
            {
                data: "cylcode"
            },
            {
                data: "size"
            },
            {
                data: "name"
            },
            {
                data: "phone"
            },
            {
                data: "location"
            },
        ],
        responsive: true,
        buttons: [{
                extend: 'copy',
                attr: {
                    class: "btn btn-sm btn-info rounded-right"
                },
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5]
                }
            },
            {
                extend: 'excel',
                attr: {
                    class: "btn btn-sm btn-info rounded-right"
                },
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5]
                }
            },
            {
                extend: 'pdf',
                attr: {
                    class: "btn btn-sm btn-info rounded-right"
                },
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5]
                }
            },
            {
                text: "Refresh",
                attr: {
                    class: "ml-2 btn-warning btn btn-sm rounded"
                },
                action: function (e, dt, node, config) {
                    dt.ajax.reload(false, null);
                }
            },
        ]
    });

    var exchangeTable = $('#exchange-table').DataTable({
        dom: 'Bfrtip',
        ajax: {
            url: `${APP_URL}/api/warehouse/fetch_exchange`,
            type: "GET"

        },
        pageLength: 100,
        ordering: false,
        order: [],
        processing: true,
        columns: [{
                data: "date"
            },
            {
                data: "customer"
            },
            {
                data: "cylcode_new"
            },
            {
                data: "new_size"
            },
            {
                data: "phone"
            },
            {
                data: "vendor"
            },
            {
                data: "cylcode_old"
            },
            {
                data: "old_size"
            },
            {
                data: "action"
            },
        ],
        responsive: true,
        buttons: [{
                extend: 'copy',
                attr: {
                    class: "btn btn-sm btn-info rounded-right"
                },
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5]
                }
            },
            {
                extend: 'excel',
                attr: {
                    class: "btn btn-sm btn-info rounded-right"
                },
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5]
                }
            },
            {
                extend: 'pdf',
                attr: {
                    class: "btn btn-sm btn-info rounded-right"
                },
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5]
                }
            },
            {
                text: "Refresh",
                attr: {
                    class: "ml-2 btn-warning btn btn-sm rounded"
                },
                action: function (e, dt, node, config) {
                    dt.ajax.reload(false, null);
                }
            },
        ]
    });

    var productionTable = $('#production-table').DataTable({
        dom: 'Bfrtip',
        ajax: {
            url: `${APP_URL}/api/warehouse/fetch_production`,
            type: "GET"

        },
        ordering: false,
        order: [],
        processing: true,
        columns: [{
                data: "cylcode_new"
            },
            {
                data: "cylcode_old"
            },
            {
                data: "empty"
            },
            {
                data: "full"
            },
            {
                data: "total"
            },
            {
                data: "staff"
            },
            {
                data: "action"
            },
        ],
        responsive: true,
        buttons: [{
                extend: 'print',
                attr: {
                    class: "btn btn-sm btn-info rounded-right"
                },
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6]
                }
            },
            {
                extend: 'copy',
                attr: {
                    class: "btn btn-sm btn-info rounded-right"
                },
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6]
                }
            },
            {
                extend: 'excel',
                attr: {
                    class: "btn btn-sm btn-info rounded-right"
                },
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6]
                }
            },
            {
                extend: 'pdf',
                attr: {
                    class: "btn btn-sm btn-info rounded-right"
                },
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6]
                }
            },
            {
                text: "Refresh",
                attr: {
                    class: "ml-2 btn-warning btn btn-sm rounded"
                },
                action: function (e, dt, node, config) {
                    dt.ajax.reload(false, null);
                }
            },
        ]
    });

    var wdTable = $('#wd-table').DataTable({
        dom: 'Bfrtip',
        ajax: {
            url: `${APP_URL}/api/warehouse/fetch_warehouse_dispatch`,
            type: "GET"

        },
        ordering: false,
        pageLength: 100,
        order: [],
        processing: true,
        columns: [{
                data: "date"
            },
            {
                data: "cylcode"
            },
            {
                data: "size"
            },
            {
                data: "vname"
            },
            {
                data: "fromwname"
            },
            {
                data: "towname"
            },
            {
                data: null,
                defaultContent: `

                <button type='button' data-row-transid='$this->transid'
                rel='tooltip' class='btn btn-danger btn-sm wd-delete-btn'>
                   <i class='fas fa-trash'></i>
                </button>
                
                `
            },
        ],
        responsive: true,
        buttons: [{
                extend: 'copy',
                attr: {
                    class: "btn btn-sm btn-info rounded-right"
                },
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5]
                }
            },
            {
                extend: 'excel',
                attr: {
                    class: "btn btn-sm btn-info rounded-right"
                },
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5]
                }
            },
            {
                extend: 'pdf',
                attr: {
                    class: "btn btn-sm btn-info rounded-right"
                },
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5]
                }
            },
            {
                text: "Refresh",
                attr: {
                    class: "ml-2 btn-warning btn btn-sm rounded"
                },
                action: function (e, dt, node, config) {
                    dt.ajax.reload(false, null);
                }
            },
        ]
    });

    $("#dispatch-table").on("click", ".return-btn", function () {
        let data = dispatchTable.row($(this).parents('tr')).data();

        $("#return-modal").modal("show");
        $("#return-transid").val(data.id);
        $("#return-size").val(data.size);
        $("#return-cylinder").val(data.cylcode).trigger('change');
        $("#return-vendor").val(data.vendor).trigger('change');
    });

    $("#warehouse-table").on("click", ".edit-btn", function () {
        let data = warehouseTable.row($(this).parents('tr')).data();

        $("#update-warehouse-modal").modal("show");
        $("#update-warehouse-code").val(data.wcode);
        $("#update-warehouse-name").val(data.wanme);
        $("#update-warehouse-region").val(data.region).trigger('change');
        $("#update-warehouse-town").val(data.town);
        $("#update-warehouse-streetname").val(data.streetname);
        $("#update-warehouse-landmark").val(data.landmark);
        $("#update-warehouse-gpsaddress").val(data.gpsaddress);
        $("#update-warehouse-phone").val(data.phone);
        $("#update-warehouse-email").val(data.email);
    });

    $("#warehouse-table").on("click", ".delete-btn", function () {
        var data = warehouseTable.row($(this).parents("tr")).data();
        var formdata = new FormData()
        formdata.append("transid", data.transid);
        formdata.append("createuser", "admin");

        data.transid

        Swal.fire({
            title: "",
            text: "Are you sure you want to remove this record?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Delete"

        }).then((result) => {
            if (result.value) {
                Swal.fire({
                    text: "Deleting...",
                    showConfirmButton: false,
                    allowEscapeKey: false,
                    allowOutsideClick: false
                });
                fetch(`${APP_URL}/api/warehouse/delete`, {
                    method: "POST",
                    body: formdata,
                }).then(function (res) {
                    return res.json()
                }).then(function (data) {
                    if (!data.ok) {
                        Swal.fire({
                            text: data.msg,
                            icon: "error"
                        });
                        return;
                    }
                    Swal.fire({
                        text: "Warehouse deleted successfully",
                        icon: "success"
                    });
                    warehouseTable.ajax.reload(false, null);
                }).catch(function (err) {
                    if (err) {
                        Swal.fire({
                            text: "Deleting warehouse failed"
                        });
                    }
                })
            }
        })

    });

    $("#dispatch-table").on("click", ".dispatch-delete-btn", function () {
        var data = dispatchTable.row($(this).parents("tr")).data();
        var formdata = new FormData()
        formdata.append("transid", data.id);
        formdata.append("createuser", "admin");

        data.transid

        Swal.fire({
            title: "",
            text: "Are you sure you want to remove this record?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Delete"

        }).then((result) => {
            if (result.value) {
                Swal.fire({
                    text: "Deleting...",
                    showConfirmButton: false,
                    allowEscapeKey: false,
                    allowOutsideClick: false
                });
                fetch(`${APP_URL}/api/warehouse/delete_dispatch`, {
                    method: "POST",
                    body: formdata,
                }).then(function (res) {
                    return res.json()
                }).then(function (data) {
                    if (!data.ok) {
                        Swal.fire({
                            text: data.msg,
                            icon: "error"
                        });
                        return;
                    }
                    Swal.fire({
                        text: "Dispatch deleted successfully",
                        icon: "success"
                    });
                    dispatchTable.ajax.reload(false, null);
                }).catch(function (err) {
                    if (err) {
                        Swal.fire({
                            text: "Deleting dispatch failed"
                        });
                    }
                })
            }
        })

    });

    $("#wd-table").on("click", ".wd-delete-btn", function () {
        var data = wdTable.row($(this).parents("tr")).data();

        Swal.fire({
            title: "",
            text: "Are you sure you want to remove this record?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Delete"

        }).then((result) => {
            if (result.value) {
                Swal.fire({
                    text: "Deleting...",
                    showConfirmButton: false,
                    allowEscapeKey: false,
                    allowOutsideClick: false
                });
                fetch(`${APP_URL}/api/warehouse/warehouse_dispatch_delete/${data.transid}`, {
                    method: "POST",
                }).then(function (res) {
                    return res.json()
                }).then(function (data) {
                    if (!data.ok) {
                        Swal.fire({
                            text: data.msg,
                            icon: "error"
                        });
                        return;
                    }
                    Swal.fire({
                        text: "Dispatch deleted successfully",
                        icon: "success"
                    });
                    wdTable.ajax.reload(false, null);
                }).catch(function (err) {
                    if (err) {
                        Swal.fire({
                            text: "Deleting dispatch failed"
                        });
                    }
                })
            }
        })

    });

    $("#exchange-table").on("click", ".view-btn", function () {
        const data = exchangeTable.row($(this).parents('tr')).data();
        $("#exch-modal").modal("show")
        $("#full-details-vendor-number").html(data.vendor_no);
        $("#full-details-vendor-name").html(data.vendor);
        $("#full-details-customer-name").html(data.customer);
        $("#full-details-customer-number").html(data.custno);
        $("#full-details-new-barcode").html(data.cylcode_new);
        $("#full-details-new-cylinder").html(data.cylcode_new);
        $("#full-details-new-size").html(data.new_size);
        $("#full-details-old-barcode").html(data.cylcode_old);
        $("#full-details-old-cylinder").html(data.cylcode_old);
        $("#full-details-old-size").html(data.old_size);

        let vendorPics = JSON.parse(data.vendor_pics);
        if (vendorPics != null) {
            vendorPics.forEach(pics => {
                document.getElementById('full-details-new-cylinder-image').setAttribute('src', pics)
            });
        }

        let custPics = JSON.parse(data.customer_pics);
        if (custPics != null) {
            custPics.forEach(pics => {
                document.getElementById('full-details-old-cylinder-image').setAttribute('src', pics)
            });
        }
    });

</script>


@endsection
