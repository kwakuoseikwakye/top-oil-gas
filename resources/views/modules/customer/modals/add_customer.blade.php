<!-- Modal-->
<div class="modal fade" id="add-customer-modal" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add Customer</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="add-customer-form" enctype="multipart/form-data">
                    @csrf

                    <div class="row mt-3">
                        <div class="col">
                            <label for="">First Name <span class="text-danger">*</span></label>
                            <input type="text" name="firstName" placeholder="John" class="form-control form-control-sm"
                                id="add-customer-fname">
                        </div>
                        <div class="col">
                            <label for="">Surname <span class="text-danger">*</span></label>
                            <input type="text" name="lastName" placeholder="Doe" class="form-control form-control-sm"
                                id="add-customer-lname" required>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col">
                            <label for="">Phone <span class="text-danger">*</span></label>
                            <input type="text" min="10" name="phoneNumber" placeholder="0555555555"
                                class="form-control form-control-sm" id="add-customer-phone" required>
                        </div>
                        <div class="col">
                            <label for="">Picture </label>
                            <input type="file" name="image" class="form-control form-control-sm">
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col">
                            <label for="">ID Type <span class="text-danger">*</span></label>
                            <select name="idType" class="form-control form-control-sm select-input" id="add-customer-idtype" required>
                                <option value="Ghana Card">Ghana Card</option>
                                {{-- <option value="Voters Card">Voters Card</option> --}}
                                {{-- <option value="Passport">Passport</option>
                                <option value="Drivers Licence">Drivers Licence</option>
                                <option value="NHIS">NHIS</option>
                                <option value="SSNIT">SSNIT</option> --}}
                            </select>
                        </div>
                        <div class="col">
                            <label for="">ID No. <span class="text-danger">*</span></label>
                            <input type="text" name="idNumber" id="add-customer-idno"
                                class="form-control form-control-sm" required>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col">
                            <label for="">Upload ID Card</label>
                            <input type="file" accept="jpeg/png" name="idimage" class="form-control form-control-sm">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-warning font-weight-bold" data-dismiss="modal">Close</button>
                <button type="submit" form="add-customer-form" class="btn btn-warning font-weight-bold">Save</button>
            </div>
        </div>
    </div>
</div>
<script>
    var addCustomer = document.getElementById("add-customer-form");

    $(addCustomer).submit(function (e) {
        e.preventDefault();

        var formdata = new FormData(addCustomer)
        formdata.append("createuser", CREATEUSER);
        Swal.fire({
            title: 'Are you sure you want to add customer?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Submit'

        }).then((result) => {

            if (result.value) {
                Swal.fire({
                    text: "Adding...",
                    showConfirmButton: false,
                    allowEscapeKey: false,
                    allowOutsideClick: false
                });
                fetch(`${APP_URL}/api/customer`, {
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
                        text: "Customer added  successfully",
                        icon: "success"
                    });
                    $("#add-customer-modal").modal('hide');
                    $("select").val(null).trigger('change');
                    customerTable.ajax.reload(false, null);
                    addCustomer.reset();

                }).catch(function (err) {
                    if (err) {
                        Swal.fire({
                            text: "Adding customer failed"
                        });
                    }
                })
            }
        })
    });

</script>
