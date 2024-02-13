@extends('layout.main')
@section('pageName', 'Customers')
@section('page-content')

<div class="content d-flex flex-column flex-column-fluid" id="kt_content">

    <!--begin::Entry-->
    <div class="d-flex flex-column-fluid">
        <!--begin::Container-->
        <div class="container">
            <div class="card card-custom">
                <div class="card-header flex-wrap border-0 pt-6 pb-0">
                    <div class="card-title">
                        <h3 class="card-label">Customers
                        </h3>
                    </div>
                </div>

                <div class="card-body">

                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home">
                                <span class="nav-text">Customers</span>
                            </a>
                        </li>
                       
                        <li class="nav-item">
                            <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile">
                                <span class="nav-text">Customer Cylinder</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" id="trash-tab" data-toggle="tab" href="#trash">
                                <span class="nav-text">Trash</span>
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content mt-5" id="myTabContent">

                        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                            <div class="row">
                                <div class="col-md-2 col-sm-4 col-xs-12 mb-1">
                                    <label for="">From</label>
                                    <input type="date" value="{{ date('Y-m-01') }}" id="customer-dateFrom"
                                        class="form-control" />
                                </div>
                                <div class="col-md-2 col-sm-4 col-xs-12 mb-1">
                                    <label for="">To</label>
                                    <input type="date" value="{{ date('2050-m-d') }}" id="customer-dateTo"
                                        class="form-control" />
                                </div>
                                <div class="col-md-2 col-sm-2 col-xs-12">
                                    <button style="margin-top:26px" class="btn btn-light-primary font-weight-bold"
                                        id="customer-filter"><i class="fa fa-filter"></i> FILTER</button>
                                </div>
                            </div>
                            <div class="table-responsive mt-3">
                                <table width="100%"
                                    class="table table-bordered table-sm table-hover dataTable js-exportable"
                                    id="customer-table">
                                    <thead>
                                        <tr>
                                            <th>Code</th>
                                            <th>Name</th>
                                            <th>Gender</th>
                                            <th>Email</th>
                                            <th>Phone</th>
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
                                    <label for="">Customer</label>
                                    <select id="customer" class="form-control select2">
                                        <option value="all">All</option>
                                        @foreach ($customer as $item)
                                        <option value="{{$item->custno}}">{{$item->fname}} {{$item->mname}}
                                            {{$item->lname}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col">
                                    <label for="">From</label>
                                    <input type="date" value="{{ date('Y-m-01') }}" id="cylinder-dateFrom"
                                        class="form-control" />
                                </div>
                                <div class="col">
                                    <label for="">To</label>
                                    <input type="date" value="{{ date('Y-m-d') }}" id="cylinder-dateTo"
                                        class="form-control" />
                                </div>
                                <div class="col">
                                    <button style="margin-top:26px" class="btn btn-light-primary font-weight-bold"
                                        id="cylinder-filter"><i class="fa fa-filter"></i> FILTER</button>
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
                                            <th>Code</th>
                                            <th>Name</th>
                                            <th>Gender</th>
                                            <th>Email</th>
                                            <th>Phone</th>
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
@include('modules.customer.modals.info')
@include('modules.cylinder.modals.info_assign')

<script>
    var customerFrom = $('#customer-dateFrom').val();
    var customerTo = $('#customer-dateTo').val();

    var customerTable = $('#customer-table').DataTable({
        dom: 'Bfrtip',
        ajax: {
            url: `${APP_URL}/api/customer/${customerFrom}/${customerTo}`,
            type: "GET"

        },
        ordering: false,
        order: [],
        processing: true,
        columns: [{
                data: "code"
            },
            {
                data: "name"
            },
            {
                data: "gender"
            },
            {
                data: "email"
            },
            {
                data: "phone"
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
        ]
    });

    // FILTER  
    $('#customer-filter').click(function () {
        var customerFrom = $('#customer-dateFrom').val();
        var customerTo = $('#customer-dateTo').val();

        customerTable.ajax.url(
                `${APP_URL}/api/customer/${customerFrom}/${customerTo}`
            )
            .load();
    })

    $("#customer-table").on("click", ".view-btn", function () {
        let data = customerTable.row($(this).parents('tr')).data();

        $("#info-customer-modal").modal("show")
        document.getElementById("info-customer-name").innerHTML = data.name;
        $("#info-customer-image").attr("src", Boolean(data.picture) ? data.picture :
            '/avatar.png');
        document.getElementById("info-customer-number").innerHTML = data.code;
        document.getElementById("info-customer-gender").innerHTML = data.gender;
        document.getElementById("info-customer-occupation").innerHTML = data.occupation;
        document.getElementById("info-customer-marital-status").innerHTML = data.marital_status;
        document.getElementById("info-customer-dob").innerHTML = data.dob;
        document.getElementById("info-customer-pob").innerHTML = data.pob;
        document.getElementById("info-customer-phone").innerHTML = data.phone;
        document.getElementById("info-customer-email").innerHTML = data.email;
        document.getElementById("info-customer-region").innerHTML = data.region;
        document.getElementById("info-customer-town").innerHTML = data.town;
        document.getElementById("info-customer-streetname").innerHTML = data.streetname;
        document.getElementById("info-customer-landmark").innerHTML = data.landmark;
        document.getElementById("info-customer-gpsaddress").innerHTML = data.address;
        document.getElementById("info-customer-coordinates").innerHTML = `Lat: ${data.lat}, Long: ${data.long}`;
        document.getElementById("info-customer-id-type").innerHTML = data.idtype;
        document.getElementById("info-customer-id-number").innerHTML = data.idno;
        $("#info-customer-id-image").attr("src", Boolean(data.picture) ? data.picture :
            '/img/noimage.jpg');
    });

    var trashTable = $('#trash-table').DataTable({
        dom: 'Bfrtip',
        ajax: {
            url: `${APP_URL}/api/customer/trash`,
            type: "GET"

        },
        ordering: false,
        order: [],
        processing: true,
        columns: [{
                data: "code"
            },
            {
                data: "name"
            },
            {
                data: "gender"
            },
            {
                data: "email"
            },
            {
                data: "phone"
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
        ]
    });

    $("#trash-table").on("click", ".view-btn", function () {
        let data = trashTable.row($(this).parents('tr')).data();

        $("#info-customer-modal").modal("show")
        document.getElementById("info-customer-name").innerHTML = data.name;
        $("#info-customer-image").attr("src", Boolean(data.picture) ? data.picture :
            '/avatar.png');
        document.getElementById("info-customer-number").innerHTML = data.code;
        document.getElementById("info-customer-gender").innerHTML = data.gender;
        document.getElementById("info-customer-occupation").innerHTML = data.occupation;
        document.getElementById("info-customer-marital-status").innerHTML = data.marital_status;
        document.getElementById("info-customer-dob").innerHTML = data.dob;
        document.getElementById("info-customer-pob").innerHTML = data.pob;
        document.getElementById("info-customer-phone").innerHTML = data.phone;
        document.getElementById("info-customer-email").innerHTML = data.email;
        document.getElementById("info-customer-region").innerHTML = data.region;
        document.getElementById("info-customer-town").innerHTML = data.town;
        document.getElementById("info-customer-streetname").innerHTML = data.streetname;
        document.getElementById("info-customer-landmark").innerHTML = data.landmark;
        document.getElementById("info-customer-gpsaddress").innerHTML = data.address;
        document.getElementById("info-customer-coordinates").innerHTML = `Lat: ${data.lat}, Long: ${data.long}`;
        document.getElementById("info-customer-id-type").innerHTML = data.idtype;
        document.getElementById("info-customer-id-number").innerHTML = data.idno;
        $("#info-customer-id-image").attr("src", Boolean(data.picture) ? data.picture :
            '/img/noimage.jpg');
    });

    var customer = $('#customer').val();
    var cylinderFrom = $('#cylinder-dateFrom').val();
    var cylinderTo = $('#cylinder-dateTo').val();

    var custCylinderTable = $('#cust-cylinder-table').DataTable({
        dom: 'Bfrtip',
        ajax: {
            url: `${APP_URL}/api/cylinder/customer/${customer}/${cylinderFrom}/${cylinderTo}`,
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
        ]
    });

    // FILTER  
    $('#customer').change(function () {
        var customer = $('#customer').val();
        var cylinderFrom = $('#cylinder-dateFrom').val();
        var cylinderTo = $('#cylinder-dateTo').val();

        custCylinderTable.ajax.url(
                `${APP_URL}/api/cylinder/customer/${customer}/${cylinderFrom}/${cylinderTo}`
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

</script>


@endsection
