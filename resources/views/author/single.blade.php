@extends('layouts.app')
@section('seo')
    <?php
    $title = $author->name . ' - ' . $settings->where('type', 'site_name')->first()->value;
    $description = $title . ' - ' . $settings->where('type', 'seo_description')->first()->value;
    $keyword = $settings->where('type', 'seo_keyword')->first()->value;
    $thumb_img_seo = asset($settings->where('type', 'seo_image')->first()->value);
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
    ?>
    @include('partials.seo')
@endsection
@section('content')
    <!--start page wrapper -->
    <div class="page-wrapper page-author-detail">
        <div class="page-content">
            <div class="container">
                <div class="card">
                    <div class="card-body">
                        <div class="row g-0">
                            <div class="col-md-2">
                                <div class="m-2 text-center">
                                    <img src="{{ asset('images/avatar/thumbs/230/' . $author->avatar) }}" class="img-fluid"
                                         alt="{{ $author->name }}"
                                         style="border: 3px solid #ccc; width: 100px; border-radius: 50%; height: 100px; object-fit: cover"
                                         onerror="this.src='{{ asset('img/no-image.png') }}'">
                                </div>
                            </div>
                            <div class="col-md-10">
                                <div class="position-relative">
                                    <h1 class="card-title">{{ $author->name }}</h1>
                                    @if($author->about_me != '')
                                        <div class="mt-3 card-text fs-6 description">
                                            {!! nl2br($author->about_me) !!}
                                        </div>
                                    @endif
                                    <div class="more-info mt-2">
                                        @if (Auth::check() && Auth::user()->id == $author->id)
                                            <div class="mb-1">
                                                <b>Xu hiện có:</b> {{ number_format(Auth::user()->coin->coin) }} xu
                                            </div>
                                        @endif
                                        <div class="mb-1"><b>Lượt theo dõi:</b> {{ number_format($author->total_follow) }}</div>
                                        <div class="mb-1"><b>Lượt xem:</b> {{ number_format($author->total_view) }}</div>
                                        <div class="mb-1"><b>Số truyện:</b> {{ number_format($author->total_stories) }}</div>
                                    </div>

                                    <div class="d-flex gap-3 mt-3 flex-wrap">
                                        <button class="btn btn-sm btn-danger px-3 radius-30" onclick="donate({{ $author->id }})">
                                            <i class="bx bx-dollar-circle"></i>Donate
                                        </button>

                                        @if($is_follow)
                                            <button type="button" id="btnBookmarkDetail" onclick="unfollowAuthor({{ $author->id }})"
                                                    class="btn btn-secondary btn-sm px-3 radius-30">
                                                <i class='bx bx-bookmark-alt font-18 me-1'></i>Huỷ theo dõi
                                            </button>
                                        @else
                                            <button type="button" id="btnBookmarkDetail" onclick="followAuthor({{ $author->id }})"
                                                    class="btn btn-primary btn-sm px-3 radius-30">
                                                <i class='bx bx-bookmark-alt font-18 me-1'></i>Theo dõi
                                            </button>
                                        @endif
                                    </div>

                                    <div class="mt-3">
                                        @if (Auth::check() && Auth::user()->id == $author->id)
                                            <a class="btn btn-sm btn-warning px-3 radius-30 text-uppercase" href="{{ route('user.recommendedMyStories') }}">
                                                <i class="bx bx-slider"></i> Thiết lập danh sách truyện hay
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if (
                            !Auth::check()
                            || (Auth::check() && Auth::user()->premium_date == '')
                            || (Auth::check() && Auth::user()->premium_date != '' && strtotime(Auth::user()->premium_date) < time())
                        )
                                <?php $adsPosition2 = Helpers::get_option_by_key($getOptions, 'clickadu-banner-300x100'); ?>
                            @if($adsPosition2 != '')
                                <div class="d-flex justify-content-center my-3">
                                    {!! $adsPosition2 !!}
                                </div>
                            @endif
                        @endif
                    </div>
                </div>

                @if(isset($recommendedStory) && count($recommendedStory))
                    <div class="card">
                        <div class="card-body">
                            <div class="position-relative">
                                <h4 class="text-uppercase" style="font-size: 20px">Truyện tâm đắc nhà {{ $author->name }}</h4>
                            </div>
                            <hr>
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
                    </div>
                @endif

                @if(isset($topStoryMonth) && count($topStoryMonth))
                    <div class="card">
                        <div class="card-body">
                            <div class="position-relative">
                                <h4 class="text-uppercase" style="font-size: 20px">Truyện có nhiều lượt xem trong tháng</h4>
                            </div>
                            <hr>
                                <?php
                                $blockStory = '';
                                foreach ($topStoryMonth as $item) {
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
                    </div>
                @endif

                <div class="card">
                    <div class="card-body">
                        <div class="position-relative">
                            <h4 class="text-uppercase" style="font-size: 20px">Danh sách truyện</h4>
                        </div>
                        @if (Auth::check() && Auth::user()->id == $author->id)
                            <div class="d-lg-flex align-items-center mt-4 gap-3">
                                <div class="position-relative">
                                    <form action="{{ route('translateTeam.detail', $author->id) }}" method="GET">
                                        <input type="text" name="search" class="form-control ps-5 radius-30" placeholder="Tìm truyện..." value="{{ strip_tags(request()->get('search')) }}">
                                        <span class="position-absolute top-50 product-show translate-middle-y"><i class="bx bx-search"></i></span>
                                    </form>
                                </div>
                                <div class="ms-auto">
                                    <a href="{{ route('user.story.create') }}" class="btn btn-sm btn-primary radius-30 mt-2 mt-lg-0">
                                        <i class="bx bxs-plus-square"></i>Thêm truyện
                                    </a>
                                </div>
                            </div>
                        @endif

                        <hr>
                        <div class="list-stories">
                            <div class="row product-grid">
                                <?php
                                if(request()->get('search') == '') {
                                    $list = $list->data;
                                }
                                ?>
                                @foreach($list as $item)
                                        <?php
                                        $hot_icon = '';
                                        if ($item->is_full == \App\Constants\BaseConstants::ACTIVE) {
                                            $hot_icon = '<div class="position-absolute top-0 end-0 m-1 product-discount">
                                            <span class="is-full">HOÀN</span>
                                        </div>';
                                        }

                                        \Carbon\Carbon::setLocale('vi');
                                        $created = $item->updated_at;
                                        $item->created = \Carbon\Carbon::parse($created)->diffForHumans(\Carbon\Carbon::now());
                                        $rating = '';
                                        if ($item->rating > 0) {
                                            $rating = '<span>⭐ ' . number_format($item->rating) . '</span>';
                                        }
                                        $edit = '';

                                        if (Auth::check() && Auth::user()->id == $item->user_id) {
                                            $edit = '<div class="user-story-edit position-absolute top-0 end-0 p-1 product-discount w-100">
                                            <div class="d-flex justify-content-end">
                                                <a href="javascript:void(0)" title="Xoá truyện" class="m-1" onclick="removeStory(' . $item->id . ', \'' . $item->name . '\')">
                                                    <span style="display: block; padding: 5px; background: #fd3550; color: #FFF; border-radius: 6px; width: 30px; height: 30px; text-align: center;">
                                                        <i class="bx bx-trash"></i>
                                                    </span>
                                                </a>
                                                <a href="' . route('user.chapter.create', $item->id) . '" title="Thêm chương mới" class="m-1">
                                                    <span style="display: block; padding: 5px; background: #15ca20; color: #FFF; border-radius: 6px; width: 30px; height: 30px; text-align: center;">
                                                        <i class="bx bx-plus"></i>
                                                    </span>
                                                </a>
                                                <a href="' . route('user.chapter.index', $item->id) . '" title="Danh sách chương" class="m-1">
                                                    <span style="display: block; padding: 5px; background: #ffc107; color: #FFF; border-radius: 6px; width: 30px; height: 30px; text-align: center;">
                                                        <i class="bx bx-list-ol"></i>
                                                    </span>
                                                </a>
                                                <a href="' . route('user.story.detail', $item->id) . '" title="Sửa truyện" class="m-1">
                                                    <span style="display: block; padding: 5px; background: #008cff; color: #FFF; border-radius: 6px; width: 30px; height: 30px; text-align: center;">
                                                        <i class="bx bx-edit-alt"></i>
                                                    </span>
                                                </a>
                                            </div>
                                        </div>';
                                        }
                                        ?>
                                    <div class="col-md-3 col-6" id="story_id_{{ $item->id }}">
                                        <div class="card">
                                            <div class="position-relative">
                                                <a href="{{ route('story.detail', $item->slug) }}">
                                                    <img alt="{{ $item->name }}" class="card-img-top"
                                                         width="200" height="260" src="{{ asset('images/story/thumbs/230/' . $item->thumbnail) }}"
                                                         onerror="this.src='{{ asset('img/no-image.png') }}'">
                                                </a>
                                                <div class="story-meta-data d-flex justify-content-start">
                                                    <span><i class="bx bx-show"></i> {{ number_format($item->total_view) }}</span>
                                                    <span><i class="bx bx-bookmark-alt"></i> {{ number_format($item->total_bookmark) }}</span>
                                                    {!! $rating !!}
                                                </div>
                                                {!! $hot_icon !!}
                                            </div>
                                            {!! $edit !!}
                                            <div class="card-body">
                                                <a href="{{ route('story.detail', $item->slug) }}">
                                                    <h3 class="card-title cursor-pointer story-item-title">{{ $item->name }}</h3>
                                                </a>
                                                <div class="d-flex justify-content-between">
                                        <span class="chapter font-meta">
                                            @if ($item->last_chapter != '')
                                                <a href="{{ route('chapter.detail', [$item->slug, \Illuminate\Support\Str::slug(str_replace('.', '-', $item->last_chapter))]) }}">
                                                    {{ $item->last_chapter }}
                                                </a>
                                            @endif
                                        </span>
                                                    <span class="post-on font-meta">{{ $item->created }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div><!--end row-->
                            <div class="pagination mt-4 justify-content-center">
                                {!! $paginate !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="donateModal" tabindex="-1" role="dialog" aria-labelledby="donateModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="donateModalLabel">Donate ủng hộ Team</h5>
                </div>
                <div class="modal-body">
                    <div id="qrCodeDonate"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(".top-story-slider").owlCarousel({
            autoplay: true,
            margin: 20,
            touchDrag: true,
            mouseDrag: true,
            dots: false,
            autoplayTimeout: 3000,
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
    </script>
    @if (Auth::check() && Auth::user()->id == $author->id)
        <script type="text/javascript">
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
    @endif
@endsection
