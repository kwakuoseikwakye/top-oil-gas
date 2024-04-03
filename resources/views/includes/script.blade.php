@section('script')
    @stack('js-scripts')

@show
{{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script> --}}

<!--begin::Global Config(global config for global JS scripts)-->
<script>
    var KTAppSettings = {
        "breakpoints": {
            "sm": 576,
            "md": 768,
            "lg": 992,
            "xl": 1200,
            "xxl": 1400
        },
        "colors": {
            "theme": {
                "base": {
                    "white": "#ffffff",
                    "primary": "#3699FF",
                    "secondary": "#E5EAEE",
                    "success": "#1BC5BD",
                    "info": "#8950FC",
                    "warning": "#FFA800",
                    "danger": "#F64E60",
                    "light": "#E4E6EF",
                    "dark": "#181C32"
                },
                "light": {
                    "white": "#ffffff",
                    "primary": "#E1F0FF",
                    "secondary": "#EBEDF3",
                    "success": "#C9F7F5",
                    "info": "#EEE5FF",
                    "warning": "#FFF4DE",
                    "danger": "#FFE2E5",
                    "light": "#F3F6F9",
                    "dark": "#D6D6E0"
                },
                "inverse": {
                    "white": "#ffffff",
                    "primary": "#ffffff",
                    "secondary": "#3F4254",
                    "success": "#ffffff",
                    "info": "#ffffff",
                    "warning": "#ffffff",
                    "danger": "#ffffff",
                    "light": "#464E5F",
                    "dark": "#ffffff"
                }
            },
            "gray": {
                "gray-100": "#F3F6F9",
                "gray-200": "#EBEDF3",
                "gray-300": "#E4E6EF",
                "gray-400": "#D1D3E0",
                "gray-500": "#B5B5C3",
                "gray-600": "#7E8299",
                "gray-700": "#5E6278",
                "gray-800": "#3F4254",
                "gray-900": "#181C32"
            }
        },
        "font-family": "Poppins"
    };
</script>

<!--end::Global Config-->
<!--begin::Global Theme Bundle(used by all pages)-->
<script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
<script src="{{ asset('assets/plugins/custom/prismjs/prismjs.bundle.js') }}"></script>
<script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
<!--end::Global Theme Bundle-->
<!--begin::Page Vendors(used by this page)-->
{{-- <script src="{{asset('assets/plugins/custom/fullcalendar/fullcalendar.bundle.js')}}"></script> --}}
<!--end::Page Vendors-->
<!--begin::Page Scripts(used by this page)-->
<script src="{{ asset('assets/js/pages/widgets.js') }}"></script>
<script src="{{ asset('bootstrap-select.min.js') }}"></script>
<script src="{{ asset('js/toastr.min.js') }}"></script>
<!-- select2 -->
{{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0/dist/js/bootstrap-select.min.js"></script> --}}
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta/js/bootstrap-select.min.js" integrity="sha512-I0sRMhP0loaoXaytYuOHHU3pGmyQklf5irZZ8cSaIPi9ETq5qvfcDAiBJ4vqpaq8xeUe7ZVwYM5xqQlxYDK3Uw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> --}}
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.9/js/select2.min.js" integrity="sha512-9p/L4acAjbjIaaGXmZf0Q2bV42HetlCLbv8EP0z3rLbQED2TAFUlDvAezy7kumYqg5T8jHtDdlm1fgIsr5QzKg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> --}}
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js" integrity="sha512-bj8HE1pKwchoYNizhD57Vl6B9ExS25Hw21WxoQEzGapNNjLZ0+kgRMEn9KSCD+igbE9+/dJO7x6ZhLrdaQ5P3g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> --}}
{{-- <script scr="{{ asset('assets/select2.min.js') }}"></script> --}}
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
    // Initialize Bootstrap Select
    $('.selectpicker').selectpicker();
    const enableNotificationsBtn = document.getElementById("enable-notifications-btn");

    function checkNotificationPermission() {
        if (Notification.permission === "granted") {
            enableNotificationsBtn.style.display = "none";
        } else {
            enableNotificationsBtn.style.display = "block";
        }
    }

    enableNotificationsBtn.addEventListener("click", function() {
        Notification.requestPermission().then(function(permission) {
            checkNotificationPermission();
        });
    });

    checkNotificationPermission();
    toastr.options = {
        "closeButton": false,
        "debug": false,
        "newestOnTop": false,
        "progressBar": false,
        "positionClass": "toast-top-full-width",
        "preventDuplicates": false,
        "onclick": function() {
            let URLParts = location.href.split("/");
            let currentRoute = URLParts[URLParts.length - 1];
            if (currentRoute == "orders") {
                // Do nothing if we are already in the orders module
                return;
            }

            // Go to the orders module if we are not there
            window.location.href = `${APP_URL}/orders`;
        },
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "15000",
        "extendedTimeOut": "2000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    }

    let notificatonSound = new Audio("{{ asset('pulse_notification.mp3') }}");
    Echo.channel('orders').listen('OrderCreated', (e) => {
        console.log(e.orderid);
        const notificationMsg = `New Order created with ID ${e.orderid}`;
        toastr.options.positionClass = "toast-top-full-width";
        toastr.warning(notificationMsg);

        let URLPart = location.href.split("/");
        let currentRoute = URLPart[URLPart.length - 1];
        if (currentRoute == "orders") {
            orderTable.ajax.reload(false, null);
        }

        if (Notification.permission === "granted") {
            let n = new Notification("Top Oil", {
                body: notificationMsg,
                // icon: `${APP_URL}/favicon.ico`,
            });
        }

        notificatonSound.play();
        // Use the received order data to update the DataTables table
        // $('#order-table').DataTable().ajax.reload(false, null);
    });

    //Payment made notification
    Echo.channel('payment-made').listen('PaymentMade', (e) => {
        console.log(e.orderid);
        const notificationMsg = `New Payment made with ID ${e.orderid}`;
        toastr.options.positionClass = "toast-top-full-width";
        toastr.success(notificationMsg);

        let URLPart = location.href.split("/");
        let currentRoute = URLPart[URLPart.length - 1];
        if (currentRoute == "orders") {
            orderTable.ajax.reload(false, null);
        }

        if (Notification.permission === "granted") {
            let n = new Notification("Top Oil", {
                body: notificationMsg,
            });
        }

        notificatonSound.play();
    });
</script>
@include('includes.change_password')
{{-- @include('modules.dispatch')
@include('modules.search')
@include('modules.customer_cylinder')
@include('modules.search_customer') --}}
<!--end::Page Scripts-->
