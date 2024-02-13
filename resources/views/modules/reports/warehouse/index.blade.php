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
                            <a class="nav-link" id="prod-tab" data-toggle="tab" href="#prod">
                                <span class="nav-text">Production</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="ex-tab" data-toggle="tab" href="#ex">
                                <span class="nav-text">Exchanged Cylinders</span>
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
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!--Data is fetched here using ajax-->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane fade show" id="dis" role="tabpanel" aria-labelledby="dis-tab">
                            <div class="row">
                                <div class="col">
                                    <label for="">Operator</label>
                                    <select id="vendor" class="form-control select2">
                                        <option value="all">All</option>
                                        @foreach ($vendor as $item)
                                        <option value="{{$item->vendor_no}}">{{$item->fname}} {{$item->mname}}
                                            {{$item->lname}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col">
                                    <label for="">From</label>
                                    <input type="date" value="{{ date('Y-m-01') }}" id="operator-dateFrom"
                                        class="form-control" />
                                </div>
                                <div class="col">
                                    <label for="">To</label>
                                    <input type="date" value="{{ date('Y-m-d') }}" id="operator-dateTo"
                                        class="form-control" />
                                </div>
                                <div class="col">
                                    <button style="margin-top:26px" class="btn btn-light-primary font-weight-bold"
                                        id="operator-filter"><i class="fa fa-filter"></i> FILTER</button>
                                </div>
                            </div>
                            <div class="table-responsive mt-3">
                                <table width="100%"
                                    class="table table-bordered table-sm table-hover dataTable js-exportable"
                                    id="dispatch-table">
                                    <thead>
                                        <tr>
                                            <th>Date/Time</th>
                                            <th>Cylinder Code</th>
                                            <th>Size</th>
                                            <th>Vendor Name</th>
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

                        <div class="tab-pane fade show" id="ret" role="tabpanel" aria-labelledby="ret-tab">
                            <div class="row">
                                <div class="col">
                                    <label for="">Operator</label>
                                    <select id="return-vendor" class="form-control select2">
                                        <option value="all">All</option>
                                        @foreach ($vendor as $item)
                                        <option value="{{$item->vendor_no}}">{{$item->fname}} {{$item->mname}}
                                            {{$item->lname}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col">
                                    <label for="">From</label>
                                    <input type="date" value="{{ date('Y-m-01') }}" id="return-dateFrom"
                                        class="form-control" />
                                </div>
                                <div class="col">
                                    <label for="">To</label>
                                    <input type="date" value="{{ date('Y-m-d') }}" id="return-dateTo"
                                        class="form-control" />
                                </div>
                                <div class="col">
                                    <button style="margin-top:26px" class="btn btn-light-primary font-weight-bold"
                                        id="return-filter"><i class="fa fa-filter"></i> FILTER</button>
                                </div>
                            </div>
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
                                            <th>Vendor</th>
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

                        <div class="tab-pane fade show" id="ex" role="tabpanel" aria-labelledby="ex-tab">
                            <div class="row">
                                <div class="col">
                                    <label for="">Customer</label>
                                    <select id="exchange-customer" class="form-control select2">
                                        <option value="all">All</option>
                                        @foreach ($customers as $item)
                                        <option value="{{$item->custno}}">{{$item->fname}} {{$item->mname}} {{$item->lname}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col">
                                    <label for="">Cylinder</label>
                                    <select id="exchange-cylinder" class="form-control select2">
                                        <option value="all">All</option>
                                        @foreach ($cylinders as $item)
                                        <option value="{{$item->cylcode}}">Cylinder Code: {{$item->cylcode}} &bullet;
                                            Barcode: {{$item->barcode}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col">
                                    <button style="margin-top:26px" class="btn btn-light-primary font-weight-bold"
                                        id="exchange-filter"><i class="fa fa-filter"></i> FILTER</button>
                                </div>
                            </div>
                            <div class="table-responsive mt-3">
                                <table width="100%"
                                    class="table table-bordered table-sm table-hover dataTable js-exportable"
                                    id="exchange-table">
                                    <thead>
                                        <tr>
                                            <th>Customer</th>
                                            <th>New Cylinder</th>
                                            <th>Barcode</th>
                                            <th>Condition</th>
                                            <th>Vendor</th>
                                            <th>Old Cylinder</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!--Data is fetched here using ajax-->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane fade show" id="prod" role="tabpanel" aria-labelledby="prod-tab">
                            <div class="row">
                                <div class="col">
                                    <label for="">Cylinder</label>
                                    <select id="cylinder-warehouse" class="form-control select2">
                                        <option value="all">All</option>
                                        @foreach ($cylinders as $item)
                                        <option value="{{$item->cylcode}}">Cylinder Code: {{$item->cylcode}} &bullet;
                                            Barcode: {{$item->barcode}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col">
                                    <label for="">From</label>
                                    <input type="date" value="{{ date('Y-m-01') }}" id="cylinder-warehouse-dateFrom"
                                        class="form-control" />
                                </div>
                                <div class="col">
                                    <label for="">To</label>
                                    <input type="date" value="{{ date('Y-m-d') }}" id="cylinder-warehouse-dateTo"
                                        class="form-control" />
                                </div>
                                <div class="col">
                                    <button style="margin-top:26px" class="btn btn-light-primary font-weight-bold"
                                        id="cylinder-warehouse-filter"><i class="fa fa-filter"></i> FILTER</button>
                                </div>
                            </div>
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
        ],
        responsive: true,
        buttons: [{
                extend: 'print',
                attr: {
                    class: "btn btn-sm btn-info rounded-right"
                },
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5]
                }
            },
            {
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

    var operatorFrom = $('#operator-dateFrom').val();
    var operatorTo = $('#operator-dateTo').val();
    var vendor = $('#vendor').val();

    var dispatchTable = $('#dispatch-table').DataTable({
        dom: 'Bfrtip',
        ajax: {
            url: `${APP_URL}/api/warehouse/fetch_dispatch/${vendor}/${operatorFrom}/${operatorTo}`,
            type: "GET"

        },
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
        // "columnDefs": [{
        //         "targets": [6],
        //         "visible": false
        //     },
        // ],
        responsive: true,
        buttons: [{
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
    $('#operator-filter').click(function () {
        var operatorFrom = $('#operator-dateFrom').val();
        var operatorTo = $('#operator-dateTo').val();
        var vendor = $('#vendor').val();

        dispatchTable.ajax.url(
                `${APP_URL}/api/warehouse/fetch_dispatch/${vendor}/${operatorFrom}/${operatorTo}`
            )
            .load();
    })

    var returnFrom = $('#return-dateFrom').val();
    var returnTo = $('#return-dateTo').val();
    var returnVendor = $('#return-vendor').val();

    var returnTable = $('#return-table').DataTable({
        dom: 'Bfrtip',
        ajax: {
            url: `${APP_URL}/api/warehouse/fetch_return/${returnVendor}/${returnFrom}/${returnTo}`,
            type: "GET"

        },
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
                    columns: [0, 1, 2, 3, 4, 5]
                }
            },
            {
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
            }, ,
        ]
    });

    // FILTER  
    $('#return-filter').click(function () {
        var returnFrom = $('#return-dateFrom').val();
        var returnTo = $('#return-dateTo').val();
        var returnVendor = $('#return-vendor').val();

        returnTable.ajax.url(
                `${APP_URL}/api/warehouse/fetch_return/${returnVendor}/${returnFrom}/${returnTo}`
            )
            .load();
    })

    var exchangeCustomer = $('#exchange-customer').val();
    var exchangeCylinder = $('#exchange-cylinder').val();

    var exchangeTable = $('#exchange-table').DataTable({
        dom: 'Bfrtip',
        ajax: {
            url: `${APP_URL}/api/warehouse/fetch_exchange/${exchangeCustomer}/${exchangeCylinder}`,
            type: "GET"

        },
        ordering: false,
        order: [],
        processing: true,
        columns: [{
                data: "customer"
            },
            {
                data: "cylcode_new"
            },
            {
                data: "barcode"
            },
            {
                data: "condition"
            },
            {
                data: "vendor"
            },
            {
                data: "cylcode_old"
            },
            // {
            //     data: null,
            //     defaultContent: `

            //     <button type='button' data-row-transid='$this->transid'
            //     rel='tooltip' class='btn btn-success btn-sm edit-btn'>
            //        Return Cylinder
            //     </button>

            //     `
            // },
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

    // FILTER  
    $('#exchange-filter').click(function () {
        var exchangeCustomer = $('#exchange-customer').val();
        var exchangeCylinder = $('#exchange-cylinder').val();

        exchangeTable.ajax.url(
                `${APP_URL}/api/warehouse/fetch_exchange/${exchangeCustomer}/${exchangeCylinder}`
            )
            .load();
    })

    var cylinderWrhFrom = $('#cylinder-warehouse-dateFrom').val();
    var cylinderWrhTo = $('#cylinder-warehouse-dateTo').val();
    var cylinder = $('#cylinder-warehouse').val();
    
    var productionTable = $('#production-table').DataTable({
        dom: 'Bfrtip',
        ajax: {
            url: `${APP_URL}/api/warehouse/fetch_production/${cylinder}/${cylinderWrhFrom}/${cylinderWrhTo}`,
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

    // FILTER  
    $('#cylinder-warehouse-filter').click(function () {
        var cylinderWrhFrom = $('#cylinder-warehouse-dateFrom').val();
        var cylinderWrhTo = $('#cylinder-warehouse-dateTo').val();
        var cylinder = $('#cylinder-warehouse').val();
   
        productionTable.ajax.url(
                `${APP_URL}/api/warehouse/fetch_production/${cylinder}/${cylinderWrhFrom}/${cylinderWrhTo}`
            )
            .load();
    })

</script>


@endsection
