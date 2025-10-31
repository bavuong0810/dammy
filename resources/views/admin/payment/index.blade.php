@extends('admin.layouts.app')
@section('seo')
    <?php
    $data_seo = array(
        'title' => 'Nap xu | ' . $settings->where('type', 'seo_title')->first()->value,
        'keywords' => $settings->where('type', 'seo_keyword')->first()->value,
        'description' => $settings->where('type', 'seo_description')->first()->value,
        'og_title' => 'Nap xu | ' . $settings->where('type', 'seo_title')->first()->value,
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
                    <h1 class="m-0 text-dark">Nap xu</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Nap xu</li>
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
                            <h3 class="card-title">Nap xu</h3>
                        </div> <!-- /.card-header -->
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="table_index"></table>
                            </div>
                            <script>
                                $(function () {
                                    let data2 = {!! $list !!};
                                    $('#table_index').DataTable({
                                        data: data2,
                                        columns: [
                                            {
                                                title: 'ID',
                                                data: 'id'
                                            },
                                            {title: 'Mã giao dịch', data: 'code'},
                                            {title: 'Số tiền', data: 'amount'},
                                            {title: 'Người nạp', data: 'user.name'},
                                            {title: 'Trạng thái', data: 'status'},
                                            {title: 'Ngày khởi tạo', data: 'created_at'},
                                            {title: 'Hành động', data: 'id'},
                                        ],
                                        order: [[0, "desc"]],
                                        columnDefs: [
                                            {//ID
                                                visible: true,
                                                targets: 0,
                                                className: 'text-center',
                                                render: function (data, type, full, meta) {
                                                    return data;
                                                }
                                            },
                                            {// Code
                                                visible: true,
                                                targets: 1,
                                                className: 'text-center',
                                                render: function (data, type, full, meta) {
                                                    return '<b>' + data + '</b>';
                                                }
                                            },
                                            {// Amount
                                                visible: true,
                                                targets: 2,
                                                className: 'text-center',
                                                render: function (data, type, full, meta) {
                                                    return number_format(data);
                                                }
                                            },
                                            {// User
                                                visible: true,
                                                targets: 3,
                                                className: 'text-center',
                                                render: function (data, type, full, meta) {
                                                    return data;
                                                }
                                            },
                                            {// Status
                                                visible: true,
                                                targets: 4,
                                                className: 'text-center',
                                                render: function (data, type, full, meta) {
                                                    switch (data) {
                                                        case 0:
                                                            return '<span class="text-success bg-light-success">Mới</span>';
                                                        case 1:
                                                            return '<span class="text-warning bg-light-warning">Xác nhận chuyển khoản</span>';
                                                        case 2:
                                                            return '<span class="text-info bg-light-info">Đã xác nhận</span>';
                                                        default:
                                                            return '<span class="text-danger bg-light-danger">Đã huỷ</span>';
                                                    }
                                                    return data;
                                                }
                                            },
                                            {//Created
                                                visible: true,
                                                targets: 5,
                                                className: 'text-center',
                                                render: function (data, type, full, meta) {
                                                    return getFormattedDate(data);
                                                }
                                            },
                                            {//Created
                                                visible: true,
                                                targets: 6,
                                                className: 'text-center',
                                                render: function (data, type, full, meta) {
                                                    if (full.status === 1) {
                                                        return '<button class="btn btn-sm btn-success mr-2 mb-2" onclick="confirmPayment(\'' + full.code + '\')">Xác nhận giao dịch</button>' +
                                                            '<button class="btn btn-sm btn-danger mr-2 mb-2" onclick="cancelPayment(\'' + full.code + '\')">Huỷ giao dịch</button>';
                                                    } else {
                                                        return '';
                                                    }
                                                }
                                            }
                                        ],
                                    });
                                });
                            </script>
                        </div> <!-- /.card-body -->
                    </div><!-- /.card -->
                </div> <!-- /.col -->
            </div> <!-- /.row -->
        </div> <!-- /.container-fluid -->
    </section>
@endsection

