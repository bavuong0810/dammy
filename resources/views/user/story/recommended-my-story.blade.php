@extends('layouts.app')
@section('seo')
    <?php
    use Jenssegers\Agent\Agent;
    $seo_title = $settings->where('type', 'seo_title')->first()->value;
    $seo_description = $settings->where('type', 'seo_description')->first()->value;
    $data_seo = array(
        'title' => 'Đề cử truyện của Team | ' . $seo_title,
        'keywords' => $settings->where('type', 'seo_keyword')->first()->value,
        'description' => $seo_description,
        'og_title' => 'Đề cử truyện của Team | ' . $seo_title,
        'og_description' => $seo_description,
        'og_url' => Request::url(),
        'og_img' => asset($settings->where('type', 'seo_image')->first()->value),
        'current_url' => Request::url(),
        'current_url_amp' => ''
    );
    $seo = WebService::getSEO($data_seo);
    $agent = new Agent();
    ?>
    @include('partials.seo')
@endsection
@section('content')
    <link href="{{ asset('plugins/select2/css/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}" rel="stylesheet" />
    <!--start page wrapper -->
    <div class="page-wrapper">
        <div class="page-content">
            <!--breadcrumb-->
            <div class="page-breadcrumb d-none d-sm-d-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">Tủ truyện</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route('user.dashboard') }}"><i
                                        class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Đề cử truyện của Team</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!--end breadcrumb-->

            <div class="container">
                <div class="card list-user-stories">
                    <div class="card-body">
                        @if($recommendedStory)
                            {{--Danh sách truyện đã được đăng ký--}}
                            <div class="d-lg-d-flex align-items-center mb-4 gap-3">
                                <?php
                                $blockStory = '';
                                foreach ($recommendedStory as $item) {
                                    $link = route('story.detail', [$item->slug]);

                                    $rating = '';
                                    if ($item->rating > 0) {
                                        $rating = '<span>⭐ ' . number_format($item->rating) . '</span>';
                                    }

                                    $blockStory .= '<div class="single-story-block">
                                    <div class="single-story-wrap">
                                        <div class="single-story-img position-relative">
                                            <a href="' . $link . '">
                                                <img
                                                    class="lazyload" src="' . asset('img/ajax-loading.gif') . '"
                                                    data-src="' . asset('images/story/thumbs/230/' . $item->thumbnail) . '"
                                                    alt="' . $item->name . '" width="200" height="260"
                                                    onerror="this.src=\'' . asset('img/no-image.png') . '\'"
                                                />
                                            </a>
                                            <div class="story-meta-data d-flex justify-content-start">
                                                <span><i class="bx bx-show"></i> ' . number_format($item->total_view) . '</span>
                                                <span><i class="bx bx-bookmark-alt"></i> ' . number_format($item->total_bookmark) . '</span>
                                                ' . $rating . '
                                            </div>
                                        </div>
                                    </div>
                                    <div class="single-story-details">
                                        <h3><a href="' . $link . '">' . $item->name . '</a></h3>
                                    </div>
                                </div>';
                                }
                                ?>
                                <div class="top-story-slider owl-carousel" id="propose-story-slider">
                                    {!! $blockStory !!}
                                </div>
                            </div>
                        @endif
                        {{--Form đăng ký--}}
                        <div class="mt-4">
                            <div class="mb-3">
                                <label for="userStory" class="fw-bold">Truyện đã chọn</label>
                                <select name="userStory[]" id="userStory" class="form-control" multiple="multiple">
                                    <option value="">Chọn truyện</option>
                                    @foreach($userStories as $story)
                                        <option value="{{ $story->id }}" @if(in_array($story->id, $myStories)) selected @endif
                                                data-title="{{ $story->name }}">
                                            {{ $story->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3 text-center">
                                <button class="btn btn-success" onclick="registerRecommendedStory()">Thiết lập</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>
    <script>
        jQuery(document).ready(function ($) {
            $('#userStory').select2();
            $(".top-story-slider").owlCarousel({
                autoplay: false,
                margin: 20,
                touchDrag: true,
                mouseDrag: true,
                dots: false,
                autoplayTimeout: 4000,
                autoplaySpeed: 1200,
                responsive: {
                    0: {
                        items: 2
                    },
                    480: {
                        items: 2
                    },
                    600: {
                        items: 2
                    },
                    750: {
                        items: 3
                    },
                    1000: {
                        items: 4
                    },
                    1200: {
                        items: 6
                    }
                }
            });
        });

        function registerRecommendedStory() {
            (function ($) {
                let value = $('#userStory').val();
                if (value.length > 10) {
                    Swal.fire(
                        'Oops...',
                        'Chỉ được chọn tối đa 10 truyện.',
                        'error'
                    );
                    return;
                }

                Swal.fire({
                    title: 'Thiết lập đề cử',
                    text: 'Đề cử các truyện đã chọn?',
                    icon: 'question',
                    showCancelButton: true,
                    cancelButtonText: 'Huỷ',
                    confirmButtonColor: '#0d6efd',
                    confirmButtonText: 'Lưu',
                }).then((result) => {
                    if (result.isConfirmed) {
                        let arr = {
                            "_token": getMetaContentByName("_token"),
                            "story_ids": $('#userStory').val(),
                        };

                        let body = $("body");
                        $.ajax({
                            type: "POST",
                            url: "{{ route('user.processRecommendedMyStories') }}",
                            data: arr,
                            cache: false,
                            beforeSend: function () {
                                body.addClass("loading");
                            },
                            success: function (result) {
                                body.removeClass("loading");
                                if (result.success) {
                                    Swal.fire({
                                        title: 'Thành công',
                                        text: 'Đề cử truyện thành công!',
                                        icon: 'success',
                                        showCancelButton: false,
                                        confirmButtonColor: '#0d6efd',
                                        confirmButtonText: 'OK',
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            location.reload();
                                        }
                                    })
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
