<div class="modal fade" id="search-customer-modal" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop"
    aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Find Customer Cylinder</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="search-customer-form">
                    @csrf

                    <div class="row mt-3">
                        <div class="col">
                            <label for="">Search Keyword<span class="text-danger">*</span></label>
                            <input type="text" name="keyword"
                                placeholder="Eg. Enter barcode, customer #, customer phone number, cylinder #."
                                class="form-control form-control-sm" id="search-customer-keyword" autofocus required>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col">
                            <label for="">Select Type<span class="text-danger">*</span></label>
                            <select name="searchType" id="select-typ" class="form-control select2" required>
                                <option value="4">Barcode</option>
                                <option value="1">Cylinder Number</option>
                                <option value="3">Customer Number</option>
                                <option value="0">Customer Phone</option>
                                <option value="2">Customer ID number</option>
                            </select>
                        </div>
                    </div>


                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Close</button>
                <button type="submit" form="search-customer-form"
                    class="btn btn-primary font-weight-bold">Search</button>
            </div>
        </div>
    </div>
</div>
<script>
    $('#search-customer-modal').on('shown.bs.modal', function () {
        $('#search-customer-keyword').focus();
    })
    var searchCustomerForm = document.getElementById("search-customer-form");

    $(searchCustomerForm).submit(function (e) {
        e.preventDefault();

        // var formdata = new FormData(searchCustomerForm)
        let type = document.forms['search-customer-form'].elements['searchType'].value;
        let keyword = document.forms['search-customer-form'].elements['keyword'].value;

        Swal.fire({
            text: "Searching...",
            showConfirmButton: false,
            allowEscapeKey: false,
            allowOutsideClick: false
        });
        fetch(`${APP_URL}/api/search_customer/${type}/${keyword}`, {
            method: "GET",
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
                text: "Customer found successfully",
                icon: "success"
            });
            console.log(data.data);
            
            let dispatch = data.data;
            document.getElementById("search-customer-code").innerText = dispatch[0].custno;
            document.getElementById("search-customer-name").innerText = dispatch[0].customer.fname + ' ' +
                dispatch[0].customer.mname + ' ' + dispatch[0].customer.lname;
            document.getElementById("search-customer-phone").innerText = dispatch[0].customer.phone;
            document.getElementById("search-customer-email").innerText = dispatch[0].customer.email;
            document.getElementById("search-customer-gender").innerText = dispatch[0].customer.gender;
            document.getElementById("search-customer-size").innerText = dispatch[0].cylinder.size;
            document.getElementById("search-customer-weight").innerText = dispatch[0].cylinder.weight;
            document.getElementById("search-customer-barcode").innerText = dispatch[0].barcode;
            document.getElementById("search-customer-cylcode").innerText = dispatch[0].cylcode;
            document.getElementById("search-customer-amount").innerText = dispatch[0].cylinder
                .initial_amount;

            $("select").val(null).trigger('change');
            searchCustomerForm.reset();
            $("#search-customer-modal").modal('hide');
            $("#search-customer-info-modal").modal('show');
            $('#search-customer-info-modal').modal({
                backdrop: 'static',
                keyboard: false
            })

        }).catch(function (err) {
            if (err) {
                Swal.fire({
                    text: "Search failed...could not find customer"
                });
            }
        })
    });

</script>
