<!-- Modal-->
<div class="modal fade" id="edit-price-modal" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Update</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="edit-price-form">
                    @csrf
                    <!--first row-->
                    <input type="text" name="transid" id="edit-price-transid" hidden>
                    <div class="row mt-3">
                        <div class="col">
                            <label for="">Celebrity<span class="text-danger">*</span></label>
                            <select name="celeb" class="form-control select2" id="edit-price-celeb">
                                @foreach ($celeb as $item)
                                <option value="{{$item->transid}}">{{$item->username}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col">
                            <label for="">Request Type<span class="text-danger">*</span></label>
                            <select name="type" class="form-control select2" id="edit-price-type">
                                @foreach ($request as $item)
                                <option value="{{$item->code}}">{{$item->desc}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col">
                            <label for="">Price<span class="text-danger">*</span></label>
                            <input type="number" step="0.2" name="price" placeholder="Prices must be in USD" class="form-control form-control-sm" id="edit-price-amt">
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Close</button>
                <button type="submit" form="edit-price-form" class="btn btn-primary font-weight-bold">Save</button>
            </div>
        </div>
    </div>
</div>
<script>
    var editPrice = document.getElementById("edit-price-form");

    $(editPrice).submit(function (e) {
        e.preventDefault();

        var formdata = new FormData(editPrice)
        formdata.append("createuser", CREATEUSER);
        Swal.fire({
            title: 'Are you sure you want to update celebrity price?',
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
                fetch(`${APP_URL}/api/celebrity_price/update`, {
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
                        text: "Celebrity price updated  successfully",
                        icon: "success"
                    });
                    $("#edit-price-modal").modal('hide');
                    priceTable.ajax.reload(false, null);
                    editPrice.reset();

                }).catch(function (err) {
                    if (err) {
                        Swal.fire({
                            text: "Updating price failed"
                        });
                    }
                })
            }
        })
    });

</script>
