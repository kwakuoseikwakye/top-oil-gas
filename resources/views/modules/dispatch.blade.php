<div class="modal fade" id="dispatch-modal" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" style="min-width: 95%" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Dispatch & Returns Report</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
                <button id="print-button" class="btn btn-primary btn-md" type="button"
                    onclick="window.print()">Print</button>
                {{-- <button id="print-button" class="btn btn-primary btn-md" type="button"
                    onclick="fnExcelReport()">Excel</button> --}}
            </div>
            <div class="modal-body">
                <style>
                    /* .printable { display: none; } */

                    @media screen {
                        #printSection {
                            display: none;
                        }

                        .print-dispatch {
                            border-collapse: collapse;
                            border: 2px solid black !important;
                            font-size: 13px;
                        }

                        .tbl-head {
                            border: 2px solid black !important;
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


                        .tbl-head {
                            border: 2px solid black !important;
                        }
                    }

                    .print-dispatch {
                        border-collapse: collapse;
                        border: 2px solid black !important;
                        font-size: 13px;
                    }

                    .tbl-head {
                        border: 2px solid black !important;
                    }


                </style>
                <div class="row">
                    <div class="col-12" id="export-excel">
                        <div class="row">
                            <div class="col">
                                <table class="table table-bordered print-dispatch">
                                    <thead>
                                        <tr>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;"  class="tbl-head">Date & Time</th>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" class="tbl-head" id="dispatch-date">{{date("Y-m-d, H:i:s A")}} </th>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" class="tbl-head">LCX No.</th>
                                            <th class="tbl-head" id="s-dispatch-location"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" class="tbl-head">Name Operator</th>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" class="tbl-head" id="s-dispatch-return-to"></th>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" class="tbl-head">TEL</th>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" class="tbl-head" id="s-dispatch-return-to-phone"></th>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>


                        <br>
                        <div class="row">
                            <div class="col-8">
                                <h6 class="font-weight-bold"> <b>DISPATCHED CYLINDERS</b> </h6>
                                <table class="table table-bordered print-dispatch">
                                    <thead>
                                        <tr>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" class="tbl-head">SN</th>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" class="tbl-head">BARCODE</th>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" class="tbl-head">SIZE</th>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" class="tbl-head">PRICE</th>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" class="tbl-head">NAME</th>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" class="tbl-head" style="">TEL</th>
                                        </tr>
                                    </thead>
                                    <tbody id="dispatch-cylinder-id">

                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;"></td>
                                            <td style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" class="font-weight-bold">Total KG</td>
                                            <td style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" class="tbl-head"></td>
                                            <td style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" class="tbl-head"></td>
                                        </tr>
                                        <tr>
                                            <td style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" class="tbl-head"></td>
                                            <td style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" class="tbl-head font-weight-bold">Total Sales</td>
                                            <td style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" class="tbl-head"></td>
                                            <td style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" class="tbl-head"></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <div class="col-4">
                                <h6 class="font-weight-bold"><b>RETURNED CYLINDERS</b></h6>
                                <table class="table table-bordered print-dispatch">
                                    <thead>
                                        <tr>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" class="tbl-head">BARCODE</th>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" class="tbl-head">SIZE</th>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" class="tbl-head">EMP/FULL</th>
                                        </tr>
                                    </thead>
                                    <tbody id="return-cylinder-id">

                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" class="tbl-head font-weight-bold">Cash</td>
                                            <td style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" class="tbl-head font-weight-bold">Momo</td>
                                            <td style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" class="tbl-head"></td>
                                        </tr>
                                        <tr>
                                            <td style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" class="tbl-head"></td>
                                            <td style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" class="tbl-head"></td>
                                            <td style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" class="tbl-head"></td>
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
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" class="tbl-head"></th>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" class="tbl-head">Name</th>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" class="tbl-head">Sign</th>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" class="tbl-head">Time</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" class="tbl-head">LCX Operator</th>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" class="tbl-head"></th>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" class="tbl-head"></th>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" class="tbl-head"></th>
                                        </tr>
                                        <tr>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" class="tbl-head">Dispatched by</th>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" class="tbl-head"></th>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" class="tbl-head"></th>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" class="tbl-head"></th>
                                        </tr>
                                        <tr>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" class="tbl-head">Office Release</th>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" class="tbl-head"></th>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" class="tbl-head"></th>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" class="tbl-head"></th>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col">
                                <p><u>RETURN CYLINDERS</u></p>
                                <table class="table table-bordered print-dispatch">
                                    <thead>
                                        <tr>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" class="tbl-head"></th>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" class="tbl-head">Name</th>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" class="tbl-head">Sign</th>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" class="tbl-head">Time</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" class="tbl-head">Check-in by</th>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" class="tbl-head"></th>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" class="tbl-head"></th>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" class="tbl-head"></th>
                                        </tr>
                                        <tr>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" class="tbl-head">Verified by</th>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" class="tbl-head"></th>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" class="tbl-head"></th>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" class="tbl-head"></th>
                                        </tr>
                                        <tr>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" class="tbl-head">Office Confirm</th>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" class="tbl-head"></th>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" class="tbl-head"></th>
                                            <th style="border-right: 2px solid black !important; border-bottom: 2px solid black !important;" class="tbl-head"></th>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- <div class="modal-footer">
                <button id="print-button" class="btn btn-primary btn-md" type="button"
                    onclick="window.print()">Print</button>

            </div> --}}
        </div>
    </div>
</div>
