<?php

namespace App\Http\Controllers;


use App\Models\Post;
use App\Models\Category;
use App\Models\Join_Category_Post;
use App\Models\Join_Category_Theme;
use App\Models\Theme;
use App\Models\Category_Theme;
use Illuminate\Http\Request;
use App\Libraries\Helpers;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Response;
use CURL;
use DB;
use Illuminate\Database\Query;

//use Ixudra\Curl\Facades\Curl;

class RobotsController extends Controller
{
    public function mauNgoinhaxinh(){
        set_time_limit(0);
        setlocale(LC_ALL, "en_US.utf8");
        $domain="https://www.ngoinhaxinh.com.vn";
        $url_ajax="https://www.ngoinhaxinh.com.vn/product/loaditem";
        $categories=Category_Theme::where('status_category','=',0)->select('category_post_ajax','categoryID','categorySlug','categorySlug_v2')->get();
        $categories_array=$categories->toArray();
        $i=0;
        $id_category=0;
        $slug_category="";
        $url_slug_v2="";
        $category_post_ajax="";
        $var_sting_boctach="";
        $categories_list=array();
        $count_category=count($categories_array);
        var_dump($categories_array);
        $html_fullNews="";
        for($i=0;$i<$count_category;$i++):
            $categories_list=$categories_array[$i];
            //dd($categories_list);
            $id_category=$categories_list['categoryID']; //ID thể loại dùng insert về sau
            $category_post_ajax=$categories_list['category_post_ajax'];
            $slug_category=$categories_list['categorySlug'];
            $url_slug_v2=$categories_list['categorySlug_v2'];
            $var_sting_boctach=str_replace(" ","",addslashes($category_post_ajax));
            if($var_sting_boctach !='' && strlen($var_sting_boctach)>0):
                $html_fullNews=CURL::post($url_ajax,$var_sting_boctach);
            //else:
            //    $html_fullNews=CURL::get($url_slug_v2);
            endif;
            echo "<br/>====****====<br/>".$url_ajax."<br/>".$var_sting_boctach."<br/>====****===<br/>";
            echo($html_fullNews);
            exit();
        endfor;
        exit();
    }


    public function DThouseOder(){
        set_time_limit(0);
        setlocale(LC_ALL, "en_US.utf8");
        $domain="http://dthouse.vn";
        //$url_group=$domain."/phong-thuy-nha-theo-tuoi-18/,2";
        $categories=Category::where('status_category','=',0)->select('categoryDescription','categoryID','categorySlug')->get();
        //$curl=new CURL();
        $j=0;
        $url="";
        $url_exports=array();
        $id_category=0;
        $slug_category="";
        $description_cate="";
        $url_request="";
        $order=1;
        $order_cat=1;
        foreach($categories as $category):
            $id_category=$category->categoryID; //ID thể loại dùng insert về sau
            $order_cat=(int)$order.$id_category;
            $description_cate=$category->categoryDescription;
            $slug_category=$category->categorySlug;
            $url_exports=explode(",",$description_cate);
            if(count($url_exports)>0):
                $url=str_replace(" ","",$url_exports[0]);
                $max_page=$url_exports[1]+1;
                //$max_page=2;
                $url_request="";
                $minhnn=0;
                $list_news_array=array();
                $html_fullNews="";
                for($j=1;$j<$max_page;$j++):
                    $order_cat=$order_cat+1;
                    $url_request=$url."page-".$j;
                    $url_request=str_replace("&lt;p&gt;","",strip_tags($url_request));
                    //$url_request=str_replace("<p>","",$url_request);
                    echo "<br/>===Bắt đầu ===========".$url_request."<br/>";
                    //$url_request=$url;
                    //echo $url_request;
                    $html_fullNews=CURL::get($url_request);
                    //$html_fullNews=file_get_contents($url_request);
                    $html_fullNews=mb_convert_encoding($html_fullNews,"HTML-ENTITIES", "UTF-8");
                    $html_fullNews=str_replace("'",'"',$html_fullNews);

                    //echo $html_fullNews;
                    //dd($html_fullNews);
                    $list_news_array=explode('<div class="newmaingr fl">',$html_fullNews);
                    $count_list_news=count($list_news_array);
                    $fields=array();
                    //for get list new------------------------------------------------
                    $p=0;
                    for($p=1;$p<$count_list_news;$p++):
                        $order_cat=$order_cat+1;
                        //echo $list_news[$p]."<br/>";
                        $datetime_now=date('Y-m-d H:i:s');
                        $datetime_convert=strtotime($datetime_now);

                        $list_news=preg_replace('/<div class="pagination">.*<\/html>/is','',$list_news_array[$p]);
                        $list_news=preg_replace('/<aside.*aside>/is','',$list_news);
                        $list_news=preg_replace('/<p class="xemtiep">.*<\/p>/is','',$list_news);
                        //CURL::remove_by_regex($list_news_array[$p],'/class=\s*pagination\s*>/is',"");
                        //echo $list_news."<br/>";
                        $title = $link_image=$url_thumbnail=$link_title= $short_news = $full_news = $title_slug ='';
                        //----------------------------------TITLE----------------------------------------
                        preg_match("/<h2.*h2>/is",$list_news,$match_title);
                        $title=CURL::remove_all_tag($match_title[0]);
                        $title=mb_convert_encoding($title,"UTF-8", "HTML-ENTITIES");
                        $title=trim($title);
                        //echo $title."<br/>";

                        //----------------------------------LINK TITLE-----------------------------------
                        $link_title=CURL::get_href_src($list_news,$domain);
                        //echo $link_title."<br/>";
                        //----------------------------------TITLE SLUG-----------------------------------
                        //$title_decode=mb_convert_encoding($title,"UTF-8","HTML-ENTITIES");
                        $title_slug_array=explode("/",$link_title);
                        //dd(end($title_slug_array));
                        $title_slug=substr(end($title_slug_array), 0, -4);
                        $title_slug=Str::slug($title_slug);

                        if($title_slug !=""):
                            if(Helpers::check_dupcontent_post($title_slug)>0):
                                //no working
                                Helpers::update_Post_Date($title_slug,$order_cat);
                            else:
                                //dd($title_slug);

                            endif;//check dup content
                        endif;//check title slug
                    endfor;
                endfor;
                echo "<br/>******************************-^^-*******************************<br/>";
                echo "<br/> Lấy được số tin:<B>".$minhnn."</B>. Của URL:<i>".$url_request."</i>"."<br/>";
                echo "<br/>******************************-$-******************************<br/>";
            endif;
        endforeach;
    }
    public function DThouseChild(){
        set_time_limit(0);
        $domain="http://dthouse.vn";
        $url_group=$domain."/du-an-sua-nha/,4";
        $id_category=10;

        $str_begin=array("thumbs/"," ");
        $str_end=array("","%20");
        $j=0;
        $url="";

        $url_request="";
        $url_exports=explode(",",$url_group);
        $url=str_replace(" ","",$url_exports[0]);
        $max_page=$url_exports[1]+1;
        //$max_page=2;
        $url_request="";
        $minhnn=0;
            $list_news_array=array();
            $html_fullNews="";
            for($j=1;$j<$max_page;$j++):
                $url_request=$url."page-".$j;
                $url_request=str_replace("&lt;p&gt;","",strip_tags($url_request));
                //$url_request=str_replace("<p>","",$url_request);
                echo "<br/>===Bắt đầu ===========".$url_request."<br/>";
                //$url_request=$url;
                //echo $url_request;
                $html_fullNews=CURL::get($url_request);
                //$html_fullNews=file_get_contents($url_request);
                $html_fullNews=mb_convert_encoding($html_fullNews,"HTML-ENTITIES", "UTF-8");
                $html_fullNews=str_replace("'",'"',$html_fullNews);

                //echo $html_fullNews;
                //dd($html_fullNews);
                $list_news_array=explode('<div class="newmaingr fl">',$html_fullNews);
                $count_list_news=count($list_news_array);
                $fields=array();
                //for get list new------------------------------------------------
                $p=0;
                for($p=1;$p<$count_list_news;$p++):
                    //echo $list_news[$p]."<br/>";
                    $datetime_now=date('Y-m-d H:i:s');
                    $datetime_convert=strtotime($datetime_now);

                    $list_news=preg_replace('/<div class="pagination">.*<\/html>/is','',$list_news_array[$p]);
                    $list_news=preg_replace('/<aside.*aside>/is','',$list_news);
                    $list_news=preg_replace('/<p class="xemtiep">.*<\/p>/is','',$list_news);
                    //CURL::remove_by_regex($list_news_array[$p],'/class=\s*pagination\s*>/is',"");
                    //echo $list_news."<br/>";
                    $title = $link_image=$url_thumbnail=$link_title= $short_news = $full_news = $title_slug ='';
                    //----------------------------------TITLE----------------------------------------
                    preg_match("/<h2.*h2>/is",$list_news,$match_title);
                    $title=CURL::remove_all_tag($match_title[0]);
                    $title=mb_convert_encoding($title,"UTF-8", "HTML-ENTITIES");
                    $title=trim($title);
                    //echo $title."<br/>";

                    //----------------------------------LINK TITLE-----------------------------------
                    $link_title=CURL::get_href_src($list_news,$domain);
                    //echo $link_title."<br/>";
                    //----------------------------------TITLE SLUG-----------------------------------
                    //$title_decode=mb_convert_encoding($title,"UTF-8","HTML-ENTITIES");
                    $title_slug_array=explode("/",$link_title);
                    //dd(end($title_slug_array));
                    $title_slug=substr(end($title_slug_array), 0, -4);
                    $title_slug=Str::slug($title_slug);

                    if($title_slug !=""):
                        if(Helpers::check_dupcontent_post($title_slug)>0):
                            //no working
                        else:
                            //dd($title_slug);
                            //----------------------------------IMAGE---------------------------------------
                            //preg_match('/(<a\s+class="border-image")(.*)(<\/a>)/is',$list_news[$i],$match_img);
                            $link_image_request=CURL::get_img_src($list_news,$domain."/");
                            $link_image=$link_image_request;
                            $url_thumbnail="";
                            $file_path_thumb="";

                            if($link_image!=""):
                                $link_image=str_replace($str_begin,$str_end,$link_image);

                                $file_thumb=@file_get_contents(CURL::spaces_url($link_image));
                                $file_path_thumb=pathinfo($link_image);
                                $name_thumb = "thumbnail_du_an_".$datetime_convert. '_' .Str::slug($file_path_thumb['filename']).".".$file_path_thumb['extension'];
                                $save_thumbnail=@file_put_contents(public_path().'/img/uploads/'.$name_thumb,$file_thumb);
                                //$save_thumbnail="";
                                if($save_thumbnail):
                                    $url_thumbnail =$name_thumb;
                                endif;
                                //$url_thumbnail=$link_image;
                                //echo $name_thumb;
                            endif;

                            //----------------------------------SHORT NEWS-----------------------------------
                            $short_news=strip_tags($list_news,'<p>');
                            //preg_match('/<p style="min-height: 60px;">.*<\/p>.*/is',$list_news,$match_short);
                            $short_news=CURL::remove_all_tag($short_news);
                            $short_news=mb_convert_encoding($short_news,"UTF-8","HTML-ENTITIES");
                            //echo $short_news."<br/>";
                            //dd('exit');
                            //---------------------------------FULL NEWS------------------------------------
                            $content_full_news = "";
                            $content_full_news=CURL::get($link_title);
                            $content_full_news = mb_convert_encoding($content_full_news, "HTML-ENTITIES", "UTF-8");
                            $content_full_news=str_replace("'",'"',$content_full_news);
                            $regex_news='/(class="page_content")(.*)<div class="leftproject fl">/is';//regex lay noi dung
                            $matchcount_news=preg_match_all($regex_news, $content_full_news, $match_news);
                            //echo $match_news[0][0]."==========<br/><br/><br/>";
                            //dd('ok');
                            $full_news='';
                            if ($matchcount_news > 0):
                                $full_news=$match_news[0][0];
                                //preg_match("/<p.*p>.*/is",$full_news,$match_fullnews);
                                $full_news=str_replace('class="page_content" style="padding: 5px 20px;">','',$full_news);
                                $full_news=str_replace('<div class="leftproject fl">','',$full_news);

                                $full_news=preg_replace('/<div style="height: 22px; padding: 5px; text-align: left; overflow: hidden">(.*)<\/div>\s+<div class="clr_fix"><\/div>/is', "", $full_news);
                                //$full_news=CURL::remove_tag_div($full_news);
                                $full_news=preg_replace('~<div([^>]*)(class\\s*=\\s*["\']fb-comments["\'])([^>]*)>(.*?)</div>~i', '', $full_news);

                                //$full_news=preg_replace('/<div class="socicalBar clearfix">.*<\/div>/is','',$full_news);
                                $full_news=CURL::remove_fix($full_news," ");
                                $full_news=str_replace("upload//","upload/",$full_news);
                                $full_news=str_replace("http://dthouse.vn","",$full_news);
                                $full_news=str_replace("//upload","upload",$full_news);
                                $full_news=str_replace("/upload","upload",$full_news);
                               // $full_news=str_replace(array("../../upload","../upload","/upload"),"upload",$full_news);
                                $full_news=str_replace(array("../../upload","../upload","//upload","..//upload"),"upload",$full_news);
                                $full_news=str_replace("upload","/upload",$full_news);
                                $full_news=mb_convert_encoding($full_news,"UTF-8","HTML-ENTITIES");
                                $full_news=htmlspecialchars($full_news);
                                //echo $full_news."<br/>";
                                //dd('ok');
                                /*-------------------------------------Inset Code-------------------------------------*/

                                $fields_post = array(
                                    'title'=>$title,
                                    'slug'=>$title_slug,
                                    'description'=>$short_news,
                                    'content'=>$full_news,
                                    'thubnail'=>$url_thumbnail,
                                    'thubnail_alt'=>$title,
                                    'created'=>Carbon::now()->format('Y-m-d H:i:s'),
                                    'updated'=>Carbon::now()->format('Y-m-d H:i:s'),
                                    'status'=>0
                                );

                                /*--------------------------------------End Inset Code------------------------------------*/
                            /*
                                echo "-------------------------------------------------------------------------<br>";
                                echo '&nbsp;-&nbsp;Title: '.$title.'<br/>';
                                echo "&nbsp;-&nbsp;Link Tilte: ".$link_title."<br/>";
                                echo '&nbsp;-&nbsp;Link Image: '.$url_thumbnail.'<br/>';
                                echo "&nbsp;-&nbsp;Short: ".$short_news;
                                echo "&nbsp;-&nbsp;Full News: ".$full_news;
                                echo "-------------------------------------------------------------------------<br>";
                            */
                                if($title!='' && $link_title !=''):
                                    $respons_post = Post::create($fields_post);
                                    if($respons_post->id >0):
                                        $fields_join_cate=array(
                                            'id_post'=>$respons_post->id,
                                            'id_category'=>$id_category
                                        );
                                        $respons_cate = Join_Category_Post::create($fields_join_cate);
                                        if($respons_cate->id >0):
                                            //return true;
                                            $minhnn++;
                                        endif;
                                        //return true;
                                    endif;
                                endif;


                            else:
                                echo "<br>FULL NEWS: Không REGEX được link URL {$link_title}";
                            endif;
                        endif;//check dup content
                    endif;//check title slug
                endfor;
            endfor;
            echo "<br/>**************************************************************<br/>";
            echo "<br/> Lấy được số tin:<B>".$minhnn."</B>. Của URL:<i>".$url_request."</i>"."<br/>";
    }
    public function DThouse(){
        set_time_limit(0);
        setlocale(LC_ALL, "en_US.utf8");
        $domain="http://dthouse.vn";
        //$url_group=$domain."/phong-thuy-nha-theo-tuoi-18/,2";
        $categories=Category::where('status_category','=',0)->select('categoryDescription','categoryID','categorySlug')->get();
        //$curl=new CURL();

        $str_begin=array("thumbs/"," ");
        $str_end=array("","%20");

        $j=0;
        $url="";
        $url_exports=array();
        $id_category=0;
        $slug_category="";
        $description_cate="";
        $url_request="";
        foreach($categories as $category):
            $id_category=$category->categoryID; //ID thể loại dùng insert về sau
            $description_cate=$category->categoryDescription;
            $slug_category=$category->categorySlug;
            $url_exports=explode(",",$description_cate);
            if(count($url_exports)>0):
            $url=str_replace(" ","",$url_exports[0]);
            $max_page=$url_exports[1]+1;
            //$max_page=2;
            $url_request="";
            $minhnn=0;
            $list_news_array=array();
            $html_fullNews="";
            for($j=1;$j<$max_page;$j++):
                $url_request=$url."page-".$j;
                $url_request=str_replace("&lt;p&gt;","",strip_tags($url_request));
                //$url_request=str_replace("<p>","",$url_request);
                echo "<br/>===Bắt đầu ===========".$url_request."<br/>";
                //$url_request=$url;
                //echo $url_request;
                $html_fullNews=CURL::get($url_request);
                //$html_fullNews=file_get_contents($url_request);
                $html_fullNews=mb_convert_encoding($html_fullNews,"HTML-ENTITIES", "UTF-8");
                $html_fullNews=str_replace("'",'"',$html_fullNews);

                //echo $html_fullNews;
                //dd($html_fullNews);
                $list_news_array=explode('<div class="newmaingr fl">',$html_fullNews);
                $count_list_news=count($list_news_array);
                $fields=array();
                //for get list new------------------------------------------------
                $p=0;
                for($p=1;$p<$count_list_news;$p++):
                    //echo $list_news[$p]."<br/>";
                    $datetime_now=date('Y-m-d H:i:s');
                    $datetime_convert=strtotime($datetime_now);

                    $list_news=preg_replace('/<div class="pagination">.*<\/html>/is','',$list_news_array[$p]);
                    $list_news=preg_replace('/<aside.*aside>/is','',$list_news);
                    $list_news=preg_replace('/<p class="xemtiep">.*<\/p>/is','',$list_news);
                    //CURL::remove_by_regex($list_news_array[$p],'/class=\s*pagination\s*>/is',"");
                    //echo $list_news."<br/>";
                    $title = $link_image=$url_thumbnail=$link_title= $short_news = $full_news = $title_slug ='';
                    //----------------------------------TITLE----------------------------------------
                    preg_match("/<h2.*h2>/is",$list_news,$match_title);
                    $title=CURL::remove_all_tag($match_title[0]);
                    $title=mb_convert_encoding($title,"UTF-8", "HTML-ENTITIES");
                    $title=trim($title);
                    //echo $title."<br/>";

                    //----------------------------------LINK TITLE-----------------------------------
                    $link_title=CURL::get_href_src($list_news,$domain);
                    //echo $link_title."<br/>";
                    //----------------------------------TITLE SLUG-----------------------------------
                    //$title_decode=mb_convert_encoding($title,"UTF-8","HTML-ENTITIES");
                    $title_slug_array=explode("/",$link_title);
                    //dd(end($title_slug_array));
                    $title_slug=substr(end($title_slug_array), 0, -4);
                    $title_slug=Str::slug($title_slug);

                    if($title_slug !=""):
                        if(Helpers::check_dupcontent_post($title_slug)>0):
                            //no working
                        else:
                            //dd($title_slug);
                            //----------------------------------IMAGE---------------------------------------
                            //preg_match('/(<a\s+class="border-image")(.*)(<\/a>)/is',$list_news[$i],$match_img);
                            $link_image_request=CURL::get_img_src($list_news,$domain."/");
                            $link_image=$link_image_request;
                            $url_thumbnail="";
                            $file_path_thumb="";
                            if($link_image!=""):

                                $link_image=str_replace($str_begin,$str_end,$link_image);
                                $file_thumb=@file_get_contents(CURL::spaces_url($link_image));

                                $file_path_thumb=pathinfo($link_image);
                                $name_thumb = "thumbnail_".$slug_category."_".$datetime_convert. '_' .Str::slug($file_path_thumb['filename']).".".$file_path_thumb['extension'];
                                $save_thumbnail=@file_put_contents(public_path().'/img/uploads/'.$name_thumb,$file_thumb);
                                //$save_thumbnail="";
                                if($save_thumbnail):
                                    $url_thumbnail =$name_thumb;
                                endif;
                            endif;
                            //----------------------------------SHORT NEWS-----------------------------------
                            $short_news=strip_tags($list_news,'<p>');
                            //preg_match('/<p style="min-height: 60px;">.*<\/p>.*/is',$list_news,$match_short);
                            $short_news=CURL::remove_all_tag($short_news);
                            $short_news=mb_convert_encoding($short_news,"UTF-8","HTML-ENTITIES");


                            //echo $short_news."<br/>";
                            //---------------------------------FULL NEWS------------------------------------
                            $content_full_news = "";
                            $content_full_news=CURL::get($link_title);
                            $content_full_news = mb_convert_encoding($content_full_news, "HTML-ENTITIES", "UTF-8");
                            $content_full_news=str_replace("'",'"',$content_full_news);
                            $regex_news='/<div class="page_content">(.*)<div class="right_pro fr">/is';//regex lay noi dung
                            $matchcount_news=preg_match_all($regex_news, $content_full_news, $match_news);
                            //echo $match_news[0][0]."==========<br/><br/><br/>";
                            $full_news='';
                            if ($matchcount_news > 0):
                                $full_news=$match_news[0][0];
                                //preg_match("/<p.*p>.*/is",$full_news,$match_fullnews);

                                $full_news=str_replace('<div class="page_content">','',$full_news);
                                $full_news=str_replace('<div class="leftproject fl">','',$full_news);

                                //$full_news=preg_replace('/<div style="height: 22px; padding: 5px; text-align: left; overflow: hidden">(.*)(<\/h2>)/is', "", $full_news);
                                $full_news=preg_replace('/<div style="height: 22px; padding: 5px; text-align: left; overflow: hidden">(.*)<\/div>\s+<div class="clr_fix"><\/div>/is', "", $full_news);
                                //$full_news=CURL::remove_tag_div($full_news);
                                $full_news=preg_replace('~<div([^>]*)(class\\s*=\\s*["\']fb-comments["\'])([^>]*)>(.*?)</div>~i', '', $full_news);

                                //$full_news=preg_replace('/<div class="socicalBar clearfix">.*<\/div>/is','',$full_news);
                                $full_news=CURL::remove_fix($full_news," ");
                                $full_news=str_replace("http://dthouse.vn","",$full_news);
                                $full_news=str_replace("upload//","upload/",$full_news);
                                $full_news=str_replace("/upload","upload",$full_news);
                                $full_news=str_replace(array("../../upload","../upload","//upload","..//upload"),"upload",$full_news);
                                $full_news=str_replace("upload/","/upload/",$full_news);
                                $full_news=mb_convert_encoding($full_news,"UTF-8","HTML-ENTITIES");
                                $full_news=htmlspecialchars($full_news);
                                //echo $full_news."<br/>";
                                /*-------------------------------------Inset Code-------------------------------------*/

                                $fields_post = array(
                                    'title'=>$title,
                                    'slug'=>$title_slug,
                                    'description'=>$short_news,
                                    'content'=>$full_news,
                                    'thubnail'=>$url_thumbnail,
                                    'thubnail_alt'=>$title,
                                    'created'=>Carbon::now()->format('Y-m-d H:i:s'),
                                    'updated'=>Carbon::now()->format('Y-m-d H:i:s'),
                                    'status'=>0
                                );

                                /*--------------------------------------End Inset Code------------------------------------*/
                                /*
                                echo "-------------------------------------------------------------------------<br>";
                                echo '&nbsp;-&nbsp;Title: '.$title.'<br/>';
                                echo "&nbsp;-&nbsp;Link Tilte: ".$link_title."<br/>";
                                echo '&nbsp;-&nbsp;Link Image: '.$url_thumbnail.'<br/>';
                                echo "&nbsp;-&nbsp;Short: ".$short_news;
                                echo "&nbsp;-&nbsp;Full News: ".$full_news;
                                echo "-------------------------------------------------------------------------<br>";*/
                                if($title!='' && $link_title !=''):
                                    $respons_post = Post::create($fields_post);
                                    if($respons_post->id >0):
                                        $fields_join_cate=array(
                                            'id_post'=>$respons_post->id,
                                            'id_category'=>$id_category
                                        );
                                        $respons_cate = Join_Category_Post::create($fields_join_cate);
                                        if($respons_cate->id >0):
                                            //return true;
                                            $minhnn++;
                                        endif;
                                        //return true;
                                    endif;
                                endif;


                            else:
                                echo "<br>FULL NEWS: Không REGEX được link URL {$link_title}";
                            endif;
                        endif;//check dup content
                    endif;//check title slug
                endfor;
            endfor;
           echo "<br/>**************************************************************<br/>";
           echo "<br/> Lấy được số tin:<B>".$minhnn."</B>. Của URL:<i>".$url_request."</i>"."<br/>";
        endif;
    endforeach;
    }






  public function AutoBocTach(){
      return view('autosuf.index');
  }
  public function AutoBocTachTop(){
    $requestURL=DB::table('requestBoc')->first();
    return view('autosuf.top')->with('Data_requests',$requestURL);
  }
  public function AutoBocTachFooter(){
        return view('autosuf.footer');
  }
  public function AutoBocTachReload(Request $request){
      $url_city=$request->input('url_city');
      $url_first=$request->input('url_first');
      $page_first=$request->input('page_first');
      $count_page=$request->input('count_page');
      $max=(int)$page_first+(int)$count_page;
      $update =DB::table('requestBoc')->limit(1) ->update([
          'begin' => $max
      ]);
      return 'ok';
      //dd($url_city);
  }


}
