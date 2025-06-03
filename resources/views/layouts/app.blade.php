<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Doğuweb | @yield('title', 'İş Yönetimi Yazılımı')</title>


    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/fontawesome.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/footable.standalone.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/fullcalendar@5.2.0.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-jvectormap-2.0.5.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/jquery.mCustomScrollbar.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/leaflet.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/line-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/magnific-popup.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/MarkerCluster.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/MarkerCluster.Default.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/slick.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/star-rating-svg.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/trumbowyg.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/wickedpicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/img/1735693898_383d13d3f1a902cc3623.png') }}">

    <!-- Icons -->
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">

    

    @stack('styles')
</head>
<body class="layout-dark side-menu">
    <div class="mobile-author-actions"></div>

    <!-- Header -->
    <header class="header-top">
        @include('layouts.header')
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <div class="sidebar-wrapper">
            @include('layouts.sidebar')
        </div>

        <!-- Content -->
        <div class="contents">
            @yield('content')
        </div>

        <!-- Notification Wrapper -->
        <div class="notification-wrapper bottom-right"></div>
    </main>

    <!-- Overlays -->
    <div id="overlayer">
        <div class="loader-overlay">
            <div class="dm-spin-dots spin-lg">
                <span class="spin-dot badge-dot dot-primary"></span>
                <span class="spin-dot badge-dot dot-primary"></span>
                <span class="spin-dot badge-dot dot-primary"></span>
                <span class="spin-dot badge-dot dot-primary"></span>
            </div>
        </div>
    </div>
    <div class="overlay-dark-sidebar"></div>
    <div class="customizer-overlay"></div>

     <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.70/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.70/vfs_fonts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/quagga/0.12.1/quagga.js" integrity="sha512-tkMtg2br+OytX7fpdDoK34wzSUc6JcJa7aOEYUKwlSAAtqTSYVLocV4BpLBIx3RS+h+Ch6W+2lVSzNxQx4yefw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery-ui.js') }}"></script>
    <script src="{{ asset('assets/js/popper.js') }}"></script>
   
    <script src="{{ asset('assets/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/js/accordion.js') }}"></script>
    <script src="{{ asset('assets/js/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/js/autoComplete.js') }}"></script>
    <script src="{{ asset('assets/js/Chart.min.js') }}"></script>
    <script src="{{ asset('assets/js/daterangepicker.js') }}"></script>
    <script src="{{ asset('assets/js/drawer.js') }}"></script>
    <script src="{{ asset('assets/js/dynamicBadge.js') }}"></script>
    <script src="{{ asset('assets/js/dynamicCheckbox.js') }}"></script>
    <script src="{{ asset('assets/js/footable.min.js') }}"></script>
    <script src="{{ asset('assets/js/fullcalendar@5.2.0.js') }}"></script>
    <script src="{{ asset('assets/js/google-chart.js') }}"></script>
    <script src="{{ asset('assets/js/jquery-jvectormap-2.0.5.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery-jvectormap-world-mill-en.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.countdown.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.filterizr.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.magnific-popup.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.peity.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.star-rating-svg.min.js') }}"></script>
    <script src="{{ asset('assets/js/leaflet.js') }}"></script>
    <script src="{{ asset('assets/js/leaflet.markercluster.js') }}"></script>
    <script src="{{ asset('assets/js/loader.js') }}"></script>
    <script src="{{ asset('assets/js/message.js') }}"></script>
    <script src="{{ asset('assets/js/moment.js') }}"></script>
    <script src="{{ asset('assets/js/muuri.min.js') }}"></script>
    <script src="{{ asset('assets/js/popover.js') }}"></script>
    <script src="{{ asset('assets/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/js/slick.min.js') }}"></script>
    <script src="{{ asset('assets/js/trumbowyg.min.js') }}"></script>
    <script src="{{ asset('assets/js/wickedpicker.min.js') }}"></script>
    <script src="{{ asset('assets/js/apexmain.js') }}"></script>
    <script src="{{ asset('assets/js/charts.js') }}"></script>
    <script src="{{ asset('assets/js/drag-drop.js') }}"></script>
    <script src="{{ asset('assets/js/footable.js') }}"></script>
    <script src="{{ asset('assets/js/full-calendar.js') }}"></script>
    <script src="{{ asset('assets/js/googlemap-init.js') }}"></script>
    <script src="{{ asset('assets/js/icon-loader.js') }}"></script>
    <script src="{{ asset('assets/js/jvectormap-init.js') }}"></script>
    <script src="{{ asset('assets/js/leaflet-init.js') }}"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <!-- Google Maps -->
    <script src="http://maps.googleapis.com/maps/api/js?key=AIzaSyBgYKHZB_QKKLWfIRaYPCadza3nhTAbv7c"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/quagga/0.12.1/quagga.js" integrity="sha512-tkMtg2br+OytX7fpdDoK34wzSUc6JcJa7aOEYUKwlSAAtqTSYVLocV4BpLBIx3RS+h+Ch6W+2lVSzNxQx4yefw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <!-- Notification Script -->
    <script>
        const toastButtons = document.querySelectorAll('.btn-toast');
        let toastCount = 0;

        function createToast(type, icon, title, message) {
            let toast = ``;
            const notificationShocase = $('.notification-wrapper');
            toast = `
                <div class="dm-notification-box notification-${type} notification-${toastCount}">
                    <div class="dm-notification-box__content media">
                        <div class="dm-notification-box__icon">
                            <i class="uil uil-${icon}"></i>
                        </div>
                        <div class="dm-notification-box__text media-body">
                            <h6>${title}</h6>
                            <p>${message}</p>
                        </div>
                    </div>
                </div>
            `;
            notificationShocase.append(toast);
            toastCount++;
        }

        let duration = (optionValue, defaultValue) => typeof optionValue === "undefined" ? defaultValue : optionValue;
        let durationaa = 4000;

        let thisToast = toastCount - 1;

        $('*[data-toast]').on('click', function() {
            $(this).parent('.dm-notification-box').remove();
        });

        setTimeout(function() {
            $(document).find(".notification-" + thisToast).remove();
        }, duration(durationaa, 3000));
    </script>

    @stack('scripts')
</body>
</html>