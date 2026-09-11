<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="Fastkart admin is super flexible, powerful, clean &amp; modern responsive bootstrap 5 admin template with unlimited possibilities.">
    <meta name="keywords"
        content="admin template, Fastkart admin template, dashboard template, flat admin template, responsive admin template, web app">
    <meta name="author" content="pixelstrap">
    <link rel="icon" href="{{ asset('new-front-assets/images/favicon/8.png') }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ asset('new-front-assets/images/favicon/8.png') }}" type="image/x-icon">
    <title>Dashboard</title>

    <!-- Google font-->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap">

    <!-- Linear Icon css -->
    <link rel="stylesheet" href="{{ asset('new-admin-assets/css/linearicon.css') }}">

    <!-- fontawesome css -->
    <link rel="stylesheet" type="text/css" href="{{ asset('new-admin-assets/css/vendors/font-awesome.css') }}  ">

    <!-- Themify icon css-->
    <link rel="stylesheet" type="text/css" href="{{ asset('new-admin-assets/css/vendors/themify.css') }}  ">

    <!-- ratio css -->
    <link rel="stylesheet" type="text/css" href="{{ asset('new-admin-assets/css/ratio.css') }}  ">

    <!-- remixicon css -->
    <link rel="stylesheet" type="text/css" href="{{ asset('new-admin-assets/css/remixicon.css') }}  ">

    <!-- Feather icon css-->
    <link rel="stylesheet" type="text/css" href="{{ asset('new-admin-assets/css/vendors/feather-icon.css') }}  ">

    <!-- Plugins css -->
    <link rel="stylesheet" type="text/css" href="{{ asset('new-admin-assets/css/vendors/scrollbar.css') }}  ">
    <link rel="stylesheet" type="text/css" href="{{ asset('new-admin-assets/css/vendors/animate.css') }}  ">

    <!-- Bootstrap css-->
    <link rel="stylesheet" type="text/css" href="{{ asset('new-admin-assets/css/vendors/bootstrap.css') }}  ">

    <!-- vector map css -->
    <link rel="stylesheet" type="text/css" href="{{ asset('new-admin-assets/css/vector-map.css') }}  ">

    <!-- Slick Slider Css -->
    <link rel="stylesheet" href="{{ asset('new-admin-assets/css/vendors/slick.css') }}  ">

    <!-- App css -->
    <link rel="stylesheet" type="text/css" href="{{ asset('new-admin-assets/css/style.css') }}  ">

    <link rel="stylesheet" href="{{ asset('admin-assets/plugins/dropzone/dropzone.css') }}">
    <link rel="stylesheet" href="{{ asset('admin-assets/plugins/dropzone/min/dropzone.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin-assets/plugins/summernote/summernote-bs4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin-assets/css/datetimepicker.css') }}">







    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        .page-wrapper.compact-wrapper .page-body-wrapper .page-body {
            min-height: calc(100vh - 80px);
            margin-top: 43px;
            margin-left: 264px;
            background-color: #f9f9f6;
            padding-top: 20px;
            padding-bottom: 54px !important;
        }
    </style>
</head>

<body>
    <!-- tap on top start -->
    <div class="tap-top">
        <span class="lnr lnr-chevron-up"></span>
    </div>
    <!-- tap on tap end -->

    <!-- page-wrapper Start-->
    <div class="page-wrapper compact-wrapper" id="pageWrapper">
        <!-- Page Header Start-->
        @include('admin.layouts.new_page_header')
        <!-- Page Header Ends-->

        <!-- Page Body Start-->
        <div class="page-body-wrapper" style="padding-top: 0px">
            <!-- Page Sidebar Start-->
            @include('admin.layouts.new_sidebar')
            <!-- Page Sidebar Ends-->

            <!-- index body start -->
            <div class="page-body">




                @yield('content')







                <!-- Container-fluid Ends-->

                <!-- footer start-->
                <div class="container-fluid">
                    <footer class="footer">
                        <div class="row">
                            <div class="col-md-12 footer-copyright text-center">
                                <p class="mb-0">Copyright 2025 © Evan</p>
                            </div>
                        </div>
                    </footer>
                </div>
                <!-- footer End-->
            </div>
            <!-- index body end -->

        </div>
        <!-- Page Body End -->
    </div>
    <!-- page-wrapper End-->

    <!-- Modal Start -->
    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
        <div class="modal-dialog  modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <h5 class="modal-title" id="staticBackdropLabel">Logging Out</h5>
                    <p>Are you sure you want to log out?</p>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="button-box">
                        <button type="button" class="btn btn--no" data-bs-dismiss="modal">No</button>
                        <button type="button" onclick="location.href = '{{ route('admin.logout') }}';"
                            class="btn  btn--yes btn-primary">Yes</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal End -->

    <!-- latest js -->
    <script src="{{ asset('new-admin-assets/js/jquery-3.6.0.min.js') }}  "></script>

    <!-- Bootstrap js -->
    <script src="{{ asset('new-admin-assets/js/bootstrap/bootstrap.bundle.min.js') }}  "></script>

    <!-- feather icon js -->
    <script src="{{ asset('new-admin-assets/js/icons/feather-icon/feather.min.js') }}  "></script>
    <script src="{{ asset('new-admin-assets/js/icons/feather-icon/feather-icon.js') }}  "></script>

    <!-- scrollbar simplebar js -->
    <script src="{{ asset('new-admin-assets/js/scrollbar/simplebar.js') }}  "></script>
    <script src="{{ asset('new-admin-assets/js/scrollbar/custom.js') }}  "></script>

    <!-- Sidebar jquery -->
    <script src="{{ asset('new-admin-assets/js/config.js') }}  "></script>

    <!-- tooltip init js -->
    <script src="{{ asset('new-admin-assets/js/tooltip-init.js') }}  "></script>

    <!-- Plugins JS -->
    <script src="{{ asset('new-admin-assets/js/sidebar-menu.js') }}  "></script>
    <script src="{{ asset('new-admin-assets/js/notify/bootstrap-notify.min.js') }}  "></script>
    <script src="{{ asset('new-admin-assets/js/notify/index.js') }}  "></script>

    <!-- Apexchar js -->
    <script src="{{ asset('new-admin-assets/js/chart/apex-chart/apex-chart1.js') }}  "></script>
    <script src="{{ asset('new-admin-assets/js/chart/apex-chart/moment.min.js') }}  "></script>
    <script src="{{ asset('new-admin-assets/js/chart/apex-chart/apex-chart.js') }}  "></script>
    <script src="{{ asset('new-admin-assets/js/chart/apex-chart/stock-prices.js') }}  "></script>
    <script src="{{ asset('new-admin-assets/js/chart/apex-chart/chart-custom1.js') }}  "></script>


    <!-- slick slider js -->
    <script src="{{ asset('new-admin-assets/js/slick.min.js') }}  "></script>
    <script src="{{ asset('new-admin-assets/js/custom-slick.js') }}  "></script>

    <!-- customizer js -->
    <script src="{{ asset('new-admin-assets/js/customizer.js') }}  "></script>

    <!-- ratio js -->
    <script src="{{ asset('new-admin-assets/js/ratio.js') }}  "></script>

    <!-- sidebar effect -->
    <script src="{{ asset('new-admin-assets/js/sidebareffect.js') }}  "></script>

    <!-- Theme js -->
    <script src="{{ asset('new-admin-assets/js/script.js') }}  "></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="{{ asset('admin-assets/plugins/dropzone/min/dropzone.min.js') }}"></script>
    <script src="{{ asset('admin-assets/plugins/dropzone/dropzone.js') }}"></script>
    <script src="{{ asset('admin-assets/plugins/summernote/summernote.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('admin-assets/js/datetimepicker.js') }}"></script>



    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ready(function() {
            $(".summernote").summernote({
                height: '500'
            });
        });
    </script>
    @yield('customJs')
</body>

</html>
