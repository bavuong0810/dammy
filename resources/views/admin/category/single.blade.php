@extends('admin.layouts.app')
<?php
if(isset($detail)){
    $title = $detail->name;
    $name = $detail->name;
    $description = $detail->description;
    $slug = $detail->slug;
    $sort = $detail->sort;
    $parent = $detail->parent;
    $thumbnail = $detail->thumbnail;
    $status = $detail->status;
    $seo_title = $detail->seo_title;
    $seo_description = $detail->seo_description;
    $seo_keyword = $detail->seo_keyword;
    $date_update = $detail->updated_at;
    $id = $detail->id;
} else{
    $title = 'Thêm thể loại truyện';
    $name = '';
    $description = '';
    $slug = '';
    $sort = '';
    $parent = '';
    $thumbnail = '';
    $status = '';
    $seo_title = '';
    $seo_description = '';
    $seo_keyword = '';
    $date_update = date('Y-m-d H:i:s');
    $id = 0;
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
        <form action="{{route('admin.category.store')}}" method="POST" id="frm-create-category" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id" value="{{ $id }}">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{$title}}</h3>
                </div> <!-- /.card-header -->
                <div class="card-body">
                    <!-- show error form -->
                    <div class="errorTxt"></div>

                    <div class="form-group">
                        <label for="title">Tiêu đề</label>
                        <input type="text" class="form-control" id="title" name="title" placeholder="Tiêu đề" value="{{ $name }}">
                    </div>
                    <div class="form-group">
                        <label for="description">Nội dung</label>
                        <textarea id="description" name="description">{!! $description !!}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="cat_thutu">Sắp xếp(Càng lớn càng nằm trên cùng)</label>
                        <input type="number" step="1" class="form-control" id="cat_thutu" name="cat_thutu" value="{{ $sort }}">
                    </div>
                    <div class="form-group text-center">
                        <button type="submit" class="btn btn-success">Lưu</button>
                    </div>
                </div> <!-- /.card-body -->
            </div><!-- /.card -->
        </form>
  	</div> <!-- /.container-fluid -->
</section>
<script type="text/javascript">
    jQuery(document).ready(function ($){
        //xử lý validate
        $("#frm-create-category").validate({
            rules: {
                title: "required",
            },
            messages: {
                title: "Nhập tiêu đề thể loại",
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
	CKEDITOR.replace('description',{
		width: '100%',
		resize_maxWidth: '100%',
		resize_minWidth: '100%',
		height:'300',
	});
	CKEDITOR.instances['description'];
</script>
@endsection
