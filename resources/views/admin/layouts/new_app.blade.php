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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">
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

    <!-- Evan Admin design system -->
    <link rel="stylesheet" type="text/css" href="{{ asset('new-admin-assets/css/admin.css') }}  ">

    <link rel="stylesheet" href="{{ asset('admin-assets/plugins/dropzone/dropzone.css') }}">
    <link rel="stylesheet" href="{{ asset('admin-assets/plugins/dropzone/min/dropzone.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin-assets/plugins/summernote/summernote-lite.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin-assets/css/datetimepicker.css') }}">

    <!-- Rich text editor fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600;700&family=Roboto:wght@400;500;700&display=swap">
    <link rel="stylesheet" href="https://fonts.maateen.me/kalpurush/font.css">

    <style>
        /* Rich text editor (Summernote lite) */
        .note-editor.note-frame {
            border: 1px solid var(--a-border);
            border-radius: 8px;
        }

        .note-editor .note-toolbar {
            background: #f8fafc;
            border-bottom: 1px solid var(--a-border);
            flex-wrap: wrap;
            gap: 2px;
        }

        .note-editor .note-toolbar .note-btn {
            background: transparent;
            border-color: transparent;
        }

        .note-editor .note-toolbar .note-btn:hover,
        .note-editor .note-toolbar .note-btn.active {
            background: #eef2f6;
        }

        .note-editor .note-editable {
            font-family: 'Poppins', 'Kalpurush', Arial, sans-serif;
            font-size: 15px;
            line-height: 1.7;
            min-height: 180px;
        }

        .note-editor .note-editable:focus {
            outline: none;
        }

        .note-editor .note-statusbar {
            background: #f8fafc;
        }

        /* Summernote renders its own caret (.note-icon-caret). Its toggle buttons
           also carry Bootstrap's .dropdown-toggle class, so Bootstrap's ::after
           caret would show a second arrow. Hide it. */
        .note-editor .dropdown-toggle::after {
            display: none !important;
            content: none !important;
            border: 0 !important;
        }
    </style>


    <!-- Searchable selects (Select2) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">







    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        .page-wrapper.compact-wrapper .page-body-wrapper .page-body {
            min-height: calc(100vh - 64px);
            margin-top: 64px;
            margin-left: 264px;
            background-color: #f4f6f9;
            padding-top: 24px;
            padding-bottom: 48px !important;
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
    <script src="{{ asset('admin-assets/plugins/summernote/summernote-lite.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('admin-assets/js/datetimepicker.js') }}"></script>



    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Reusable searchable <select> initialiser (Select2).
        // The placeholder is taken from the first empty option so the existing
        // "Select ..." labels are preserved.
        window.initSelect2 = function(selector) {
            if (!window.jQuery || !jQuery.fn.select2) return;

            jQuery(selector).each(function() {
                var el = jQuery(this);
                if (el.data('select2')) return;

                var placeholder = el.find('option[value=""]').first().text().trim();

                el.select2({
                    width: '100%',
                    placeholder: placeholder || undefined
                });
            });
        };

        // ---------------------------------------------------------------------
        // Rich text editor (Summernote lite) — shared configuration.
        // ---------------------------------------------------------------------
        window.RICH_EDITOR_FONTS = ['Kalpurush', 'Poppins', 'Inter', 'Roboto', 'Arial', 'Georgia', 'Times New Roman'];
        window.RICH_EDITOR_SIZES = ['10', '12', '14', '16', '18', '20', '24', '28', '32', '36', '42', '48'];

        window.initRichEditors = function() {
            if (!window.jQuery || !jQuery.fn.summernote) return;

            jQuery('.summernote').each(function() {
                var $el = jQuery(this);
                if ($el.next('.note-editor').length) return; // already initialised

                $el.summernote({
                    height: 300,
                    minHeight: 180,
                    maxHeight: 640,
                    dialogsInBody: true,
                    placeholder: $el.attr('placeholder') || 'Write your content…',
                    fontNames: window.RICH_EDITOR_FONTS,
                    fontNamesIgnoreCheck: window.RICH_EDITOR_FONTS,
                    fontSizes: window.RICH_EDITOR_SIZES,
                    styleTags: ['p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'blockquote', 'pre'],
                    toolbar: [
                        ['history', ['undo', 'redo']],
                        ['style', ['style']],
                        ['font', ['fontname', 'fontsize']],
                        ['format', ['bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear']],
                        ['color', ['color']],
                        ['para', ['paragraph']],
                        ['list', ['ul', 'ol']],
                        ['insert', ['link', 'picture', 'table', 'hr']],
                        ['view', ['codeview']],
                    ],
                    buttons: {
                        picture: function(context) {
                            var ui = jQuery.summernote.ui;
                            var button = ui.button({
                                contents: '<i class="note-icon-picture"></i>',
                                tooltip: 'Insert Image',
                                click: function() {
                                    Swal.fire({
                                        title: 'Insert Image',
                                        html: '<input id="rich-img-url" class="swal2-input" placeholder="Image URL">' +
                                            '<input id="rich-img-alt" class="swal2-input" placeholder="Alt text">' +
                                            '<input id="rich-img-width" class="swal2-input" placeholder="Width in px (optional)">',
                                        showCancelButton: true,
                                        confirmButtonText: 'Insert',
                                        focusConfirm: false,
                                        preConfirm: function() {
                                            var url = document.getElementById('rich-img-url').value.trim();
                                            if (!url) {
                                                Swal.showValidationMessage('Image URL is required');
                                                return false;
                                            }
                                            return {
                                                url: url,
                                                alt: document.getElementById('rich-img-alt').value.trim(),
                                                width: document.getElementById('rich-img-width').value.trim(),
                                            };
                                        }
                                    }).then(function(res) {
                                        if (!res.isConfirmed || !res.value) return;
                                        var style = res.value.width ?
                                            ' style="width:' + parseInt(res.value.width, 10) + 'px;max-width:100%;height:auto;"' :
                                            ' style="max-width:100%;height:auto;"';
                                        var html = '<img src="' + res.value.url + '" alt="' + res.value.alt + '"' + style + '>';
                                        context.invoke('editor.insertNode', jQuery(html)[0]);
                                    });
                                }
                            });
                            return button.render();
                        }
                    }
                });

                // Keep the underlying textarea in sync on form submit.
                var $form = $el.closest('form');
                if ($form.length) {
                    $form.on('submit', function() {
                        $el.val($el.summernote('code'));
                    });
                }
            });
        };

        $(document).ready(function() {
            window.initRichEditors();

            // Auto-enhance any select marked with .js-select2.
            window.initSelect2('.js-select2');
        });
    </script>
    @yield('customJs')
</body>

</html>
