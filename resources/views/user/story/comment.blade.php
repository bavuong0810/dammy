@extends('layouts.app')
@section('seo')
<?php
$seo_title = $settings->where('type', 'seo_title')->first()->value;
$seo_description = $settings->where('type', 'seo_description')->first()->value;
$data_seo = array(
    'title' => 'Quản lý bình luận truyện #' . $story_id . ' | ' . $seo_title,
    'keywords' => '',
    'description' => $seo_description,
    'og_title' => 'Quản lý bình luận truyện #' . $story_id . ' | '. $seo_title,
    'og_description' => $seo_description,
    'og_url' => Request::url(),
    'og_img' => asset($settings->where('type', 'seo_image')->first()->value),
    'current_url' =>Request::url(),
    'current_url_amp' => ''
);
$seo = WebService::getSEO($data_seo);
?>
@include('partials.seo')
@endsection
@section('content')
    <!--start page wrapper -->
    <div class="page-wrapper">
        <div class="page-content">
            <!--breadcrumb-->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">Tủ truyện</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route('user.dashboard') }}"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Quản lý bình luận truyện #{{ $story_id }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!--end breadcrumb-->

            <div class="card">
                <div class="card-body">
                    <div class="d-lg-flex align-items-center mb-4 gap-3">
                        <div class="position-relative">
                            <form action="{{ route('user.story.comments', $story_id) }}" method="GET">
                                <input type="text" name="search" class="form-control ps-5 radius-30"
                                       placeholder="Tìm bình luận..." value="{{ request()->get('search') }}">
                                <span class="position-absolute top-50 product-show translate-middle-y"><i class="bx bx-search"></i></span>
                            </form>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead class="table-light">
                            <tr>
                                <th>Người dùng</th>
                                <th>Bình luận</th>
                                <th>Ngày cập nhật</th>
                                <th>Hành động</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($comments as $comment)
                                <?php
                                    $created = date('Y-m-d H:i:s', $comment->date_post);
                                ?>
                                <tr id="cmt_id_{{ $comment->id }}">
                                    <td>{{ $comment->user->name }}</td>
                                    <td>{{ $comment->content }}</td>
                                    <td>{{ $created }}</td>
                                    <td>
                                        <div class="d-flex order-actions">
                                            <a href="javascript:void(0)"
                                               onclick="removeComment({{ $comment->story_id }}, {{ $comment->id }})">
                                                <i class="bx bxs-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="pagination mt-4 justify-content-center">
                        {!! $comments->withQueryString()->links('vendor.pagination.rocker-pagination') !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        function removeComment(story_id, cmt_id) {
            (function ($) {
                Swal.fire({
                    title: 'Xoá bình luận',
                    text: 'Bạn muốn xoá bình luận này?',
                    icon: 'question',
                    showCancelButton: true,
                    cancelButtonText: 'Huỷ',
                    confirmButtonColor: '#0d6efd',
                    confirmButtonText: 'OK',
                }).then((result) => {
                    if (result.isConfirmed) {
                        var arr = {
                            "_token": getMetaContentByName("_token"),
                        };

                        let body = $("body");
                        $.ajax({
                            type: "DELETE",
                            url: site + "/user/truyen/" + story_id + "/comments/" + cmt_id,
                            data: arr,
                            cache: false,
                            beforeSend: function () {
                                body.addClass("loading");
                            },
                            success: function (result) {
                                body.removeClass("loading");
                                if (result.success) {
                                    Swal.fire({
                                        icon: "success",
                                        title: "Đã xoá bình luận",
                                        text: result.message,
                                        showConfirmButton: false,
                                        timer: 1500
                                    });
                                    $("#cmt_id_" + cmt_id).remove();
                                } else {
                                    Swal.fire(
                                        'Oops...',
                                        result.message,
                                        'error'
                                    );
                                }
                            }
                        });
                    }
                })
            })(jQuery);
        }
    </script>
@endsection
