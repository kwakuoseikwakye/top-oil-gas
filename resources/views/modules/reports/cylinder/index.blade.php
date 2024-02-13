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
                        <h3 class="card-label">Cylinders
                        </h3>
                    </div>
                </div>

                <div class="card-body">

                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home">
                                <span class="nav-text">Cylinders</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile"
                                aria-controls="profile">
                                <span class="nav-text">Cylinder Customer</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="trash-tab" data-toggle="tab" href="#trash" aria-controls="profile">
                                <span class="nav-text">Cylinder Trash</span>
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content mt-5" id="myTabContent">

                        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                            <div class="row">
                                <div class="col-md-2 col-sm-4 col-xs-12 mb-1">
                                    <label for="">From</label>
                                    <input type="date" value="{{ date('Y-m-01') }}" id="cylinder-dateFrom"
                                        class="form-control" />
                                </div>
                                <div class="col-md-2 col-sm-4 col-xs-12 mb-1">
                                    <label for="">To</label>
                                    <input type="date" value="{{ date('2050-m-d') }}" id="cylinder-dateTo"
                                        class="form-control" />
                                </div>
                                <div class="col-md-2 col-sm-2 col-xs-12">
                                    <button style="margin-top:26px" class="btn btn-light-primary font-weight-bold"
                                        id="cylinder-filter"><i class="fa fa-filter"></i> FILTER</button>
                                </div>
                            </div>
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
                            <div class="row">
                                <div class="col">
                                    <label for="">Cylinder</label>
                                    <select id="customer-cylinder" class="form-control select2">
                                        <option value="all">All</option>
                                        @foreach ($cylinder as $item)
                                        <option value="{{$item->cylcode}}">{{$item->cylcode}} &bullet;
                                            {{$item->barcode}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col">
                                    <label for="">From</label>
                                    <input type="date" value="{{ date('Y-m-01') }}" id="customer-cylinder-dateFrom"
                                        class="form-control" />
                                </div>
                                <div class="col">
                                    <label for="">To</label>
                                    <input type="date" value="{{ date('Y-m-d') }}" id="customer-cylinder-dateTo"
                                        class="form-control" />
                                </div>
                                <div class="col">
                                    <button style="margin-top:26px" class="btn btn-light-primary font-weight-bold"
                                        id="customer-cylinder-filter"><i class="fa fa-filter"></i> FILTER</button>
                                </div>
                            </div>
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
                                            <th>Vendor</th>
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

                        <div class="tab-pane fade show" id="trash" role="tabpanel" aria-labelledby="trash-tab">
                            <div class="table-responsive mt-3">
                                <table width="100%"
                                    class="table table-bordered table-sm table-hover dataTable js-exportable"
                                    id="trash-table">
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
                    </div>
                </div>
            </div>
        </div>
        <!--end::Container-->
    </div>
    <!--end::Entry-->
</div>
{{-- @include('modules.cylinder.modals.file_upload') --}}
@include('modules.cylinder.modals.info_assign')
@include('modules.cylinder.modals.info')

<script>
    var cylinderFrom = $('#cylinder-dateFrom').val();
    var cylinderTo = $('#cylinder-dateTo').val();
    var cylinderTable = $('#cylinder-table').DataTable({
        dom: 'Bfrtip',
        ajax: {
            url: `${APP_URL}/api/cylinder/${cylinderFrom}/${cylinderTo}`,
            type: "GET"

        },
        ordering: false,
        order: [],
        processing: true,
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

    // FILTER  
    $('#cylinder-filter').click(function () {
        var cylinderFrom = $('#cylinder-dateFrom').val();
        var cylinderTo = $('#cylinder-dateTo').val();

        cylinderTable.ajax.url(
                `${APP_URL}/api/cylinder/${cylinderFrom}/${cylinderTo}`
            )
            .load();
    })

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

    var cylinder = $('#customer-cylinder').val();
    var cylinderFrom = $('#customer-cylinder-dateFrom').val();
    var cylinderTo = $('#customer-cylinder-dateTo').val();

    var custCylinderTable = $('#cust-cylinder-table').DataTable({
        dom: 'Bfrtip',
        ajax: {
            url: `${APP_URL}/api/cylinder/${cylinder}/${cylinderFrom}/${cylinderTo}`,
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
            {
                data: "vendor"
            },
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

    // FILTER  
    $('#customer-cylinder-filter').click(function () {
        var cylinder = $('#customer-cylinder').val();
        var cylinderFrom = $('#customer-cylinder-dateFrom').val();
        var cylinderTo = $('#customer-cylinder-dateTo').val();

        custCylinderTable.ajax.url(
                `${APP_URL}/api/cylinder/${cylinder}/${cylinderFrom}/${cylinderTo}`
            )
            .load();
    })

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

    var trashFrom = $('#trash-dateFrom').val();
    var trashTo = $('#trash-dateTo').val();
    var trashTable = $('#trash-table').DataTable({
        dom: 'Bfrtip',
        ajax: {
            url: `${APP_URL}/api/cylinder/trash`,
            type: "GET"

        },
        ordering: false,
        order: [],
        processing: true,
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

    // FILTER  
    $('#trash-filter').click(function () {
        var trashFrom = $('#trash-dateFrom').val();
         var trashTo = $('#trash-dateTo').val();

         trashTable.ajax.url(
                `${APP_URL}/api/cylinder/trash`
            )
            .load();
    })

    $("#trash-table").on("click", ".view-btn", function () {
        let data = trashTable.row($(this).parents('tr')).data();

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
</script>


@endsection
