<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="robots" content="noindex, nofollow"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="{{ asset($settings->where('type', 'favicon')->first()->value) }}" rel="shortcut icon"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @yield('seo')
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Admin Css -->
    <?php $ver = env('APP_VERSION'); ?>
    <link rel="stylesheet" href="{{asset('css/style-admin.min.css')}}?ver={{ $ver }}">
    <link rel="stylesheet" href="{{asset('css/style_admin.css')}}?ver={{ $ver }}">
    <!-- Admin js -->
    <script src="{{asset('js/js-admin.min.js')}}?ver={{ $ver }}"></script>
    <script src="{{asset('js/js_admin.js')}}?ver={{ $ver }}"></script>
    <script src="{{asset('ckeditor/ckeditor.js')}}?ver={{ $ver }}"></script>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
    <script type="text/javascript">
        var admin_url = '{{route('admin.dashboard')}}';
    </script>
