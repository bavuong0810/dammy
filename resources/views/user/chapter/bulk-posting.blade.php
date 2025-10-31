@extends('layouts.app')
<?php
$title = 'Đăng chương hàng loạt';
$content = old('content', '');
?>
@section('seo')
<?php
$seo_title = $settings->where('type', 'seo_title')->first()->value;
$seo_description = $settings->where('type', 'seo_description')->first()->value;
$data_seo = array(
    'title' => $title.' | ' . $seo_title,
    'keywords' => '',
    'description' => $seo_description,
    'og_title' => $title.' | ' . $seo_title,
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
                            <li class="breadcrumb-item">
                                <a href="{{ route('user.story.detail', $story_id) }}">
                                    <i class="bx bx-book-alt"></i> {{ $story->name }}
                                </a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $title }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!--end breadcrumb-->

            <div class="my-3">
                <a href="{{ route('user.chapter.index', $story_id) }}" class="btn btn-primary btn-sm radius-30 px-4">Trở về danh sách chương</a>
            </div>

            <div class="card">
                <div class="card-body p-4">
                    <h5 class="card-title">{{ $title }}</h5>
                    <hr/>
                    <form action="{{ route('user.chapter.processBulkPosting', $story_id) }}" method="POST">
                        @csrf
                        @if ($errors->any())
                            <div class="mgt-10 alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if(Session::has('success_msg'))
                            <div class="mgt-10  alert alert-success alert-dismissible" role="alert">
                                {{ Session::get('success_msg') }}
                            </div>
                        @endif

                        <div class="form-body mt-4">
                            <div class="card">
                                <div class="card-header">
                                    Cách tính năng này hoạt động
                                </div>
                                <div class="card-body p-4">
                                    <b>Định dạng để đăng chương hàng loạt:</b> <br>
                                    <div class="m-3">
                                        Chương + STT: Tên chương <br>
                                        Nội dung chương
                                    </div>
                                    <b>Ví dụ bạn có đoạn nôi dung:</b> <br>
                                    <div class="m-3">
                                        Chương 1: Tên chương <br>
                                        Nội dung chương 1 <br>
                                        <br>
                                        Chương 2: Tên chương <br>
                                        Nội dung chương 2
                                    </div>
                                    Hệ thống sẽ tự tách thành 2 chương riêng biệt. <br>
                                    <div class="border-0 bg-warning p-2 my-2 radius-10">
                                        Bạn chỉ có thể đăng tối đa 40 chương 1 lần. Hãy kiểm tra định dạng nội dung chính xác hay chưa trước khi ấn đăng truyện.
                                        <br>
                                        Với trường hợp không có tên chương chỉ cần đặt theo định dạng <b>"Chương 1: "</b>
                                    </div>
                                </div>
                            </div>
                            <div class="border border-3 p-4 rounded">
                                <div class="mb-3">
                                    <label for="content" class="form-label">Nội dung</label>
                                    <textarea class="form-control" name="content" id="content"
                                              rows="8">{!! $content !!}</textarea>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary" onclick="$('body').addClass('loading')">Lưu</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function () {
            addEventListener("submit", (event) => {
                let body = $('body');
                body.addClass("loading");
            });
        })
    </script>
@endsection
