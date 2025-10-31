@extends('admin.layouts.app')
@section('seo')
    <?php
    $data_seo = array(
        'title' => 'Đánh giá truyện | ' . $settings->where('type', 'seo_title')->first()->value,
        'keywords' => $settings->where('type', 'seo_keyword')->first()->value,
        'description' => $settings->where('type', 'seo_description')->first()->value,
        'og_title' => 'Đánh giá truyện | ' . $settings->where('type', 'seo_title')->first()->value,
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
                    <h1 class="m-0 text-dark">Đánh giá truyện</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Đánh giá truyện</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Đánh giá truyện</h3>
                        </div> <!-- /.card-header -->
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="users-table">
                                    <thead>
                                    <tr>
                                        <th class="text-center font-weight-bold">ID</th>
                                        <th class="text-center font-weight-bold">Người dùng</th>
                                        <th class="text-center font-weight-bold">Đánh giá</th>
                                        <th class="text-center font-weight-bold">Truyện</th>
                                        <th class="text-center font-weight-bold">Thời gian</th>
                                        <th class="text-center font-weight-bold">Trạng thái</th>
                                        <th class="text-center font-weight-bold">Hành động</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($reviews as $review)
                                        <tr>
                                            <td>{{ $review->id }}</td>
                                            <td>{{ $review->user->name }}</td>
                                            <td>
                                                <b>Rating: </b> {{ $review->rating }} ⭐ <br>
                                                <b>Nội dung: </b>{{ $review->content }}
                                            </td>
                                            <td>
                                                @if ($review->story)
                                                    <a href="{{ route('story.detail', $review->story->slug) }}">
                                                        {{ $review->story->name }}
                                                    </a>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $review->created_at }}
                                            </td>
                                            <td>
                                                <select name="status-{{ $review->id }}" id="status-{{ $review->id }}" class="form-control">
                                                    <option value="0" @if($review->status == 0) selected @endif>
                                                        Chờ duyệt
                                                    </option>
                                                    <option value="1" @if($review->status == 1) selected @endif>
                                                        Duyệt
                                                    </option>
                                                    <option value="2" @if($review->status == 2) selected @endif>
                                                        Không duyệt
                                                    </option>
                                                </select>
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-sm btn-success mr-2 mb-2"
                                                        onclick="quickUpdate({{ $review->id }}, '{{ $review->story->name }}')">
                                                    Cập nhật nhanh
                                                </button>
                                                <a class="btn btn-sm btn-primary mr-2 mb-2" href="{{ route('admin.review.detail', $review->id) }}">Chi tiết</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="pagination">
                                {{ $reviews->links() }}
                            </div>
                        </div> <!-- /.card-body -->
                    </div><!-- /.card -->
                </div> <!-- /.col -->
            </div> <!-- /.row -->
        </div> <!-- /.container-fluid -->
    </section>

    <script>
        jQuery(document).ready(function($) {
            $('#transfer_user').select2();
        })

        function quickUpdate(id, tieu_de) {
            (function ($) {
                Swal.fire({
                    title: 'Cập nhật đánh giá truyện',
                    text: 'Bạn muốn cập nhật đánh giá cho truyện ' + tieu_de + '?',
                    icon: 'question',
                    showCancelButton: true,
                    cancelButtonText: 'Huỷ',
                    confirmButtonColor: '#0d6efd',
                    confirmButtonText: 'OK',
                }).then((result) => {
                    if (result.isConfirmed) {
                        let status = $('#status-' + id).val();
                        var arr = {
                            "_token": getMetaContentByName("csrf-token"),
                            "review_id": id,
                            "status": status
                        };
                        $.ajax({
                            type: "POST",
                            url: "{{ route('admin.review.quickAction') }}",
                            data: arr,//pass the array to the ajax call
                            cache: false,
                            beforeSend: function () {

                            },
                            success: function (result) {
                                if (result.success) {
                                    Swal.fire({
                                        icon: "success",
                                        title: "Thành công",
                                        text: 'Cập nhật thành công trạng thái đánh giá cho truyện ' + tieu_de,
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
