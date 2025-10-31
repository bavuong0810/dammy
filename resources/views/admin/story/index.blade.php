@extends('admin.layouts.app')
@section('seo')
<?php
    $data_seo = array(
    'title' => 'Truyện | '.$settings->where('type', 'seo_title')->first()->value,
    'keywords' => $settings->where('type', 'seo_keyword')->first()->value,
    'description' => $settings->where('type', 'seo_description')->first()->value,
    'og_title' => 'Truyện | '.$settings->where('type', 'seo_title')->first()->value,
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
        <h1 class="m-0 text-dark">Truyện</h1>
      </div><!-- /.col -->
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
          <li class="breadcrumb-item active">Truyện</li>
        </ol>
      </div><!-- /.col -->
    </div><!-- /.row -->
  </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->
<!-- Main content -->
<section class="content">
  	<div class="container-fluid">
        <div class="mt-2 mb-2 text-center" id="waiting-delete" style="display: none">
            <img src="{{ asset('img/ajax-loader.gif') }}" alt="">
        </div>
	    <div class="row">
	      	<div class="col-12">
	        	<div class="card">
		          	<div class="card-header">
		            	<h3 class="card-title">Truyện</h3>
		          	</div> <!-- /.card-header -->
		          	<div class="card-body">

                        @if ($errors->any())
                            <div class="mgt-10 alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if(Session::has('success_msg'))
                            <div class="mgt-10  alert alert-success alert-dismissible" role="alert">
                                {{ Session::get('success_msg') }}
                            </div>
                        @endif

                        <div class="clear">
                            <ul class="nav fl">
                                <li class="nav-item">
                                    <a class="btn btn-danger" onclick="delete_id('story')" href="javascript:void(0)"><i class="fas fa-trash"></i> Delete</a>
                                </li>
                            </ul>
                            <div class="fr">
                                <form method="GET" action="{{route('admin.story.index')}}" id="frm-filter-post" class="form-inline">
                                    <div class="form-check mb-2 mr-sm-2">
                                        <input class="form-check-input" type="checkbox" id="long_story"
                                               name="long_story" value="1"
                                               @if(request()->get('long_story') == 1) checked @endif>
                                        <label class="form-check-label" for="long_story">
                                            Lọc truyện dài
                                        </label>
                                    </div>
                                    <select class="custom-select mr-2" name="story_type">
                                        <option value="">Tất cả truyện</option>
                                        <option value="proposed"
                                                @if(request()->get('story_type') == 'proposed') selected @endif>
                                            Truyện đề cử
                                        </option>
                                    </select>
                                    <?php
                                        $list_cate = App\Models\Category::orderBy('name', 'ASC')->select('id', 'name')->get();
                                    ?>
                                    <select class="custom-select mr-2" name="category">
                                        <option value="">Thể loại truyện</option>
                                        @foreach($list_cate as $cate)
                                            <option value="{{$cate->id}}"
                                                @if(isset($_GET['category']) && $_GET['category'] == $cate->id)
                                                    selected
                                                @endif>
                                                {{$cate->name}}
                                            </option>
                                        @endforeach
                                    </select>
                                    <input type="text" class="form-control" name="search_title" id="search_title"
                                           placeholder="Từ khoá" value="{{ Request::get('search_title') }}">
                                    <button type="submit" class="btn btn-primary ml-2">Tìm kiếm</button>
                                </form>
                            </div>
                        </div>
                        <div class="my-3">
                            <b>Tổng số truyện: </b> {{ $totalStories }}
                        </div>
                        <br/>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="table_index">
                                <thead>
                                    <tr>
                                        <th scope="col" class="text-center"><input type="checkbox" id="selectall" onclick="select_all()"></th>
                                        <th scope="col" class="text-center">Tiêu đề</th>
                                        <th scope="col" class="text-center">Ảnh bìa</th>
                                        <th scope="col" class="text-center">Ngày tạo</th>
                                        <th scope="col" class="text-center">Trạng thái</th>
                                        <th scope="col" class="text-center">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($list as $data)
                                    <tr>
                                        <td class="text-center"><input type="checkbox" id="{{$data->id}}" name="seq_list[]" value="{{$data->id}}"></td>
                                        <td class="text-center">
                                            <a class="row-title" href="{{route('admin.story.detail', $data->id)}}">
                                                <b>{{$data->name}}</b>
                                                <br>
                                                <b style="color:#c76805;">{{$data->slug}}</b>
                                            </a>
                                            <br/>
                                            <a href="{{ route('admin.chapter.index',  $data->id) }}">Danh sách chương</a>
                                        </td>
                                        <td class="text-center">
                                            @if($data->thumbnail != '')
                                                <img src="{{ asset('images/story/' . $data->thumbnail) }}" style="height: 100px">
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            {{ $data->created_at }}
                                        </td>
                                        <td class="text-center" id="statusResult_{{ $data->id }}">
                                            @if($data->status == \App\Constants\BaseConstants::ACTIVE)
                                                <span class="badge badge-success">Hoạt động</span>
                                            @else
                                                <span class="badge badge-danger">Đã ẩn</span>
                                            @endif

                                            @if($data->audio == \App\Constants\BaseConstants::ACTIVE)
                                                <div class="my-3">
                                                    <span class="badge badge-success">Hỗ trợ Audio</span>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a href="javascript:void(0)" class="btn btn-sm btn-warning mr-2"
                                               onclick="transferComics({{ $data->id }}, '{{ $data->name }}')">Chuyển nhượng</a>
                                            @if($data->status == \App\Constants\BaseConstants::ACTIVE)
                                            <a href="javascript:void(0)" class="btn btn-sm btn-danger mr-2" id="showHideComics_{{ $data->id }}"
                                               onclick="showHideComics({{ $data->id }}, '{{ $data->name }}', {{ $data->status }})">Ẩn</a>
                                            @else
                                                <a href="javascript:void(0)" class="btn btn-sm btn-success mr-2" id="showHideComics_{{ $data->id }}"
                                                   onclick="showHideComics({{ $data->id }}, '{{ $data->name }}', {{ $data->status }})">Hiển thị</a>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="fr">
                            {!! $list->links() !!}
                        </div>
		        	</div> <!-- /.card-body -->
	      		</div><!-- /.card -->
	    	</div> <!-- /.col -->
	  	</div> <!-- /.row -->
  	</div> <!-- /.container-fluid -->
</section>

<!-- Modal -->
<div class="modal fade" id="transferModal" tabindex="-1" role="dialog" aria-labelledby="transferModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold" id="transferModalLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.story.transfer') }}" method="POST">
                    @csrf
                    <input type="hidden" id="comics_transfer_id" name="comics_transfer_id">
                    <div class="form-group">
                        <label for="transfer_user">Chuyển nhượng cho User</label>
                        <select name="transfer_user" id="transfer_user" class="form-control">
                            <option value=""></option>
                            @foreach($translateTeams as $translateTeam)
                                <option value="{{ $translateTeam->id }}">
                                    {{ $translateTeam->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group text-center">
                        <button class="btn btn-primary" type="submit">Chuyển nhượng</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    jQuery(document).ready(function($) {
        $('#transfer_user').select2();
    })
    function transferComics(id, tieu_de) {
        $('#transferModalLabel').html(tieu_de);
        $('#comics_transfer_id').val(id);
        $('#transferModal').modal('show');
    }

    function showHideComics(id, tieu_de, status) {
        (function ($) {
            let textQ = 'ẩn';
            let textT = 'Ẩn';
            if (status === 0) {
                textQ = 'hiển thị';
                textT = 'Hiển thị';
            }
            Swal.fire({
                title: textT + ' truyện',
                text: 'Bạn muốn ' + textQ + ' truyện ' + tieu_de + '?',
                icon: 'question',
                showCancelButton: true,
                cancelButtonText: 'Huỷ',
                confirmButtonColor: '#0d6efd',
                confirmButtonText: 'OK',
            }).then((result) => {
                if (result.isConfirmed) {
                    var arr = {
                        "_token": getMetaContentByName("csrf-token"),
                        "story_id": id,
                        "status": status
                    };
                    $.ajax({
                        type: "POST",
                        url: "{{ route('admin.story.hide') }}",
                        data: arr,//pass the array to the ajax call
                        cache: false,
                        beforeSend: function () {

                        },
                        success: function (result) {
                            if (result.success) {
                                let resultHtml = '<span class="badge badge-danger">Đã ẩn</span>';
                                let newStatus = 0;
                                if (status === 0) {
                                    resultHtml = '<span class="badge badge-success">Hoạt động</span>';
                                    newStatus = 1;
                                    $('#showHideComics_' + id).removeClass('btn-success');
                                    $('#showHideComics_' + id).addClass('btn-danger');
                                    $('#showHideComics_' + id).html('Ẩn');
                                } else {
                                    $('#showHideComics_' + id).removeClass('btn-danger');
                                    $('#showHideComics_' + id).addClass('btn-success');
                                    $('#showHideComics_' + id).html('Hiển thị');
                                }
                                $('#statusResult_' + id).html(resultHtml);
                                $('#showHideComics_' + id).attr('onclick', 'showHideComics(' + id + ', \'' + tieu_de + '\', ' + newStatus + ')');

                                Swal.fire({
                                    icon: "success",
                                    title: "Thành công",
                                    text: result.message + ' ' + tieu_de,
                                    showConfirmButton: false,
                                    timer: 1200
                                });
                            } else {
                                Swal.fire(
                                    'Oops...',
                                    result.message,
                                    'error'
                                );
                            }
                        }
                    });//ajax
                }
            });
        })(jQuery);
    }
</script>
@endsection
