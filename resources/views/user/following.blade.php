@extends('layouts.app')
@section('seo')
<?php
$seo_title = $settings->where('type', 'seo_title')->first()->value;
$seo_description = $settings->where('type', 'seo_description')->first()->value;
$data_seo = array(
    'title' => 'Đang theo dõi | ' . $seo_title,
    'keywords' => '',
    'description' => $seo_description,
    'og_title' => 'Đang theo dõi | ' . $seo_title,
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
                <h1 class="mb-0 text-uppercase">Đang theo dõi</h1>
                <hr>
                <div class="row">
                    <div class="col-lg-9 col-md-9 col-12">
                        <div class="row product-grid bookmark">
                            @foreach($list->data as $item)
                                <div class="col-md-3 col-6">
                                    <div class="card">
                                        <div class="position-relative">
                                            <a href="{{ route('translateTeam.detail', $item->id) }}">
                                                <img alt="{{ $item->name }}" class="card-img-top"
                                                     width="200" height="260" src="{{ asset('images/avatar/thumbs/230/' . $item->avatar) }}"
                                                     onerror="this.src='{{ asset('img/no-image.png') }}'">
                                            </a>
                                            <div class="story-meta-data d-flex justify-content-start">
                                                <span><i class="bx bx-show"></i> {{ number_format($item->total_view) }}</span>
                                                <span><i class="bx bx-bell"></i> {{ number_format($item->total_follow) }}</span>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <a href="{{ route('translateTeam.detail', $item->id) }}">
                                                <h3 class="card-title cursor-pointer story-item-title">{{ $item->name }}</h3>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div><!--end row-->
                        <div class="pagination mt-4 justify-content-center">
                            {!! $paginate !!}
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3 col-12">
                        {!! WebService::WidgetRight($getOptions) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
