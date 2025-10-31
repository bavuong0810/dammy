@extends('layouts.app')
@section('seo')
    <?php
    $title = 'Team - ' . $settings->where('type', 'seo_title')->first()->value;
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
        <div class="page-content page-author">
            <div class="container">
                @if (
                    !Auth::check()
                    || (Auth::check() && Auth::user()->premium_date == '')
                    || (Auth::check() && Auth::user()->premium_date != '' && strtotime(Auth::user()->premium_date) < time())
                )
                    <?php $adsPosition1 = Helpers::get_option_by_key($getOptions, 'clickadu-banner-300x100'); ?>
                    @if($adsPosition1 != '')
                        <div class="mb-2 d-flex justify-content-center">
                            {!! $adsPosition1 !!}
                        </div>
                    @endif
                @endif
                <h1 class="mb-0 text-uppercase">Team</h1>
                <hr>
                <div class="row">
                    <div class="col-lg-9 col-md-9 col-12">
                        <div class="d-lg-flex align-items-center mb-4 gap-3">
                            <div class="position-relative">
                                <form action="{{ route('translateTeam.index') }}" method="GET">
                                    <input type="text" class="form-control ps-5 radius-30" name="search" id="search"
                                           value="{{ request()->get('search') }}" placeholder="Tìm dịch giả/tác giả">
                                    <span class="position-absolute top-50 product-show translate-middle-y">
                                        <i class="bx bx-search"></i>
                                    </span>
                                </form>
                            </div>
                        </div>
                        <div class="row product-grid">
                            <?php
                                if (request()->get('search') == '') {
                                    $translateTeams = $translateTeams->data;
                                }
                            ?>
                            @foreach($translateTeams as $item)
                                <div class="col-md-3 col-6">
                                    <div class="card">
                                        <a href="{{ route('translateTeam.detail', $item->id) }}">
                                            <img alt="{{ $item->name }}" class="card-img-top"
                                                 width="200" height="260"
                                                 onerror="this.src='{{ asset('img/no-image.png') }}'"
                                                 src="{{ asset('images/avatar/thumbs/230/' . $item->avatar) }}">
                                        </a>
                                        <div class="card-body">
                                            <a href="{{ route('translateTeam.detail', $item->id) }}">
                                                <h3 class="card-title cursor-pointer story-item-title">
                                                    {{ $item->name }}
                                                </h3>
                                            </a>
                                            <div class="d-flex justify-content-between">
                                                <span><i class="bx bx-show"></i> {{ number_format($item->total_view) }}</span>
                                                <span><i class="bx bx-book-alt"></i> {{ number_format($item->total_stories) }}</span>
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
                    <div class="col-lg-3 col-md-3 col-12">
                        {!! WebService::WidgetRight($getOptions) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
