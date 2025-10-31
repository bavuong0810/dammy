@extends('layouts.app')
@section('seo')
<?php
$seo_title = $settings->where('type', 'seo_title')->first()->value;
$seo_description = $settings->where('type', 'seo_description')->first()->value;
$data_seo = array(
    'title' => 'Lịch sử giao dịch | ' . $seo_title,
    'keywords' => '',
    'description' => $seo_description,
    'og_title' => 'Lịch sử giao dịch | ' . $seo_title,
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
                            <li class="breadcrumb-item active" aria-current="page">Lịch sử giao dịch</li>
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
                                <th>Thời gian</th>
                                <th>Loại giao dịch</th>
                                <th>Số xu</th>
                                <th>Nội dung</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($histories as $history)
                                <tr>
                                    <td>
                                        {{ date('d/m/Y H:i:s', strtotime($history->created_at)) }}
                                    </td>
                                    <td>{{ \App\Models\CoinHistory::TypeText[$history->type] }}</td>
                                    <td>@if($history->transaction_type == \App\Models\CoinHistory::TransactionType['MINUS']) - @else + @endif{{ number_format($history->coin) }}</td>
                                    <td>
                                        {{ $history->message }}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="pagination mt-4 justify-content-center">
                        {!! $histories->withQueryString()->links('vendor.pagination.rocker-pagination') !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
