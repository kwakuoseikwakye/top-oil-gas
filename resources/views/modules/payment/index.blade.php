@extends('layout.main')
@section('pageName', 'Payment')
@section('page-content')

<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <!--begin::Entry-->
    <div class="d-flex flex-column-fluid">
        <!--begin::Container-->
        <div class="container">
            <div class="card card-custom">
                <div class="card-header flex-wrap border-0 pt-6 pb-0">
                    <div class="card-title">
                        <h3 class="card-label">All payments
                            {{-- <div class="text-muted pt-2 font-size-sm">Datatable initialized from HTML table</div> --}}
                        </h3>
                    </div>
                    <div class="card-toolbar">

                        <!--begin::Button-->
                        {{-- <a data-toggle="modal" data-target="#add-payment-modal"
                            class="btn btn-primary font-weight-bolder">
                            Add payment</a> --}}
                        <!--end::Button-->
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table width="100%" class="table table-bordered table-sm table-hover dataTable js-exportable"
                            id="payment-table">
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>Order ID</th>
                                    <th>Status</th>
                                    <th>Transaction</th>
                                    <th>Payment mode</th>
                                    <th>Amount Paid</th>
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
        <!--end::Container-->
    </div>
    <!--end::Entry-->
</div>
{{-- @include('modules.payment.modals.add')
@include('modules.payment.modals.edit') --}}
<script>
    var paymentTable = $('#payment-table').DataTable({
        "lengthChange": false,
        dom: 'Bfrtip',
        ajax: {
            url: `${APP_URL}/api/payment`,
            type: "GET",
        },
        processing: true,
        columns: [{
                data: "name"
            },
            {
                data: "order_id"
            },
            {
                data: "status"
            },
            {
                data: "transaction"
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

    //Update payment script
    // $("#payment-table").on("click", ".update-btn", function () {
    //     let data = paymentTable.row($(this).parents('tr')).data();
    //     $("#edit-payment-modal").modal("show")
    //     $("#edit-occ-transid").val(data.transid);
    //     $("#edit-occ-desc").val(data.desc);
    // });

    //Delete category script
    // $("#payment-table").on("click", ".delete-btn", function () {
    //     let data = paymentTable.row($(this).parents('tr')).data();
    //     deletepayment(data.transid)
    // });

    //Delete Category function
    // function deletepayment(transid) {
    //     Swal.fire({
    //         title: "Are you sure you want to delete this sub category?",
    //         icon: "warning",
    //         showCancelButton: true,
    //         confirmButtonColor: "#DD6B55",
    //         confirmButtonText: "Delete"

    //     }).then((result) => {
    //         if (result.value) {
    //             Swal.fire({
    //                 text: "Deleting please wait...",
    //                 showConfirmButton: false,
    //                 allowEscapeKey: false,
    //                 allowOutsideClick: false
    //             });
    //             $.ajax({
    //                 url: `${APP_URL}/api/payment/${transid}`,
    //                 type: "DELETE",
    //             }).done(function (data) {
    //                 if (!data.ok) {
    //                     Swal.fire({
    //                         text: data.msg,
    //                         icon: "error"
    //                     });
    //                     return;
    //                 }
    //                 Swal.fire({
    //                     text: "payment deleted successfully",
    //                     icon: "success"
    //                 });
    //                 paymentTable.ajax.reload(false, null);

    //             }).fail(() => {
    //                 alert('Processing failed');
    //             })
    //         }
    //     })
    // }

</script>
@endsection
