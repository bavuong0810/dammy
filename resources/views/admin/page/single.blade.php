@extends('admin.layouts.app')
<?php
if(isset($detail)){
    $title = $detail->title;
    $post_title = $detail->title;
    $post_slug = $detail->slug;
    $post_description = $detail->description;
    $post_content = $detail->content;
    $template = $detail->template;
    $status = $detail->status;
    $date_update = $detail->updated_at;
    $id = $detail->id;
    $link_demo = route('page.detail', $detail->slug);
} else{
    $title = 'Thêm trang';
    $post_title = "";
    $post_slug = "";
    $post_description = "";
    $post_content = "";
    $template = 0;
    $status = \App\Constants\BaseConstants::ACTIVE;
    $date_update = date('Y-m-d H:i:s');
    $id = 0;
    $link_demo = "";
}
?>
@section('seo')
<?php
    $data_seo = array(
    'title' => $title.' | '.$settings->where('type', 'seo_title')->first()->value,
    'keywords' => $settings->where('type', 'seo_keyword')->first()->value,
    'description' => $settings->where('type', 'seo_description')->first()->value,
    'og_title' => $title.' | '.$settings->where('type', 'seo_title')->first()->value,
    'og_description' => $settings->where('type', 'seo_description')->first()->value,
    'og_url' => Request::url(),
    'og_img' => asset($settings->where('type', 'seo_image')->first()->value),
    'current_url' =>Request::url(),
    'current_url_amp' => ''
);
$seo = WebService::getSEO($data_seo);
?>
@include('admin.partials.seo')
@endsection
@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark">{{$title}}</h1>
      </div><!-- /.col -->
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
          <li class="breadcrumb-item active">{{$title}}</li>
        </ol>
      </div><!-- /.col -->
    </div><!-- /.row -->
  </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->
<!-- Main content -->
<section class="content">
  	<div class="container-fluid">
        <form action="{{route('admin.storePage')}}" method="POST" id="frm-create-page" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id" value="{{$id}}">
    	    <div class="row">
    	      	<div class="col-9">
    	        	<div class="card">
    		          	<div class="card-header">
    		            	<h3 class="card-title">Post Page</h3>
    		          	</div> <!-- /.card-header -->
    		          	<div class="card-body">
                            <!-- show error form -->
                            <div class="errorTxt"></div>
                            <div class="form-group">
                                <label for="post_title">Tiêu đề</label>
                                <input type="text" class="form-control title_slugify" id="post_title" name="post_title" placeholder="Tiêu đề" value="{{$post_title}}">
                            </div>
                            <div class="form-group">
                                @if($link_demo != "")
                                    <span style="color: red;"><strong>Link:</strong></span><a href="{{$link_demo}}" target="_blank">{{$link_demo}}</a>
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="post_description">Trích dẫn</label>
                                <textarea id="post_description" name="post_description">{!!$post_description!!}</textarea>
                            </div>
                            <div class="form-group">
                                <label for="post_content">Nội dung</label>
                                <textarea id="post_content" name="post_content">{!!$post_content!!}</textarea>
                            </div>
                            <div class="form-group">
                                <label for="template_checkID" style="color: #0C7FCC;" class="title_txt">Template Page (<i style="color: #0b2e13; font-size: 11px;">Lựa chọn sẽ không có link ra ngoài, sử dụng template html</i>)</label>
                                <input id="template_checkID" type="checkbox" name="template" value="1" @if($template == 1) checked @endif>
                            </div>
    		        	</div> <!-- /.card-body -->
    	      		</div><!-- /.card -->
    	    	</div> <!-- /.col-9 -->
                <div class="col-3">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Publish</h3>
                        </div> <!-- /.card-header -->
                        <div class="card-body">
                            <div class="form-group clearfix">
                                <div class="icheck-primary d-inline">
                                    <input type="radio" id="radioDraft" name="status" value="0" @if($status == 0) checked @endif>
                                    <label for="radioDraft">Draft</label>
                                </div>
                                <div class="icheck-primary d-inline" style="margin-left: 15px;">
                                    <input type="radio" id="radioPublic" name="status" value="1" @if($status == 1) checked @endif>
                                    <label for="radioPublic">Public</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Date:</label>
                                <div class="input-group date" id="reservationdate" data-target-input="nearest">
                                    <input type="text" name="created" class="form-control datetimepicker-input" data-target="#reservationdate" value="{{$date_update}}">
                                    <div class="input-group-append" data-target="#reservationdate" data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group text-right">
                                <button type="submit" class="btn btn-success">Save</button>
                            </div>
                        </div> <!-- /.card-body -->
                    </div><!-- /.card -->
                </div> <!-- /.col-9 -->
    	  	</div> <!-- /.row -->
        </form>
  	</div> <!-- /.container-fluid -->
</section>
<script type="text/javascript">
    jQuery(document).ready(function ($){
        $('.slug_slugify').slugify('.title_slugify');

        //Date range picker
        $('#reservationdate').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss'
        });

        $('#thumbnail_file').change(function(evt) {
            $("#thumbnail_file_link").val($(this).val());
            $("#thumbnail_file_link").attr("value",$(this).val());
        });

        //xử lý validate
        $("#frm-create-page").validate({
            rules: {
                post_title: "required",
            },
            messages: {
                post_title: "Nhập tiêu đề trang",
            },
            errorElement : 'div',
            errorLabelContainer: '.errorTxt',
            invalidHandler: function(event, validator) {
                $('html, body').animate({
                    scrollTop: 0
                }, 500);
            }
        });
    });
</script>
<script type="text/javascript">
	CKEDITOR.replace('post_content',{
		width: '100%',
		resize_maxWidth: '100%',
		resize_minWidth: '100%',
		height:'300',
		filebrowserBrowseUrl: '{{ route('ckfinder_browser') }}',
	});
	CKEDITOR.instances['post_content'];

    CKEDITOR.replace('post_description',{
        width: '100%',
        resize_maxWidth: '100%',
        resize_minWidth: '100%',
        height:'300',
        filebrowserBrowseUrl: '{{ route('ckfinder_browser') }}',
    });
    CKEDITOR.instances['post_description'];
</script>
@endsection
