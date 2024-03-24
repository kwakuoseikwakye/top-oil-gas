<!-- Modal-->
<div class="modal fade" id="edit-customer-modal" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Update Customer</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="edit-customer-form" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="custno" id="edit-customer-number">
                    <div class="row mt-3">
                        <div class="col">
                            <label for="">First Name <span class="text-danger">*</span></label>
                            <input type="text" name="firstName" placeholder="John" class="form-control form-control-sm"
                                id="edit-customer-fname">
                        </div>
                        <div class="col">
                            <label for="">Surname <span class="text-danger">*</span></label>
                            <input type="text" name="lastName" placeholder="Doe" class="form-control form-control-sm"
                                id="edit-customer-lname" required>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col">
                            <label for="">Phone <span class="text-danger">*</span></label>
                            <input type="text" min="10" name="phoneNumber" placeholder="0555555555"
                                class="form-control form-control-sm" id="edit-customer-phone" required>
                        </div>
                        <div class="col">
                            <label for="">Picture </label>
                            <input type="file" name="image" class="form-control form-control-sm">
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col">
                            <label for="">ID Type <span class="text-danger">*</span></label>
                            <select name="idType" class="form-control form-control-sm select-input" id="edit-customer-idtype" required>
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
                            <input type="text" name="idNumber" id="edit-customer-idno"
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
                <button type="submit" form="edit-customer-form" class="btn btn-warning font-weight-bold">Update</button>
            </div>
        </div>
    </div>
</div>
<script>
    var editCustomer = document.getElementById("edit-customer-form");

    $(editCustomer).submit(function (e) {
        e.preventDefault();

        var formdata = new FormData(editCustomer)
        formdata.append("createuser", CREATEUSER);
        Swal.fire({
            title: 'Are you sure you want to edit customer?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Submit'

        }).then((result) => {

            if (result.value) {
                Swal.fire({
                    text: "Saving changes...",
                    showConfirmButton: false,
                    allowEscapeKey: false,
                    allowOutsideClick: false
                });
                fetch(`${APP_URL}/api/customer/update`, {
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
                        text: "Customer updated successfully",
                        icon: "success"
                    });
                    $("#edit-customer-modal").modal('hide');
                    $("select").val(null).trigger('change');
                    customerTable.ajax.reload(false, null);
                    editCustomer.reset();

                }).catch(function (err) {
                    if (err) {
                        Swal.fire({
                            text: "Updating customer failed"
                        });
                    }
                })
            }
        })
    });

</script>
