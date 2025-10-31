@extends('admin.layouts.app')
@section('seo')
    <?php
    $data_seo = [
        'title' => 'Nhóm dịch | ' . $settings->where('type', 'seo_title')->first()->value,
        'keywords' => '',
        'description' => $settings->where('type', 'seo_description')->first()->value,
        'og_title' => 'Nhóm dịch | ' . $settings->where('type', 'seo_title')->first()->value,
        'og_description' => $settings->where('type', 'seo_description')->first()->value,
        'og_url' => Request::url(),
        'og_img' => asset($settings->where('type', 'seo_image')->first()->value),
        'current_url' => Request::url(),
        'current_url_amp' => ''
    ];

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
                    <h1 class="m-0 text-dark">Nhóm dịch</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Nhóm dịch</li>
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
                            <h3 class="card-title">Nhóm dịch</h3>
                        </div> <!-- /.card-header -->
                        <div class="card-body">

                            @if(request()->get('user') == 'vuong-do')
                                <div class="my-3 d-flex justify-content-start">
                                    <a href="{{ route('admin.translateTeam.export') }}" target="_blank"
                                       class="btn btn-success btn-sm mr-3">Xuất File</a>
                                    <a href="javascript:void(0)" onclick="resetView()"
                                       class="btn btn-danger btn-sm mr-2">Reset View</a>
                                </div>
                            @endif

                            <div class="clear">
                                <div class="fr">
                                    <form method="GET" action="{{route('admin.translateTeam.index')}}" id="frm-filter-post" class="form-inline">
                                        <input type="text" class="form-control mr-2" name="email" id="email"
                                               placeholder="Email" value="{{ Request::get('email') }}">
                                        <input type="text" class="form-control" name="name" id="name"
                                               placeholder="Tên hiển thị" value="{{ Request::get('name') }}">
                                        <button type="submit" class="btn btn-primary ml-2">Tìm kiếm</button>
                                    </form>
                                </div>
                            </div>

                            <div class="mt-3">
                                <b>Tổng view tháng: </b> {{ number_format($total_view) }} <br>
                                <b>Tổng xu cần trả: </b> {{ number_format($total_coin) }} <br>
                                <b>Tổng tiền cần trả tạm tính: </b> {{ number_format($total_money) }} VNĐ <br>
                                <b>So với ngày hôm trước: </b> {{ number_format($total_money - $lastDayReport->total_money) }} VNĐ <br>

                                <b>Doanh thu truyện đề cử tháng {{ date('m') }}: </b> {{ number_format($totalRevenueRecommendStory) }} Xu <br>
                                <b>Doanh thu nạp xu tháng {{ date('m') }}: </b> {{ number_format($total_payment) }} VNĐ <br>
                            </div>

                            <div class="table-responsive mt-4">
                                <table class="table table-bordered" id="users-table">
                                    <thead>
                                    <tr>
                                        <th class="text-center font-weight-bold">STT</th>
                                        <th class="text-center font-weight-bold">Tên hiển thị</th>
                                        <th class="text-center font-weight-bold">Username</th>
                                        <th class="text-center font-weight-bold">Lượt xem tháng</th>
                                        <th class="text-center font-weight-bold">Quy đổi xu</th>
                                        <th class="text-center font-weight-bold">Tiền nhận được</th>
                                        <th class="text-center font-weight-bold">Hành động</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($users as $key => $user)
                                        <tr>
                                            <td class="text-center">{{ $key + 1 }}</td>
                                            <td class="text-center">{{ $user->id }} - <a href="{{ route('admin.user.detail', $user->id) }}">{{ $user->name }}</a></td>
                                            <td class="text-center">{{ $user->username }}</td>
                                            <td class="text-center">{{ number_format($user->total_view_month) }}</td>
                                            <td class="text-center">{{ number_format($user->user_coin) }}</td>
                                            <td class="text-center">
                                                {{ number_format($user->money_will_get) }}
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-success" type="button"
                                                        onclick="convertViewForAuthor({{ $user->id }}, '{{ $user->name }}')">
                                                    Đổi xu
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div> <!-- /.card-body -->
                    </div><!-- /.card -->
                </div> <!-- /.col -->
            </div> <!-- /.row -->
        </div> <!-- /.container-fluid -->
    </section>
    <script>
        function convertViewForAuthor(id, name) {
            (function ($) {
                if (window.confirm('Bạn muốn chuyển đổi view sang xu cho team ' + name + '?')) {
                    let body = $('body');
                    var arr = {
                        "_token": getMetaContentByName("csrf-token"),
                    };
                    $.ajax({
                        type: "PUT",
                        url: admin_url + "/translate-team/convert-view/" + id,
                        data: arr,//pass the array to the ajax call
                        cache: false,
                        beforeSend: function () {
                            body.addClass("loading");
                        },
                        success: function (result) {
                            body.removeClass("loading");
                            if (result.success) {
                                Swal.fire({
                                    title: 'Thành công',
                                    text: result.message,
                                    icon: 'success',
                                    showCancelButton: false,
                                    confirmButtonColor: '#0d6efd',
                                    confirmButtonText: 'OK',
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        location.reload();
                                    }
                                })
                            } else {
                                Swal.fire(
                                    'Oops...',
                                    result.message,
                                    'error'
                                );
                            }
                        }
                    });//ajax
                } else {
                    return false;
                }
            })(jQuery);
        }

        function resetView() {
            (function ($) {
                if (window.confirm('Bạn muốn reset lại toàn bộ lượt xem của nhóm dịch?')) {
                    let body = $('body');
                    var arr = {
                        "_token": getMetaContentByName("csrf-token"),
                    };
                    $.ajax({
                        type: "POST",
                        url: admin_url + "/translate-team/reset-view",
                        data: arr,//pass the array to the ajax call
                        cache: false,
                        beforeSend: function () {
                            body.addClass("loading");
                        },
                        success: function (result) {
                            body.removeClass("loading");
                            if (result.success) {
                                Swal.fire({
                                    title: 'Thành công',
                                    text: result.message,
                                    icon: 'success',
                                    showCancelButton: false,
                                    confirmButtonColor: '#0d6efd',
                                    confirmButtonText: 'OK',
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        location.reload();
                                    }
                                })
                            } else {
                                Swal.fire(
                                    'Oops...',
                                    result.message,
                                    'error'
                                );
                            }
                        }
                    });//ajax
                } else {
                    return false;
                }
            })(jQuery);
        }
    </script>
@endsection
