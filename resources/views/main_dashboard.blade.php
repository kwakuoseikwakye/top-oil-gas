@extends('layout.main')
@section('pageName', 'Dashboard')
@section('page-content')

<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
   
    <!--begin::Entry-->
    <!--begin::Container-->
    <div class="container">
        <!--begin::Dashboard-->
        <!--begin::Row-->
        <div class="row">
            {{-- diary--}}
            <div class="col-xl-4 col-md-6 col-12">
                <div class="card shadow">
                    <a href="{{ config("app.url") }}/customers">
                        <div class="card-body">
                            <div class="dash-widget-header">
                                <span class="dash-widget-icon text-info border-info">
                                    <i class="fas fa-users"></i>
                                </span>
                                <div class="dash-count text-right">
                                    <h3 id="today-booking">{{$customers}}</h3>
                                </div>
                            </div>
                            <div class="dash-widget">

                                <h6 class="text-dark">All Customers</h6>
                                <div class="progress progress-sm">
                                    <div class="progress-bar bg-info w-50"></div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            {{-- payments today --}}
            <div class="col-xl-4 col-md-6 col-12">
                <div class="card shadow">
                    <a href="{{ config("app.url") }}/customers">
                        <div class="card-body">
                            <div class="dash-widget-header">
                                <span class="dash-widget-icon text-success">
                                    <i class="fa fa-user-alt"></i>
                                </span>
                                <div class="dash-count text-right">
                                    <h3 id="today-payment">{{$cus}}</h3>
                                </div>
                            </div>
                            <div class="dash-widget">

                                <h6 class="text-dark">Customers This Week</h6>
                                <div class="progress progress-sm">
                                    <div class="progress-bar bg-success w-50"></div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-xl-4 col-md-6 col-12">
                <div class="card shadow">
                    <a href="{{ config("app.url") }}/cylinders">
                        <div class="card-body">
                            <div class="dash-widget-header">
                                <span class="dash-widget-icon text-success">
                                    <i class="fa fa-user"></i>
                                </span>
                                <div class="dash-count text-right">
                                    <h3 id="today-payment">{{$cylinders}}</h3>
                                </div>
                            </div>
                            <div class="dash-widget">

                                <h6 class="text-dark">Total Cylinders</h6>
                                <div class="progress progress-sm">
                                    <div class="progress-bar bg-primary w-50"></div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            {{-- diary--}}
            <div class="col-xl-4 col-md-6 col-12">
                <div class="card shadow">
                    <a href="{{ config("app.url") }}/employees">
                        <div class="card-body">
                            <div class="dash-widget-header">
                                <span class="dash-widget-icon text-info border-info">
                                    <i class="fas fa-bookmark"></i>
                                </span>
                                <span class="dash-count text-right">
                                    <h3 id="today-booking">{{$staff}}</h3>
                                </span>
                            </div>
                            <div class="dash-widget">

                                <h6 class="text-dark">Total Staff</h6>
                                <div class="progress progress-sm">
                                    <div class="progress-bar bg-danger w-50"></div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            {{-- payments today --}}
            <div class="col-xl-4 col-md-6 col-12">
                <div class="card shadow">
                    <a href="{{ config("app.url") }}/vendors">
                        <div class="card-body">
                            <div class="dash-widget-header">
                                <span class="dash-widget-icon text-success">
                                    <i class="fa fa-credit-card"></i>
                                </span>
                                <div class="dash-count text-right">
                                    <h3 id="today-payment">{{$vendors}}</h3>
                                </div>
                            </div>
                            <div class="dash-widget">

                                <h6 class="text-dark">Total Vendors</h6>
                                <div class="progress progress-sm">
                                    <div class="progress-bar bg-warning w-50"></div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            {{-- Doctors --}}
            <div class="col-xl-4 col-md-6 col-12">
                <div class="card shadow">
                    <a href="{{ config("app.url") }}/payment">
                        <div class="card-body">
                            <div class="dash-widget-header">
                                <span class="dash-widget-icon text-success">
                                    <i class="fa fa-credit-card"></i>
                                </span>
                                <div class="dash-count text-right">
                                    <h3 id="today-payment">GHS {{$paid}}</h3>
                                </div>
                            </div>
                            <div class="dash-widget">

                                <h6 class="text-dark">Total Payments</h6>
                                <div class="progress progress-sm">
                                    <div class="progress-bar bg-success w-50"></div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

        </div>
        <div class="row mt-3">
            <div class="col-sm-6 col-sm-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Cylinders (Petrocel & Customers)</h4>
                    </div>
                    <div class="card-body shadow-lg">
                        <canvas id="cylinder-chart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-sm-6">
                <div class="card">
                    <div class="card-header">
                        <h4>Warehouse & Carrier Users</h4>
                    </div>
                    <div class="card-body shadow-lg">
                        <canvas id="myDoughnutChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-sm-12 col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Exchange Graph</h4>
                    </div>
                    <div class="card-body shadow-lg">
                        <canvas id="exchange-chart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
       

        {{-- <div class="row mt-3">
            <div class="col-sm-12 col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Transaction Graph</h4>
                    </div>
                    <div class="card-body shadow-lg">
                        <canvas id="staff-paid-chart"></canvas>
                    </div>
                </div>
            </div>
        </div> --}}
        <!--end::Row-->
        <!--begin::Row-->

        <!--end::Row-->
        <!--end::Dashboard-->
    </div>
    <!--end::Container-->
    <!--end::Entry-->
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {

        let male = "17";
        let female = "8";
        let ptc = document.getElementById('myDoughnutChart');
        var myDoughnutChart = new Chart(ptc, {
            type: 'doughnut',
            data: {
                labels: ['Warehouse', 'Carrier'],
                datasets: [{
                    data: [male, female],
                    backgroundColor: [
                        'rgb(255, 99, 132)',
                        'rgb(54, 162, 235)',
                        'rgb(255, 205, 86)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                    ],
                }],
            }
        });
    });



    // student staff stat
    document.addEventListener("DOMContentLoaded", () => {

        let student = "{{$petrocellCyl}}";
        let staff = "{{$customerCyl}}";
        let ptc = document.getElementById('cylinder-chart');
        var myDoughnutChart = new Chart(ptc, {
            type: 'doughnut',
            data: {
                labels: ['Petrocel', 'Customer'],
                datasets: [{
                    data: [student, staff],
                    backgroundColor: [
                        'rgb(255, 99, 132)',
                        'rgb(54, 162, 235)',
                        'rgb(255, 205, 86)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                    ],
                }],
            }
        });
    });

    document.addEventListener("DOMContentLoaded", () => {

        let ptc = document.getElementById('exchange-chart');
        var myDoughnutChart = new Chart(ptc, {
            type: 'line',
            data: {
                labels: ["January", "February", "March", "April", "May", "June", "July", "August",
                    "September", "October", "November", "December"
                ],
                datasets: [{
                        label: 'Exchange Profit',
                        data: [65, 59, 45, 5, 6, 32, 43, 57, 89, 99, 56, 24],
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(255, 159, 64, 0.2)',
                        ],
                        borderColor: [
                            'rgb(255, 99, 132)',
                            'rgb(54, 162, 235)',
                        ],
                    },
                    {
                        label: 'Exchange Loss',
                        data: [15, 49, 75, 25, 16, 12, 53, 67, 69, 49, 56, 24],
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(255, 159, 64, 0.2)',
                        ],
                        borderColor: [
                            'rgb(75, 192, 192)',
                            'rgb(54, 162, 235)',
                        ],
                    }
                ]

            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Exchange Chart'
                    }
                }
            },
        });
    });

</script>
@endsection

@push('js-scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.2.0/chart.min.js"
    integrity="sha512-VMsZqo0ar06BMtg0tPsdgRADvl0kDHpTbugCBBrL55KmucH6hP9zWdLIWY//OTfMnzz6xWQRxQqsUFefwHuHyg=="
    crossorigin="anonymous"></script>

@endpush
