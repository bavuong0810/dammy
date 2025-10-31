@extends('layouts.app')
@section('seo')
    <?php
    use App\Libraries\Helpers;
    use Carbon\Carbon;
    use Illuminate\Support\Facades\Cache;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Str;

    $title = $story->name;
    $is_full_text = '';
    if ($story->is_full) {
        $is_full_text = ' - Đã full (Hoàn thành)';
    }
    $description = 'Đọc truyện ' . $story->name . ' tại ' . $settings->where('type', 'site_name')->first()->value . $is_full_text . '. Hỗ trợ xem trên di động, máy tính bảng.';
    $nameStoryWithoutUTF8 = str_replace('-', ' ', Str::slug($story->name));
    $keyword = $story->name . ', ' . $nameStoryWithoutUTF8 . ', doc truyen ' . $nameStoryWithoutUTF8 . ', ' . $nameStoryWithoutUTF8 . ' full';
    if ($story->thumbnail != "") {
        $thumb_img_seo = asset('images/story/' . $story->thumbnail);
    } else {
        $thumb_img_seo = asset($settings->where('type', 'seo_image')->first()->value);
    }

    $teamOrAuthorText = ($story->creative) ? 'tác giả' : 'team';
    $data_seo = array(
        'title' => $title,
        'keywords' => $keyword,
        'description' => $description,
        'og_title' => $title,
        'og_description' => 'Truyện ' . $story->name . ' của ' . $teamOrAuthorText . ' ' . $author->name . '.',
        'og_url' => Request::url(),
        'og_img' => $thumb_img_seo,
        'current_url' => Request::url(),
        'current_url_amp' => ''
    );
    $seo = WebService::getSEO($data_seo);
    $classNames = Helpers::spanClassNameArray();
    ?>
    @include('partials.seo')
@endsection
@section('content')
    <!--start page wrapper -->
    <div class="page-wrapper main" id="main" data-type="story" role="main" itemscope itemprop="mainContentOfPage">
        <div class="page-content page-comics-detail">
            <div class="container">
                <!--breadcrumb-->
                <div class="page-breadcrumb d-flex align-items-center mb-3">
                    <div class="ps-3">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0 p-0" itemtype="https://schema.org/BreadcrumbList" itemscope>
                                <li class="breadcrumb-item"
                                    itemprop="itemListElement"
                                    itemscope
                                    itemtype="https://schema.org/ListItem">
                                    <a href="{{ route('index') }}" itemprop="item">
                                        <span itemprop="name">Trang chủ</span>
                                        <meta itemprop="position" content="1">
                                    </a>
                                </li>
                                <li class="breadcrumb-item active"
                                    itemprop="itemListElement"
                                    itemscope
                                    itemtype="https://schema.org/ListItem"
                                    aria-current="page">
                                    <h1 style="font-size: 16px;color: #000;font-weight: normal;display: inline-block;">
                                        <a href="{{ route('story.detail', $story->slug) }}" itemprop="item">
                                            <span itemprop="name">{{ $story->name }}</span>
                                            <meta itemprop="position" content="2">
                                        </a>
                                    </h1>
                                </li>
                            </ol>
                        </nav>
                    </div>
                </div>
                <!--end breadcrumb-->

                @if ($errors->any())
                    <div class="alert alert-primary border-0 bg-danger alert-dismissible fade show py-3">
                        <div class="d-flex align-items-center">
                            <div class="ms-3">
                                <div class="text-white">
                                    @foreach ($errors->all() as $error)
                                        <p class="mb-0">
                                            {{ $error }}
                                        </p>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="card" itemscope itemtype="http://schema.org/Book">
                    <div class="row g-0" data-type="0">
                        <meta itemprop="bookFormat" content="EBook">
                        <meta itemprop="datePublished" content="{{ date('Y-m-d', strtotime($story->created_at)) }}">
                        <div class="col-md-3">
                            <div class="m-3 text-center">
                                <img src="{{ asset('images/story/' . $story->thumbnail) }}" class="img-fluid"
                                     alt="{{ $story->name }}" style="width: 100%;" onerror="this.src='{{ asset('img/no-image.png') }}'">
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="card-body">
                                <h2 itemprop="name" class="card-title">{{ $story->name }}</h2>
                                <dl class="row">
                                    <?php
                                    Carbon::setLocale('vi');
                                    $created = $story->updated_at;
                                    $update_post = Carbon::parse($created)->diffForHumans(Carbon::now());
                                    ?>
                                    <dt class="col-sm-3">Cập nhật</dt>
                                    <dd class="col-sm-9">{!! $update_post !!}</dd>
                                    <dt class="col-sm-3">Loại</dt>
                                    <dd class="col-sm-9">
                                        <span class="btn btn-sm btn-primary px-3 radius-30">
                                            Truyện Chữ
                                        </span>
                                    </dd>

                                    @if ($story->another_name != '')
                                        <dt class="col-sm-3">Tên khác</dt>
                                        <dd class="col-sm-9">{{ $story->another_name }}</dd>
                                    @endif

                                    @if ($story->author != '' && $story->creative == 0)
                                        <dt class="col-sm-3">Tác giả</dt>
                                        <dd class="col-sm-9">{{ $story->author }}</dd>
                                    @endif

                                    <?php
                                    $cate = "";
                                    foreach ($categories as $it_cate) {
                                        $cate .= "<a class='cate-item' itemprop='genre' title='" . $it_cate->name . "' href='" . route('category.list', $it_cate->slug) . "'>" . $it_cate->name . "</a>";
                                    }
                                    ?>
                                    <dt class="col-sm-3">Thể loại</dt>
                                    <dd class="col-sm-9">{!! $cate !!}</dd>

                                    @if($author)
                                        @if ($story->creative == 0)
                                            <dt class="col-sm-3">Team</dt>
                                            <dd class="col-sm-9">
                                                <a href="{{ route('translateTeam.detail', $author->id) }}" class="btn btn-sm btn-info px-3 radius-30">
                                                    {{ $author->name }}
                                                </a>
                                            </dd>
                                        @else
                                            <dt class="col-sm-3">Tác giả</dt>
                                            <dd class="col-sm-9">
                                                <a href="{{ route('translateTeam.detail', $author->id) }}" class="btn btn-sm btn-info px-3 radius-30">
                                                    {{ $author->name }}
                                                </a>
                                            </dd>
                                        @endif
                                    @endif

                                    <dt class="col-sm-3">Lượt xem</dt>
                                    <dd class="col-sm-9">{{ number_format($story->total_view) }}</dd>

                                    @if ($story->audio == 1)
                                        <dt class="col-sm-3">Lượt nghe</dt>
                                        <dd class="col-sm-9">{{ number_format($story->total_listen) }}</dd>
                                    @endif

                                    <dt class="col-sm-3">Yêu thích</dt>
                                    <dd class="col-sm-9">{{ number_format($story->total_favourite) }}</dd>
                                    {{--
                                    <dt class="col-sm-3">Đánh giá</dt>
                                    <dd class="col-sm-9">
                                        @if($story->rating == 0)
                                            Chưa có đánh giá
                                        @else
                                            {{ $story->rating }}⭐ (<a href="javascript:void(0)" class="text-primary"
                                              onclick="$([document.documentElement, document.body]).animate({scrollTop: $('#loadReviewBtn').offset().top - 20}, 100);">{{ number_format($story->total_review) }} đánh giá</a>)
                                        @endif
                                    </dd>
                                    --}}

                                    <dt class="col-sm-3">Lượt theo dõi</dt>
                                    <dd class="col-sm-9">{!! number_format($story->total_bookmark) !!}</dd>

                                    <?php
                                    if ($story->is_full == 1) {
                                        $status = 'Đã đủ bộ';
                                    } else {
                                        $status = 'Đang phát hành';
                                    }
                                    ?>
                                    <dt class="col-sm-3">Trạng thái</dt>
                                    <dd class="col-sm-9">{{ $status }}</dd>
                                </dl>
                                <hr>

                                <div class="d-flex gap-3 mt-3 flex-wrap" id="control">
                                    <button class="btn btn-sm btn-danger px-3 radius-30" onclick="donate({{ $story->user_id }})">
                                        <i class="bx bx-dollar-circle"></i>Donate
                                    </button>
                                    @if (count($list_chapters))
                                        <a href="{{ route('chapter.detail', [$story->slug, $list_chapters[(count($list_chapters) - 1)]->slug]) }}"
                                           class="btn btn-sm btn-warning px-3">
                                            Đọc từ đầu
                                        </a>
                                        <a href="{{ route('chapter.detail', [$story->slug, $list_chapters[0]->slug]) }}"
                                           class="btn btn-sm btn-success px-3">
                                            Đọc tập mới
                                        </a>
                                    @endif

                                    @if($is_bookmark)
                                        <button type="button" id="btnBookmarkDetail" onclick="removeBookmark({{ $story->id }}, '{{ $story->slug }}')"
                                                class="btn btn-secondary btn-sm px-3">
                                            <i class='bx bx-minus font-18 me-1'></i>Bỏ theo dõi
                                        </button>
                                    @else
                                        <button type="button" id="btnBookmarkDetail" onclick="bookmark({{ $story->id }}, '{{ $story->slug }}')"
                                                class="btn btn-primary btn-sm px-3">
                                            <i class='bx bx-plus font-18 me-1'></i>Theo dõi
                                        </button>
                                    @endif
                                    <button type="button" onclick="report({{ $story->id }})" class="btn btn-dark btn-sm px-3">
                                        <i class='bx bx-error-circle font-18 me-1'></i>Báo lỗi
                                    </button>
                                </div>

                                {{--
                                <div class="d-flex gap-3 mt-3 flex-wrap">
                                    <button type="button" class="btn btn-sm btn-danger radius-30 px-3" onclick="reportLicense()">
                                        Báo cáo vi phạm bản quyền
                                    </button>
                                </div>
                                --}}

                                <div class="mt-3 card-text story-description">
                                    <div class="ql-editor inner" itemprop="description">
                                        <?php
                                        $contentHtml = Helpers::replaceWithSpans($story->content, $classNames);
                                        ?>
                                        {!! htmlspecialchars_decode($contentHtml) !!}
                                    </div>
                                    <span class="more cursor-pointer text-primary">Xem thêm</span>
                                </div>

                                @if (
                                    !Auth::check()
                                    || (Auth::check() && Auth::user()->premium_date == '')
                                    || (Auth::check() && Auth::user()->premium_date != '' && strtotime(Auth::user()->premium_date) < time())
                                )
                                    <div class="mb-4 mx-lg-4 mx-3 d-flex justify-content-center">
                                        {!! Helpers::get_option_by_key($getOptions, 'clickadu-banner-300x250') !!}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($story->warning)
                        <div class="card-body mt-4">
                            <div class="alert alert-warning border-0 bg-warning alert-dismissible fade show py-2">
                                <div class="d-flex align-items-center">
                                    <div class="font-35 text-dark"><i class="bx bx-info-circle"></i>
                                    </div>
                                    <div class="ms-3">
                                        <div class="text-dark">Nội dung truyện có thể sử dụng các từ ngữ nhạy cảm, bạo lực,... bạn có thể cân nhắc trước khi đọc truyện!</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="card-body @if(!$story->warning) mt-4 @endif">
                        <ul class="nav nav-pills nav-primary justify-content-center" id="controlStoryDetail" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" data-bs-toggle="tab" href="#listChapters" role="tab" aria-selected="true">
                                    <div class="d-flex align-items-center">
                                        <div class="tab-icon"><i class="bx bx-list-ol font-18 me-1"></i>
                                        </div>
                                        <div class="tab-title">Danh sách chương</div>
                                    </div>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a id="loadReviewBtn" class="nav-link" data-bs-toggle="tab" href="#reviews" role="tab"
                                   aria-selected="false" tabindex="-1" onclick="loadReviews({{ $story->id }})">
                                    <div class="d-flex align-items-center position-relative">
                                        <div class="tab-icon"><i class="bx bx-star font-18 me-1"></i></div>
                                        <div class="tab-title">Đánh giá</div>
                                        <div class="count-items">{{ $total_reviews }}</div>
                                    </div>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a id="loadCommentBtn" class="nav-link" data-bs-toggle="tab" href="#comments" role="tab"
                                   aria-selected="false" tabindex="-1" onclick="loadComments({{ $story->id }})">
                                    <div class="d-flex align-items-center position-relative">
                                        <div class="tab-icon"><i class="bx bx-comment-detail font-18 me-1"></i></div>
                                        <div class="tab-title">Bình luận</div>
                                        <div class="count-items">{{ $total_comments }}</div>
                                    </div>
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content py-2">
                            <div class="tab-pane fade active show" id="listChapters" role="tabpanel">
                                @if(count($list_chapters) > 0)
                                    <div class="list-chapters">
                                            <?php $count_chapter = count($list_chapters); ?>
                                        @foreach($list_chapters as $key => $chapterItem)
                                            <?php
                                            $time = WebService::time_request($chapterItem->created_at);
                                            ?>
                                            <div class="item d-flex justify-content-between">
                                                <div class="episode-title">
                                                    <a href="{{ route('chapter.detail', [$story->slug, $chapterItem->slug]) }}">
                                                        {{ $chapterItem->name }}
                                                    </a>@if($chapterItem->coin > 0) <i class="bx bx-lock"></i> @endif
                                                </div>
                                                <div class="episode-date">
                                                    <span>{{ $time }}</span>
                                                </div>
                                            </div>
                                                <?php $count_chapter = $count_chapter - 1; ?>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            <div class="tab-pane fade" id="reviews" role="tabpanel">
                                <div class="userReview row justify-content-center">
                                    <div class="col-md-6">
                                        <hr>
                                        <div class="mb-3">
                                            <label for="reviewRating" class="form-label">Chấm điểm nội dung truyện: ⭐ <b><span id="ratingResult">5</span></b></label>
                                            <input type="range" name="reviewRating" class="form-range" min="1" max="5" value="5" id="reviewRating">
                                        </div>
                                        <div class="mb-3">
                                            <label for="ratingOnly">
                                                <input type="checkbox" name="ratingOnly" id="ratingOnly" value="1"> Tôi chỉ muốn chấm điểm (không viết đánh giá)
                                            </label>
                                        </div>
                                        <div class="mb-3 writeReview">
                                            <textarea name="reviewContent" id="reviewContent" cols="30"
                                                      placeholder="Nội dung đánh giá (ít nhất 30 từ)"
                                                      rows="4" class="form-control"></textarea>
                                        </div>
                                        <div class="mb-3 text-center">
                                            <button type="button" onclick="sendReview()" class="btn btn-sm btn-success">Gửi đánh giá</button>
                                        </div>
                                        <hr>
                                        <div class="alert alert-info border-0 bg-info">
                                            <div class="text-dark">
                                                - Đánh giá của bạn sẽ được <b>Đam Mỹ</b> duyệt đọc trước khi hiển thị.
                                                <br>
                                                - Nếu bạn chỉ muốn chấm điểm cho truyện, không muốn viết đánh giá, hãy tích vào <b>"Tôi chỉ muốn chấm điểm"</b>.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="reviewResult" class="block-rating">
                                    <div class="text-center my-3">
                                        <img src="{{ asset('img/ajax-loader.gif') }}" alt="">
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="comments" role="tabpanel">
                                <div class="comment-static">
                                    <div class="ajax_load_cmt">
                                        <div class="text-center my-3">
                                            <img src="{{ asset('img/ajax-loader.gif') }}" alt="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relatedStory mt-5">
                    {!! WebService::ProposeStory() !!}
                </div>
            </div>
        </div>
    </div>

    @if($author)
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
    @endif

    @include('partials.report-story')
@endsection

@section('script')
    <script>
        jQuery(document).ready(function ($) {
            $('.more.cursor-pointer').on('click', function () {
                if($('.story-description .inner').hasClass('expanded')) {
                    $(this).html('Xem thêm');
                    $('.story-description .inner').removeClass('expanded');
                } else {
                    $('.story-description .inner').addClass('expanded');
                    $(this).html('Thu gọn');
                }
            });

            $('#reviewRating').on('change', function () {
                $('#ratingResult').html($('#reviewRating').val())
            });

            $('#ratingOnly').on('click', function () {
                if ($('#ratingOnly').is(':checked')) {
                    $('.writeReview').hide();
                } else {
                    $('.writeReview').show();
                }
            });


            $(".top-story-slider").owlCarousel({
                autoplay: false,
                margin: 20,
                touchDrag: true,
                mouseDrag: true,
                dots: false,
                autoplayTimeout: 5000,
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

        function loadReviews(story_id) {
            (function ($) {
                $.ajax({
                    type: "GET",
                    url: site + "/ajax/get-reviews?story_id=" + story_id,
                    cache: false,
                    success: function (result) {
                        $('#reviewResult').html(result);
                        $('#loadReviewBtn').removeAttr('onclick');
                    }
                });
            })(jQuery);
        }
    </script>
@endsection
