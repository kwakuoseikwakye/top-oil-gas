<div class="modal fade" id="print-dispatch-modal" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop"
    aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Print Dispatch Report</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="print-dispatch-form">
                    @csrf
                    @php
                         $vendors = DB::table("tblvendor")->where("deleted",0)->get();
                    @endphp
                    {{-- <div class="row mt-3">
                        <div class="col">
                            <label for="">Search Keyword<span class="text-danger">*</span></label>
                            <input type="text" name="keyword"
                                placeholder="Eg. Enter barcode, customer #, customer phone number, cylinder #."
                                class="form-control form-control-sm" id="search-keyword" autofocus required>
                        </div>
                    </div> --}}

                    <div class="row mt-3">
                        <div class="col">
                            <label for="">Select Vendor<span class="text-danger">*</span></label>
                            <select name="vendor" id="select-v" class="form-control select2" required>
                                <option value="">--Select--</option>
                                @foreach ($vendors as $item)
                                <option value="{{$item->vendor_no}}">{{$item->fname}} {{$item->mname}} 
                                    {{$item->lname}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    

                    {{-- <div class="row mt-3">
                        <div class="col">
                            <label for="">Date<span class="text-danger">*</span></label>
                            <input type="date" value="{{date("Y-m-d")}}" name="date" class="form-control form-control-sm" required>
                        </div>
                    </div> --}}


                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Close</button>
                <button type="submit" form="print-dispatch-form" class="btn btn-primary font-weight-bold">Search</button>
            </div>
        </div>
    </div>
</div>
<script>
    $('#search-modal').on('shown.bs.modal', function () {
        $('#search-keyword').focus();
    })
    var printForm = document.getElementById("print-dispatch-form");

    $(printForm).submit(function (e) {
        e.preventDefault();

        var formdata = new FormData(printForm)


        Swal.fire({
            text: "Searching...",
            showConfirmButton: false,
            allowEscapeKey: false,
            allowOutsideClick: false
        });
        fetch(`${APP_URL}/api/print_dispatch`, {
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
                text: "Operator found successfully",
                icon: "success"
            });
            console.log(data.data);

            var printReturnCylinderTableBody = document.getElementById("print-return-cylinder-id")
            var printDispatchCylinderTableBody = document.getElementById("print-dispatch-cylinder-id")

            printReturnCylinderTableBody.innerHTML = null;
            printDispatchCylinderTableBody.innerHTML = null;

            let dispatch = data.data;
            document.getElementById("dispatch-return-to").innerText = dispatch[0].vfname + ' ' +
            dispatch[0].vmname+ ' ' + dispatch[0].vlname;
            document.getElementById("dispatch-return-to-phone").innerText = dispatch[0].phone;
            document.getElementById("dispatch-location").innerText = dispatch[0].location;

            dispatch.forEach(element => {
                let tRow = document.createElement('tr');
                let dRow = document.createElement('tr');

                //create first row for return cylinder column
                let returnCylcode = document.createElement('td')
                // returnCylcode.appendChild(document.createTextNode());
                tRow.appendChild(returnCylcode);

                //create second row for return cylinder column
                let returnSize = document.createElement('td')
                // returnSize.appendChild(document.createTextNode());
                tRow.appendChild(returnSize);

                //create third row for return cylinder column
                let returnEmpty = document.createElement('td')
                // returnEmpty.appendChild(document.createTextNode());
                tRow.appendChild(returnEmpty);

                //create fourth row for return cylinder column
                let returnRemarks = document.createElement('td')
                // returnRemarks.appendChild(document.createTextNode());
                tRow.appendChild(returnRemarks);

                printReturnCylinderTableBody.appendChild(tRow);

                //CREATES TABLE DATA FOR DISPATCH

                //create first row for dispatch cylinder column
                let dispatchCylcode = document.createElement('td')
                dispatchCylcode.appendChild(document.createTextNode(element.cylcode));
                dRow.appendChild(dispatchCylcode);

                //create second row for dispatch cylinder column
                let dispatchSize = document.createElement('td')
                dispatchSize.appendChild(document.createTextNode(element.cylinder_size));
                dRow.appendChild(dispatchSize);

                //create third row for dispatch cylinder column
                let dispatchName = document.createElement('td')
                // dispatchName.appendChild(document.createTextNode());
                dRow.appendChild(dispatchName);

                 //create third row for dispatch cylinder column
                 let dispatchPrize = document.createElement('td')
                //  dispatchPrize.appendChild(document.createTextNode());
                dRow.appendChild(dispatchPrize);

                //create third row for dispatch cylinder column
                let dispatchPhone = document.createElement('td')
                // dispatchPhone.appendChild(document.createTextNode());
                dRow.appendChild(dispatchPhone);


                printDispatchCylinderTableBody.appendChild(dRow);

            });

            $("select").val(null).trigger('change');
            printForm.reset();
            $("#print-dispatch-modal").modal('hide');
            $("#vendor-dispatch-modal").modal('show');

        }).catch(function (err) {
            if (err) {
                Swal.fire({
                    text: "Search failed...could not find customer"
                });
            }
        })
    });

</script>
