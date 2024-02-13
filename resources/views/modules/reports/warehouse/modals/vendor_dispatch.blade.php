<div class="modal fade" id="vendor-dispatch-modal" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" style="min-width: 75%" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Daily Dispatch</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
                <button id="print-button" class="btn btn-primary btn-md" type="button"
                    onclick="window.print()">Print</button>
            </div>
            <div class="modal-body">
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
                            overflow: visible;
                        }

                        .main-page * {
                            display: none;
                        }

                        #print-button {
                            display: none;
                        }

                        .modal {
                            position: absolute;
                            left: 0;
                            top: 0;
                            margin: 0;
                            padding: 0;
                            min-height: 550px;
                            visibility: visible;
                            overflow: visible !important;
                            /* Remove scrollbar for printing. */
                        }

                        .modal-dialog {
                            visibility: visible !important;
                            overflow: visible !important;
                            /* Remove scrollbar for printing. */
                        }

                        #printSection,
                        #printSection * {
                            visibility: visible;
                        }

                        #printSection {
                            position: absolute;
                            left: 0;
                            top: 0;
                        }
                    }


                    .footer {
                        text-align: right;
                        font-weight: bold;
                    }

                </style>
                <div class="row">
                    <div class="col text-center">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col">Date & Time</th>
                                    <th scope="col" id="dispatch-date">{{date("Y-m-d, H:i:s A")}} </th>
                                    <th scope="col ">LCX No.</th>
                                    <th scope="col" id="dispatch-location"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th scope="col">Name Operator</th>
                                    <th scope="col" id="dispatch-return-to"></th>
                                    <th scope="col">TEL</th>
                                    <th scope="col" id="dispatch-return-to-phone"></th>
                                </tr>
                            </tbody>
                        </table>


                        <br>
                        <div class="row">
                            <div class="col-6" style="border-right: 6px solid rgba(1, 4, 7, 0.753);">
                                <h6 class="font-weight-bold"> <b>DISPATCHED CYLINDERS</b> </h6>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            {{-- <th scope="col">#</th> --}}
                                            <th scope="col">CYLCODE</th>
                                            <th scope="col">SIZE</th>
                                            <th scope="col">PRICE</th>
                                            <th scope="col">NAME</th>
                                            <th scope="col">TEL</th>
                                        </tr>
                                    </thead>
                                    <tbody id="print-dispatch-cylinder-id">

                                    </tbody>
                                </table>
                            </div>

                            <div class="col-6">
                                <h6 class="font-weight-bold"><b>RETURNED CYLINDERS</b></h6>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            {{-- <th scope="col">#</th> --}}
                                            <th scope="col">CYLCODE</th>
                                            <th scope="col">SIZE</th>
                                            <th scope="col">EMP/FULL</th>
                                            <th scope="col">REMARKS</th>
                                        </tr>
                                    </thead>
                                    <tbody id="print-return-cylinder-id">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <br>
                        <br>
                        <p class="px-4">
                            <em> Kindly ensure that every portion is signed for both Dispatch and Returns</em>
                        </p>
                        <div class="row">
                            <div class="col border-right">
                                <p><u>DISPATCHED</u></p>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th scope="col"></th>
                                            <th scope="col">Name</th>
                                            <th scope="col">Sign</th>
                                            <th scope="col">Time</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th scope="col">LCX Operator</th>
                                            <th scope="col"></th>
                                            <th scope="col"></th>
                                            <th scope="col"></th>
                                        </tr>
                                        <tr>
                                            <th scope="col">Dispatched by</th>
                                            <th scope="col"></th>
                                            <th scope="col"></th>
                                            <th scope="col"></th>
                                        </tr>
                                        <tr>
                                            <th scope="col">Office Release</th>
                                            <th scope="col"></th>
                                            <th scope="col"></th>
                                            <th scope="col"></th>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col">
                                <p><u>RETURN CYLINDERS</u></p>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th scope="col"></th>
                                            <th scope="col">Name</th>
                                            <th scope="col">Sign</th>
                                            <th scope="col">Time</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th scope="col">Check-in by</th>
                                            <th scope="col"></th>
                                            <th scope="col"></th>
                                            <th scope="col"></th>
                                        </tr>
                                        <tr>
                                            <th scope="col">Verified by</th>
                                            <th scope="col"></th>
                                            <th scope="col"></th>
                                            <th scope="col"></th>
                                        </tr>
                                        <tr>
                                            <th scope="col">Office Confirm</th>
                                            <th scope="col"></th>
                                            <th scope="col"></th>
                                            <th scope="col"></th>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
