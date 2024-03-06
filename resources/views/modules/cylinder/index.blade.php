@extends('layout.main')
@section('pageName', 'Cylinders')
@section('page-content')

<div class="content d-flex flex-column flex-column-fluid" id="kt_content">

    <!--begin::Entry-->
    <div class="d-flex flex-column-fluid">
        <!--begin::Container-->
        <div class="container">
            <div class="card card-custom">
                <div class="card-header flex-wrap border-0 pt-6 pb-0">
                    <div class="card-title">
                        <h3 class="card-label">Orders
                        </h3>
                    </div>
                    <div class="card-toolbar">

                        <!--begin::Button-->
                        <a data-toggle="modal" data-target="#add-cylinder-modal"
                            class="btn btn-primary font-weight-bolder">
                            Add Cylinder</a>
                        <a onclick="errors()" class="btn btn-primary font-weight-bolder">
                            Merge Customer Cylinder</a>
                        <!--end::Button-->
                    </div>
                </div>

                <div class="card-body">

                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="order-tab" data-toggle="tab" href="#order-nav">
                                <span class="nav-text">All Orders</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="home-tab" data-toggle="tab" href="#home">
                                <span class="nav-text">All Cylinders</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile"
                                aria-controls="profile">
                                <span class="nav-text">Customer Cylinders</span>
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content mt-5" id="myTabContent">
                        <div class="tab-pane fade show active" id="order-nav" role="tabpanel" aria-labelledby="order-tab">
                            <div class="table-responsive mt-3">
                                <table width="100%"
                                    class="table table-bordered table-sm table-hover dataTable js-exportable"
                                    id="order-table">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Customer</th>
                                            <th>Cylinder Code</th>
                                            <th>Date</th>
                                            <th>Weight</th>
                                            <th>Location</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!--Data is fetched here using ajax-->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="home" role="tabpanel" aria-labelledby="home-tab">
                            <div class="table-responsive mt-3">
                                <table width="100%"
                                    class="table table-bordered table-sm table-hover dataTable js-exportable"
                                    id="cylinder-table">
                                    <thead>
                                        <tr>
                                            <th>Cylinder Code</th>
                                            <th>Owner</th>
                                            <th>Barcode</th>
                                            <th>Size</th>
                                            <th>Notes</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!--Data is fetched here using ajax-->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                            <div class="table-responsive mt-3">
                                <table width="100%"
                                    class="table table-bordered table-sm table-hover dataTable js-exportable"
                                    id="cust-cylinder-table">
                                    <thead>
                                        <tr>
                                            <th>Cylinder</th>
                                            <th>Barcode</th>
                                            <th>Customer</th>
                                            <th>Date Acquired</th>
                                            {{-- <th>Vendor</th> --}}
                                            <th>Status</th>
                                            <th>Actions</th>
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
{{-- @include('modules.cylinder.modals.file_upload') --}}
@include('modules.cylinder.modals.add_cylinder')
@include('modules.cylinder.modals.update_cylinder')
@include('modules.cylinder.modals.assign_cylinder')
@include('modules.cylinder.modals.update_assign_cylinder')
@include('modules.cylinder.modals.info_assign')
@include('modules.cylinder.modals.info')

<script>
    var orderTable = $('#order-table').DataTable({
        dom: 'Bfrtip',
        ajax: {
            url: `${APP_URL}/api/cylinder/get_orders`,
            type: "GET"

        },
        ordering: false,
        order: [],
        processing: true,
        pageLength : 100,
        columns: [{
                data: "order_id"
            },
            {
                data: "customer"
            },
            {
                data: "cylcode"
            },
            {
                data: "date"
            },
            {
                data: "weight"
            },
            {
                data: "location"
            },
            {
                data: "status"
            },
        ],
        // "columnDefs": [{
        //         "targets": [6],
        //         "visible": false
        //     },
        // ],
        responsive: true,
        buttons: [{
                extend: 'print',
                attr: {
                    class: "btn btn-sm btn-info rounded-right"
                },
                exportOptions: {
                    columns: [0, 1, 2, 3, 4]
                }
            },
            {
                extend: 'copy',
                attr: {
                    class: "btn btn-sm btn-info rounded-right"
                },
                exportOptions: {
                    columns: [0, 1, 2, 3, 4]
                }
            },
            {
                extend: 'excel',
                attr: {
                    class: "btn btn-sm btn-info rounded-right"
                },
                exportOptions: {
                    columns: [0, 1, 2, 3, 4]
                }
            },
            {
                extend: 'pdf',
                attr: {
                    class: "btn btn-sm btn-info rounded-right"
                },
                exportOptions: {
                    columns: [0, 1, 2, 3, 4]
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

    var cylinderTable = $('#cylinder-table').DataTable({
        dom: 'Bfrtip',
        ajax: {
            url: `${APP_URL}/api/cylinder`,
            type: "GET"

        },
        ordering: false,
        order: [],
        processing: true,
        pageLength : 100,
        columns: [{
                data: "cylcode"
            },
            {
                data: "owner"
            },
            {
                data: "barcode"
            },
            {
                data: "size"
            },
            {
                data: "notes"
            },
            // {
            //     data: "amount"
            // },
            {
                data: null,
                defaultContent: `

                <button type='button' data-row-transid='$this->transid'
                rel='tooltip' class='btn btn-primary btn-sm view-btn'>
                    <i class='fas fa-eye'></i>
                </button>

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
        // "columnDefs": [{
        //         "targets": [6],
        //         "visible": false
        //     },
        // ],
        responsive: true,
        buttons: [{
                extend: 'print',
                attr: {
                    class: "btn btn-sm btn-info rounded-right"
                },
                exportOptions: {
                    columns: [0, 1, 2, 3, 4]
                }
            },
            {
                extend: 'copy',
                attr: {
                    class: "btn btn-sm btn-info rounded-right"
                },
                exportOptions: {
                    columns: [0, 1, 2, 3, 4]
                }
            },
            {
                extend: 'excel',
                attr: {
                    class: "btn btn-sm btn-info rounded-right"
                },
                exportOptions: {
                    columns: [0, 1, 2, 3, 4]
                }
            },
            {
                extend: 'pdf',
                attr: {
                    class: "btn btn-sm btn-info rounded-right"
                },
                exportOptions: {
                    columns: [0, 1, 2, 3, 4]
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

    $("#cylinder-table").on("click", ".edit-btn", function () {
        let data = cylinderTable.row($(this).parents('tr')).data();

        $("#update-cylinder-modal").modal("show");
        $("#update-cylinder-number").val(data.transid);
        $("#update-cylinder-code").val(data.cylcode);
        $("#update-cylinder-owner").val(data.owner).trigger('change');
        $("#update-cylinder-barcode").val(data.barcode);
        $("#update-cylinder-size").val(data.size);
        $("#update-cylinder-weight").val(data.weight);
        $("#update-cylinder-amount").val(data.amount);
        $("#update-cylinder-notes").val(data.notes);
    });

    $("#cylinder-table").on("click", ".view-btn", function () {
        let data = cylinderTable.row($(this).parents('tr')).data();

        $("#info-cylinder-modal").modal("show")
        document.getElementById("info-cylinder-code").innerHTML = data.cylcode;
        document.getElementById("info-cylinder-owner").innerHTML = data.owner;
        document.getElementById("info-cylinder-barcode").innerHTML = data.barcode;
        document.getElementById("info-cylinder-size").innerHTML = data.size;
        document.getElementById("info-cylinder-weight").innerHTML = data.weight;
        document.getElementById("info-cylinder-amount").innerHTML = data.amount;
        var images = data.images;
        var imagePlacer = '';
        var cont = document.getElementById('image-place-holder');

        if (Boolean(images)) {
            images.forEach(function (image) {
                imagePlacer += `<img src="${image}" height="150" width="150" class="mr-2">`;
            });
            cont.innerHTML = imagePlacer;
        }

    });

    $("#cylinder-table").on("click", ".delete-btn", function () {
        var data = cylinderTable.row($(this).parents("tr")).data();
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
                fetch(`${APP_URL}/api/cylinder/delete`, {
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
                        text: "Cylinder deleted successfully",
                        icon: "success"
                    });
                    cylinderTable.ajax.reload(false, null);
                }).catch(function (err) {
                    if (err) {
                        Swal.fire({
                            text: "Deleting cylinder failed"
                        });
                    }
                })
            }
        })

    });

    var custCylinderTable = $('#cust-cylinder-table').DataTable({
        dom: 'Bfrtip',
        ajax: {
            url: `${APP_URL}/api/cylinder/customer`,
            type: "GET"

        },
        ordering: false,
        order: [],
        processing: true,
        columns: [{
                data: "cylcode"
            },
            {
                data: "barcode"
            },
            {
                data: "customer"
            },
            {
                data: "date"
            },
            // {
            //     data: "vendor"
            // },
            {
                data: "status"
            },
            {
                data: null,
                defaultContent: `

                <button type='button' data-row-transid='$this->transid'
                rel='tooltip' class='btn btn-primary btn-sm view-btn'>
                    <i class='fas fa-eye'></i>
                </button>

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
        // "columnDefs": [{
        //         "targets": [6],
        //         "visible": false
        //     },
        // ],
        responsive: true,
        buttons: [{
                extend: 'print',
                attr: {
                    class: "btn btn-sm btn-info rounded-right"
                },
                exportOptions: {
                    columns: [0, 1, 2, 3, 4]
                }
            },
            {
                extend: 'copy',
                attr: {
                    class: "btn btn-sm btn-info rounded-right"
                },
                exportOptions: {
                    columns: [0, 1, 2, 3, 4]
                }
            },
            {
                extend: 'excel',
                attr: {
                    class: "btn btn-sm btn-info rounded-right"
                },
                exportOptions: {
                    columns: [0, 1, 2, 3, 4]
                }
            },
            {
                extend: 'pdf',
                attr: {
                    class: "btn btn-sm btn-info rounded-right"
                },
                exportOptions: {
                    columns: [0, 1, 2, 3, 4]
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
                text: "Assign Cylinder",
                attr: {
                    class: "ml-2 btn-success btn btn-sm rounded"
                },
                action: function (e, dt, node, config) {
                    $("#assign-cylinder-modal").modal("show")
                }
            },
        ]
    });

    $("#cust-cylinder-table").on("click", ".edit-btn", function () {
        let data = custCylinderTable.row($(this).parents('tr')).data();

        $("#update-assign-cylinder-modal").modal("show");
        $("#update-cylinder-assignment-id").val(data.transid);
        $("#update-assign-customer").val(data.customer_code).trigger('change');
        $("#update-assign-cylinder").val(data.barcode).trigger('change');
        $("#update-assign-vendor").val(data.vendor_code).trigger('change');
    });

    $("#cust-cylinder-table").on("click", ".view-btn", function () {
        let data = custCylinderTable.row($(this).parents('tr')).data();

        $("#info-assign-cylinder-modal").modal("show")
        document.getElementById("info-cylinder-assign-code").innerHTML = data.cylcode;
        document.getElementById("info-cylinder-assign-barcode").innerHTML = data.barcode;
        document.getElementById("info-cylinder-assign-customer").innerHTML = data.customer;
        document.getElementById("info-cylinder-assign-vendor").innerHTML = data.vendor;
        document.getElementById("info-cylinder-assign-date").innerHTML = data.date;
        document.getElementById("info-cylinder-assign-status").innerHTML = data.status;
        var images = data.images;
        var imagePlacer = '';
        var cont = document.getElementById('image-assign-place-holder');

        if (Boolean(images)) {
            images.forEach(function (image) {
                imagePlacer += `<img src="${image}" height="150" width="150" class="mr-2">`;
            });
            cont.innerHTML = imagePlacer;
        }

    });

    $("#cust-cylinder-table").on("click", ".delete-btn", function () {
        var data = custCylinderTable.row($(this).parents("tr")).data();
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
                fetch(`${APP_URL}/api/cylinder/delete_assign`, {
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
                        text: "Customer cylinder deleted successfully",
                        icon: "success"
                    });
                    custCylinderTable.ajax.reload(false, null);
                }).catch(function (err) {
                    if (err) {
                        Swal.fire({
                            text: "Deleting cylinder failed"
                        });
                    }
                })
            }
        })

    });

    function errors() {

        Swal.fire({
            title: 'Are you sure you want to merge cylinders?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Submit'

        }).then((result) => {

            if (result.value) {
                Swal.fire({
                    text: "Processing...",
                    showConfirmButton: false,
                    allowEscapeKey: false,
                    allowOutsideClick: false
                });
                fetch(`${APP_URL}/api/import_cylinder`, {
                    method: "GET",
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
                        text: "Cylinder added successfully",
                        icon: "success"
                    });
                    cylinderTable.ajax.reload(false, null);

                }).catch(function (err) {
                    if (err) {
                        Swal.fire({
                            text: "Adding cylinder failed"
                        });
                    }
                })
            }
        })
    }

</script>


@endsection
