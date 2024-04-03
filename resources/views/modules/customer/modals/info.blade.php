<div class="modal fade" id="info-customer-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">CUSTOMER DETAILS</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col text-center">
                        <div>
                            <img height="150" width="150" src="{{ asset('avatar.png') }}"
                                alt="customer's photo" class="rounded-circle" id="info-customer-image">
                        </div>

                        <div class="h5" id="info-customer-name"></div>
                        <br>
                        <div class="row">
                            <div style="border-right: 6px solid rgba(5, 131, 248, 0.753);" class="col-md-6 col-xs-12">
                                <h6 class="text-left text-primary"><u> BIO DETAILS</u></h6>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Customer Number:
                                        <span id="info-customer-number"></span>
                                    </li>
                                </ul>
                                <br>
                                <h6 class="text-left text-primary"><u>CONTACT</u></h6>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Phone:
                                        <span id="info-customer-phone"></span>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6 col-xs-12">
                                <h6 class="text-left text-primary"><u>IDENTIFICATION</u></h6>
                                <ul class="list-group list-group-flush">
                                   <li class="list-group-item d-flex justify-content-between align-items-center">
                                       ID Type:
                                        <span id="info-customer-id-type"></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        ID Number:
                                        <span id="info-customer-id-number"></span>
                                    </li>
                                    {{-- <li class="list-group-item d-flex justify-content-between align-items-center">
                                       <img alt="ID's photo" class="rounded-circle" id="info-customer-id-image">             
                                    </li> --}}
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>