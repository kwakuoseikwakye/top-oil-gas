<div class="modal fade" id="cash-payment-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content ">
            <div class="modal-header">
                <h5 class="modal-title">Collect Cash Payments</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="cash-payment-form">
                    @csrf
                    <input type="text" name="order_id" id="cash-payment-orderid" class="form-control form-control-sm" required hidden>
                    <input type="text" name="customer" id="cash-payment-customer" class="form-control form-control-sm" required hidden>

                    <div class="col mt-3">
                        <label for="">Enter Amount *</label>
                        <input type="number" name="amount" class="form-control form-control-sm" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Close</button>
                <button type="reset" form="cash-payment-form" class="btn btn-light btn-sm">Reset</button>
                <button type="submit" form="cash-payment-form" class="btn btn-light-warning btn-sm">Save</button>
            </div>
        </div>
    </div>
</div>
<script>
    var CashForm = document.getElementById("cash-payment-form");
    // var spin = document.getElementById("spin");
    const loadFeeds = document.getElementById("load-feed");

    $(CashForm).submit(function(e) {
        e.preventDefault();

        var formdata = new FormData(CashForm);
        formdata.append("createuser", CREATEUSER);
        Swal.fire({
            title: 'Are you sure you want to collect payment?',
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
                fetch(`${APP_URL}/api/payment/collect_cash_payment`, {
                    method: "POST",
                    body: formdata,
                }).then(function(res) {
                    return res.json()
                }).then(function(data) {
                    if (!data.ok) {
                        Swal.fire({
                            title: data.msg,
                            icon: "error"
                        });
                        return;
                    }
                    Swal.fire({
                        text: "Payment recorded successfully",
                        icon: "success"
                    });

                    orderTable.ajax.reload(false, null);
                    CashForm.reset();

                }).catch(function(err) {
                    if (err) {
                        Swal.fire({
                            text: " failed",
                            icon: "error"
                        });
                    }
                })
            }
        })
    });
</script>
