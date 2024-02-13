<div class="modal fade" id="update-location-modal" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop"
    aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Update Location</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="update-location-form">
                    @csrf
 
                    <input type="text" name="transid" id="update-location-code" hidden required>
                    <div class="row mt-3">
                        <div class="col">
                            <label for="">Location Description<span class="text-danger">*</span></label>
                            <input type="text" name="desc" placeholder="Eg. Tema, Ashtown"
                                class="form-control form-control-sm" id="update-location-desc" required>
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Close</button>
                <button type="submit" form="update-location-form" class="btn btn-primary font-weight-bold">Save</button>
            </div>
        </div>
    </div>
</div>
<script>
    var updateLocationForm = document.getElementById("update-location-form");

    $(updateLocationForm).submit(function (e) {
        e.preventDefault();

        var formdata = new FormData(updateLocationForm)
        formdata.append("createuser", CREATEUSER);
        Swal.fire({
            title: 'Are you sure you want to update location?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Submit'

        }).then((result) => {

            if (result.value) {
                Swal.fire({
                    text: "Updating...",
                    showConfirmButton: false,
                    allowEscapeKey: false,
                    allowOutsideClick: false
                });
                fetch(`${APP_URL}/api/settings/update_location`, {
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
                        text: "Location updated successfully",
                        icon: "success"
                    });


                    $("select").val(null).trigger('change');
                    updateLocationForm.reset();
                    locationTable.ajax.reload(false, null);
                    $("#update-location-modal").modal('hide');

                }).catch(function (err) {
                    if (err) {
                        Swal.fire({
                            text: "An error occurred in the form"
                        });
                    }
                })
            }
        })
    });

</script>
