@extends('layouts.app')
@section('seo')
    <?php
    $seoTitle = $settings->where('type', 'seo_title')->first()->value;
    $seoDescription = $settings->where('type', 'seo_description')->first()->value;
    $data_seo = array(
        'title' => $seoTitle,
        'keywords' => $settings->where('type', 'seo_keyword')->first()->value,
        'description' => $seoDescription,
        'og_title' => $seoTitle,
        'og_description' => $seoDescription,
        'og_url' => Request::url(),
        'og_img' => asset($settings->where('type', 'seo_image')->first()->value),
        'current_url' => Request::url()
    );
    $seo = WebService::getSEO($data_seo);
    ?>
    @include('partials.seo')
@endsection
@section('content')
    {!! $homepageContent !!}
@endsection
@section('script')
    <script>
        jQuery(document).ready(function ($) {
            $(".top-story-slider").owlCarousel({
                autoplay: true,
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

            $(".creative-stories-slider").owlCarousel({
                autoplay: true,
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
                        items: 5
                    }
                }
            });

            $(".hot-stories-slider").owlCarousel({
                autoplay: true,
                margin: 0,
                touchDrag: true,
                mouseDrag: true,
                dots: false,
                autoplayTimeout: 5000,
                autoplaySpeed: 1200,
                autoplayHoverPause: true,
                items: 1
            });

            $(".recommendedStory").owlCarousel({
                autoplay: true,
                margin: 40,
                touchDrag: true,
                mouseDrag: true,
                dots: true,
                autoplayTimeout: 5000,
                autoplaySpeed: 1500,
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
    </script>
@endsection

