@extends('admin.layouts.app')
<?php
$title = $story->name;
$post_title = $story->name;
$another_title = $story->another_name;
$post_slug = $story->slug;
$content = $story->content;
$category = ($story->categories != '') ? json_decode($story->categories, true) : [];
$total_view = $story->otal_view;
$is_full = $story->is_full;
$hot = $story->hot;
$author = $story->author;
$thumbnail = $story->thumbnail;
$date_update = $story->updated_at;
$proposed = $story->proposed;
$cover_image = $story->cover_image;
$id = $story->id;
$link_url_check = route('story.detail', $story->slug);
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
        <form action="{{route('admin.story.store')}}" method="POST" id="frm-create-post" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id" value="{{$id}}">
            @if(Session::has('success_msg'))
                <div class="mgt-10  alert alert-success alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    {{ Session::get('success_msg') }}
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{$title}}</h3>
                </div> <!-- /.card-header -->
                <div class="card-body">
                    <!-- show error form -->
                    <div class="errorTxt"></div>
                    <div class="form-group">
                        <label for="post_title">Tiêu đề</label>
                        <input type="text" class="form-control title_slugify" id="post_title" name="post_title" placeholder="Tiêu đề" value="{{ $post_title }}">
                    </div>
                    <div class="form-group">
                        @if($id > 0)
                            <b style="color: #0000cc;">Demo Link:</b>
                            <u>
                                <i>
                                    <a  style="color: #F00;" href="<?php echo  $link_url_check; ?>" target="_blank">
                                            <?php echo  $link_url_check; ?>
                                    </a>
                                </i>
                            </u>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="another_title">Tên khác</label>
                        <input id="another_title" type="text" value="{{ $another_title }}" name="another_title" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="content">Nội dung</label>
                        <textarea id="content" name="content" class="form-control">{!! $content !!}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="is_full">Đã đủ bộ</label>
                        <input id="is_full" type="checkbox" value="1" name="is_full"
                               @if($is_full == 1) checked @endif data-toggle="toggle">
                    </div>

                    <div class="form-group">
                        <label for="proposed">Đề cử</label>
                        <input id="proposed" type="checkbox" value="1" name="proposed"
                               @if($proposed == 1) checked @endif data-toggle="toggle">
                    </div>
                    <div class="form-group">
                        <label for="logo">Ảnh cover</label>
                        <input type="file" name="cover_image" id="cover_image" class="form-control" accept="image/*">
                        <?php
                        if ($cover_image != '') {
                            $cover_image = asset($cover_image);
                        }
                        ?>
                        @if($cover_image != '')
                            <div class="mt-2 text-center">
                                <img src="{{ $cover_image }}" style="max-height: 400px">
                            </div>
                        @endif
                    </div>
                </div> <!-- /.card-body -->
            </div><!-- /.card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Thể loại truyện</h3>
                </div> <!-- /.card-header -->
                <div class="card-body">
                    <?php
                    $list_cate = App\Models\Category::orderBy('name', 'ASC')->select('name', 'id')->get();
                    ?>
                    <div class="list_category row">
                        <div class="col-md-4">
                            <?php
                            $count_item = count($list_cate);
                            ?>
                            @foreach($list_cate as $key => $cate)
                                <div class="custom-control custom-checkbox mb-1">
                                    <input type="checkbox" class="category_item_input" id="category_item_{{$cate->id}}"
                                           value="{{$cate->id}}" name="category_item[]"
                                           @if(in_array($cate->id, $category)) checked @endif
                                    >
                                    <label for="category_item_{{$cate->id}}">{{$cate->name}}</label>
                                </div>
                                @if($key == floor($count_item / 2))
                                </div>
                                <div class="col-md-4">
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div> <!-- /.card-body -->
            </div><!-- /.card -->

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Image Thumbnail</h3>
                </div> <!-- /.card-header -->
                <div class="card-body">
                    <div class="form-group">
                        <label for="thumbnail_file">File input (.jpg, .jpeg, .png)</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" name="thumbnail_file" class="custom-file-input" id="thumbnail_file" style="display: none;">
                                <input type="text" name="thumbnail_file_link" class="custom-file-link form-control" id="thumbnail_file_link" value="{{ $thumbnail }}">
                                <label class="custom-file-label custom-file-label-thumb" for="thumbnail_file"></label>
                            </div>
                        </div>
                        @if($thumbnail != "")
                            <div class="demo-img" style="padding-top: 15px; text-align: center; width: 150px; margin: 0 auto">
                                <img src="{{ asset('images/story/' . $thumbnail) }}">
                            </div>
                        @endif
                    </div>

                    <div class="form-group text-center">
                        <button type="submit" class="btn btn-success">Lưu</button>
                    </div>
                </div> <!-- /.card-body -->
            </div>
        </form>
  	</div> <!-- /.container-fluid -->
</section>
<script type="text/javascript">
    jQuery(document).ready(function ($){
        $('.slug_slugify').slugify('.title_slugify');

        $('#thumbnail_file').change(function(evt) {
            $("#thumbnail_file_link").val($(this).val());
            $("#thumbnail_file_link").attr("value",$(this).val());
        });

        //xử lý validate
        $("#frm-create-post").validate({
            rules: {
                post_title: "required",
                'category_item[]': { required: true, minlength: 1 }
            },
            messages: {
                post_title: "Nhập tiêu đề truyện tranh",
                'category_item[]': "Chọn thể loại truyện tranh",
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
    CKEDITOR.replace('content',{
        width: '100%',
        resize_maxWidth: '100%',
        resize_minWidth: '100%',
        height:'300'
    });
    CKEDITOR.instances['content'];
</script>
@endsection
