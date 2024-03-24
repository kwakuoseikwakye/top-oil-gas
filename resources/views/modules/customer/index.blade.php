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
                    <div class="card-toolbar">

                        <!--begin::Button-->
                        <a data-toggle="modal" data-target="#add-customer-modal"
                            class="btn btn-light-warning font-weight-bolder">
                            Add Customer</a>
                        {{-- <a data-toggle="modal" data-target="#file-modal"
                            class="btn btn-primary font-weight-bolder ml-1">
                            Upload Excel</a> --}}
                        <!--end::Button-->
                    </div>
                </div>

                <div class="card-body">

                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home">
                                <span class="nav-text">Active Customers</span>
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content mt-5" id="myTabContent">
                        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                            <div class="table-responsive mt-3">
                                <table width="100%"
                                    class="table table-bordered table-sm table-hover dataTable js-exportable"
                                    id="customer-table">
                                    <thead>
                                        <tr>
                                            <th>Code</th>
                                            <th>Name</th>
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
@include('modules.customer.modals.add_customer')
@include('modules.customer.modals.edit_customer')
@include('modules.customer.modals.info')
@include('modules.customer.modals.file_upload')
@include('modules.customer.modals.add_location')
<script>
    var customerTable = $('#customer-table').DataTable({
        dom: 'Bfrtip',
        ajax: {
            url: `${APP_URL}/api/customer`,
            type: "GET"

        },
        ordering: false,
        order: [],
        processing: true,
        // pageLength : 100,
        columns: [{
                data: "code"
            },
            {
                data: "name"
            },
            // {
            //     data: "gender"
            // },
            // {
            //     data: "email"
            // },
            {
                data: "phone"
            },
            {
                data: "action"
            },
            // {
            //     data: null,
            //     defaultContent: `

            //     <button type='button' data-row-transid='$this->transid'
            //     rel='tooltip' class='btn btn-primary btn-sm view-btn'>
            //         <i class='fas fa-eye'></i>
            //     </button>

            //     <button type='button' data-row-transid='$this->transid'
            //     rel='tooltip' class='btn btn-success btn-sm edit-btn'>
            //        <i class='fas fa-edit'></i>
            //     </button>

            //     <button type='button' data-row-transid='$this->transid'
            //     rel='tooltip' class='btn btn-danger btn-sm delete-btn'>
            //        <i class='fas fa-trash'></i>
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
                    class: "btn btn-sm btn-secondary rounded-right"
                },
                exportOptions: {
                    columns: [0, 1, 2, 3, 4]
                }
            },
            {
                extend: 'copy',
                attr: {
                    class: "btn btn-sm btn-secondary rounded-right"
                },
                exportOptions: {
                    columns: [0, 1, 2, 3, 4]
                }
            },
            {
                extend: 'excel',
                attr: {
                    class: "btn btn-sm btn-secondary rounded-right"
                },
                exportOptions: {
                    columns: [0, 1, 2, 3, 4]
                }
            },
            {
                extend: 'pdf',
                attr: {
                    class: "btn btn-sm btn-secondary rounded-right"
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

    $("#customer-table").on("click", ".location-btn", function () {
        let data = customerTable.row($(this).parents('tr')).data();
        $("#add-customer-location-number").val(data.code);
        $("#add-customer-location-modal").modal("show");
    });

    $("#customer-table").on("click", ".edit-btn", function () {
        let data = customerTable.row($(this).parents('tr')).data();

        $("#edit-customer-modal").modal("show");
        $("#edit-customer-number").val(data.code);
        $("#edit-customer-fname").val(data.fname);
        $("#edit-customer-lname").val(data.lname);
        $("#edit-customer-phone").val(data.phone);
        $("#edit-customer-idtype").val(data.idtype).trigger('change');
        $("#edit-customer-idno").val(data.idno);
    });

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

    $("#customer-table").on("click", ".delete-btn", function () {
        var data = customerTable.row($(this).parents("tr")).data();
        var formdata = new FormData()
        formdata.append("transid", data.code);
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
                fetch(`${APP_URL}/api/customer/delete`, {
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
                        text: "Customer deleted successfully",
                        icon: "success"
                    });
                    customerTable.ajax.reload(false, null);
                }).catch(function (err) {
                    if (err) {
                        Swal.fire({
                            text: "Deleting customer failed"
                        });
                    }
                })
            }
        })

    });

</script>


@endsection
