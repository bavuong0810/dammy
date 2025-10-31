<?php
use Carbon\Carbon;
?>
<!--start page wrapper -->
<div class="page-wrapper">
    <div class="page-content">
        @if($recommendedStories)
            <section class="recommendedStories-section mt-2">
                <div class="container">
                    <div class="total-item-show">
                        <h5 class="mb-0 text-uppercase">Đề cử hôm nay</h5> <hr>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="d-lg-d-flex align-items-center gap-3">
                                <div class="recommendedStory owl-carousel owl-theme">
                                    <div class="grid grid-cols-1 gap-y-4">
                                            <?php $k = 1; ?>
                                        @foreach($recommendedStories->stories as $key => $item)
                                            <div class="d-flex space-x-3 pt-2 px-2">
                                                <div class="flex-shrink-0">
                                                    <a href="{{ route('story.detail', $item->slug) }}"
                                                       title="{{ $item->name }}" class="text-title font-weight-bold">
                                                        <img onerror="this.src='{{ asset('img/no-image.png') }}'"
                                                             class="lazyload h-32 w-24 shadow-xl rounded" alt="{{ $item->name }}"
                                                             src="{{ asset('img/ajax-loading.gif') }}" width="200" height="260"
                                                             data-src="{{ asset('images/story/thumbs/230/' . $item->thumbnail) }}">
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
                        </div>
                    </div>

                    @if (
                        !Auth::check()
                        || (Auth::check() && Auth::user()->premium_date == '')
                        || (Auth::check() && Auth::user()->premium_date != '' && strtotime(Auth::user()->premium_date) < time())
                    )
                            <?php $adsPosition1 = Helpers::get_option_by_key($getOptions, 'clickadu-banner-300x250'); ?>
                        @if($adsPosition1 != '')
                            <div class="mt-3 mx-lg-4 mx-3 d-flex justify-content-center">
                                {!! $adsPosition1 !!}
                            </div>
                        @endif
                    @endif
                </div>
            </section>
        @endif

        <section class="propose-stories-home mt-5">
            <div class="container">
                {!! WebService::ProposeStory() !!}
            </div>
        </section>

        <section class="section-news py-4">
            <div class="container">
                <div class="row">
                    <div class="col-lg-9 col-md-9 col-12">
                        {{--
                        <div class="chat-block">
                            <h5 class="mb-0 text-uppercase">Sảnh trò chuyện</h5>
                            <hr>
                            <div class="group-chat">
                                {!! WebService::ChatBox() !!}
                            </div>
                        </div>
                        --}}

                        <div id="news" class="">
                            <h5 class="mb-0 text-uppercase">Truyện mới cập nhật</h5>
                            <hr>
                            <div id="new-story">
                                <div class="row product-grid">
                                    @foreach ($list->data as $row)
                                            <?php
                                            $hot_icon = '';
                                            if ($row->is_full == \App\Constants\BaseConstants::ACTIVE) {
                                                $hot_icon = '<div class="hot-item"><span class="is-full">HOÀN</span></div>';
                                            }

                                            Carbon::setLocale('vi');
                                            $created = $row->updated_at;
                                            $row->created = Carbon::parse($created)->diffForHumans(Carbon::now());
                                            $chapter_item = '<div class="d-flex justify-content-between">
                                                    <span class="chapter font-meta">
                                                        <a href="' . route('chapter.detail', [$row->slug, \Illuminate\Support\Str::slug(str_replace('.', '-', $row->last_chapter))]) . '">' . $row->last_chapter . '</a>
                                                    </span>
                                                    <span class="post-on font-meta">' . $row->created . '</span>
                                                </div>';

                                            $rating = '';
                                            if ($row->rating > 0) {
                                                $rating = '<span>⭐ ' . number_format($row->rating) . '</span>';
                                            }
                                            ?>
                                        <div class="col-md-3 col-4">
                                            <div class="card">
                                                <div class="position-relative">
                                                    <a href="{{ route('story.detail', $row->slug) }}">
                                                        <img onerror="this.src='{{ asset('img/no-image.png') }}'"
                                                             class="lazyload card-img-top" alt="{{ $row->name }}"
                                                             data-src="{{ asset('images/story/thumbs/230/' . $row->thumbnail) }}"
                                                             src="{{ asset('img/ajax-loading.gif') }}"
                                                             width="200" height="260">
                                                    </a>
                                                    <div class="story-meta-data d-flex justify-content-start">
                                                        <span><i class="bx bx-show"></i> {{ number_format($row->total_view) }}</span>
                                                        <span><i class="bx bx-bookmark-alt"></i> {{ number_format($row->total_bookmark) }}</span>
                                                        {!! $rating !!}
                                                    </div>

                                                    <div class="position-absolute top-0 end-0 m-1 product-discount">
                                                        {!! $hot_icon !!}
                                                    </div>
                                                </div>

                                                <div class="card-body">
                                                    <h6 class="card-title cursor-pointer story-item-title">
                                                        <a href="{{ route('story.detail', $row->slug) }}">{{ $row->name }}</a>
                                                    </h6>
                                                    {!! $chapter_item !!}
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="text-center mb-4 pagination justify-content-center">
                                    @if(request()->get('page') == 1 || request()->get('page') == '')
                                        {!! $paginate !!}
                                    @else
                                        {!! $list->links('vendor.pagination.rocker-pagination') !!}
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if(count($creativeStories))
                        {!! WebService::creativeStories($creativeStories) !!}
                        @endif

                        @if(count($completedStories))
                            <div id="news" class="mt-5">
                                <div class="total-item-show position-relative">
                                    <h5 class="mb-0 text-uppercase">Truyện đã hoàn thành</h5> <hr>
                                    <a href="{{ route('completedStory') }}"
                                       class="read-more-story btn btn-outline-primary radius-30 btn-sm">Xem thêm</a>
                                </div>
                                <div id="new-story">
                                    <div class="row product-grid">
                                        @foreach ($completedStories as $row)
                                                <?php
                                                $hot_icon = '';
                                                if ($row->is_full == \App\Constants\BaseConstants::ACTIVE) {
                                                    $hot_icon = '<div class="hot-item"><span class="is-full">HOÀN</span></div>';
                                                }

                                                Carbon::setLocale('vi');
                                                $created = $row->updated_at;
                                                $row->created = Carbon::parse($created)->diffForHumans(Carbon::now());
                                                $chapter_item = '<div class="d-flex justify-content-between">
                                                        <span class="chapter font-meta">
                                                            <a href="' . route('chapter.detail', [$row->slug, \Illuminate\Support\Str::slug(str_replace('.', '-', $row->last_chapter))]) . '">' . $row->last_chapter . '</a>
                                                        </span>
                                                        <span class="post-on font-meta">' . $row->created . '</span>
                                                    </div>';

                                                $rating = '';
                                                if ($row->rating > 0) {
                                                    $rating = '<span>⭐ ' . number_format($row->rating) . '</span>';
                                                }
                                                ?>
                                            <div class="col-md-3 col-4">
                                                <div class="card">
                                                    <div class="position-relative">
                                                        <a href="{{ route('story.detail', $row->slug) }}">
                                                            <img onerror="this.src='{{ asset('img/no-image.png') }}'"
                                                                 class="card-img-top lazyload" alt="{{ $row->name }}"
                                                                 src="{{ asset('img/ajax-loading.gif') }}"
                                                                 data-src="{{ asset('images/story/thumbs/230/' . $row->thumbnail) }}"
                                                                 width="200" height="260">
                                                        </a>
                                                        <div class="story-meta-data d-flex justify-content-start">
                                                            <span><i class="bx bx-show"></i> {{ number_format($row->total_view) }}</span>
                                                            <span><i class="bx bx-bookmark-alt"></i> {{ number_format($row->total_bookmark) }}</span>
                                                            {!! $rating !!}
                                                        </div>

                                                        <div class="position-absolute top-0 end-0 m-1 product-discount">
                                                            {!! $hot_icon !!}
                                                        </div>
                                                    </div>

                                                    <div class="card-body">
                                                        <h6 class="card-title cursor-pointer story-item-title">
                                                            <a href="{{ route('story.detail', $row->slug) }}">{{ $row->name }}</a>
                                                        </h6>
                                                        {!! $chapter_item !!}
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="text-center mb-4 pagination justify-content-center">
                                        <a href="{{ route('completedStory') }}" class="btn btn-sm btn-primary">Xem thêm</a>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="col-lg-3 col-md-3 col-12">
                        {!! WebService::WidgetRight($getOptions) !!}
                    </div>
                </div>
            </div>
        </section>

        <section class="section-categories">
            <div class="container">
                <?php
                $list_categories = Helpers::getListCategories();
                ?>
                <div class="list-story-categories">
                    <div class="clear">
                        <div class="blog-heading fl">Thể Loại</div>
                        <a class="btn btn-genres icon ion-md-arrow-dropdown fr"></a>
                    </div>
                    <div class="show-list-categories active">
                        <div class="row">
                            @foreach($list_categories as $category)
                                <div class="col-6 col-sm-4 col-md-3">
                                    <h4 class="item-category">
                                        <a href="{{ route('category.list', $category->slug) }}">
                                            <i class="bx bx-caret-right"></i> {{ $category->name }}
                                        </a>
                                    </h4>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
