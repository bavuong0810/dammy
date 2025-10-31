@extends('layouts.app')
@section('seo')
    <?php
    $title = $chapter->story->name . ' - ' . $chapter->name . ' - ' . $settings->where('type', 'site_name')->first()->value;
    $description = $title . ' - ' . $settings->where('type', 'seo_description')->first()->value;
    $keyword = $settings->where('type', 'seo_keyword')->first()->value;

    if ($chapter->story->thumbnail != "") {
        $thumb_img_seo = asset('images/story/' . $chapter->story->thumbnail);
    } else {
        $thumb_img_seo = asset($settings->where('type', 'seo_image')->first()->value);
    }
    $data_seo = array(
        'title' => $title,
        'keywords' => $keyword,
        'description' => $description,
        'og_title' => $title,
        'og_description' => $description,
        'og_url' => Request::url(),
        'og_img' => $thumb_img_seo,
        'current_url' => Request::url(),
        'current_url_amp' => ''
    );
    $seo = WebService::getSEO($data_seo);
    $story = $chapter->story;
    ?>
    @include('partials.seo')
@endsection
@section('content')
    <style>
        .chapter-content {
            -webkit-user-select: none; /* Safari */
            -ms-user-select: none; /* IE 10 and IE 11 */
            user-select: none; /* Standard syntax */
        }

        .signature {
            font-size: 16px !important;
            line-height: 1.5 !important;
        }
    </style>
    <!--start page wrapper -->
    <div class="page-wrapper">
        <div class="page-content page-chapter-detail">
            <div class="container">
                <!--breadcrumb-->
                <div class="page-breadcrumb d-flex align-items-center mb-3">
                    <div class="ps-3">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0 p-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('index') }}">
                                        Trang chủ
                                    </a>
                                </li>
                                <li class="breadcrumb-item" aria-current="page">
                                    <a href="{{ route('story.detail', $chapter->story->slug) }}">
                                        {{ $chapter->story->name }}
                                    </a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">{{ $chapter->name }}</li>
                            </ol>
                        </nav>
                    </div>
                </div>
                <!--end breadcrumb-->

                <input type="hidden" name="story_title" id="story_title"
                       value="{{ $chapter->story->name }}">
                <input type="hidden" name="story_thumbnail" id="story_thumbnail"
                       value="{{ asset('images/story/' . $chapter->story->thumbnail) }}">

                <div class="card">
                    <div class="card-body" id="chapterContentLoading">
                        @include('story.chapter-content')
                    </div>
                </div>

                <div class="mt-4 mb-3 text-center">
                    <button type="button" class="btn btn-danger" onclick="report({{ $chapter->story->id }})">
                        Báo cáo nội dung vi phạm
                    </button>
                </div>
                <div class="text-center">
                    <button type="button" id="chapterLoadComment" class="btn btn-success mt-1" onclick="loadComments({{ $chapter->story->id }})">
                        Xem bình luận
                    </button>
                </div>

                <div class="card mt-5" id="blockComments" style="display: none">
                    <div class="card-body">
                        <h5 class="mb-0 text-uppercase text-primary">Bình luận</h5>
                        <hr>
                        <div class="comment-static">
                            <div class="ajax_load_cmt">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Setting Modal -->
    <div class="modal fade" id="settingModal" tabindex="-1" role="dialog" aria-labelledby="settingModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="settingModalLabel">Cài đặt</h5>
                </div>
                <div class="modal-body">
                    <div class="setting">
                        <div id="optFont" class="input-field mb-2">
                            <label for="selectFont">Font:</label>
                            <select id="selectFont" data-change="changeFont"
                                    class="initialized form-control">
                                <option value="Arial, sans-serif" style="font-family: Arial, sans-serif;">
                                    Arial
                                </option>
                                <option value="Roboto, sans-serif" style="font-family: Roboto, sans-serif;">
                                    Roboto
                                </option>
                                <option value="Tahoma, Geneva, sans-serif"
                                        style="font-family: Tahoma, Geneva, sans-serif;">
                                    Tahoma
                                </option>
                                <option value="'Times New Roman', Times, serif"
                                        style="font-family: 'Times New Roman', Times, serif;">
                                    Times New Roman
                                </option>
                                <option value="'Verdana', Geneva, sans-serif"
                                        style="font-family: 'Verdana', Geneva, sans-serif;">
                                    Verdana
                                </option>
                            </select>
                        </div>
                        <div id="optSize" class="input-field mb-2">
                            <label for="selectSize">Size:</label>
                            <select id="selectSize" data-change="changeSize"
                                    class="initialized form-control">
                                <option value="14">14</option>
                                <option value="16">16</option>
                                <option value="18">18</option>
                                <option value="20">20</option>
                                <option value="22">22</option>
                                <option value="24">24</option>
                                <option value="26">26</option>
                                <option value="28">28</option>
                                <option value="30">30</option>
                            </select>
                        </div>
                        <div id="optLine" class="input-field mb-2">
                            <label for="selectLine">Line:</label>
                            <select id="selectLine" data-change="changeLine"
                                    class="initialized form-control">
                                <option value="120">120%</option>
                                <option value="140">140%</option>
                                <option value="160">160%</option>
                                <option value="180">180%</option>
                                <option value="200">200%</option>
                                <option value="220">220%</option>
                            </select>
                        </div>
                        <div class="mb-3 mt-3">
                            <button type="button" class="btn btn-success" onclick="closeSetting()">Đóng</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('partials.report-story')
@endsection

@section('script')
    <script type="text/javascript">
        jQuery(document).ready(function ($) {
            $('.input-field select').on('change', function () {
                if (typeof (Storage) !== 'undefined') {
                    let settings;
                    if (localStorage.getItem('otruyenChapterSetting') !== null) {
                        let data = localStorage.getItem('otruyenChapterSetting');
                        settings = JSON.parse(data);
                    } else {
                        settings = {
                            "font": 'Roboto, sans-serif',
                            "size": '20',
                            "line": '200',
                        }
                    }

                    let dataChange = $(this).attr('data-change');
                    let value = $(this).val();
                    switch (dataChange) {
                        case 'changeFont':
                            settings.font = value;
                            break;
                        case 'changeSize':
                            settings.size = value;
                            break;
                        default:
                            settings.line = value;
                            break;
                    }

                    localStorage.setItem(
                        'otruyenChapterSetting',
                        JSON.stringify(settings)
                    );

                    let contentContainer = $('.content-container');
                    contentContainer.css('font-family', settings.font);
                    contentContainer.css('font-size', settings.size + 'px');
                    contentContainer.css('line-height', settings.line + '%');
                } else {
                    //Nếu không hỗ trợ
                    console.log('Trình duyệt của bạn không hỗ trợ Storage');
                }
            });

            if (typeof (Storage) !== 'undefined') {
                //Setting font
                let settings;
                if (localStorage.getItem('otruyenChapterSetting') !== null) {
                    let data = localStorage.getItem('otruyenChapterSetting');
                    settings = JSON.parse(data);
                } else {
                    settings = {
                        "font": 'Roboto, sans-serif',
                        "size": '20',
                        "line": '140',
                    }
                }
                $('.input-field select[data-change="changeFont"]').val(settings.font).change();
                $('.input-field select[data-change="changeSize"]').val(settings.size).change();
                $('.input-field select[data-change="changeLine"]').val(settings.line).change();

                let contentContainer = $('.content-container');
                contentContainer.css('font-family', settings.font);
                contentContainer.css('font-size', settings.size + 'px');
                contentContainer.css('line-height', settings.line + '%');
            }

            // Lắng nghe sự kiện `popstate`
            window.addEventListener("popstate", function(event) {
                if (event.state && event.state.data) {
                    // Lấy URL từ trạng thái và tải lại nội dung
                    jQuery('.page-wrapper .page-content .container').html(event.state.data)
                }
            });
        });

        @if(env('APP_ENV') == 'local')
        jQuery(document).ready(function ($) {
            let timeOut = setTimeout(function () {
                var sendInfo = {
                    '_token': getMetaContentByName("_token"),
                    'chapter_id': $('input[name="chapter_id"]').val(),
                    'story_id': $('input[name="story_id"]').val(),
                    'story_slug': '{{ $story->slug }}',
                };
                $.ajax({
                    url: '{{ route('ajax.storyAddView') }}',
                    type: 'POST',
                    data: sendInfo,
                    dataType: 'html',
                    beforeSend: function () {
                    },
                    success: function (data) {
                    },
                    complete: function () {
                    },
                    error: function (errorThrown) {
                        console.log(errorThrown);
                    }
                });
            }, 10.5e4);
            sessionStorage.setItem("_0vx57u3v2", timeOut);

            $('#selected_chapter').on('change', function () {
                let stringSlug = $(this).val();
                let arr = stringSlug.split(',');
                changeChapter(arr[0], arr[1]);
            });

            //lưu lịch sử đọc truyện
            if (typeof (Storage) !== 'undefined') {
                //Nếu có hỗ trợ
                //Thực hiện thao tác với Storage
                let item = {
                    "id": $('#story_id').val(),
                    "title": $('#story_title').val(),
                    "thumbnail": $('#story_thumbnail').val(),
                    "link": "{{ route('story.detail', $chapter->story->slug) }}",
                    "lastChapterId": $('#chapter_id').val(),
                    "lastChapterTitle": $('#chapter_title').val(),
                    "lastChapterLink": "{{ route('chapter.detail', [$chapter->story->slug, $chapter->slug]) }}",
                };
                if (localStorage.getItem('otruyenHistories') !== null) {
                    let data = localStorage.getItem('otruyenHistories');
                    let histories = JSON.parse(data);
                    let check = false;
                    for (let i = 0; i < histories.length; i++) {
                        if (histories[i]['id'] == $('#story_id').val()) {
                            histories[i]['title'] = $('#story_title').val();
                            histories[i]['thumbnail'] = $('#story_thumbnail').val();
                            histories[i]['link'] = "{{ route('story.detail', $chapter->story->slug) }}";
                            histories[i]['lastChapterId'] = $('#chapter_id').val();
                            histories[i]['lastChapterTitle'] = $('#chapter_title').val();
                            histories[i]['lastChapterLink'] = "{{ route('chapter.detail', [$chapter->story->slug, $chapter->slug]) }}";
                            check = true;
                        }
                    }

                    if (!check) {
                        histories.push(item);
                    }

                    localStorage.setItem(
                        'otruyenHistories',
                        JSON.stringify(histories)
                    );
                } else {
                    let histories = [item];
                    localStorage.setItem(
                        'otruyenHistories',
                        JSON.stringify(histories)
                    );
                }
            }
        });
        @else
        jQuery(document).ready(function(t){let e=setTimeout(function(){var e={_token:getMetaContentByName("_token"),chapter_id:t('input[name="chapter_id"]').val(),story_id:t('input[name="story_id"]').val(),story_slug:"{{ $story->slug }}"};t.ajax({url:"{{ route('ajax.storyAddView') }}",type:"POST",data:e,dataType:"html",beforeSend:function(){},success:function(t){},complete:function(){},error:function(t){console.log(t)}})},10.5e4);if(sessionStorage.setItem("_0vx57u3v2",e),t("#selected_chapter").on("change",function(){let e=t(this).val().split(",");changeChapter(e[0],e[1])}),"undefined"!=typeof Storage){let i={id:t("#story_id").val(),title:t("#story_title").val(),thumbnail:t("#story_thumbnail").val(),link:"{{ route('story.detail', $chapter->story->slug) }}",lastChapterId:t("#chapter_id").val(),lastChapterTitle:t("#chapter_title").val(),lastChapterLink:"{{ route('chapter.detail', [$chapter->story->slug, $chapter->slug]) }}"};if(null!==localStorage.getItem("otruyenHistories")){let a=JSON.parse(localStorage.getItem("otruyenHistories")),l=!1;for(let r=0;r<a.length;r++)a[r].id==t("#story_id").val()&&(a[r].title=t("#story_title").val(),a[r].thumbnail=t("#story_thumbnail").val(),a[r].link="{{ route('story.detail', $chapter->story->slug) }}",a[r].lastChapterId=t("#chapter_id").val(),a[r].lastChapterTitle=t("#chapter_title").val(),a[r].lastChapterLink="{{ route('chapter.detail', [$chapter->story->slug, $chapter->slug]) }}",l=!0);l||a.push(i),localStorage.setItem("otruyenHistories",JSON.stringify(a))}else localStorage.setItem("otruyenHistories",JSON.stringify([i]))}});
        @endif

        function openSetting() {
            (function ($) {
                $('#settingModal').modal('show');
            })(jQuery);
        }

        function closeSetting() {
            (function ($) {
                $('#settingModal').modal('hide');
            })(jQuery);
        }

        function affLinkClick() {
            setCookie("affShowInChapter", "true", 0.15);
            $('.affActive').show();
            $('.affClick').hide();
        }
    </script>
@endsection
