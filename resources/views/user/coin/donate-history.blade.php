@extends('layouts.app')
@section('seo')
<?php
$seo_title = $settings->where('type', 'seo_title')->first()->value;
$seo_description = $settings->where('type', 'seo_description')->first()->value;
$data_seo = array(
    'title' => 'Lịch sử Donate | ' . $seo_title,
    'keywords' => '',
    'description' => $seo_description,
    'og_title' => 'Lịch sử Donate | ' . $seo_title,
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
    <!--start page wrapper -->
    <div class="page-wrapper">
        <div class="page-content">
            <!--breadcrumb-->
            <div class="page-breadcrumb d-flex align-items-center mb-3">
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route('index') }}">Trang chủ</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Lịch sử Donate</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!--end breadcrumb-->

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead class="table-light">
                            <tr>
                                <th>ID#</th>
                                <th>Số Coin</th>
                                <th>Người nhận</th>
                                <th>Nội dung</th>
                                <th>Ngày Donate</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($donates as $donate)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="ms-2">
                                                <h6 class="mb-0 font-14">#{{ $donate->id }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td><i class="bx bx-coin-stack"></i> {{ number_format($donate->coin) }}</td>
                                    <td>
                                        {{ $donate->receiver->name }}
                                    </td>
                                    <td>
                                        @if ($donate->story)
                                            Donate truyện
                                            <a href="{{ route('story.detail', $donate->story->slug) }}" target="_blank">
                                                <b>{{ $donate->story->name }}</b>
                                            </a>
                                        @else
                                            Donate team <a href="{{ route('translateTeam.detail', $donate->receiver->id) }}" target="_blank">
                                                <b>{{ $donate->receiver->name }}</b>
                                            </a>
                                        @endif
                                    </td>
                                    <td>{{ date('d/m/Y', strtotime($donate->created_at)) }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="pagination mt-4 justify-content-center">
                        {!! $donates->withQueryString()->links('vendor.pagination.rocker-pagination') !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
