<?php ob_start();
$xml_file='';
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns:xhtml="http://www.w3.org/1999/xhtml">';
?>
@foreach ($stories as $story)
<?php
if(isset($story->thumbnail) && $story->thumbnail !=''):
    $img_thumb = asset('images/story/' . $story->thumbnail);
else:
    $img_thumb = asset($settings->where('type', 'seo_image')->first()->value);
endif;
$xml_file .='
<url>
    <loc>' . route('story.detail', $story->slug) . '</loc>
    <image:image>
        <image:loc>' . $img_thumb . '</image:loc>
        <image:caption>' . $story->name . '</image:caption>
        <image:license>' . route('index') . '</image:license>
        <image:family_friendly>yes</image:family_friendly>
    </image:image>
        <lastmod>' . date("Y-m-d", strtotime($story->updated_at)) . '</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.9</priority>
</url>';
?>
@endforeach
<?php
$xml_file .='</urlset>';
header('Content-type: text/xml');
echo $xml_file;
ob_flush();
?>
