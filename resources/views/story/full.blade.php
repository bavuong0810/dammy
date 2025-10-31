@extends('layouts.app')
@section('seo')
    <?php
    $title = 'Truyện Full - ' . $settings->where('type', 'site_name')->first()->value;
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
                <div class="clear">
                    <div class="float-start">
                        <h1 class="mb-0 text-uppercase">Truyện Full</h1>
                    </div>
                    <div class="float-end">
                        <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                            <button type="button" class="btn btn-white">Sắp xếp</button>
                            <div class="btn-group" role="group">
                                <button id="btnGroupDrop1" type="button" class="btn btn-white dropdown-toggle dropdown-toggle-nocaret px-1" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bx bx-chevron-down"></i>
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="btnGroupDrop1" style="">
                                    <li><a class="dropdown-item @if(request()->get('sort') == 'view_desc') active @endif" href="{{ request()->url() }}?sort=view_desc">View cao</a></li>
                                    <li><a class="dropdown-item @if(request()->get('sort') == 'view_asc') active @endif" href="{{ request()->url() }}?sort=view_asc">View thấp</a></li>
                                    <li><a class="dropdown-item @if(request()->get('sort') == 'chapter_desc') active @endif" href="{{ request()->url() }}?sort=chapter_desc">Nhiều chương</a></li>
                                    <li><a class="dropdown-item @if(request()->get('sort') == 'chapter_asc') active @endif" href="{{ request()->url() }}?sort=chapter_asc">Ít chương</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-lg-9 col-12">
                        <div class="row product-grid">
                            @foreach($list->data as $item)
                                <?php
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
                            {!! $paginate !!}
                        </div>
                    </div>
                    <div class="col-lg-3 col-12">
                        {!! WebService::WidgetRight($getOptions) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
