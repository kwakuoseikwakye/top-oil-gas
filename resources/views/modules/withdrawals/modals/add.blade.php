<!-- Modal-->
<div class="modal fade" id="add-request-modal" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Request For a Fan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="add-request-form">
                    @csrf
                    <!--first row-->
                    <div class="row mt-3">
                        <div class="col">
                            <label for="">Celebrity<span class="text-danger">*</span></label>
                            <select name="celeb" class="form-control select2" id="add-request-celeb" required>
                                <option value="">--select--</option>
                                @foreach ($celeb as $item)
                                <option value="{{$item->transid}}">{{$item->username}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col">
                            <label for="">Fan<span class="text-danger">*</span></label>
                            <select name="fan" class="form-control select2" id="add-request-fan" required>
                                <option value="">--select--</option>
                                @foreach ($fan as $item)
                                <option value="{{$item->transid}}">{{$item->fname}} {{$item->mname}} {{$item->lname}}
                                    ({{$item->username}})
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col">
                            <label for="">Request Type<span class="text-danger">*</span></label>
                            <select name="type" class="form-control select2" id="add-request-type" required>
                                <option value="">--select--</option>
                                @foreach ($request as $item)
                                <option value="{{$item->code}}">{{$item->desc}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col">
                            <label for="">Occassion<span class="text-danger">*</span></label>
                            <select name="occassion" class="form-control select2" id="add-occassion" required>
                                <option value="">--select--</option>
                                @foreach ($occassion as $item)
                                <option value="{{$item->code}}">{{$item->desc}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col">
                            <label for="">Payment Channel</label>
                            <select name="channel" class="form-control select2">
                                <option value="mobile_money">Mobile Money</option>
                            </select>
                        </div>
                        <div class="col">
                            <label for="">Transaction ID</label>
                            <input type="text" name="reference" class="form-control form-control-sm"
                                id="add-request-ref">
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col">
                            <label for="">Price </label>
                            <input type="number" step="0.2" name="price" placeholder="Prices must be in USD"
                                class="form-control form-control-sm" id="add-request-amt">
                        </div>
                        <div class="col">
                            <label for="">Introduction<span class="text-danger">*</span></label>
                            <input type="text" name="introduction" class="form-control form-control-sm"
                                id="add-request-amt" required>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col">
                            <label for="">Instructions<span class="text-danger">*</span></label>
                            <textarea name="instruction" class="form-control" id="" cols="30" rows="10"></textarea>
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Close</button>
                <button type="submit" form="add-request-form" class="btn btn-primary font-weight-bold">Save</button>
            </div>
        </div>
    </div>
</div>
<script>
    var requestFan = document.getElementById("add-request-form");

    $(requestFan).submit(function (e) {
        e.preventDefault();

        var formdata = new FormData(requestFan)
        formdata.append("createuser", CREATEUSER);
        Swal.fire({
            title: 'Are you sure you want to request for a fan?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Submit'

        }).then((result) => {

            if (result.value) {
                Swal.fire({
                    text: "Requesting...",
                    showConfirmButton: false,
                    allowEscapeKey: false,
                    allowOutsideClick: false
                });
                fetch(`${APP_URL}/api/celebRequests/request_fan`, {
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
                        text: "Request made successfully",
                        icon: "success"
                    });
                    $("#add-request-modal").modal('hide');
                    requestTable.ajax.reload(false, null);
                    todayRequestTable.ajax.reload(false, null);
                    requestFan.reset();

                }).catch(function (err) {
                    if (err) {
                        Swal.fire({
                            text: "Process failed"
                        });
                    }
                })
            }
        })
    });

</script>
