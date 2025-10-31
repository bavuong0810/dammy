<!doctype html>
<html lang="vi" xml:lang="vi" @if(Session::get('darkmode')) class="dark-theme" @endif>

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="_token" content="{{ csrf_token() }}">
    <meta property="fb:app_id" content="{{ env('FACEBOOK_APP_ID') }}" />
{{--    <meta name="google-adsense-account" content="{{ env('GOOGLE_ADSENSE_ACCOUNT') }}">--}}
    <!--favicon-->
    <link rel="stylesheet" href="{{ asset('assets/app.min.css') }}?ver={{ env('APP_VERSION') }}"/>
{{--    <link rel="stylesheet" href="{{ asset('assets/style-noel.min.css') }}?ver={{ env('APP_VERSION') }}"/>--}}

    <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('images/favicon/apple-icon-57x57.png') }}">
    <link rel="apple-touch-icon" sizes="60x60" href="{{ asset('images/favicon/apple-icon-60x60.png') }}">
    <link rel="apple-touch-icon" sizes="72x72" href="{{ asset('images/favicon/apple-icon-72x72.png') }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('images/favicon/apple-icon-76x76.png') }}">
    <link rel="apple-touch-icon" sizes="114x114" href="{{ asset('images/favicon/apple-icon-114x114.png') }}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('images/favicon/apple-icon-120x120.png') }}">
    <link rel="apple-touch-icon" sizes="144x144" href="{{ asset('images/favicon/apple-icon-144x144.png') }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('images/favicon/apple-icon-152x152.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/favicon/apple-icon-180x180.png') }}">
    <link rel="icon" type="image/png" sizes="192x192"  href="{{ asset('images/favicon/android-icon-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('images/favicon/favicon-96x96.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="{{ asset('images/favicon/ms-icon-144x144.png') }}">
    <meta name="theme-color" content="#ffffff">

    @if(Route::currentRouteName() == 'user.chapter.create' || Route::currentRouteName() == 'user.chapter.detail')
        <link href="{{ asset('assets/plugins/Drag-And-Drop/dist/imageuploadify.min.css') }}" rel="stylesheet" />
    @endif
{{--    <link href="{{ asset('assets/css/app.css') }}?ver={{ env('APP_VERSION') }}" rel="stylesheet">--}}

    @yield('seo')

    {!! $settings->where('type', 'header')->first()->value !!}

    <script type="text/javascript">
        const islogin = '<?php if (Auth::check()) echo '1'; ?>';
        const site = '{{ route('index') }}';
        const popupModal = '{!! Helpers::get_option_by_key($getOptions, 'popup-modal') !!}';
    </script>
    <style>
        .transparent-layer {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0);
            z-index: 9999999999;
            justify-content: center;
            align-items: center;
        }
    </style>

    @if(env('APP_ENV') == 'production' && Route::currentRouteName() == 'chapter.detail')
        <script language="JavaScript">
            function killCopy(e)
            {
                return false;
            }
            function reEnable()
            {
                return true;
            }
            document.onselectstart = new Function ('return false');

            if (window.sidebar){
                document.onmousedown=killCopy;
                document.onclick=reEnable;
            }

            window.onload = function() {
                document.addEventListener("contextmenu", function(e) {
                    e.preventDefault();
                }, false);
                document.addEventListener("keydown", function(e) {
                    //document.onkeydown = function(e) {
                    // "I" key
                    if (e.ctrlKey && e.shiftKey && e.keyCode == 73) {
                        disabledEvent(e);
                    }
                    // "J" key
                    if (e.ctrlKey && e.shiftKey && e.keyCode == 74) {
                        disabledEvent(e);
                    }
                    // "S" key + macOS
                    if (e.keyCode == 83 && (navigator.platform.match("Mac") ? e.metaKey : e.ctrlKey)) {
                        disabledEvent(e);
                    }
                    // "U" key
                    if (e.ctrlKey && e.keyCode == 85) {
                        disabledEvent(e);
                    }
                    // "F12" key
                    if (event.keyCode == 123) {
                        disabledEvent(e);
                    }
                }, false);

                function disabledEvent(e) {
                    if (e.stopPropagation) {
                        e.stopPropagation();
                    } else if (window.event) {
                        window.event.cancelBubble = true;
                    }
                    e.preventDefault();
                    return false;
                }
            };
        </script>
    @endif

    @if(Route::currentRouteName() == 'story.detail' || Route::currentRouteName() == 'chapter.detail')
        <?php
        $classNames = Helpers::spanClassNameArray();
        $styleHtml = Helpers::styleRenderFromClassName($classNames);
        ?>
        {!! $styleHtml !!}
    @endif
</head>

<body itemscope itemtype="http://schema.org/WebPage">
@if (
    !Auth::check()
    || (Auth::check() && Auth::user()->premium_date == '')
    || (Auth::check() && Auth::user()->premium_date != '' && strtotime(Auth::user()->premium_date) < time())
)
    <?php
        $linkShopee = Helpers::get_option_by_key($getOptions, 'shopee-links');
        $linkArray = Helpers::parseComplexLinkTextarea($linkShopee, 'shopee-links');

        $linkAff = $linkArray[rand(0, count($linkArray) - 1)];

        $linkLazada = Helpers::get_option_by_key($getOptions, 'lazada-links');
        $linkArrayLazada = Helpers::parseComplexLinkTextarea($linkLazada, 'lazada-links');
        $linkAffLazada = $linkArrayLazada[rand(0, count($linkArrayLazada) - 1)];
    ?>
    {{--class link-aff--}}
    <a id="affLayer" class="transparent-layer" href="{{ $linkAff }}" target="_blank" rel="sponsored"></a>
    {{--class link-aff--}}
    <a id="affLayer2" class="transparent-layer" href="{{ $linkAffLazada }}" target="_blank" rel="sponsored"></a>
@endif


<!--wrapper-->
<div class="wrapper">
    @include('layouts.header')

    @yield('content')

    @include('layouts.footer')
</div>

{{--
<div class="initial-snow">
    <div class="snow">&#10052;</div>
    <div class="snow">&#10052;</div>
    <div class="snow">&#10052;</div>
    <div class="snow">&#10052;</div>
    <div class="snow">&#10052;</div>
    <div class="snow">&#10052;</div>
    <div class="snow">&#10052;</div>
    <div class="snow">&#10052;</div>
    <div class="snow">&#10052;</div>
    <div class="snow">&#10052;</div>
    <div class="snow">&#10052;</div>
    <div class="snow">&#10052;</div>
    <div class="snow">&#10052;</div>
    <div class="snow">&#10052;</div>
    <div class="snow">&#10052;</div>
    <div class="snow">&#10052;</div>
    <div class="snow">&#10052;</div>
    <div class="snow">&#10052;</div>
    <div class="snow">&#10052;</div>
    <div class="snow">&#10052;</div>
    <div class="snow">&#10052;</div>
    <div class="snow">&#10052;</div>
    <div class="snow">&#10052;</div>
    <div class="snow">&#10052;</div>
    <div class="snow">&#10052;</div>
    <div class="snow">&#10052;</div>
    <div class="snow">&#10052;</div>
    <div class="snow">&#10052;</div>
    <div class="snow">&#10052;</div>
    <div class="snow">&#10052;</div>
    <div class="snow">&#10052;</div>
    <div class="snow">&#10052;</div>
    <div class="snow">&#10052;</div>
    <div class="snow">&#10052;</div>
    <div class="snow">&#10052;</div>
    <div class="snow">&#10052;</div>
    <div class="snow">&#10052;</div>
    <div class="snow">&#10052;</div>
    <div class="snow">&#10052;</div>
    <div class="snow">&#10052;</div>
    <div class="snow">&#10052;</div>
    <div class="snow">&#10052;</div>
    <div class="snow">&#10052;</div>
    <div class="snow">&#10052;</div>
    <div class="snow">&#10052;</div>
    <div class="snow">&#10052;</div>
    <div class="snow">&#10052;</div>
    <div class="snow">&#10052;</div>
    <div class="snow">&#10052;</div>
    <div class="snow">&#10052;</div>
</div>
--}}

<!--end wrapper-->
<div class="loading-modal">
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>
{{--
<!--start switcher-->
<div class="switcher-wrapper">
    <div class="switcher-btn"><i class='bx bx-cog bx-spin'></i>
    </div>
    <div class="switcher-body">
        <div class="d-flex align-items-center">
            <h5 class="mb-0 text-uppercase">Tuỳ chỉnh giao diện</h5>
            <button type="button" class="btn-close ms-auto close-switcher" aria-label="Close"></button>
        </div>
        <hr/>
        <h6 class="mb-0">Kiểu chủ đề</h6>
        <hr/>
        <div class="d-flex align-items-center justify-content-between">
            <div class="form-check">
                <input class="form-check-input" type="radio" name="flexRadioDefault" id="lightmode" checked>
                <label class="form-check-label" for="lightmode">Sáng</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="flexRadioDefault" id="darkmode">
                <label class="form-check-label" for="darkmode">Tối</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="flexRadioDefault" id="semidark">
                <label class="form-check-label" for="semidark">Nữa Tối</label>
            </div>
        </div>
        <hr/>
        <div class="form-check">
            <input class="form-check-input" type="radio" id="minimaltheme" name="flexRadioDefault">
            <label class="form-check-label" for="minimaltheme">Tối giản</label>
        </div>
        <hr/>
        <h6 class="mb-0">Màu tiêu đề</h6>
        <hr/>
        <div class="header-colors-indigators">
            <div class="row row-cols-auto g-3">
                <div class="col">
                    <div class="indigator headercolor1" id="headercolor1"></div>
                </div>
                <div class="col">
                    <div class="indigator headercolor2" id="headercolor2"></div>
                </div>
                <div class="col">
                    <div class="indigator headercolor3" id="headercolor3"></div>
                </div>
                <div class="col">
                    <div class="indigator headercolor4" id="headercolor4"></div>
                </div>
                <div class="col">
                    <div class="indigator headercolor5" id="headercolor5"></div>
                </div>
                <div class="col">
                    <div class="indigator headercolor6" id="headercolor6"></div>
                </div>
                <div class="col">
                    <div class="indigator headercolor7" id="headercolor7"></div>
                </div>
                <div class="col">
                    <div class="indigator headercolor8" id="headercolor8"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--end switcher-->
--}}
<script src="{{ asset('assets/app.min.js') }}?ver={{ env('APP_VERSION') }}"></script>

{{--
@if(Route::currentRouteName() != 'resetPasswordView' && Route::currentRouteName() != 'login' && Route::currentRouteName() != 'translateTeam.detail')
    @if (
        !Auth::check()
        || (Auth::check() && Auth::user()->premium_date == '')
        || (Auth::check() && Auth::user()->premium_date != '' && strtotime(Auth::user()->premium_date) < time())
    )
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const layers = [
                    { id: "affLayer", delay: 260 },
                    { id: "affLayer2", delay: 460 }
                ];

                function getElapsedTime(startTime) {
                    return (Date.now() - startTime) / 1000;
                }

                function showElementAfterDelay(element, delay, storageKey) {
                    let startTime = sessionStorage.getItem(storageKey);

                    if (!startTime) {
                        startTime = Date.now();
                        sessionStorage.setItem(storageKey, startTime);
                    }

                    const remainingTime = delay - getElapsedTime(startTime);

                    if (remainingTime <= 0) {
                        element.style.display = "block";
                    } else {
                        setTimeout(() => element.style.display = "block", remainingTime * 1000);
                    }
                }

                function restoreVisibility(element, visibilityKey) {
                    if (sessionStorage.getItem(visibilityKey) === "false") {
                        element.style.display = "none";
                    }
                }

                layers.forEach(({ id, delay }) => {
                    const element = document.getElementById(id);
                    if (!element) return;

                    const startTimeKey = `${id}StartTime`;
                    const visibilityKey = `${id}Enable`;

                    showElementAfterDelay(element, delay, startTimeKey);
                    restoreVisibility(element, visibilityKey);

                    element.addEventListener("click", () => {
                        element.style.display = "none";
                        sessionStorage.setItem(visibilityKey, "false");
                    });
                });
            });
        </script>
    @endif
@endif
--}}

<!--app JS-->
{{--<script src="{{ asset('assets/js/app.js') }}?ver={{ env('APP_VERSION') }}"></script>--}}
{!! $settings->where('type', 'footer')->first()->value !!}
@yield('script')
</body>
</html>
