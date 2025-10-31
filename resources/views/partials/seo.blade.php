<?php
$seoTitle = $settings->where('type', 'seo_title')->first()->value;
$companyName = $settings->where('type', 'company_name')->first()->value;
$email = $settings->where('type', 'email')->first()->value;
$logo = $settings->where('type', 'logo')->first()->value;
?>
<title>{{ $seo['title'] }}</title>
<meta name="keywords" content="{{ $seo['keywords'] }}" />
<meta name="description" content="{{ $seo['description'] }}" />
<!--Facebook Seo-->
<meta property="og:title" content="{{ $seo['og_title'] }}" />
<meta property="og:description" content="{{ $seo['og_description'] }}" />
<meta property="og:url" content="{{ $seo['og_url'] }}" />
<meta property="og:type" content="article" />
<meta property="og:image" content="{{ $seo['og_img'] }}" />
<meta property="og:image:alt" content="{{ $seo['og_title'] }}" />
<meta property="og:image:width" content="290" />
<meta property="og:image:height" content="360" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
@if(Route::currentRouteName() == 'story.detail')
    <meta property="og:type" content="book">
    <meta property="book:author" content="{{ route('translateTeam.detail', $story->user_id) }}">
    <meta property="book:release_date" content="{{ $story->created_at }}">
    <meta property="book:tag" content="{{ $story->name }}">
    <meta property="book:tag" content="{{ $nameStoryWithoutUTF8 }}">
    <meta property="book:tag" content="{{ $author->name }}">
@endif
<link rel="canonical" href="{{ $seo['current_url'] }}" />

<link href='//fonts.googleapis.com' rel='dns-prefetch'/>
<link href='//ajax.googleapis.com' rel='dns-prefetch'/>
<link href='//apis.google.com' rel='dns-prefetch'/>
<link href='//connect.facebook.net' rel='dns-prefetch'/>
<link href='//www.facebook.com' rel='dns-prefetch'/>
<link href='//twitter.com' rel='dns-prefetch'/>
<link href='//www.google-analytics.com' rel='dns-prefetch'/>
<link href='//www.googletagservices.com' rel='dns-prefetch'/>
<link href='//pagead2.googlesyndication.com' rel='dns-prefetch'/>
<link href='//googleads.g.doubleclick.net' rel='dns-prefetch'/>
<link href='//static.xx.fbcdn.net' rel='dns-prefetch'/>
<link href='//platform.twitter.com' rel='dns-prefetch'/>
<link href='//syndication.twitter.com' rel='dns-prefetch'/>
<base href="{{ route('index') }}"/>
<meta name="robots" content="index,follow" />
<meta name="author" content="{{ $companyName }}" />
<meta name="copyright" content="Copyright&copy;{{ date('Y') }} {{ $companyName }}.　All Right Reserved." />
<meta http-equiv="content-script-type" content="text/javascript" />
<meta http-equiv="content-style-type" content="text/css" />
<meta http-equiv="content-language" content="vi" />
<meta name="robots" content="notranslate"/>
<link rev="made" href="mailto:{{ $email }}" />
<meta name="distribution" content="global" />
<meta name="rating" content="general" />
<meta property="og:site_name" content="{{ $companyName }}" />
<link rel="index" href="{{ route('index') }}" />
<script type='application/ld+json'>
{
	"@context":"http:\/\/schema.org",
	"@type":"WebSite",
	"@id":"#website",
	"url":"{{ route('index') }}",
	"name":"{{ $companyName }}",
	"alternateName":"{{ $seoTitle }}",
	"potentialAction":{
	    "@type":"SearchAction",
	    "target":"{{ route('search') }}?search={search_term_string}",
	    "query-input":"required name=search_term_string"
	}
}
</script>
<script type='application/ld+json'>
{
	"@context":"http:\/\/schema.org",
	"@type":"Organization",
	"url":"{{ route('index') }}",
	"foundingDate": "2024",
	"founders": [
	 {
	 "@type": "Person",
	 "name": "Đam Mỹ"
	 }],
	 "contactPoint": {
	 "@type": "ContactPoint",
	 "contactType": "customer support",
	 "telephone": "[{{ $settings->where('type', 'hotline')->first()->value }}]",
	 "email": "{{ $email }}"
	 },
	"sameAs":["https:\/\/www.facebook.com\/"],
	"@id":"#organization",
	"name":"{{ $companyName }}",
	"logo":"{{ asset($logo) }}"
}
</script>
@if(Route::currentRouteName() == 'story.detail' && isset($story))

    <?php
    $link_img = '';
    if ($story->thumbnail != '') {
        $link_img = asset('images/story/' . $story->thumbnail);
    }
    $author = 'Chưa xác định';
    ?>
    <script type="application/ld+json">
        {
            "@context": "http://schema.org",
            "@type": "Article",
            "mainEntityOfPage": "{{ URL::current() }}",
            "headline": "{{ $story->name }}",
            "datePublished": "{{ $story->created_at }}",
            "dateModified": "{{ $story->updated_at }}",
            "description": "{{ $seo['description'] }}",
            "author": {
                "@type": "Person",
                "name": "{{ $author }}"
            },
            "publisher": {
                "@type": "Organization",
                "name": "Đam Mỹ",
                "logo": {
                    "@type": "ImageObject",
                    "url": "{{ asset($logo) }}",
                    "width": 300,
                    "height": 300
                }
            }
            @if($link_img != ''),
            "image": {
                "@type": "ImageObject",
                "url": "{{ $link_img }}",
                "height": 390,
                "width": 290
            }
            @endif
        }
    </script>

    <?php
    $total_favourite = ($story->total_favourite > 3) ? $story->total_favourite : 3;
    ?>
    <script type='application/ld+json'>
    {
        "@context":"https://schema.org/",
        "@type":"Book",
        "name":"{{ $story->name }}",
        "description":"Đọc truyện {{ $story->name }} tại {{ $companyName }}",
        "aggregateRating": {
            "@type": "AggregateRating",
            "ratingValue": "4.{!! rand(4, 9) !!}",
            "bestRating": "5",
            "ratingCount": "{!! $total_favourite !!}"
        }
    }
    </script>
@endif
