@extends('layouts.app')
@section('seo')
    <?php
    use Jenssegers\Agent\Agent;
    $seo_title = $settings->where('type', 'seo_title')->first()->value;
    $seo_description = $settings->where('type', 'seo_description')->first()->value;
    $data_seo = array(
        'title' => 'Đăng ký truyện đề cử | ' . $seo_title,
        'keywords' => $settings->where('type', 'seo_keyword')->first()->value,
        'description' => $seo_description,
        'og_title' => 'Đăng ký truyện đề cử | ' . $seo_title,
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
                            <li class="breadcrumb-item active" aria-current="page">Đăng ký truyện đề cử</li>
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
                                <div class="recommendedStory owl-carousel owl-theme">
                                    <div class="grid grid-cols-1 gap-y-4">
                                        <?php $k = 1; ?>
                                        @foreach($recommendedStory->stories as $key => $item)
                                            <div class="d-flex space-x-3 pt-3 px-3">
                                                <div class="flex-shrink-0">
                                                    <a href="{{ route('story.detail', $item->slug) }}"
                                                       title="{{ $item->name }}" class="text-title font-weight-bold">
                                                        <img onerror="this.src='{{ asset('img/no-image.png') }}'"
                                                             class="h-32 w-24 shadow-xl rounded" alt="{{ $item->name }}"
                                                             src="{{ asset('images/story/thumbs/230/' . $item->thumbnail) }}">
                                                    </a>
                                                </div>
                                                <div class="space-y-2">
                                                    <div>
                                                        <a href="{{ route('story.detail', $item->slug) }}" title="{{ $item->name }}"
                                                           class="text-title fw-bold">
                                                            {{ $item->name }}
                                                        </a>
                                                    </div>
                                                    <div class="text-gray-500 text-overflow-multiple-lines">
                                                        {!! WebService::excerpts(htmlspecialchars_decode($item->content), 100) !!}
                                                    </div>
                                                    <div class="d-flex justify-content-between items-center space-x-2 pt-1">
                                                        <div class="d-flex grow-0 align-items-center space-x-1">
                                                            <a href="{{ route('translateTeam.detail', $item->user->id) }}"
                                                               class="text-title"><i class="bx bx-user" style="margin-right: 5px;"></i>{{ $item->user->name }}</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @if($k == 3)
                                            </div>
                                            <div class="grid grid-cols-1 gap-y-4">
                                            <?php $k = 0; ?>
                                            @endif
                                            <?php $k++; ?>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                        {{--Form đăng ký--}}
                        <div class="mt-4">
                            <?php
                                $totalStoryCanRegister = 12;
                                if($recommendedStory) {
                                    $totalStoryCanRegister = 12 - count($recommendedStory->stories);
                                }
                            ?>
                            @if ($totalStoryCanRegister > 0)
                                <div class="alert alert-primary border-0 bg-primary alert-dismissible fade show py-2">
                                    <div class="d-flex align-items-center">
                                        <div class="font-35 text-white"><i class="bx bx-bell"></i>
                                        </div>
                                        <div class="ms-3">
                                            <h6 class="mb-2 text-white font-18">Thông báo</h6>
                                            <div class="text-white">Còn lại <b>{{ $totalStoryCanRegister }}</b> vị trí để đăng ký cho ngày {{ $tomorrowDateString }}. <br>
                                                Với mỗi lượt đăng ký sẽ mất 2.000 xu cho một ngày. <br>
                                                <b>Vui lòng đợi 1-2 phút sau khi nhận thông báo thành công</b>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="userStory" class="fw-bold">Truyện muốn đăng ký</label>
                                    <select name="userStory" id="userStory" class="form-control">
                                        <option value="">Chọn truyện</option>
                                        @foreach($userStories as $story)
                                            <option value="{{ $story->id }}" data-title="{{ $story->name }}">{{ $story->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3 text-center">
                                    <button class="btn btn-success" onclick="registerRecommendedStory()">Đăng ký</button>
                                </div>
                            @else
                                <div class="alert alert-danger border-0 bg-danger alert-dismissible fade show py-2">
                                    <div class="d-flex align-items-center">
                                        <div class="font-35 text-white"><i class="bx bx-bell"></i>
                                        </div>
                                        <div class="ms-3">
                                            <h6 class="mb-2 text-white font-18">Thông báo</h6>
                                            <div class="text-white">Danh sách đề cử đã đầy, vui lòng quay lại vào lúc 18:00 tối ngày mai.</div>
                                        </div>
                                    </div>
                                </div>
                            @endif
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
            $(".recommendedStory").owlCarousel({
                autoplay: true,
                margin: 40,
                touchDrag: true,
                mouseDrag: true,
                dots: true,
                autoplayTimeout: 5000,
                autoplaySpeed: 1200,
                responsive: {
                    0: {
                        items: 1
                    },
                    480: {
                        items: 1
                    },
                    600: {
                        items: 1
                    },
                    750: {
                        items: 2
                    },
                    1000: {
                        items: 2
                    },
                    1200: {
                        items: 2
                    }
                }
            });
        });

        function registerRecommendedStory() {
            (function ($) {
                if ($('#userStory').val() == '') {
                    Swal.fire(
                        'Oops...',
                        'Vui lòng chọn truyện',
                        'error'
                    );
                    return;
                }

                let title = $('#userStory option:selected').attr('data-title');
                Swal.fire({
                    title: 'Đăng ký đề cử',
                    text: 'Bạn muốn đăng ký đề cử truyện ' + title + '?',
                    icon: 'question',
                    showCancelButton: true,
                    cancelButtonText: 'Huỷ',
                    confirmButtonColor: '#0d6efd',
                    confirmButtonText: 'Đăng ký',
                }).then((result) => {
                    if (result.isConfirmed) {
                        let arr = {
                            "_token": getMetaContentByName("_token"),
                            "story_id": $('#userStory').val(),
                        };

                        let body = $("body");
                        $.ajax({
                            type: "POST",
                            url: "{{ route('user.registerRecommendedStoryProcess') }}",
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
                                        text: result.message,
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
