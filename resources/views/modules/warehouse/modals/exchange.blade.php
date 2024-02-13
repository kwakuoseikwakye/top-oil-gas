<style>
    /* .printable { display: none; } */

    @media screen {
        #printSection {
            display: none;
        }

    }

    @media print {
        body * {
            visibility: hidden;
        }

        .modal-content * {
            visibility: visible;
        }

        .main-page * {
            display: none;
        }

        #print-button {
            display: none;
        }
    }

    .print-dispatch {
        border-collapse: collapse;
        border: 2px solid black !important;
        font-size: 20px;
    }

    #hr {
        border: none;
        height: 1px;
        /* Set the hr color */
        color: rgb(0, 0, 0);
        /* old IE */
        background-color: rgb(0, 0, 0);
        /* Modern Browsers */
    }
    /* fadafful@gmail.com */
</style>
<div class="modal fade" id="exch-modal" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="min-width: 80%" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Exchanged Cylinder</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
                <button id="print-button" class="btn btn-primary btn-md" type="button"
                    onclick="window.print()">Print</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col border-right print-dispatch">
                        <u class="font-weight-bold">EXCHANGED BY OPERATOR</u>
                        <p class="h5 pt-2" id="full-details-vendor-name"></p>
                        <p class="h6" id="full-details-vendor-number"></p>
                    </div>
                    <div class="col print-dispatch">
                        <u class="font-weight-bold">EXCHANGED WITH CUSTOMER</u>
                        <p class="h5 pt-2" id="full-details-customer-name"></p>
                        <p class="h6" id="full-details-customer-number"></p>
                    </div>
                </div>
                <br>
                <br>
                <hr id="hr">
                <div class="row">
                    <div class="col" style="border-right: 4px solid rgba(0, 5, 10, 0.753);">
                        <h6 class="">OLD CYLINDER DETAILS</h6>
                        <div class="mt-3">
                            <img style="height: 250px; width:250px;" src="{{asset('img/cyl.jpg')}}"
                                alt="new cylinder photo" class="rounded-circle" id="full-details-old-cylinder-image">
                        </div>
                        <ul class="list-group list-group-flush print-dispatch">
                            <li
                                class="print-dispatch list-group-item d-flex justify-content-between align-items-center">
                                Barcode:
                                <span id="full-details-old-barcode">N/A</span>
                            </li>
                            <li
                                class="print-dispatch list-group-item d-flex justify-content-between align-items-center">
                                Cylinder Code:
                                <span id="full-details-old-cylinder">N/A</span>
                            </li>
                            <li
                                class="print-dispatch list-group-item d-flex justify-content-between align-items-center">
                                Cylinder Size:
                                <span id="full-details-old-size">N/A</span>
                            </li>
                        </ul>
                    </div>
                    <div class="col">
                        <h6 class="">NEW CYLINDER DETAILS</h6>
                        <div class="mt-3">
                            <img style="height: 250px; width:250px;" src="{{asset('img/cyl.jpg')}}"
                                alt="old cylinder photo" class="rounded-circle" id="full-details-new-cylinder-image">
                        </div>
                        <ul class="list-group list-group-flush print-dispatch">
                            <li
                                class="print-dispatch list-group-item d-flex justify-content-between align-items-center">
                                Barcode:
                                <span id="full-details-new-barcode">N/A</span>
                            </li>
                            <li
                                class="print-dispatch list-group-item d-flex justify-content-between align-items-center">
                                Cylinder Code:
                                <span id="full-details-new-cylinder">N/A</span>
                            </li>
                            <li
                                class="print-dispatch list-group-item d-flex justify-content-between align-items-center">
                                Cylinder Size:
                                <span id="full-details-new-size">N/A</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
