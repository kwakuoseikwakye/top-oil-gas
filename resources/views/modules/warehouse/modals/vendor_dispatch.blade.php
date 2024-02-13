<div class="modal fade" id="vendor-dispatch-modal" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" style="min-width: 95%" role="document">
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

                        .print-dispatch {
                            width: 100%;
                            border: 2px solid black !important;
                            border-collapse: collapse;
                            font-size: 21px;
                        }
                    }


                    .footer {
                        text-align: right;
                        font-weight: bold;
                    }

                </style>
                <div class="row">
                    <div class="col text-center">
                        <table class="table table-bordered print-dispatch">
                            <thead>
                                <tr>
                                    <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" scope="col">Date & Time</th>
                                    <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" scope="col" id="dispatch-date">{{date("Y-m-d, H:i:s A")}} </th>
                                    <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" scope="col ">LCX No.</th>
                                    <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" scope="col" id="dispatch-location"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" scope="col">Name Operator</th>
                                    <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" scope="col" id="dispatch-return-to"></th>
                                    <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" scope="col">TEL</th>
                                    <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" scope="col" id="dispatch-return-to-phone"></th>
                                </tr>
                            </tbody>
                        </table>


                        <br>
                        <div class="row">
                            <div class="col-8">
                                <h6 class="font-weight-bold"> <b>DISPATCHED CYLINDERS</b> </h6>
                                <table class="table table-bordered print-dispatch">
                                    <thead>
                                        <tr>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" scope="col">SN</th>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" scope="col">BARCODE</th>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" scope="col">SIZE</th>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" scope="col">PRICE</th>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" scope="col">NAME</th>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" scope="col">TEL</th>
                                        </tr>
                                    </thead>
                                    <tbody id="print-dispatch-cylinder-id">

                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;"></td>
                                            <td style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" class="font-weight-bold">Total KG</td>
                                            <td style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;"></td>
                                            <td style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;"></td>
                                        </tr>
                                        <tr>
                                            <td style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;"></td>
                                            <td style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" class="font-weight-bold">Total Sales</td>
                                            <td style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;"></td>
                                            <td style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;"></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <div class="col-4">
                                <h6 class="font-weight-bold"><b>RETURNED CYLINDERS</b></h6>
                                <table class="table table-bordered print-dispatch">
                                    <thead>
                                        <tr>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" scope="col">BARCODE</th>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" scope="col">SIZE</th>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" scope="col">EMP/FULL</th>
                                        </tr>
                                    </thead>
                                    <tbody id="print-return-cylinder-id">

                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" class="font-weight-bold">Cash</td>
                                            <td style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" class="font-weight-bold">Momo</td>
                                            <td style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;"></td>
                                        </tr>
                                        <tr>
                                            <td style="border-right: 2px solid black !important; border-bottom: 2px solid black !important; height: 45px; overflow:hidden;"></td>
                                            <td style="border-right: 2px solid black !important; border-bottom: 2px solid black !important; height: 45px; overflow:hidden;"></td>
                                            <td style="border-right: 2px solid black !important; border-bottom: 2px solid black !important; height: 45px; overflow:hidden;"></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <br>
                        <br>
                        {{-- <p class="px-4">
                            <em> Kindly ensure that every portion is signed for both Dispatch and Returns</em>
                        </p> --}}
                        <div class="row">
                            <div class="col border-right">
                                <p><u>DISPATCHED</u></p>
                                <table class="table table-bordered print-dispatch">
                                    <thead>
                                        <tr>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" scope="col"></th>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" scope="col">Name</th>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" scope="col">Sign</th>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" scope="col">Time</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" scope="col">LCX Operator</th>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" scope="col"></th>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" scope="col"></th>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" scope="col"></th>
                                        </tr>
                                        <tr>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" scope="col">Dispatched by</th>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" scope="col"></th>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" scope="col"></th>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" scope="col"></th>
                                        </tr>
                                        <tr>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" scope="col">Office Release</th>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" scope="col"></th>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" scope="col"></th>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" scope="col"></th>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col">
                                <p><u>RETURN CYLINDERS</u></p>
                                <table class="table table-bordered print-dispatch">
                                    <thead>
                                        <tr>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" scope="col"></th>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" scope="col">Name</th>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" scope="col">Sign</th>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" scope="col">Time</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" scope="col">Check-in by</th>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" scope="col"></th>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" scope="col"></th>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" scope="col"></th>
                                        </tr>
                                        <tr>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" scope="col">Verified by</th>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" scope="col"></th>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" scope="col"></th>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" scope="col"></th>
                                        </tr>
                                        <tr>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" scope="col">Office Confirm</th>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" scope="col"></th>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" scope="col"></th>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" scope="col"></th>
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
