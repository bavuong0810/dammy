@extends('layouts.app')
<?php
    if (isset($story)) {
        $title = $story->name;
        $name = $story->name;
        $slug = $story->slug;
        $author = $story->author;
        $content = $story->content;
        $categories = ($story->categories != '') ? json_decode($story->categories, true) : [];
        $total_view = $story->total_view;
        $is_full = $story->is_full;
        $thumbnail = $story->thumbnail;
        $type = $story->type;
        $creative = $story->creative;
        $date_update = $story->updated_at;
        $id = $story->id;
        $link_url_check = route('story.detail', $story->slug);
    } else {
        $title = 'Thêm truyện mới';
        $name = old('title', '');
        $author = old('author', '');
        $content = old('content', '');
        $categories = old('category_item', []);
        $total_view = 0;
        $is_full = old('is_full', \App\Constants\BaseConstants::INACTIVE);
        $thumbnail = old('thumbnail', '');
        $type = \App\Constants\BaseConstants::INACTIVE;
        $creative = 0;
        $date_update = date('Y-m-d H:i:s');
        $id = 0;
        $link_url_check = '';
    }
?>
@section('seo')
<?php
$seo_title = $settings->where('type', 'seo_title')->first()->value;
$seo_description = $settings->where('type', 'seo_description')->first()->value;
$data_seo = array(
    'title' => $title.' | ' . $seo_title,
    'keywords' => $settings->where('type', 'seo_keyword')->first()->value,
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
    @if($id == 0 || (isset($schedule) && $schedule))
        <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet" />
    @endif
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
                            <li class="breadcrumb-item active" aria-current="page">{{ $title }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!--end breadcrumb-->

            <div class="card">
                <div class="card-body p-4">
                    <h5 class="card-title">{{ $title }}</h5>
                    <hr/>
                    <form action="{{ route('user.story.store') }}" method="POST" enctype="multipart/form-data">
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
                            <div class="row">
                                <div class="col-lg-8">
                                    <div class="border border-3 p-4 rounded">
                                        <div class="mb-3">
                                            <label for="inputTitle" class="form-label">Tên truyện</label>
                                            <input type="text" name="title" class="form-control" id="inputTitle"
                                                   placeholder="Nhập tên truyện" value="{{ $name }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="inputAuthor" class="form-label">Tác giả</label>
                                            <input type="text" name="author" class="form-control"
                                                   id="inputAuthor" value="{{ $author }}">
                                        </div>
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" role="switch" value="1"
                                                   id="creative" name="creative" @if($creative == 1) checked @endif >
                                            <label class="form-check-label" for="creative">Truyện sáng tác</label>
                                        </div>
                                        <div class="mb-3">
                                            <label for="inputContent" class="form-label">Mô tả truyện</label>
                                            <textarea class="form-control" name="content" id="inputContent"
                                                      rows="3">{!! $content !!}</textarea>
                                        </div>
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" role="switch" value="1"
                                                   id="is_full" name="is_full" @if($is_full == 1) checked @endif >
                                            <label class="form-check-label" for="is_full">Đủ bộ</label>
                                        </div>
                                        <div class="mb-3">
                                            <div class="form-group">
                                                <label for="thumbnail_file">Ảnh bìa</label>
                                                <div class="input-group">
                                                    <div class="custom-file">
                                                        <input type="file" name="thumbnail_file" class="form-control"
                                                               accept="image/*" id="thumbnail_file">
                                                        <input type="hidden" name="thumbnail_file_link" class="form-control"
                                                               id="thumbnail_file_link" value="{{$thumbnail}}">
                                                    </div>
                                                </div>
                                                @if($thumbnail != "")
                                                    <div class="demo-img" style="padding-top: 15px;">
                                                        <img src="{{ asset('images/story/thumbs/230/' . $thumbnail) }}" width="290">
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="border border-3 p-4 rounded">
                                        <div class="row g-3">
                                            <div class="col-md-12">
                                                <h6><b>Thể loại</b></h6>
                                                <?php
                                                $list_cate = App\Models\Category::orderBy('name', 'ASC')->select('name', 'id')->get();
                                                ?>
                                                <div class="list_category row">
                                                    <div class="col-md-6">
                                                        <?php
                                                            $count_item = count($list_cate);
                                                        ?>
                                                        @foreach($list_cate as $key => $cate)
                                                            <div class="form-check form-check-info">
                                                                <input class="form-check-input" type="checkbox" value="{{ $cate->id }}"
                                                                       id="category_item_{{ $cate->id }}" name="category_item[]"
                                                                       @if(in_array($cate->id, $categories)) checked @endif>
                                                                <label class="form-check-label" for="category_item_{{ $cate->id }}">
                                                                    {{ $cate->name }}
                                                                </label>
                                                            </div>
                                                            @if($key == floor($count_item / 2))
                                                                </div>
                                                                <div class="col-md-6">
                                                            @endif
                                                        @endforeach
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="col-12">
                                                @if ((isset($schedule) && $schedule) || $id == 0)
                                                    <div class="mb-3">
                                                        <label class="form-label" for="schedule_time"><b>Hẹn giờ lên truyện (Để huỷ hẹn giờ, chỉ cần chọn lại thời gian nhỏ hơn thời gian hiện tại)</b></label>
                                                        <input type="text" class="form-control date-time"
                                                               name="schedule_time" id="schedule_time"/>
                                                    </div>
                                                @endif
                                                <div class="d-grid">
                                                    <button type="submit" class="btn btn-primary">Lưu</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div><!--end row-->
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    @if($id == 0 || (isset($schedule) && $schedule))
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="https://npmcdn.com/flatpickr/dist/l10n/vn.js"></script>
    @endif
    <script type="text/javascript">
        @if($id == 0 || (isset($schedule) && $schedule))
            <?php
                $defaultDate = date('d-m-Y H:i');
                if ((isset($schedule) && $schedule)) {
                    $defaultDate = date('d-m-Y H:i', strtotime($schedule->time));
                }
            ?>
            jQuery(document).ready(function ($) {
                $(".date-time").flatpickr({
                    enableTime: true,
                    dateFormat: "d-m-Y H:i",
                    locale: "vn",
                    time_24hr: true,
                    defaultDate: "{{ $defaultDate }}",
                });
            });
        @endif

        addEventListener("submit", (event) => {
            let body = $('body');
            body.addClass("loading");
        });

        CKEDITOR.replace('inputContent',{
            width: '100%',
            resize_maxWidth: '100%',
            resize_minWidth: '100%',
            height:'300',
        });
        CKEDITOR.instances['inputContent'];
    </script>
@endsection
