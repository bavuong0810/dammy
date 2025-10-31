@extends('layouts.app')
@section('seo')
    <?php
    $title = $page->title . ' - ' . $settings->where('type', 'seo_title')->first()->value;
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
        <div class="page-content">
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
                <h1 class="mb-0 text-uppercase">{{ $page->title }}</h1>
                <hr>
                <div class="row">
                    <div class="col-lg-9 col-md-9 col-12">
                        <div class="card">
                            <div class="card-body">
                                @if($slug == 'chinh-sach-va-quy-dinh-chung')
                                    <div class="my-3 text-center">
                                        <div class="mb-2">
                                            <img src="{{ asset($settings->where('type', 'logo')->first()->value) }}" alt="Đam Mỹ" width="170px">
                                        </div>
                                    </div>
                                @endif
                                {!! htmlspecialchars_decode($page->content) !!}
                                @if ($slug == 'chinh-sach-va-quy-dinh-chung')
                                    @include('page.policy-for-author')
                                @endif
                            </div>
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
