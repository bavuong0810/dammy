<?php ob_start();
$datetime = date('Y-m-d');
$xml_file = '';
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">';
$xml_file .= '
<url>
<loc>' . route('index') . '</loc>
<lastmod>' . $datetime . '</lastmod>
<changefreq>daily</changefreq>
<priority>1.0</priority>
</url>';
$xml_file .= '
<url>
<loc>' . route('pageStory') . '</loc>
<lastmod>' . $datetime . '</lastmod>
<changefreq>never</changefreq>
<priority>0.7</priority>
</url>';
$xml_file .= '
<url>
<loc>' . route('completedStory') . '</loc>
<lastmod>' . $datetime . '</lastmod>
<changefreq>never</changefreq>
<priority>0.7</priority>
</url>';
$xml_file .= '
<url>
<loc>' . route('translateTeam.index') . '</loc>
<lastmod>' . $datetime . '</lastmod>
<changefreq>never</changefreq>
<priority>0.7</priority>
</url>';
?>
<?php
$xml_file .= '</urlset>';
header('Content-type: text/xml');
echo $xml_file;
ob_flush();
?>
