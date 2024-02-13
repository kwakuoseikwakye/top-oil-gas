@extends('layout.main')
@section('pageName', 'Withdrawals')
@section('page-content')
<style>
    .wishwave-request-status {
        text-transform: uppercase;
        font-weight: bold;
        white-space: nowrap;
    }

    .wishwave-request-declined {
        background-color: #a10705;
        color: #fff;
    }

    .wishwave-request-paid {
        background-color: #d1ff82;
    }

    .wishwave-request-pending-approval {
        background-color: orange;
        color: #fff;
    }

    .wishwave-request-fulfilled {
        background-color: #0f0;
        color: #000;
    }

    .wishwave-request-not-paid {
        background-color: red;
        color: #fff;
    }

    .wishwave-request-unfulfilled {
        background-color: #57392d;
        color: #fff;
    }

</style>
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">

    <!--begin::Entry-->
    <div class="d-flex flex-column-fluid">
        <!--begin::Container-->
        <div class="container">
            <div class="card card-custom">
                <div class="card-header flex-wrap border-0 pt-6 pb-0">
                    <div class="card-title">
                        <h3 class="card-label">Withdrawals
                            {{-- <div class="text-muted pt-2 font-size-sm">Datatable initialized from HTML table</div> --}}
                        </h3>
                    </div>
                    <div class="card-toolbar">

                        <!--begin::Button-->
                        {{-- <a data-toggle="modal" data-target="#add-request-modal"
                            class="btn btn-primary font-weight-bolder">
                            <i class="fas fa-Requests">
                            </i>Request For a Staff</a> --}}
                        <!--end::Button-->
                    </div>
                </div>

                <div class="card-body">

                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home">
                                <span class="nav-text">Today's Payments</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="req-tab" data-toggle="tab" href="#req" aria-controls="req">
                                <span class="nav-text">Today's Requests</span>
                            </a>
                        <li class="nav-item">
                            <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile"
                                aria-controls="profile">
                                <span class="nav-text">All Requests</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="pay-tab" data-toggle="tab" href="#pay"
                                aria-controls="pay">
                                <span class="nav-text">All Payments</span>
                            </a>
                        </li>

                    </ul>

                    <div class="tab-content mt-5" id="myTabContent">
                        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                            <div class="table-responsive">
                                <table width="100%"
                                    class="table-sm table-bordered datatable-bordered datatable-head-custom"
                                    id="today-with-table">
                                    <thead>
                                        <tr>
                                            <th>Staff</th>
                                            <th>Payment Date</th>
                                            <th>Paid Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!--Data is fetched here using ajax-->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane fade " id="req" role="tabpanel" aria-labelledby="req-tab">
                            <div class="table-responsive">
                                <table width="100%"
                                    class="table-sm table-bordered datatable-bordered datatable-head-custom"
                                    id="today-req-table">
                                    <thead>
                                        <tr>
                                            <th>Staff</th>
                                            <th>Payment Date</th>
                                            <th>Requested Amount</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!--Data is fetched here using ajax-->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                            <form id="filter-request">
                                <div class="row mb-5">
                                    <div class="col">
                                        <label for="">From <span class="text-danger">*</span></label>
                                        <input type="date" name="from" class="form-control form-control-sm" required>
                                    </div>
                                    <div class="col">
                                        <label for="">To <span class="text-danger">*</span></label>
                                        <input type="date" name="to" class="form-control form-control-sm" required>
                                    </div>
                                    <div class="col">
                                        <label for="">Staff</label>
                                        <select name="staff" class="form-control select2">
                                            <option value="">--select--</option>
                                            @foreach ($staff as $item)
                                            <option value="{{$item->code}}">{{$item->fname}} {{$item->mname}} {{$item->lname}}</option>

                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="input-group-prepend px-0 pt-0 mt-5">
                                        <button type="submit" class="btn btn-outline-success btn-sm"
                                            form="filter-request" name='submit'><i
                                                class="fas fa-filter ml-1"></i>Filter</button>
                                    </div>
                                </div>

                            </form>
                            <hr>
                            <div class="table-responsive">
                                <table width="100%"
                                    class="table-sm table-bordered datatable-bordered datatable-head-custom"
                                    id="request-table">
                                    <thead>
                                        <tr>
                                            <th>Staff</th>
                                            <th>Date Requested</th>
                                            <th>Requested Amount</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!--Data is fetched here using ajax-->
                                    </tbody>
                                </table>
                            </div>

                        </div>

                        <div class="tab-pane fade" id="pay" role="tabpanel" aria-labelledby="pay-tab">
                            <form id="payment-filter">
                                <div class="row mb-5">
                                    <div class="col">
                                        <label for="">From <span class="text-danger">*</span></label>
                                        <input type="date" name="from" class="form-control form-control-sm" required>
                                    </div>
                                    <div class="col">
                                        <label for="">To <span class="text-danger">*</span></label>
                                        <input type="date" name="to" class="form-control form-control-sm" required>
                                    </div>
                                    <div class="col">
                                        <label for="">Staff</label>
                                        <select name="staff" class="form-control select2">
                                            <option value="">--select--</option>
                                            @foreach ($staff as $item)
                                            <option value="{{$item->code}}">{{$item->fname}} {{$item->mname}} {{$item->lname}}</option>

                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="input-group-prepend px-0 pt-0 mt-5">
                                        <button type="submit" class="btn btn-outline-success btn-sm"
                                            form="payment-filter" name='submit'><i
                                                class="fas fa-filter ml-1"></i>Filter</button>
                                    </div>
                                </div>

                            </form>
                            <hr>
                            <div class="table-responsive">
                                <table width="100%"
                                    class="table-sm table-bordered datatable-bordered datatable-head-custom"
                                    id="payment-table">
                                    <thead>
                                        <tr>
                                            <th>Staff</th>
                                            <th>Payment Date</th>
                                            <th>Paid Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!--Data is fetched here using ajax-->
                                    </tbody>
                                </table>
                            </div>

                        </div>

                    </div>

                </div>
            </div>
            <!--end::Container-->
        </div>
        <!--end::Entry-->
    </div>
</div>
{{-- @include('modules.requests.modals.add') --}}
@endsection
@push('js-scripts')

<script src="{{ mix('/js/modules/withdrawals/index.js') }}"></script>
@endpush
