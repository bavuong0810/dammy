@extends('layouts.app')
@section('seo')
<?php
$seo_title = $settings->where('type', 'seo_title')->first()->value;
$seo_description = $settings->where('type', 'seo_description')->first()->value;
$data_seo = array(
    'title' => 'Truyện đã đọc | ' . $seo_title,
    'keywords' => '',
    'description' => $seo_description,
    'og_title' => 'Truyện đã đọc | ' . $seo_title,
    'og_description' => $seo_description,
    'og_url' => Request::url(),
    'og_img' => asset($settings->where('type', 'seo_image')->first()->value),
    'current_url' =>Request::url(),
    'current_url_amp' => ''
);
$seo = WebService::getSEO($data_seo);
?>
@include('partials.seo')
@endsection
@section('content')
    <style>
        .bookmark .card-img-top {
            width: 100%;
            max-height: 300px;
            object-fit: cover;
        }

        .bookmark .product-discount a {
            font-size: 18px;
            padding: 5px;
            background: #FFF;
            border-radius: 5px;
            border: 1px solid #ccc;
            line-height: 1;
        }
    </style>

    <!--start page wrapper -->
    <div class="page-wrapper">
        <div class="page-content">
            <div class="container">
                <h1 class="mb-0 text-uppercase">Truyện đã xem gần đây</h1>
                <hr>
                <div class="row">
                    <div class="col-lg-9 col-md-9 col-12">
                        <div class="row product-grid bookmark" id="readHistoriesResult">
                        </div><!--end row-->
                    </div>
                    <div class="col-lg-3 col-md-3 col-12">
                        {!! WebService::WidgetRight($getOptions) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        jQuery(document).ready(function ($) {
            historiesRender();
        });
    </script>
@endsection
