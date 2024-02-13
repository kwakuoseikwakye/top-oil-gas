<div class="modal fade" id="search-modal" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop"
    aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Print Dispatch & Return Report</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="search-form">
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
                            <select name="vendor" id="select-type" class="form-control select2" required>
                                <option value="">--Select--</option>
                                @foreach ($vendors as $item)
                                <option value="{{$item->vendor_no}}">{{$item->fname}} {{$item->mname}}
                                    {{$item->lname}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>


                    <div class="row mt-3">
                        <div class="col">
                            <label for="">Date<span class="text-danger">*</span></label>
                            <input type="date" value="{{date("Y-m-d")}}" name="date"
                                class="form-control form-control-sm" required>
                        </div>
                    </div>


                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Close</button>
                <button type="submit" form="search-form" class="btn btn-primary font-weight-bold">Search</button>
            </div>
        </div>
    </div>
</div>
<script>
    $('#search-modal').on('shown.bs.modal', function () {
        $('#search-keyword').focus();
    })
    var searchForm = document.getElementById("search-form");

    $(searchForm).submit(function (e) {
        e.preventDefault();

        var formdata = new FormData(searchForm)


        Swal.fire({
            text: "Searching...",
            showConfirmButton: false,
            allowEscapeKey: false,
            allowOutsideClick: false
        });
        fetch(`${APP_URL}/api/search`, {
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
                text: "Report found successfully",
                icon: "success"
            });
            console.log(data.data);

            var returnCylinderTableBody = document.getElementById("return-cylinder-id")
            var dispatchCylinderTableBody = document.getElementById("dispatch-cylinder-id")

            returnCylinderTableBody.innerHTML = null;
            dispatchCylinderTableBody.innerHTML = null;

            let dispatch = data.data;
            let returns = data.return;

            document.getElementById("s-dispatch-return-to").innerText = dispatch[0].vfname + ' ' +
                dispatch[0].vmname + ' ' + dispatch[0].vlname;
            document.getElementById("s-dispatch-return-to-phone").innerText = dispatch[0].phone;
            document.getElementById("s-dispatch-location").innerText = dispatch[0].location;


            returns.forEach(element => {
                let tRow = document.createElement('tr');

                tRow.setAttribute("class", "print-disptach")

                //create first row for return cylinder column
                let returnCylcode = document.createElement('td')
                returnCylcode.appendChild(document.createTextNode(element.cylcode));
                tRow.appendChild(returnCylcode);
                returnCylcode.setAttribute("style", "border: 2px solid black !important;")


                //create second row for return cylinder column
                let returnSize = document.createElement('td')
                returnSize.appendChild(document.createTextNode(element.cylinder_size));
                tRow.appendChild(returnSize);
                returnSize.setAttribute("style", "border: 2px solid black !important;")


                //create third row for return cylinder column
                let returnEmpty = document.createElement('td')
                let empty;
                if (element.empty_full) {
                    empty = element.empty_full;
                } else {
                    empty = "";
                }
                returnEmpty.appendChild(document.createTextNode(empty));
                tRow.appendChild(returnEmpty);
                returnEmpty.setAttribute("style", "border: 2px solid black !important;")


                //create fourth row for return cylinder column
                // let returnRemarks = document.createElement('td')
                // returnRemarks.appendChild(document.createTextNode(element.createuser));
                // tRow.appendChild(returnRemarks);

                returnCylinderTableBody.appendChild(tRow);
            });

            dispatch.forEach(element => {
                let dRow = document.createElement('tr');

                dRow.setAttribute("class", "print-disptach")



                //CREATES TABLE DATA FOR DISPATCH

                let dispatchRowNo = document.createElement('td')
                // dispatchCylcode.appendChild(document.createTextNode(null));
                dRow.appendChild(dispatchRowNo);
                dispatchRowNo.setAttribute("style", "border: 2px solid black !important;")

                //create first row for dispatch cylinder column
                let dispatchCylcode = document.createElement('td')
                dispatchCylcode.appendChild(document.createTextNode(element.cylcode));
                dRow.appendChild(dispatchCylcode);
                dispatchCylcode.setAttribute("style", "border: 2px solid black !important;")


                //create second row for dispatch cylinder column
                let dispatchSize = document.createElement('td')
                dispatchSize.appendChild(document.createTextNode(element.cylinder_size));
                dRow.appendChild(dispatchSize);
                dispatchSize.setAttribute("style", "border: 2px solid black !important;")


                let dispatchPrize = document.createElement('td')
                let prize;
                if (element.amount_paid) {
                    prize = element.amount_paid;
                } else {
                    prize = "N/A";
                }

                dispatchPrize.appendChild(document.createTextNode(prize));
                dRow.appendChild(dispatchPrize);
                dispatchPrize.setAttribute("style", "border: 2px solid black !important;")


                //create third row for dispatch cylinder column
                let dispatchName = document.createElement('td')
                dispatchName.appendChild(document.createTextNode(element.fname + ' ' + element
                    .mname + ' ' + element.lname));
                dRow.appendChild(dispatchName);
                dispatchName.setAttribute("style", "border: 2px solid black !important;")


                //create third row for dispatch cylinder column
                let dispatchPhone = document.createElement('td')
                dispatchPhone.appendChild(document.createTextNode(element.customerPhone));
                dRow.appendChild(dispatchPhone);
                dispatchPhone.setAttribute("style", "border: 2px solid black !important;")



                dispatchCylinderTableBody.appendChild(dRow);

            });

            $("select").val(null).trigger('change');
            searchForm.reset();
            $("#search-modal").modal('hide');
            $("#dispatch-modal").modal('show');


        }).catch(function (err) {
            if (err) {
                Swal.fire({
                    text: "Search failed...could not find customer"
                });
            }
        })
    });

</script>
