@extends('layouts.app')
@section('seo')
<?php
use Jenssegers\Agent\Agent;
$seo_title = $settings->where('type', 'seo_title')->first()->value;
$seo_description = $settings->where('type', 'seo_description')->first()->value;
$data_seo = array(
    'title' => 'Danh sách chương của:  | ' . $seo_title,
    'keywords' => '',
    'description' => $seo_description,
    'og_title' => 'Danh sách chương của:  | ' . $seo_title,
    'og_description' => $seo_description,
    'og_url' => Request::url(),
    'og_img' => asset($settings->where('type', 'seo_image')->first()->value),
    'current_url' =>Request::url(),
    'current_url_amp' => ''
);
$seo = WebService::getSEO($data_seo);
$agent = new Agent();
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
                            <li class="breadcrumb-item"><a href="{{ route('user.dashboard') }}">Trang chủ</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Danh sách chương của: {{ $story->name }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!--end breadcrumb-->

            <div class="card">
                <div class="card-body">
                    <div class="d-lg-flex align-items-center mb-4 gap-3">
                        <div class="position-relative">
                            <form action="{{ route('user.chapter.index', $story->id) }}" method="GET">
                                <input type="text" name="search" class="form-control ps-5 radius-30" placeholder="Tìm chương...">
                                <span class="position-absolute top-50 product-show translate-middle-y"><i class="bx bx-search"></i></span>
                            </form>
                        </div>
                        <div class="ms-auto">
                            <a href="{{ route('user.chapter.create', $story->id) }}"
                               class="btn btn-sm btn-primary radius-30 mt-2 mt-lg-0"><i class="bx bxs-plus-square"></i>Thêm chương</a>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table mb-0">
                            @if(!$agent->isMobile())
                                <thead class="table-light">
                                <tr>
                                    <th>Chương</th>
                                    <th>Lượt xem</th>
                                    <th>Trạng thái</th>
                                    <th>Xem chi tiết</th>
                                    <th>Hành động</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($chapters as $chapter)
                                    <tr id="chapter_id_{{ $chapter->id }}">
                                        <td>{{ $chapter->name }}</td>
                                        <td>{{ number_format($chapter->view) }}</td>
                                        <td>
                                            <b>Ngày đăng: </b>{{ date('d/m/Y H:i:s', strtotime($chapter->created_at)) }} <br>
                                            <b>Trạng thái:</b>@if($chapter->status) Công khai @else Bản nháp @endif <br>
                                            @if($chapter->coin > 0)
                                                <b>Đã khoá chương với {{ number_format($chapter->coin) }} xu</b>
                                            @endif
                                            <div class="form-check form-switch form-check-warning">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                       onclick="quickUpdateStatus(this, 'warning', {{ $chapter->story_id }}, {{ $chapter->id }})"
                                                       id="flexSwitchCheckWarning" value="1" @if($chapter->warning) checked @endif>
                                                <label class="form-check-label" for="flexSwitchCheckWarning">
                                                    Chương có sử dụng từ ngữ <br>nhạy cảm (bạo lực, kích thích,...)
                                                </label>
                                            </div>
                                        </td>
                                        <td>
                                            <a href="{{ route('chapter.detail', [$story->slug, $chapter->slug]) }}"
                                               target="_blank" class="btn btn-primary btn-sm radius-30 px-4">Xem chương</a>
                                        </td>
                                        <td>
                                            <div class="d-flex order-actions">
                                                <a href="{{ route('user.chapter.detail', [$chapter->story_id, $chapter->id]) }}" class="">
                                                    <i class='bx bxs-edit'></i>
                                                </a>
                                                <a href="javascript:void(0)"
                                                   onclick="removeChapter({{ $chapter->story_id }}, {{ $chapter->id }}, '{{ $chapter->name }}')"
                                                   class="ms-3">
                                                    <i class="bx bxs-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            @else
                                <thead class="table-light">
                                <tr>
                                    <th>Chương</th>
                                    <th>Hành động</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($chapters as $chapter)
                                    <tr id="chapter_id_{{ $chapter->id }}">
                                        <td>
                                            <a href="{{ route('chapter.detail', [$story->slug, $chapter->slug]) }}" style="text-wrap: wrap">
                                                <b>{{ $chapter->name }}</b>
                                            </a>
                                            <div>Lượt xem: {{ number_format($chapter->view) }}</div>
                                            <div>Ngày đăng: {{ date('d/m/Y H:i:s', strtotime($chapter->created_at)) }}</div>
                                            <div>Trạng thái:@if($chapter->status) Công khai @else Bản nháp @endif</div>
                                            @if($chapter->coin > 0)
                                                <b>Đã khoá chương với {{ number_format($chapter->coin) }} xu</b>
                                            @endif
                                            <div class="form-check form-switch form-check-warning">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                       onclick="quickUpdateStatus(this, 'warning', {{ $chapter->story_id }}, {{ $chapter->id }})"
                                                       id="flexSwitchCheckWarning" value="1" @if($chapter->warning) checked @endif>
                                                <label class="form-check-label" for="flexSwitchCheckWarning">
                                                    Chương có nội dung <br>nhạy cảm (bạo lực, kích thích,...)
                                                </label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-block mb-2">
                                                <a href="{{ route('user.chapter.detail', [$chapter->story_id, $chapter->id]) }}"
                                                   class="btn btn-success btn-sm radius-30 px-4">Chỉnh sửa</a>
                                            </div>
                                            <div class="d-block mb-2">
                                                <a href="{{ route('chapter.detail', [$story->slug, $chapter->slug]) }}"
                                                   target="_blank" class="btn btn-primary btn-sm radius-30 px-4">Xem chương</a>
                                            </div>
                                            <div class="d-block mb-2">
                                                <a href="javascript:void(0)" class="btn btn-danger btn-sm radius-30 px-4"
                                                   onclick="removeChapter({{ $chapter->story_id }}, {{ $chapter->id }}, '{{ $chapter->name }}')">
                                                    Xoá chương
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            @endif
                        </table>
                    </div>

                    <div class="pagination mt-4 justify-content-center">
                        {!! $chapters->withQueryString()->links('vendor.pagination.rocker-pagination') !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        function quickUpdateStatus(e, type, story_id, chapter_id) {
            (function ($) {
                let value = $(e).is(":checked") ? 1 : 0;
                let arr = {
                    "_token": getMetaContentByName("_token"),
                    "story_id": story_id,
                    "chapter_id": chapter_id,
                    "type": type,
                    "value": value
                };

                // let body = $("body");
                $.ajax({
                    type: "POST",
                    url: "{{ route('user.chapter.quickUpdate') }}",
                    data: arr,
                    cache: false,
                    beforeSend: function () {
                        // body.addClass("loading");
                    },
                    success: function (result) {
                        // body.removeClass("loading");
                        if (result.success) {

                        } else {
                            Swal.fire(
                                'Oops...',
                                result.message,
                                'error'
                            );
                        }
                    }
                });
            })(jQuery);
        }

        function removeChapter(story_id, chapter_id, title) {
            (function ($) {
                Swal.fire({
                    title: 'Xoá Chapter',
                    text: 'Bạn muốn xoá ' + title + '?',
                    icon: 'question',
                    showCancelButton: true,
                    cancelButtonText: 'Huỷ',
                    confirmButtonColor: '#0d6efd',
                    confirmButtonText: 'OK',
                }).then((result) => {
                    if (result.isConfirmed) {
                        let arr = {
                            "_token": getMetaContentByName("_token"),
                            "story_id": story_id,
                            "chapter_id": chapter_id,
                            "title": title
                        };

                        let body = $("body");
                        $.ajax({
                            type: "POST",
                            url: "{{ route('ajax.deleteChapter') }}",
                            data: arr,
                            cache: false,
                            beforeSend: function () {
                                body.addClass("loading");
                            },
                            success: function (result) {
                                body.removeClass("loading");
                                if (result.success) {
                                    $('#chapter_id_' + chapter_id).remove();
                                    Swal.fire({
                                        icon: "success",
                                        title: "Thành công",
                                        text: result.message,
                                        showConfirmButton: false,
                                        timer: 1500
                                    });
                                } else {
                                    Swal.fire(
                                        'Oops...',
                                        result.message,
                                        'error'
                                    );
                                }
                            }
                        });
                    }
                })
            })(jQuery);
        }
    </script>
@endsection
