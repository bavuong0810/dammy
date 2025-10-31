@extends('layouts.app')
<?php
    use App\Constants\BaseConstants;
    if (isset($chapter)) {
        $title = $chapter->name;
        $date_update = $chapter->updated_at;
        $content = $chapter->content;
        $content_images = ($chapter->content_images != '') ? json_decode($chapter->content_images, true) : [];
        $coin = $chapter->coin;
        $status = $chapter->status;
        $warning = $chapter->warning;
        $id = $chapter->id;
    } else {
        $title = old('title', '');
        $date_update = date('Y-m-d H:i:s');
        $content = old('content', '');
        $content_images = [];
        $coin = 0;
        $status = BaseConstants::ACTIVE;
        $warning = BaseConstants::INACTIVE;
        $id = 0;
    }
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
    <style>
        .images .item {
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .images .item img {
            height: 250px;
            display: block;
            margin: 0 auto;
            width: 100%;
            object-fit: contain;
        }

        .images .item .trash {
            position: absolute;
            top: 3px;
            right: 3px;
            border-radius: 4px;
            padding: 3px 4px;
            background: red;
            color: #FFF;
        }

        .images .item .trash:hover {
            background: #FFF;
            color: red;
        }
    </style>
    <script src="{{ asset('ckeditor/ckeditor.js') }}?ver={{ env('APP_VERSION') }}"></script>
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
                    <h5 class="card-title">Chapter {{ $title }}</h5>
                    <hr/>
                    <div class="my-3 d-flex justify-content-between">
                        <div class="prev">
                            @if($prev_chapter != '' && count($list_chapters) > 1)
                                <a href="{{ $prev_chapter }}" class="btn btn-secondary btn-sm">Chương trước</a>
                            @endif
                        </div>
                        <div class="list-chapter">
                            <select name="list_chapter" id="list_chapter" class="form-control" style="max-width: 300px">
                                <option value="">Danh sách</option>
                                @foreach($list_chapters as $item)
                                    <option value="{{ route('user.chapter.detail', [$story_id, $item->id]) }}" @if($item->id == $id) style="font-weight: bold; background: #ccc" @endif>
                                        {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="next">
                            @if($next_chapter != '' && count($list_chapters) > 1)
                                <a href="{{ $next_chapter }}" class="btn btn-secondary btn-sm">Chương sau</a>
                            @else
                                @if ($id != 0)
                                    <a href="{{ route('user.chapter.create', $story_id) }}" class="btn btn-success btn-sm">Thêm chương</a>
                                @endif
                            @endif
                        </div>
                    </div>
                    <form action="{{ route('user.chapter.store', $story_id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id" value="{{ $id }}">

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
                            <div class="border border-3 p-4 rounded">
                                <div class="mb-3">
                                    <label for="inputTitle" class="form-label">Tên Chapter</label>
                                    <input type="text" name="title" class="form-control" id="inputTitle"
                                           placeholder="Nhập tên chapter" value="{{ $title }}" required>
                                    @if ($id > 0)
                                        <div class="my-2">
                                            <a href="{{ route('chapter.detail', [$story->slug, $chapter->slug]) }}"
                                               target="_blank">Xem chi tiết chương</a>
                                        </div>
                                    @endif
                                </div>
                                {{--
                                <div class="mb-3">
                                    <label for="inputVolNumber" class="form-label">Thứ tự chương</label>
                                    <input type="number" name="vol_number" class="form-control" id="inputVolNumber"
                                           placeholder="Thứ tự" value="{{ $vol_number }}" required>
                                </div>
                                --}}
                                @if($story->type == 0)
                                    <div class="alert alert-primary border-0 bg-warning alert-dismissible fade show py-3 my-3">
                                        <div class="d-flex align-items-center">
                                            <div class="font-35"><i class="bx bx-message-check"></i>
                                            </div>
                                            <div class="ms-3">
                                                <h6 class="mb-2 font-18">Khuyến Nghị</h6>
                                                <div class="">
                                                    Mỗi chương nên được chia với độ dài khoảng <b>1.100 – 1.200 từ</b> để hệ thống kịp thời cộng view.
                                                    <br>
                                                    Nếu chia chương với số từ quá ít, truyện của bạn có thể sẽ nhận được <b>lượng view thấp hơn</b>.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="content" class="form-label">Nội dung chương</label>
                                        <textarea class="form-control" name="content" id="content"
                                                  rows="8">{!! $content !!}</textarea>
                                    </div>
                                @else
                                    <div class="mb-3">
                                        <h6 class="mb-0 text-uppercase">Nội dung chương</h6>
                                        <hr/>
                                        <div class="row">
                                            <div class="col-xl-9 mx-auto">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <input id="image-uploadify" type="file" name="content_files[]" accept="image/*" multiple>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-3">
                                            <input type="hidden" name="content_images" value="{{ json_encode($content_images) }}" id="content_images">
                                            @if(count($content_images))
                                                <div class="images row align-items-center">
                                                    @foreach($content_images as $image)
                                                        <div class="mb-2 col-md-2">
                                                            <div class="item position-relative" data-src="{{ $image }}" draggable="true">
                                                                <img src="{{ asset($image) }}" alt="">
                                                                <a href="javascript:void(0)" class="trash" onclick="removeContentImage(this)">
                                                                    <i class="bx bx-trash-alt"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                                <div class="mb-3">
                                    <div class="form-check form-switch form-check-warning">
                                        <input class="form-check-input" type="checkbox" role="switch"
                                               id="warning" name="warning" value="1" @if($warning) checked @endif>
                                        <label class="form-check-label" for="warning">
                                            Chương có sử dụng từ ngữ nhạy cảm (bạo lực, kích thích,...)
                                        </label>
                                    </div>
                                </div>

                                {{--
                                <div class="mb-3">
                                    <label for="inputCoin" class="form-label">Số xu cần để xem chương</label>
                                    <input type="number" name="coin" class="form-control" id="inputCoin"
                                           placeholder="Xu" value="{{ $coin }}">
                                </div>
                                --}}
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" name="status"
                                               id="flexSwitchCheckDefault1" value="1" @if($status) checked @endif>
                                        <label class="form-check-label" for="flexSwitchCheckDefault1">Công khai</label>
                                    </div>
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

            $('#list_chapter').on('change', function () {
                window.location.href = $(this).val();
            });

            @if($story->type == 0)
                CKEDITOR.replace('content',{
                    width: '100%',
                    resize_maxWidth: '100%',
                    resize_minWidth: '100%',
                    height:'300',
                });
                CKEDITOR.instances['content'];
            @else
                $('#image-uploadify').imageuploadify();
                $(".images").sortable({
                    stop: function (event, ui) {
                        let content_images = [];
                        $('.images').find('.item').each(function (e) {
                             content_images.push($(this).attr('data-src'));
                        });
                        $('#content_images').val(JSON.stringify(content_images));
                    }
                });
            @endif
        })

        @if($story->type == 1)
            function removeContentImage(e) {
                (function ($) {
                    $(e).parent().parent().remove();
                    let content_images = [];
                    $('.images').find('.item').each(function (e) {
                        content_images.push($(this).attr('data-src'));
                    });
                    $('#content_images').val(JSON.stringify(content_images));
                })(jQuery);
            }
        @endif
    </script>
@endsection
