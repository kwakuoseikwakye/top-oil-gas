<!-- Modal-->
<div class="modal fade" id="order-cylinder-modal" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Order New Cylinder</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="order-cylinder-form">
                    @csrf
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label for="">Customer *</label>
                            <select name="customer" class="form-control  selectpicker" data-live-search="true" required
                                id="order-cylinder-customer">
                                <option value="">--Select--</option>
                                @foreach ($customer as $item)
                                    <option value="{{ $item->custno }}">{{ $item->fname }} {{ $item->lname }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col">
                            <label for="">Delivery Mode *</label>
                            <select type="text" name="delivery_mode"
                                class="form-control  selectpicker" data-live-search="true" id="order-cylinder-delivery" required>
                                <option value="">--Select--</option>
                                <option value="delivery">Delivery</option>
                                <option value="pickup">Pickup</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col">
                            <label for="">Location </label>
                            <select type="text" name="location_id" data-live-search="true" id="order-cylinder-location"
                                class="form-control  selectpicker" required>
                                {{-- @foreach ($customerLocations as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach --}}
                            </select>
                        </div>
                        <div class="col">
                            <label for="">Cylinder *</label>
                            <select type="text" name="cylcode" data-live-search="true" class="form-control  selectpicker"
                                required>
                                @foreach ($cylinderWeight as $item)
                                    <option value="{{ $item->cylcode }}">{{ $item->cylcode }} ({{ $item->weight }} -
                                        {{ $item->amount }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col">
                            <label for="">Payment Type *</label>
                            <select type="text" name="payment_type" data-live-search="true" class="form-control  selectpicker"
                                required>
                                <option value="online">Online Payment</option>
                                <option value="cash">Cash Payment</option>
                            </select>
                        </div>
                        <div class="col">
                            <label for="">Pickup</label>
                            <select type="text" name="pickup_id" data-live-search="true" class="form-control  selectpicker">
                                <option value="">--Select--</option>
                                @foreach ($pickup as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col">
                            <label for="">Pickup Date</label>
                            <input type="datetime-local" name="date" class="form-control "
                                id="order-cylinder-date">
                        </div>
                        <div class="col">
                            <label for="">Order Type</label>
                            <select type="text" name="order_type" data-live-search="true" class="form-control  selectpicker">
                                <option value="">--Select--</option>
                                <option value="pickup_now">Pickup Now</option>
                                <option value="pickup_later">Pickup Later</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-warning font-weight-bold"
                    data-dismiss="modal">Close</button>
                <button type="submit" form="order-cylinder-form" class="btn btn-warning font-weight-bold">Save</button>
            </div>
        </div>
    </div>
</div>
<script>
    var addCylinder = document.getElementById("order-cylinder-form");

    $(addCylinder).submit(function(e) {
        e.preventDefault();

        var formdata = new FormData(addCylinder)
        formdata.append("createuser", CREATEUSER);
        Swal.fire({
            title: 'Are you sure you want to order new cylinder for this customer?',
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
                fetch(`${APP_URL}/api/cylinder/add_single_order`, {
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
                        text: "Cylinder assigned successfully",
                        icon: "success"
                    });
                    $("#order-cylinder-modal").modal('hide');
                    $("select").val(null).trigger('change');
                    orderTable.ajax.reload(false, null);
                    addCylinder.reset();

                }).catch(function(err) {
                    if (err) {
                        Swal.fire({
                            text: "Adding cylinder failed"
                        });
                    }
                })
            }
        })
    });
</script>
