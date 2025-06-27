<!doctype html>

<html lang="en" class="layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-skin="default"
    data-assets-path="{{ asset('backend/assets/') }}" data-template="vertical-menu-template" data-bs-theme="light">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Dashboard | @yield('title') </title>


    <meta name="description" content="" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('backend/assets/img/favicon/favicon.ico') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&ampdisplay=swap"
        rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('backend/assets/vendor/fonts/iconify-icons.css') }}" />

    <!-- Core CSS -->
    <!-- build:css assets/vendor/css/theme.css  -->

    {{-- <link rel="stylesheet" href="../../assets/vendor/libs/node-waves/node-waves.css" /> --}}
    <link rel="stylesheet" href="{{ asset('backend/assets/vendor/libs/node-waves/node-waves.css') }}" />

    <link rel="stylesheet" href="{{ asset('backend/assets/vendor/libs/pickr/pickr-themes.css') }}" />

    <link rel="stylesheet" href="{{ asset('backend/assets/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('backend/assets/css/demo.css') }}" />

    <!-- Vendors CSS -->

    <link rel="stylesheet" href="{{ asset('backend/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />

    <!-- endbuild -->
    <link rel="stylesheet" href="{{ asset('backend/assets/vendor/libs/bootstrap-select/bootstrap-select.css') }}" />
    <link rel="stylesheet" href="{{ asset('backend/assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('backend/assets/vendor/libs/tagify/tagify.css') }}" />

    <link rel="stylesheet" href="{{ asset('backend/assets/vendor/libs/apex-charts/apex-charts.css') }}" />
    <link rel="stylesheet" href="{{ asset('backend/assets/vendor/libs/swiper/swiper.css') }}" />
    <link rel="stylesheet" href="{{ asset('backend/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet"
        href="{{ asset('backend/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('backend/assets/vendor/fonts/flag-icons.css') }}" />
    <link rel="stylesheet"
        href="{{ asset('backend/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('backend/assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <link rel="stylesheet"
        href="{{ asset('backend/assets/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('backend/assets/vendor/libs/@form-validation/form-validation.css') }}" />
    <link rel="stylesheet" href="{{ asset('backend/assets/vendor/libs/typeahead-js/typeahead.css') }}" />
    <link rel="stylesheet" href="{{ asset('backend/assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />

    <link rel="stylesheet"
        href="{{ asset('backend/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.css') }}" />
    <link rel="stylesheet" href="{{ asset('backend/assets/vendor/libs/jquery-timepicker/jquery-timepicker.css') }}" />

    <link rel="stylesheet" href="{{ asset('backend/assets/vendor/libs/notyf/notyf.css') }}" />
    <link rel="stylesheet" href="{{ asset('backend/assets/vendor/libs/animate-css/animate.css') }}" />
    <link rel="stylesheet" href="{{ asset('backend/assets/vendor/libs/spinkit/spinkit.css') }}" />
    <link rel="stylesheet" href="{{ asset('backend/assets/vendor/libs/dropzone/dropzone.css') }}" />
    <link rel="stylesheet" href="{{ asset('backend/assets/vendor/libs/chartjs/chartjs.css') }}" />


    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('backend/assets/vendor/css/pages/cards-advance.css') }}" />

    <!-- Helpers -->
    <script src="{{ asset('backend/assets/vendor/js/helpers.js') }}"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->

    <!--? Template customizer: To hide customizer set displayCustomizer value false in config.js.  -->
    {{-- <script src="{{ asset('backend/assets/vendor/js/template-customizer.js') }}"></script> --}}

    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->

    <script src="{{ asset('backend/assets/js/config.js') }}"></script>
    @yield('styles')
    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
</head> 

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->

            @include('backend.layouts.sidebar')
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->

                @include('backend.layouts.navbar')
                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->
                    <div class="container-xxl flex-grow-1 container-p-y">

                        @yield('content')

                        @if (session('show_profile_update_modal'))
                            @include('backend.users.profile.update-profie')
                        @endif
                    </div>
                    <!-- / Content -->

                    <!-- Footer -->
                    @include('backend.layouts.footer')
                    <!-- / Footer -->

                    <div class="content-backdrop fade"></div>
                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>

        <!-- Drag Target Area To SlideIn Menu On Small Screens -->
        <div class="drag-target"></div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/theme.js -->

    <script src="{{ asset('backend/assets/vendor/libs/jquery/jquery.js') }}"></script>

    <script src="{{ asset('backend/assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('backend/assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('backend/assets/vendor/libs/node-waves/node-waves.js') }}"></script>

    <script src="{{ asset('backend/assets/vendor/libs/@algolia/autocomplete-js.js') }}"></script>

    <script src="{{ asset('backend/assets/vendor/libs/pickr/pickr.js') }}"></script>

    <script src="{{ asset('backend/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>

    <script src="{{ asset('backend/assets/vendor/libs/hammer/hammer.js') }}"></script>

    <script src="{{ asset('backend/assets/vendor/libs/i18n/i18n.js') }}"></script>

    <script src="{{ asset('backend/assets/vendor/js/menu.js') }}"></script>

    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="{{ asset('backend/assets/vendor/libs/chartjs/chartjs.js') }}"></script>
    <script src="{{ asset('backend/assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
    <script src="{{ asset('backend/assets/vendor/libs/swiper/swiper.js') }}"></script>
    <script src="{{ asset('backend/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
    <script src="{{ asset('backend/assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('backend/assets/vendor/libs/bootstrap-select/bootstrap-select.js') }}"></script>
    <script src="{{ asset('backend/assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>
    <script src="{{ asset('backend/assets/vendor/libs/bloodhound/bloodhound.js') }}"></script>

    <script src="{{ asset('backend/assets/vendor/libs/tagify/tagify.js') }}"></script>
    <script src="{{ asset('backend/assets/vendor/libs/notyf/notyf.js') }}"></script>
    <script src="{{ asset('backend/assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>

    <script src="{{ asset('backend/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js') }}">
    </script>
    <script src="{{ asset('backend/assets/vendor/libs/jquery-timepicker/jquery-timepicker.js') }}"></script>
    <script src="{{ asset('backend/assets/vendor/libs/dropzone/dropzone.js') }}"></script>

    <!-- Flat Picker -->
    <script src="{{ asset('backend/assets/vendor/libs/moment/moment.js') }}"></script>
    <script src="{{ asset('backend/assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
    <!-- Form Validation -->
    <script src="{{ asset('backend/assets/vendor/libs/@form-validation/popular.js') }}"></script>
    <script src="{{ asset('backend/assets/vendor/libs/@form-validation/bootstrap5.js') }}"></script>
    <script src="{{ asset('backend/assets/vendor/libs/@form-validation/auto-focus.js') }}"></script>
    <!-- Main JS -->

    <script src="{{ asset('backend/assets/js/main.js') }}"></script>

    <!-- Page JS -->
    <script src="{{ asset('backend/assets/js/dashboards-analytics.js') }}"></script>
    <script src="{{ asset('backend/assets/js/tables-datatables-basic.js') }}"></script>
    <script src="{{ asset('backend/assets/js/form-validation.js') }}"></script>
    <script src="{{ asset('backend/assets/js/extended-ui-sweetalert2.js') }}"></script>
    <script src="{{ asset('backend/assets/js/forms-pickers.js') }}"></script>
    <script src="{{ asset('backend/assets/js/forms-selects.js') }}"></script>
    <script src="{{ asset('backend/assets/js/forms-tagify.js') }}"></script>
    <script src="{{ asset('backend/assets/js/forms-typeahead.js') }}"></script>
    <script src="{{ asset('backend/assets/js/forms-file-upload.js') }}"></script>
    <script src="{{ asset('backend/assets//js/charts-chartjs-legend.js') }}"></script>
    <script src="{{ asset('backend/assets/js/charts-chartjs.js') }}"></script>

    <!-- toster -->
    <script src="{{ asset('backend/assets/js/ui-toasts.js') }}"></script>

    <!-- ckeditor -->
    <script src=" {{ asset('backend/plugin-links/ckeditor/ckeditor.js') }}"></script>
    @yield('scripts')
    <script>
        $("#image").change(function() {
            let reader = new FileReader();

            $("#imgPreview").html('');

            reader.onload = function(e) {
                let fileExtension = $("#image").val().split('.').pop().toLowerCase();

                if (fileExtension === 'pdf') {
                    $("#imgPreview").append(
                        '<div class="col-3 mt-2"><img src="{{ asset('default-image/pdf.png') }}" class="img-fluid" /></div>'
                    );
                } else if (fileExtension === 'ppt' || fileExtension === 'pptx' || fileExtension === 'pptm') {
                    $("#imgPreview").append(
                        '<div class="col-3 mt-2"><img src="{{ asset('default-image/ppt.png') }}" class="img-fluid" /></div>'
                    );
                } else if (fileExtension === 'doc' || fileExtension === 'docx') {
                    $("#imgPreview").append(
                        '<div class="col-3 mt-2"><img src="{{ asset('default-image/word.png') }}" class="img-fluid" /></div>'
                    );
                } else {
                    $("#imgPreview").append('<div class="col-3 mt-2"><img src="' + e.target.result +
                        '" class="img-fluid" /></div>');
                }
            };

            reader.readAsDataURL(this.files[0]);
        });

        $("#image2").change(function() {
            let reader = new FileReader();

            $("#imgPreview2").html('');

            reader.onload = function(e) {
                let fileExtension2 = $("#image2").val().split('.').pop().toLowerCase();

                if (fileExtension2 === 'pdf') {
                    $("#imgPreview2").append(
                        '<div class="col-3 mt-2"><img src="{{ asset('default-image/pdf.png') }}" class="img-fluid" /></div>'
                    );
                } else if (fileExtension2 === 'ppt' || fileExtension2 === 'pptx' || fileExtension2 === 'pptm') {
                    $("#imgPreview2").append(
                        '<div class="col-3 mt-2"><img src="{{ asset('default-image/ppt.png') }}" class="img-fluid" /></div>'
                    );
                } else if (fileExtension2 === 'doc' || fileExtension2 === 'docx') {
                    $("#imgPreview2").append(
                        '<div class="col-3 mt-2"><img src="{{ asset('default-image/word.png') }}" class="img-fluid" /></div>'
                    );
                } else {
                    $("#imgPreview2").append('<div class="col-3 mt-2"><img src="' + e.target.result +
                        '" class="img-fluid" /></div>');
                }
            };

            reader.readAsDataURL(this.files[0]);
        });


        //Ck editor inisilize
        document.querySelectorAll('.ckeditor').forEach(function(element) {
            CKEDITOR.replace(element.id, {
                filebrowserUploadUrl: '{{ route('ckeditor.fileUpload', ['_token' => csrf_token()]) }}',
                filebrowserUploadMethod: "form",
                extraPlugins: 'wordcount',
                wordcount: {
                    showWordCount: true,
                    showCharCount: true,
                    countSpacesAsChars: true,
                    countHTML: false,
                    maxCharCount: -1,
                    maxWordCount: -1
                }
            });
        });


        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>

    <script>
        $('.delete').click(function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Are you sure to delete?',
                text: "You won't be able to revert it.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Delete!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $(this).parent('form').submit();
                }
            })
        });
    </script>
    <script>
        const notyf = new Notyf({
            dismissible: true,
            position: {
                x: 'right',
                y: 'top',
            }
        });
    </script>
    @if (Session::has('status'))
        <script>
            notyf.success('{!! Session::get('status') !!}');
        </script>
    @endif
    @if (Session::has('delete'))
        <script>
            notyf.error('{!! Session::get('delete') !!}');
        </script>
    @endif
    <script>
        $(document).ready(function() {

            $(".integerValue").on("keydown", function(event) {
                // Allow backspace, delete, tab, escape, and enter keys
                if (event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode ==
                    27 || event
                    .keyCode == 13 ||
                    // Allow Ctrl+A
                    (event.keyCode == 65 && event.ctrlKey === true) ||
                    // Allow home, end, left, right
                    (event.keyCode >= 35 && event.keyCode <= 39) ||
                    // Allow numbers from the main keyboard (0-9) and the numpad (96-105)
                    (event.keyCode >= 48 && event.keyCode <= 57) || (event.keyCode >= 96 && event.keyCode <=
                        105)) {
                    return;
                } else {
                    event.preventDefault();
                }
            });
        });
    </script>

</body>

</html>
