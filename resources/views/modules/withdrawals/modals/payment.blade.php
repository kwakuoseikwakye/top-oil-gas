<!-- Modal-->
<div class="modal fade" id="payment-modal" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Complete Payment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="payment-form">
                    @csrf
                    <!--first row-->
                    <input type="text" name="fan" id="payment-fan" hidden>
                    <input type="text" name="celeb" id="payment-celeb" hidden>
                    <input type="text" name="requestCode" id="payment-request-code" hidden>
                    <div class="row mt-3">
                        <div class="col">
                            <label for="">Amount<span class="text-danger">*</span></label>
                            <input type="number" name="price" placeholder="Price must be in USD" class="form-control form-control-sm"
                                id="payment-amt" readonly>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col">
                            <label for="">Payment Channel<span class="text-danger">*</span></label>
                            <select name="channel" class="form-control select2" required>
                                <option value="mobile_money">Mobile Money</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col">
                            <label for="">Transaction ID<span class="text-danger">*</span></label>
                            <input type="text" name="reference" class="form-control form-control-sm"
                                id="add-request-ref" required>
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Close</button>
                <button type="submit" form="payment-form" class="btn btn-primary font-weight-bold">Save</button>
            </div>
        </div>
    </div>
</div>
<script>
    var addPayment = document.getElementById("payment-form");

    $(addPayment).submit(function (e) {
        e.preventDefault();

        var formdata = new FormData(addPayment)
        formdata.append("createuser", CREATEUSER);
        Swal.fire({
            title: 'Are you sure you want to complete payment for this fan?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Submit'

        }).then((result) => {

            if (result.value) {
                Swal.fire({
                    text: "Processing...",
                    showConfirmButton: false,
                    allowEscapeKey: false,
                    allowOutsideClick: false
                });
                fetch(`${APP_URL}/api/celebRequests/payment`, {
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
                        text: "Successfully paid",
                        icon: "success"
                    });
                    $("#payment-modal").modal('hide');
                    paidRequestTable.ajax.reload(false, null);
                    requestTable.ajax.reload(false, null);
                    todayRequestTable.ajax.reload(false, null);
                    addPayment.reset();

                }).catch(function (err) {
                    if (err) {
                        Swal.fire({
                            text: "Adding payment failed"
                        });
                    }
                })
            }
        })
    });

</script>
