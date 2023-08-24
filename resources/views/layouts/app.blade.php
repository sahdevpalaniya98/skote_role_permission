<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>@yield('title')</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="Themesbrand" name="author" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">
        @yield('page-style')
        @section('styles')
            <link href="{{ asset('assets/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />
            <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
            <link href="{{ asset('assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />
            <link href="{{ asset('assets/libs/select2/select2.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />
            <link href="{{ asset('assets/libs/dataTables/dataTables.min.css') }}" rel="stylesheet">
            {{-- Select 2 --}}
            <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
            {{-- Dropzone css --}}
            <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
            <link rel="stylesheet" href="{{ asset('assets/css/custom.css')}}">
            <style>
                .btn-reset_btn {
                    border: 2px solid #556ee6 !important;
                    background: #556ee61f;
                    color: #556ee6;
                }
                .dataTables_wrapper .dataTables_paginate .paginate_button.current,
                .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
                    color: #fff !important;
                    border: 1px solid #556ee6;
                    background-color: #556ee6;
                }
                div.dataTables_wrapper div.dataTables_filter input {
                    padding: 0.47rem 0.75rem;
                    font-size: .8125rem;
                    font-weight: 400;
                    line-height: 1.5;
                    color: #495057;
                    background-color: #fff;
                    background-clip: padding-box;
                    border: 1px solid #ced4da;
                    -webkit-appearance: none;
                    -moz-appearance: none;
                    appearance: none;
                    border-radius: 0.25rem;
                    -webkit-transition: border-color .15s ease-in-out, -webkit-box-shadow .15s ease-in-out;
                    transition: border-color .15s ease-in-out, -webkit-box-shadow .15s ease-in-out;
                    transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
                    transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out, -webkit-box-shadow .15s ease-in-out;

                }
                div.dataTables_wrapper div.dataTables_filter input:focus-visible {
                    color: #495057;
                    background-color: #fff;
                    border: 2px solid #556ee6 !important;
                }
                #dd_filter, #btn_generateExcel {
                    margin-bottom: 10px;
                }
                li.nav-item.dropdown {
                    padding-right: 15px;
                }

                .swal2-popup.swal2-toast {
                    font-size: 13px;
                }
               
                .logo .logo-text{
                    color: white;
                    font-size: 30px;
                    font-weight: bold;
                }

            </style>
        @show

    </head>

    <body data-sidebar="dark">

        <div id="preloader">
            <div id="status">
                <div class="spinner-chase">
                    <div class="chase-dot"></div>
                    <div class="chase-dot"></div>
                    <div class="chase-dot"></div>
                    <div class="chase-dot"></div>
                    <div class="chase-dot"></div>
                    <div class="chase-dot"></div>
                </div>
            </div>
        </div>

        <div id="loader" style="display: none">
            <div id="loader-status">
                <div class="spinner-chase">
                    <div class="chase-dot"></div>
                    <div class="chase-dot"></div>
                    <div class="chase-dot"></div>
                    <div class="chase-dot"></div>
                    <div class="chase-dot"></div>
                    <div class="chase-dot"></div>
                </div>
            </div>
        </div>

        <div id="layout-wrapper">
            @include('layouts.includes.topheader')

            @include('layouts.includes.sidebar')

            <div class="main-content">
                <div class="page-content">
                    <div class="container-fluid">
                        @include('layouts.includes.breadcrumb')
                        <div class="row">
                            @yield('content')
                        </div>
                    </div>
                </div>
                @section('modals')
                @show
                @include('layouts.includes.footer')
            </div>
        </div>

        @section('scripts')
            <script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>
            <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
            <script src="{{ asset('assets/libs/metismenu/metisMenu.min.js') }}"></script>
            <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
            <script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
            <script src="{{ asset('assets/js/app.js') }}"></script>
            <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.js') }}"></script>
            <script src="{{ asset('assets/libs/select2/select2.min.js') }}"></script>
            <script src="{{ asset('assets/common/common.js') }}"></script>
            <script src="{{ asset('assets/libs/dataTables/dataTables.min.js') }}"></script>
            {{-- Select 2 --}}
            <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
            {{-- Dropzone js --}}
            <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
            {{-- jquery validate --}}
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js" ></script>
            @if (Session::has('alert-message'))
                <script>
                    Toast.fire({icon: "{{ Session::get('alert-class', 'info') }}", title: "{{ Session::get('alert-message') }}"})
                </script>
            @endif
            <script>
                $('.select2').select2();
            </script>

            <script>
                $(document).ready(function() {
                    $('.js-example-basic-multiple').select2();
                });
            </script>
        @show
    </body>
</html>
