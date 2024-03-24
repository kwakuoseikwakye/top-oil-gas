<!-- Modal-->
<div class="modal fade" id="add-customer-location-modal" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop"
    aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add Location</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="add-customer-location-form">
                    @csrf
                    <input type="hidden" name="custno" id="add-customer-location-number">

                    <div class="row mt-3">
                        <div class="col">
                            <label for="">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" placeholder="Eg. My home"
                                class="form-control form-control-sm" id="add-customer-location-name">
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col">
                            <label for="">Phone <span class="text-danger">*</span></label>
                            <input type="text" min="10" name="phoneNumber" placeholder="0555555555"
                                class="form-control form-control-sm" id="add-customerloc-phone" required>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col">
                            <label for="">Address <span class="text-danger">*</span></label>
                            <textarea name="address" class="form-control" id="add-customer-loc-address" required>
                            </textarea>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col">
                            <label for="">Additional Info</label>
                            <textarea type="text" name="additional_info" id="add-customer-loc-info"
                                class="form-control"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-warning font-weight-bold"
                    data-dismiss="modal">Close</button>
                <button type="submit" form="add-customer-location-form"
                    class="btn btn-warning font-weight-bold">Save</button>
            </div>
        </div>
    </div>
</div>
<script>
    var addCustomerLoc = document.getElementById("add-customer-location-form");

    $(addCustomerLoc).submit(function(e) {
        e.preventDefault();

        var formdata = new FormData(addCustomerLoc)
        formdata.append("createuser", CREATEUSER);
        Swal.fire({
            title: 'Are you sure you want to add customer location?',
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
                fetch(`${APP_URL}/api/customer/add_location`, {
                    method: "POST",
                    body: formdata,
                }).then(function(res) {
                    return res.json()
                }).then(function(data) {
                    if (!data.ok) {
                        Swal.fire({
                            text: data.msg,
                            icon: "error"
                        });
                        return;
                    }
                    Swal.fire({
                        text: "Customer location added successfully",
                        icon: "success"
                    });
                    $("#add-customer-location-modal").modal('hide');
                    $("select").val(null).trigger('change');
                    customerTable.ajax.reload(false, null);
                    addCustomerLoc.reset();

                }).catch(function(err) {
                    if (err) {
                        Swal.fire({
                            text: "Adding customer location failed"
                        });
                    }
                })
            }
        })
    });
</script>
