<?php
ob_start();
$datetime = date('Y-m-d');
$xml_file = '<?xml version="1.0" encoding="utf-8"?>';
$xml_file .= '<sitemapindex xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/siteindex.xsd">
         <sitemap>
             <loc>' . route('sitemap.static') . '</loc>
             <lastmod>' . $datetime . '</lastmod>
         </sitemap>
         <sitemap>
             <loc>' . route('sitemap.pages') . '</loc>
             <lastmod>' . $datetime . '</lastmod>
         </sitemap>
         <sitemap>
             <loc>' . route('sitemap.categories') . '</loc>
             <lastmod>' . $datetime . '</lastmod>
         </sitemap>
         <sitemap>
             <loc>' . route('sitemap.stories') . '</loc>
             <lastmod>' . $datetime . '</lastmod>
         </sitemap>
         <sitemap>
             <loc>' . route('sitemap.chapters') . '</loc>
             <lastmod>' . $datetime . '</lastmod>
         </sitemap>
         <sitemap>
             <loc>' . route('sitemap.authors') . '</loc>
             <lastmod>' . $datetime . '</lastmod>
         </sitemap>' . "\n" . '</sitemapindex>';
header('Content-type: text/xml');
echo $xml_file;
ob_flush();
?>
