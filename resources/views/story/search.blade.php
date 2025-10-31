@extends('layouts.app')
@section('seo')
    <?php
    $title = 'Tìm Kiếm: ' . strip_tags(Request::get('search')) . ' - ' . $settings->where('type', 'site_name')->first()->value;
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
    <div class="page-wrapper">
        <div class="page-content page-category">
            <div class="container">
                @if (
                    !Auth::check()
                    || (Auth::check() && Auth::user()->premium_date == '')
                    || (Auth::check() && Auth::user()->premium_date != '' && strtotime(Auth::user()->premium_date) < time())
                )
                    <div class="mb-3 mt-2 mx-lg-4 mx-3 d-flex justify-content-center">
                        {!! Helpers::get_option_by_key($getOptions, 'clickadu-banner-300x100') !!}
                    </div>
                @endif
                <div class="row">
                    <div class="col-lg-3 col-12">
                        <div class="mobile">
                            <div class="alert alert-info border-0 bg-info alert-dismissible fade show py-2 mb-3">
                                Tìm thấy <b>{{ number_format($total_stories) }} </b> truyện
                            </div>
                        </div>
                        <form action="{{ route('search') }}" method="GET">
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" placeholder="Từ khoá cần tìm"
                                       aria-label="Từ khoá cần tìm" aria-describedby="button-addon2"
                                       name="search" value="{{ request()->get('search') }}">
                                <button class="btn btn-primary" type="submit" id="button-addon2">Tìm kiếm</button>
                            </div>
                            <div class="mb-3">
                                <h6><i class="bx bx-caret-right-circle"></i> Số chương</h6>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="form-check form-check-success">
                                        <input class="form-check-input" type="radio" name="total_chapter"
                                               id="total_chapter1" value="<10"
                                               @if(request()->get('total_chapter') == '<10') checked @endif>
                                        <label class="form-check-label" for="total_chapter1">
                                            <10
                                        </label>
                                    </div>
                                    <div class="form-check form-check-success">
                                        <input class="form-check-input" type="radio" name="total_chapter"
                                               id="total_chapter2" value="11-50"
                                               @if(request()->get('total_chapter') == '11-50') checked @endif>
                                        <label class="form-check-label" for="total_chapter2">
                                            11-50
                                        </label>
                                    </div>
                                    <div class="form-check form-check-success">
                                        <input class="form-check-input" type="radio" name="total_chapter"
                                               id="total_chapter3" value="50-100"
                                               @if(request()->get('total_chapter') == '50-100') checked @endif>
                                        <label class="form-check-label" for="total_chapter3">
                                            50-100
                                        </label>
                                    </div>
                                    <div class="form-check form-check-success">
                                        <input class="form-check-input" type="radio" name="total_chapter"
                                               id="total_chapter4" value="100+"
                                               @if(request()->get('total_chapter') == '100+') checked @endif>
                                        <label class="form-check-label" for="total_chapter4">
                                            100+
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <h6><i class="bx bx-caret-right-circle"></i> Tình trạng truyện</h6>
                                <select name="story_status" id="story_status" class="form-control">
                                    <option value="">Tất cả</option>
                                    <option value="full" @if(request()->get('story_status') == 'full') selected @endif>
                                        Hoàn thành
                                    </option>
                                    <option value="update" @if(request()->get('story_status') == 'update') selected @endif>
                                        Đang cập nhật
                                    </option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <h6><i class="bx bx-caret-right-circle"></i> Sắp xếp</h6>
                                <select name="sort" id="sort" class="form-control">
                                    <option value="">Mới nhất</option>
                                    <option value="view_desc" @if(request()->get('sort') == 'view_desc') selected @endif>
                                        View cao
                                    </option>
                                    <option value="view_asc" @if(request()->get('sort') == 'view_asc') selected @endif>
                                        View thấp
                                    </option>
                                    <option value="chapter_desc" @if(request()->get('sort') == 'chapter_desc') selected @endif>
                                        Nhiều chương
                                    </option>
                                    <option value="chapter_asc" @if(request()->get('sort') == 'chapter_asc') selected @endif>
                                        Ít chương
                                    </option>
                                </select>
                            </div>
                            <div class="mb-3 list-filter-cate">
                                <h6><i class="bx bx-caret-right-circle"></i> Thể loại</h6>
                                <input type="hidden" name="cate" id="cate" value="{{ request()->get('cate') }}">
                                <?php
                                    $listCate = Helpers::getListCategories();
                                    $cateArr = [];
                                    $getCate = request()->get('cate');
                                    if ($getCate != '') {
                                        $cateArr = explode(',', $getCate);
                                    }
                                ?>
                                <div class="list-chapters">
                                    <div class="d-flex flex-wrap">
                                        @foreach($listCate as $cate)
                                            <div class="filter-cate">
                                                <div class="form-check form-check-success">
                                                    <input class="form-check-input cate-select" type="checkbox" value="{{ $cate->id }}"
                                                           id="cate-select-{{ $cate->id }}"
                                                           @if(in_array($cate->id, $cateArr)) checked @endif>
                                                    <label class="form-check-label" for="cate-select-{{ $cate->id }}">
                                                        {{ $cate->name }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="mb-4 text-center">
                                <button type="submit" class="btn btn-sm btn-primary"><i class="fadeIn animated bx bx-search"></i> Tìm kiếm</button>
                            </div>
                        </form>
                    </div>
                    <div class="col-lg-9 col-12">
                        <div class="pc">
                            <div class="alert alert-info border-0 bg-info alert-dismissible fade show py-2">
                                Tìm thấy <b>{{ number_format($total_stories) }} </b> truyện
                            </div>
                        </div>

                        <div class="row product-grid">
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
                                ?>
                                <div class="col-lg-6 col-12">
                                    <div class="card">
                                        <div class="d-flex">
                                            <div style="width: 35%">
                                                <div class="position-relative">
                                                    <a href="{{ route('story.detail', $item->slug) }}">
                                                        <img alt="{{ $item->name }}" class="card-img-top lazyload"
                                                             src="{{ asset('img/ajax-loading.gif') }}"
                                                             width="200" height="260"
                                                             data-src="{{ asset('images/story/thumbs/230/' . $item->thumbnail) }}"
                                                             onerror="this.src='{{ asset('img/no-image.png') }}'">
                                                    </a>
                                                    <div class="story-meta-data d-flex justify-content-between">
                                                        <span><i class="bx bx-show"></i> {{ number_format($item->total_view) }}</span>
                                                        <span><i class="bx bx-bookmark-alt"></i> {{ number_format($item->total_bookmark) }}</span>
                                                        {!! $rating !!}
                                                    </div>
                                                    {!! $hot_icon !!}
                                                </div>
                                            </div>
                                            <div style="width: 65%;">
                                                <div class="p-3">
                                                    <a href="{{ route('story.detail', $item->slug) }}">
                                                        <h3 class="card-title cursor-pointer story-item-title">{{ $item->name }}</h3>
                                                    </a>
                                                    <div class="mb-2 story-item-excerpt">
                                                        {!! WebService::excerpts(htmlspecialchars_decode($item->content), 300) !!}
                                                    </div>
                                                    <div class="author mb-1">
                                                        <a href="{{ route('translateTeam.detail', $item->user->id) }}" class="text-primary" style="font-size: 15px"><i class="bx bx-user-circle"></i> {{ $item->user->name }}</a>
                                                    </div>
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
                                    </div>
                                </div>
                            @endforeach
                        </div><!--end row-->
                        <div class="pagination mt-4 justify-content-center">
                            {!! $list->withQueryString()->links('vendor.pagination.rocker-pagination') !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        jQuery(document).ready(function ($) {
            $('.cate-select').on('change', function () {
                let selectedCate = [];
                $('.cate-select').each(function () {
                    if ($(this).is(':checked')) {
                        selectedCate.push($(this).val());
                    }
                });
                $('#cate').val(selectedCate);
            });
        });
    </script>
@endsection
