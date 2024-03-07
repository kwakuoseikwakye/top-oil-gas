<!-- Modal-->
<div class="modal fade" id="update-assign-cylinder-modal" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Cylinder Refill</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="update-assign-cylinder-form" enctype="multipart/form-data">
                    @csrf
                    <input type="text" name="transid" id="update-assign-cylinder-transid" required hidden>
                    <input type="text" name="custno" id="update-assign-cylinder-custno" required hidden>
                    <input type="text" name="order_id" id="update-assign-cylinder-orderid" required hidden>
                    <input type="text" name="weight_id" id="update-assign-cylinder-weight" required hidden>
                    <label for="">Cylinder<span class="text-danger">*</span></label>
                    <div class="col mt-3">
                        <select name="cylcode" class="form-control" id="update-assign-customer-cylinder" required>

                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Close</button>
                <button type="submit" form="update-assign-cylinder-form" class="btn btn-primary font-weight-bold">Save</button>
            </div>
        </div>
    </div>
</div>
<script>
    var updateAssignCylinder = document.getElementById("update-assign-cylinder-form");

    $(updateAssignCylinder).submit(function (e) {
        e.preventDefault();

        var formdata = new FormData(updateAssignCylinder)
        formdata.append("createuser", CREATEUSER);
        Swal.fire({
            title: 'Are you sure you want to refill customer cylinder?',
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
                fetch(`${APP_URL}/api/cylinder/update_assign`, {
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
                        text: "Assigned successfully",
                        icon: "success"
                    });
                    $("#update-assign-cylinder-modal").modal('hide');
                    $("select").val(null).trigger('change');
                    orderTable.ajax.reload(false, null);
                    updateAssignCylinder.reset();

                }).catch(function (err) {
                    if (err) {
                        Swal.fire({
                            text: "Assigning cylinder failed"
                        });
                    }
                })
            }
        })
    });

</script>
