@extends('layout.main')
@section('pageName', 'Sales')
@section('page-content')

<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <!--begin::Entry-->
    <div class="d-flex flex-column-fluid">
        <!--begin::Container-->
        <div class="container">
            <div class="card card-custom">
                <div class="card-header flex-wrap border-0 pt-6 pb-0">
                    <div class="card-title">
                        <h3 class="card-label">Sales
                        </h3>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col">
                            <label for="">Customer</label>
                            <select id="customer" class="form-control select2">
                                <option value="all">All</option>
                                @foreach ($customers as $item)
                                <option value="{{$item->custno}}">{{$item->fname}} {{$item->mname}} {{$item->lname}}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col">
                            <label for="">Cylinder</label>
                            <select id="cylinder" class="form-control select2">
                                <option value="all">All</option>
                                @foreach ($cylinders as $item)
                                <option value="{{$item->cylcode}}">{{$item->cylcode}} &bullet;
                                    {{$item->barcode}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col">
                            <label for="">From</label>
                            <input type="date" value="{{ date('Y-m-01') }}" id="dateFrom" class="form-control" />
                        </div>
                        <div class="col">
                            <label for="">To</label>
                            <input type="date" value="{{ date('Y-12-d') }}" id="dateTo" class="form-control" />
                        </div>
                        <div class="col">
                            <button style="margin-top:26px" class="btn btn-light-primary font-weight-bold"
                                id="filter"><i class="fa fa-filter"></i> FILTER</button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table width="100%" class="table table-bordered table-sm table-hover dataTable js-exportable"
                            id="payment-table">
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>Cylinder</th>
                                    <th>Barcode</th>
                                    <th>Payment mode</th>
                                    <th>Amount Paid</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!--Data is fetched here using ajax-->
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

            </div>
        </div>
        <!--end::Container-->
    </div>
    <!--end::Entry-->
</div>

<script>
    var dateFrom = $('#dateFrom').val();
    var dateTo = $('#dateTo').val();
    var cylinder = $('#cylinder').val();
    var customer = $('#customer').val();

    var paymentTable = $('#payment-table').DataTable({
        "footerCallback": function (row, data, start, end, display) {

            var api = this.api(),
                data;

            // converting to interger to find total
            var intVal = function (i) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '') * 1 :
                    typeof i === 'number' ?
                    i : 0;
            };

            // computing column Total of the complete result 
            var total = api
                .column(4, {
                    page: 'current'
                })
                .data()
                .reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0);

            // Update footer by showing the total with the reference of the column index 
            $(api.column(3).footer()).html('Total');
            $(api.column(4).footer()).html(total);
        },
        "lengthChange": false,
        dom: 'Bfrtip',
        ajax: {
            url: `${APP_URL}/api/payment/reports/${customer}/${cylinder}/${dateFrom}/${dateTo}`,
            type: "GET",
        },
        processing: true,
        columns: [{
                data: "name"
            },
            {
                data: "cylcode"
            },
            {
                data: "barcode"
            },
            {
                data: "mode"
            },
            {
                data: "amount"
            },
        ],
        buttons: [{
                extend: 'print',
                title: `payment List`,
                attr: {
                    class: "btn btn-sm btn-primary rounded-right"
                },
                exportOptions: {
                    columns: [0, 1, 2, 3, 4]
                }
            },
            {
                extend: 'copy',
                title: `payment List`,
                attr: {
                    class: "btn btn-sm btn-primary rounded-right"
                },
                exportOptions: {
                    columns: [0, 1, 2, 3, 4]
                }
            },
            {
                extend: 'excel',
                title: `payment List`,
                attr: {
                    class: "btn btn-sm btn-primary rounded-right"
                },
                exportOptions: {
                    columns: [0, 1, 2, 3, 4]
                }
            },
            {
                extend: 'pdf',
                title: `payment List`,
                attr: {
                    class: "btn btn-sm btn-primary rounded-right"
                },
                exportOptions: {
                    columns: [0, 1, 2, 3, 4]
                }
            },
            {
                text: "Refresh",
                attr: {
                    class: "ml-2 btn-secondary btn btn-sm rounded"
                },
                action: function (e, dt, node, config) {
                    dt.ajax.reload(false, null);
                }
            },
        ]
    });

    // FILTER  
    $('#filter').click(function () {
        var dateFrom = $('#dateFrom').val();
        var dateTo = $('#dateTo').val();
        var cylinder = $('#cylinder').val();
        var customer = $('#customer').val();

        paymentTable.ajax.url(
                `${APP_URL}/api/payment/reports/${customer}/${cylinder}/${dateFrom}/${dateTo}`
            )
            .load();
    })

</script>
@endsection
