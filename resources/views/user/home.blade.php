@extends('layouts.app')
@section('seo')
<?php
$seo_title = $settings->where('type', 'seo_title')->first()->value;
$seo_description = $settings->where('type', 'seo_description')->first()->value;
$data_seo = array(
    'title' => 'Dashboard - ' . $seo_title,
    'keywords' => $settings->where('type', 'seo_keyword')->first()->value,
    'description' => $seo_description,
    'og_title' => 'Dashboard - ' . $seo_title,
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
                @if(Session::has('success_msg'))
                    <div class="my-3">
                        <div class="mgt-10  alert alert-success alert-dismissible fade in" role="alert">
                            {{ Session::get('success_msg') }}
                        </div>
                    </div>
                @endif
                @if (Auth::user()->type == 1)
                    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4">
                        <div class="col">
                            <div class="card radius-10 border-start border-0 border-4 border-info">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <p class="mb-0 text-secondary">Tổng số truyện</p>
                                            <h4 class="my-1 text-info">{{ number_format($total_comics) }}</h4>
                                        </div>
                                        <div class="widgets-icons-2 rounded-circle bg-gradient-blues text-white ms-auto"><i
                                                class='bx bxs-book'></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card radius-10 border-start border-0 border-4 border-warning">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <p class="mb-0 text-secondary">Tổng số truyện đã hoàn thành</p>
                                            <h4 class="my-1 text-warning">{{ number_format($total_comics_full) }}</h4>
                                        </div>
                                        <div class="widgets-icons-2 rounded-circle bg-gradient-orange text-white ms-auto"><i
                                                class='bx bxs-book'></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card radius-10 border-start border-0 border-4 border-danger">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <p class="mb-0 text-secondary">Tổng lượt xem trong tháng</p>
                                            <h4 class="my-1 text-danger">{{ number_format($total_views) }}</h4>
                                        </div>
                                        <div class="widgets-icons-2 rounded-circle bg-gradient-burning text-white ms-auto"><i
                                                class='bx bxs-show'></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card radius-10 border-start border-0 border-4 border-success">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <p class="mb-0 text-secondary">Tổng coin được donate</p>
                                            <h4 class="my-1 text-success">{{ number_format($total_donate) }}</h4>
                                        </div>
                                        <div class="widgets-icons-2 rounded-circle bg-gradient-ohhappiness text-white ms-auto">
                                            <i class='bx bxs-coin'></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!--end row-->

                    <div class="card radius-10">
                        <div class="card-header">
                            <div class="d-flex align-items-center">
                                <div>
                                    <h6 class="mb-0">Top 10 truyện được xem nhiều nhất</h6>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table align-middle mb-0">
                                    <thead class="table-light">
                                    <tr>
                                        <th>Truyện</th>
                                        <th>Hình ảnh</th>
                                        <th>Lượt xem</th>
                                        <th>Số chương</th>
                                        <th>Ngày cập nhật</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if (count($top_comics) > 0)
                                        @foreach($top_comics as $item)
                                            <tr>
                                                <td><a href="{{ route('story.detail', $item['slug']) }}"><b>{{ $item['name'] }}</b></a></td>
                                                <td>
                                                    <img src="{{ asset('images/story/' . $item['thumbnail']) }}"
                                                         class="product-img-2" alt="{{ $item['name'] }}"
                                                         onerror="this.src='{{ asset('img/no-image.png') }}'">
                                                </td>
                                                <td>{{ number_format($item['total_view']) }}</td>
                                                <td>{{ $item['total_chapters'] }}</td>
                                                <td>{{ $item['updated_at'] }}</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @else
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
                    {{--Độc giả--}}
                    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4">
                        <div class="col">
                            <div class="card radius-10 border-start border-0 border-4 border-info">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <p class="mb-0 text-secondary">Đã Donate</p>
                                            <h4 class="my-1 text-info">{{ number_format($total_donate) }}</h4>
                                        </div>
                                        <div class="widgets-icons-2 rounded-circle bg-gradient-blues text-white ms-auto"><i
                                                class='bx bxs-coin-stack'></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card radius-10 border-start border-0 border-4 border-warning">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <p class="mb-0 text-secondary">Đã nạp</p>
                                            <h4 class="my-1 text-warning">{{ number_format($total_deposited) }}</h4>
                                        </div>
                                        <div class="widgets-icons-2 rounded-circle bg-gradient-orange text-white ms-auto"><i
                                                class='bx bxs-coin-stack'></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card radius-10 border-start border-0 border-4 border-danger">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <p class="mb-0 text-secondary">Số truyện đã lưu</p>
                                            <h4 class="my-1 text-danger">{{ number_format($total_bookmark) }}</h4>
                                        </div>
                                        <div class="widgets-icons-2 rounded-circle bg-gradient-burning text-white ms-auto"><i
                                                class='bx bxs-book-bookmark'></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card radius-10 border-start border-0 border-4 border-success">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <p class="mb-0 text-secondary">Số bình luận đã đóng góp</p>
                                            <h4 class="my-1 text-success">{{ number_format($total_comments) }}</h4>
                                        </div>
                                        <div class="widgets-icons-2 rounded-circle bg-gradient-ohhappiness text-white ms-auto">
                                            <i class='bx bxs-comment'></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!--end row-->

                    <div class="row">
                        <div class="col-lg-9 col-md-9 col-12">
                            <div class="row product-grid bookmark" id="readHistoriesResult">
                            </div><!--end row-->
                        </div>
                        <div class="col-lg-3 col-md-3 col-12">
                            {!! WebService::WidgetRight($getOptions) !!}
                        </div>
                    </div>
                @endif
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
