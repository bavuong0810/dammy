@extends('layouts.app')
@section('seo')
<?php
$title='Liên hệ | '.Helpers::get_setting('seo_title');
$description='Liên hệ - '.Helpers::get_setting('seo_description');
$keyword='lien he,'.Helpers::get_setting('seo_keyword');
$thumb_img_seo=url('/images/').'/logo_1397577072.png';
$data_seo = array(
    'title' => $title,
    'keywords' => $keyword,
    'description' =>$description,
    'og_title' => $title,
    'og_description' => $description,
    'og_url' => Request::url(),
    'og_img' => $thumb_img_seo,
    'current_url' =>Request::url(),
    'current_url_amp' => ''
);
$seo = WebService::getSEO($data_seo);
?>
@include('partials.seo')
@endsection
@section('content')
<div class="main_content clear">
    <div class="container clear">
            <div class="project_index clear">
                <div class="container_contact clear">
                     <div class="wrapper-contact-form clear">
                         <div class="lien-he-container clear">
                             @if(Session::has('success_msg'))
                                 <div class="mgt-10  alert alert-success alert-dismissible" role="alert">
                                     <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                     {{ Session::get('success_msg') }}
                                 </div>
                             @endif
                                <div class="row">
                                    <div class="col-lg-6 col-md-6 col-xs-12 pull-left">
                                        <p class="sort_name"> <span class="title">{!!Helpers::get_setting('company_name')!!}</span></p>
                                        <div class="view_order clear">
                                            <b>{!!Helpers::get_option('info-footer-address')!!}</b>
                                        </div><!--view_order-->
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-xs-12 pull-right map_create">
                                        @if ($errors->any())
                                            <div class="mgt-10 alert alert-danger">
                                                <ul>
                                                    @foreach ($errors->all() as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                        <div id="frm_contact" class="frm_contact clear">
                                            <form id="contactForm" action="{{route('storeContact')}}" method="post">
                                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                <div class="form-group">
                                                    <input type="text" class="form-control" id="your_name" name="your_name" value="" placeholder="Họ và tên(*)">
                                                    <p id="erroryour_name"></p>
                                                </div>
                                                <div class="form-group">
                                                    <input type="email" class="form-control" id="your_email" name="your_email"  placeholder="Email(*)">
                                                    <p id="erroryour_email"></p>
                                                </div>
                                                <div class="form-group">
                                               <textarea class="form-control" type="textarea" id="your_message" name="your_message" placeholder="Góp ý(*)" maxlength="1000" rows="4"></textarea>
                                                <p id="erroryour_message"></p>
                                                 </div>
                                                 <div class="form-group content_tbl_contact clear text-center">
                                                    <button type="submit" id="submit" name="tbl_submit" class="btn btn-success">
                                                        <i class="fas fa-paper-plane"></i> Gửi
                                                    </button>
                                                 </div>
                                            </form>
                                        </div><!--frm_contact-->
                                    </div><!--map_create-->
                                </div><!--row-->
                         </div>
                     </div><!--wrapper-contact-form-->
                </div><!--container_contact-->
        </div>

    </div>
</div>
 <script type="text/javascript">
      var onloadCallback = function() {
        grecaptcha.render('capchar_google_element', {
          'sitekey' : ''
        });
      };
    </script>
<script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit"async defer></script>
@endsection
