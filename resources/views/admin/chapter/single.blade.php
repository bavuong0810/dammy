@extends('admin.layouts.app')
<?php
$user_role_id = Request()->user_role['role_id'];
$is_super_admin = ($user_role_id == \App\Constants\BaseConstants::SUPER_ADMIN_ROLE_ID) ? true : false;
$title = $chapter->name;
$slug = $chapter->slug;
$vol_number = $chapter->vol_number;
$date_update = $chapter->updated_at;
$content = $chapter->content;
$processing = $chapter->processing;
$coin = $chapter->coin;
$id = $chapter->id;
$story_id = $chapter->story_id;
?>
@section('seo')
    <?php
    $data_seo = array(
        'title' => $title . ' | ' . $settings->where('type', 'seo_title')->first()->value,
        'keywords' => $settings->where('type', 'seo_keyword')->first()->value,
        'description' => $settings->where('type', 'seo_description')->first()->value,
        'og_title' => $title . ' | ' . $settings->where('type', 'seo_title')->first()->value,
        'og_description' => $settings->where('type', 'seo_description')->first()->value,
        'og_url' => Request::url(),
        'og_img' => asset($settings->where('type', 'seo_image')->first()->value),
        'current_url' => Request::url(),
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
                    <h1 class="m-0 text-dark">{{ $title }}</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">{{ $title }}</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->

            <div class="d-flex justify-content-between">
                <div class="form-group">
                    <a href="{{ route('admin.chapter.index', $story_id) }}" class="btn btn-info">Quay lại danh sách
                        chương</a>
                </div>
                <div class="form-group">
                    <a href="javascript:void(0)" onclick="delete_id('chapter')" class="btn btn-danger">Xoá chương</a>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
    <!-- Main content -->
    <section class="content">
        <div class="text-center mb-4" id="form-loading" style="display: none">
            <img src="{{ asset('img/ajax-loader.gif') }}" alt="">
        </div>
        <div class="container-fluid">
            <div class="d-none">
                <input type="checkbox" name="seq_list[]" value="{{ $id }}" checked>
            </div>
            <form action="{{ route('admin.chapter.store') }}" method="POST" id="frm-create-category"
                  enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" value="{{ $id }}">
                <input type="hidden" name="story_id" value="{{ $story_id }}">
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
                        <h3 class="card-title">{{ $title }}</h3>
                    </div> <!-- /.card-header -->
                    <div class="card-body">
                        <!-- show error form -->
                        <div class="errorTxt"></div>
                        <div class="form-group">
                            <label for="post_title">Tên chương</label>
                            <input type="text" class="form-control title_slugify" id="post_title" name="post_title"
                                   placeholder="Tiêu đề" value="{{ $title }}">
                        </div>
                        <div class="form-group">
                            <label for="post_slug">Slug</label>
                            <input type="text" class="form-control slug_slugify" id="post_slug" name="post_slug"
                                   placeholder="Slug" value="{{ $slug }}">
                        </div>
                        <div class="form-group">
                            <label for="vol_number">Số thứ tự chương</label>
                            <input type="number" class="form-control" id="vol_number" name="vol_number"
                                   value="{{ $vol_number }}">
                        </div>
                        <div class="form-group">
                            <label for="inputCoin" class="form-label">Số coin cần để xem chương</label>
                            <input type="number" name="coin" class="form-control" id="inputCoin"
                                   placeholder="Xu" value="{{ $coin }}">
                        </div>
                        <!--********************************************Content Chapter**************************************************-->
                        <div class="form-group">
                            <label>Nội dung chương</label>
                            <textarea name="content" id="content" cols="30" rows="10"
                                      class="form-control">{!! $content !!}</textarea>
                        </div>
                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-success" id="save">Lưu</button>
                        </div>
                    </div> <!-- /.card-body -->
                </div><!-- /.card -->
            </form>
        </div> <!-- /.container-fluid -->
    </section>
    <script type="text/javascript">
        jQuery(document).ready(function ($) {
            $('.slug_slugify').slugify('.title_slugify');

            //xử lý validate
            $("#frm-create-category").validate({
                rules: {
                    post_title: "required",
                },
                messages: {
                    post_title: "Nhập tên chương",
                },
                errorElement: 'div',
                errorLabelContainer: '.errorTxt',
                invalidHandler: function (event, validator) {
                    $('html, body').animate({
                        scrollTop: 0
                    }, 500);
                }
            });

            CKEDITOR.replace('content',{
                width: '100%',
                resize_maxWidth: '100%',
                resize_minWidth: '100%',
                height:'300',
            });
            CKEDITOR.instances['content'];
        });
    </script>
@endsection
