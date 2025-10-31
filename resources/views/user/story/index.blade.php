@extends('layouts.app')
@section('seo')
<?php
use Jenssegers\Agent\Agent;
$seo_title = $settings->where('type', 'seo_title')->first()->value;
$seo_description = $settings->where('type', 'seo_description')->first()->value;
$data_seo = array(
    'title' => 'Truyện của bạn | ' . $seo_title,
    'keywords' => $settings->where('type', 'seo_keyword')->first()->value,
    'description' => $seo_description,
    'og_title' => 'Truyện của bạn | ' . $seo_title,
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
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">Tủ truyện</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route('user.dashboard') }}"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Truyện của bạn</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!--end breadcrumb-->

            <div class="card list-user-stories">
                <div class="card-body">
                    <div class="d-lg-flex align-items-center mb-4 gap-3">
                        <div class="position-relative">
                            <form action="{{ route('user.story.index') }}" method="GET">
                                <input type="text" name="search" class="form-control ps-5 radius-30"
                                       placeholder="Tìm truyện..." value="{{ request()->get('search') }}">
                                <span class="position-absolute top-50 product-show translate-middle-y"><i class="bx bx-search"></i></span>
                            </form>
                        </div>
                        <div class="ms-auto"><a href="{{ route('user.story.create') }}" class="btn btn-primary radius-30 mt-2 mt-lg-0"><i class="bx bxs-plus-square"></i>Thêm truyện mới</a></div>
                    </div>
                    <div class="table-responsive">
                        <table class="table mb-0">
                            @if(!$agent->isMobile())
                                <thead class="table-light">
                                <tr>
                                    <th>Truyện</th>
                                    <th class="text-center" style="width: 250px;">Hình ảnh</th>
                                    <th class="text-center">Lượt xem</th>
                                    <th>Trạng thái</th>
                                    <th class="text-center">Ngày cập nhật</th>
                                    <th>Hành động</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($list as $story)
                                    <tr id="story_id_{{ $story->id }}">
                                        <td>
                                            {{ $story->name }}
                                            <div class="d-block">
                                                <a href="{{ route('story.detail', $story->slug) }}" target="_blank"
                                                   class="font-weight-bold" style="color: #008cff; font-weight: bold; text-decoration: underline;">
                                                    Xem truyện
                                                </a>
                                            </div>
                                        </td>
                                        <td><img src="{{ asset('images/story/thumbs/230/' . $story->thumbnail) }}"
                                                 alt="{{ $story->name }}" onerror="this.src='{{ asset('img/no-image.png') }}'"
                                                 style="width: 50%; display: block; padding: 5px; border-radius: 20px; margin: 0 auto"></td>
                                        <td class="text-center">{{ number_format($story->total_view) }}</td>
                                        <td>
                                            <div class="d-flex justify-content-center flex-column-reverse">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" role="switch" onclick="quickUpdateStatus(this, 'is_full', {{ $story->id }})"
                                                           id="flexSwitchCheckDefault_{{ $story->id }}" value="1" @if($story->is_full) checked @endif>
                                                    <label class="form-check-label" for="flexSwitchCheckDefault_{{ $story->id }}">Hoàn thành</label>
                                                </div>
                                                <div class="form-check form-switch form-check-success">
                                                    <input class="form-check-input" type="checkbox" role="switch" onclick="quickUpdateStatus(this, 'creative', {{ $story->id }})"
                                                           id="flexSwitchCheckSuccess_{{ $story->id }}" value="1" @if($story->creative) checked @endif>
                                                    <label class="form-check-label" for="flexSwitchCheckSuccess_{{ $story->id }}">Truyện sáng tác</label>
                                                </div>
                                                <div class="form-check form-switch form-check-warning">
                                                    <input class="form-check-input" type="checkbox" role="switch" onclick="quickUpdateStatus(this, 'warning', {{ $story->id }})"
                                                           id="flexSwitchCheckWarning_{{ $story->id }}" value="1" @if($story->warning) checked @endif>
                                                    <label class="form-check-label" for="flexSwitchCheckWarning_{{ $story->id }}">Truyện có sử dụng một số từ ngữ
                                                        <br>nhạy cảm (bạo lực, kích thích,...)</label>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">{{ date('d/m/Y', strtotime($story->updated_at)) }}</td>
                                        <td>
                                            <div class="d-block mb-2">
                                                <a href="{{ route('user.story.detail', $story->id) }}"
                                                   class="btn btn-primary btn-sm radius-30 px-4">Chỉnh sửa</a>
                                            </div>
                                            <div class="d-block mb-2">
                                                <a href="{{ route('user.chapter.index', $story->id) }}"
                                                   class="btn btn-warning btn-sm radius-30 px-4">Danh sách chương</a>
                                            </div>
                                            <div class="d-block mb-2">
                                                <a href="{{ route('user.chapter.bulkPosting', $story->id) }}"
                                                   class="btn btn-info btn-sm radius-30 px-4">Đăng nhiều chương</a>
                                            </div>
                                            <div class="d-block mb-2">
                                                <a href="{{ route('user.chapter.create', $story->id) }}"
                                                   class="btn btn-secondary btn-sm radius-30 px-4">Đăng chương mới</a>
                                            </div>
                                            <div class="d-block mb-2">
                                                <a href="{{ route('user.story.comments', $story->id) }}"
                                                   class="btn btn-success btn-sm radius-30 px-4">
                                                    Quản lý bình luận
                                                </a>
                                            </div>
                                            <div class="d-block mb-2">
                                                <a href="javascript:void(0)" class="btn btn-danger btn-sm radius-30 px-4"
                                                   onclick="removeStory({{ $story->id }}, '{{ $story->name }}')">
                                                    Xoá truyện
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            @else
                                <thead class="table-light">
                                <tr>
                                    <th style="width: 55%">Truyện</th>
                                    <th>Hành động</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($list as $story)
                                    <tr id="story_id_{{ $story->id }}">
                                        <td>
                                            <div class="d-flex justify-content-between">
                                                <div class="image" style="width: 35%">
                                                    <img src="{{ asset('images/story/' . $story->thumbnail) }}"
                                                         alt="{{ $story->name }}" style="display:block; width: 100%;"
                                                         onerror="this.src='{{ asset('img/no-image.png') }}'">
                                                </div>
                                                <div class="info" style="width: 65%; padding: 0 10px; text-wrap: wrap; font-size: 13px">
                                                    <a href="{{ route('user.story.detail', $story->id) }}"
                                                       class="text-uppercase">
                                                        <b>{{ $story->name }}</b>
                                                    </a>
                                                    <div class="d-block">
                                                        <a href="{{ route('story.detail', $story->slug) }}" target="_blank"
                                                           class="font-weight-bold" style="color: #008cff; font-weight: bold; text-decoration: underline;">
                                                            Xem truyện
                                                        </a>
                                                    </div>
                                                    <div>Lượt xem: {{ number_format($story->total_view) }}</div>
                                                    <div class="d-flex justify-content-center flex-column-reverse mt-2">
                                                        <div class="form-check form-switch form-check-warning">
                                                            <input class="form-check-input" type="checkbox" role="switch" onclick="quickUpdateStatus(this, 'warning', {{ $story->id }})"
                                                                   id="flexSwitchCheckWarning_{{ $story->id }}" value="1" @if($story->warning) checked @endif>
                                                            <label class="form-check-label" for="flexSwitchCheckWarning_{{ $story->id }}">Truyện có sử dụng một số từ ngữ
                                                                <br>nhạy cảm (bạo lực, kích thích,...)</label>
                                                        </div>
                                                        <div class="form-check form-switch form-check-success">
                                                            <input class="form-check-input" type="checkbox" role="switch" onclick="quickUpdateStatus(this, 'creative', {{ $story->id }})"
                                                                   id="flexSwitchCheckSuccess_{{ $story->id }}" value="1" @if($story->creative) checked @endif>
                                                            <label class="form-check-label" for="flexSwitchCheckSuccess_{{ $story->id }}">Truyện sáng tác</label>
                                                        </div>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" role="switch" onclick="quickUpdateStatus(this, 'is_full', {{ $story->id }})"
                                                                   id="flexSwitchCheckDefault_{{ $story->id }}" value="1" @if($story->is_full) checked @endif>
                                                            <label class="form-check-label" for="flexSwitchCheckDefault_{{ $story->id }}">Hoàn thành</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-block mb-2">
                                                <a href="{{ route('user.story.detail', $story->id) }}"
                                                   class="btn btn-primary btn-sm radius-30 px-2 font-13">Chỉnh sửa</a>
                                            </div>
                                            <div class="d-block mb-2">
                                                <a href="{{ route('user.chapter.index', $story->id) }}"
                                                   class="btn btn-warning btn-sm radius-30 px-2 font-13">DS chương</a>
                                            </div>
                                            <div class="d-block mb-2">
                                                <a href="{{ route('user.chapter.bulkPosting', $story->id) }}"
                                                   class="btn btn-success btn-sm radius-30 px-2 font-13">Đăng nhiều chương</a>
                                            </div>
                                            <div class="d-block mb-2">
                                                <a href="{{ route('user.chapter.create', $story->id) }}"
                                                   class="btn btn-info btn-sm radius-30 px-2 font-13">Đăng chương</a>
                                            </div>
                                            <div class="d-block mb-2">
                                                <a href="{{ route('user.story.comments', $story->id) }}"
                                                   class="btn btn-success btn-sm radius-30 px-2 font-13">QL bình luận</a>
                                            </div>
                                            <div class="d-block mb-2">
                                                <a href="javascript:void(0)" class="btn btn-danger btn-sm radius-30 px-2 font-13"
                                                   onclick="removeStory({{ $story->id }}, '{{ $story->name }}')">
                                                    Xoá truyện
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
                        {!! $list->withQueryString()->links('vendor.pagination.rocker-pagination') !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script type="text/javascript">
        function quickUpdateStatus(e, type, story_id) {
            (function ($) {
                let value = $(e).is(":checked") ? 1 : 0;
                let arr = {
                    "_token": getMetaContentByName("_token"),
                    "story_id": story_id,
                    "type": type,
                    "value": value
                };

                // let body = $("body");
                $.ajax({
                    type: "POST",
                    url: "{{ route('user.story.quickUpdate') }}",
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
        function removeStory(story_id, title) {
            (function ($) {
                Swal.fire({
                    title: 'Xoá truyện',
                    text: 'Bạn muốn xoá truyện ' + title + '?',
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
                            "title": title
                        };

                        let body = $("body");
                        $.ajax({
                            type: "DELETE",
                            url: "{{ route('user.story.delete') }}",
                            data: arr,
                            cache: false,
                            beforeSend: function () {
                                body.addClass("loading");
                            },
                            success: function (result) {
                                body.removeClass("loading");
                                if (result.success) {
                                    $('#story_id_' + story_id).remove();
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
