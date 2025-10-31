@extends('admin.layouts.app')
@section('seo')
    <?php
    $data_seo = array(
        'title' => 'Danh sách chương | ' . $settings->where('type', 'seo_title')->first()->value,
        'keywords' => $settings->where('type', 'seo_keyword')->first()->value,
        'description' => $settings->where('type', 'seo_description')->first()->value,
        'og_title' => 'Danh sách chương | ' . $settings->where('type', 'seo_title')->first()->value,
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
                    <h1 class="m-0 text-dark">Danh sách chương</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Danh sách chương</li>
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
                            <h3 class="card-title">Danh sách chương</h3>
                        </div> <!-- /.card-header -->
                        <div class="card-body">
                            <ul class="nav">
                                <li class="nav-item">
                                    <a class="btn btn-danger" onclick="delete_id('chapter')"
                                       href="javascript:void(0)">
                                        <i class="fas fa-trash"></i> Xoá
                                    </a>
                                </li>
                            </ul>
                            <br/>
                            <table class="table table-bordered" id="table_index"></table>

                            <script>
                                $(function () {
                                    let data2 ={!! $list_chapter !!};
                                    $('#table_index').DataTable({
                                        data: data2,
                                        columns: [
                                            {
                                                title: '<input type="checkbox" id="selectall" onclick="select_all()">',
                                                data: 'id'
                                            },
                                            {title: 'Tiêu đề', data: 'name'},
                                            {title: 'Thứ tự chương', data: 'vol_number'},
                                            {title: 'Ngày tạo', data: 'created_at'},
                                        ],
                                        order: [[2, "desc"]],
                                        columnDefs: [
                                            {//ID
                                                visible: true,
                                                targets: 0,
                                                className: 'text-center',
                                                orderable: false,
                                                render: function (data, type, full, meta) {
                                                    return '<input type="checkbox" id="' + data + '" name="seq_list[]" value="' + data + '">';
                                                }
                                            },
                                            {//Title
                                                visible: true,
                                                targets: 1,
                                                className: 'text-center',
                                                render: function (data, type, full, meta) {
                                                    return '<a href="{{route("admin.dashboard")}}/chapter/' + full.story_id + '/' + full.id + '">' +
                                                        '<span class="bold">' + data + '</span><br/><b style="color:#c76805;">' + full.slug + '</b></a>';
                                                }
                                            },
                                            {//Vol Number
                                                visible: true,
                                                targets: 2,
                                                className: 'text-center',
                                                render: function (data, type, full, meta) {
                                                    return '<b>' + data + '</b> ';
                                                }
                                            },
                                            {//Created
                                                visible: true,
                                                targets: 3,
                                                className: 'text-center',
                                                render: function (data, type, full, meta) {
                                                    return getFormattedDate(data);
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
