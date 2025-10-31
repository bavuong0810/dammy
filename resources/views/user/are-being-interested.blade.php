@extends('layouts.app')
@section('seo')
<?php
$seo_title = $settings->where('type', 'seo_title')->first()->value;
$seo_description = $settings->where('type', 'seo_description')->first()->value;
$data_seo = array(
    'title' => 'Truyện đang được quan tâm - ' . $seo_title,
    'keywords' => '',
    'description' => $seo_description,
    'og_title' => 'Truyện đang được quan tâm - ' . $seo_title,
    'og_description' => $seo_description,
    'og_url' => Request::url(),
    'og_img' => asset($settings->where('type', 'seo_image')->first()->value)
);
$seo = WebService::getSEO($data_seo);
?>
@include('partials.seo')
@endsection
@section('content')
    <!--start page wrapper -->
    <div class="page-wrapper">
        <div class="page-content">
            <div class="container">
                <div class="card radius-10">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <div>
                                <h5 class="mb-0">15 truyện được xem nhiều trong ngày</h5>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead class="table-light">
                                <tr>
                                    <th style="width: 200px">Truyện</th>
                                    <th>Hình ảnh</th>
                                    <th>Lượt xem</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if (count($day) > 0)
                                    @foreach($day as $item)
                                        <tr>
                                            <td style="width: 200px">
                                                <a href="{{ route('story.detail', $item['slug']) }}">
                                                    <b>{{ WebService::excerpts($item['name'], 20) }}</b>
                                                </a>
                                            </td>
                                            <td>
                                                <img src="{{ asset('images/story/thumbs/230/' . $item['thumbnail']) }}"
                                                     class="product-img-2" alt="{{ $item['name'] }}"
                                                     style="width: 80px;height: 80px;object-fit: cover;"
                                                     onerror="this.src='{{ asset('img/no-image.png') }}'">
                                            </td>
                                            <td>{{ number_format($item['day']) }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <div class="card radius-10">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <div>
                                <h5 class="mb-0">15 truyện được xem nhiều trong tuần</h5>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead class="table-light">
                                <tr>
                                    <th style="width: 200px">Truyện</th>
                                    <th>Hình ảnh</th>
                                    <th>Lượt xem</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if (count($week) > 0)
                                    @foreach($week as $item)
                                        <tr>
                                            <td style="width: 200px">
                                                <a href="{{ route('story.detail', $item['slug']) }}">
                                                    <b>{{ WebService::excerpts($item['name'], 20) }}</b>
                                                </a>
                                            </td>
                                            <td>
                                                <img src="{{ asset('images/story/thumbs/230/' . $item['thumbnail']) }}"
                                                     class="product-img-2" alt="{{ $item['name'] }}"
                                                     style="width: 80px;height: 80px;object-fit: cover;"
                                                     onerror="this.src='{{ asset('img/no-image.png') }}'">
                                            </td>
                                            <td>{{ number_format($item['week']) }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <div class="card radius-10">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <div>
                                <h5 class="mb-0">15 truyện được xem nhiều trong tháng</h5>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead class="table-light">
                                <tr>
                                    <th style="width: 200px">Truyện</th>
                                    <th>Hình ảnh</th>
                                    <th>Lượt xem</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if (count($month) > 0)
                                    @foreach($month as $item)
                                        <tr>
                                            <td style="width: 200px">
                                                <a href="{{ route('story.detail', $item['slug']) }}">
                                                    <b>{{ WebService::excerpts($item['name'], 20) }}</b>
                                                </a>
                                            </td>
                                            <td>
                                                <img src="{{ asset('images/story/thumbs/230/' . $item['thumbnail']) }}"
                                                     class="product-img-2" alt="{{ $item['name'] }}"
                                                     style="width: 80px;height: 80px;object-fit: cover;"
                                                     onerror="this.src='{{ asset('img/no-image.png') }}'">
                                            </td>
                                            <td>{{ number_format($item['month']) }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end page wrapper -->
@endsection
@section('script')
    <script>
        jQuery(document).ready(function ($) {
            historiesRender();
        });
    </script>
@endsection
