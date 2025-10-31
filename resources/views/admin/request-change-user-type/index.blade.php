@extends('admin.layouts.app')
@section('seo')
    <?php
    $data_seo = array(
        'title' => 'Duyệt yêu cầu đóng góp truyện | ' . $settings->where('type', 'seo_title')->first()->value,
        'keywords' => $settings->where('type', 'seo_keyword')->first()->value,
        'description' => $settings->where('type', 'seo_description')->first()->value,
        'og_title' => 'Duyệt yêu cầu đóng góp truyện | ' . $settings->where('type', 'seo_title')->first()->value,
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
                    <h1 class="m-0 text-dark">Duyệt yêu cầu đóng góp truyện</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Duyệt yêu cầu đóng góp truyện</li>
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
                            <h3 class="card-title">Duyệt yêu cầu đóng góp truyện</h3>
                        </div> <!-- /.card-header -->
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="users-table">
                                    <thead>
                                    <tr>
                                        <th class="text-center font-weight-bold">ID</th>
                                        <th class="text-center font-weight-bold">Người dùng</th>
                                        <th class="text-center font-weight-bold">Thông tin</th>
                                        <th class="text-center font-weight-bold">Trạng thái</th>
                                        <th class="text-center font-weight-bold">Ngày gửi</th>
                                        <th class="text-center font-weight-bold">Hành động</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($requestChangeUserTypes as $requestChangeUserType)
                                        <tr>
                                            <td>{{ $requestChangeUserType->id }}</td>
                                            <td>{{ $requestChangeUserType->user->name }}</td>
                                            <td>
                                                <b>SĐT: </b> {{ $requestChangeUserType->phone }}<br>
                                                <b>FB: </b> <a href="{{ $requestChangeUserType->facebook }}" target="_blank">
                                                    {{ $requestChangeUserType->facebook }}</a><br>
                                                <b>Ghi chú: </b>{{ $requestChangeUserType->note }}
                                            </td>
                                            <td>
                                                @if ($requestChangeUserType->status == \App\Models\RequestChangeUserType::Status['New'])
                                                    <span class="text-warning">Mới</span>
                                                @elseif ($requestChangeUserType->status == \App\Models\RequestChangeUserType::Status['Confirm'])
                                                    <span class="text-success">Đã xử lý</span>
                                                @else
                                                    <span class="text-danger">Đã huỷ</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $requestChangeUserType->created_at }}
                                            </td>
                                            <td class="text-center">
                                                @if($requestChangeUserType->status == \App\Models\RequestChangeUserType::Status['New'])
                                                    <button class="btn btn-sm btn-success mr-2 mb-2"
                                                            onclick="confirmRequestChangeType({{ $requestChangeUserType->id }})">
                                                        Duyệt
                                                    </button>
                                                    <button class="btn btn-sm btn-danger mr-2 mb-2"
                                                            onclick="cancelRequestChangeType({{ $requestChangeUserType->id }})">
                                                        Không duyệt
                                                    </button>
                                                @else
                                                    @if($requestChangeUserType->status == \App\Models\RequestChangeUserType::Status['Confirm'])
                                                        <span class="badge badge-success">Đã duyệt</span>
                                                    @else
                                                        <span class="badge badge-danger">Từ chối</span>
                                                    @endif
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="pagination">
                                {{ $requestChangeUserTypes->links() }}
                            </div>
                        </div> <!-- /.card-body -->
                    </div><!-- /.card -->
                </div> <!-- /.col -->
            </div> <!-- /.row -->
        </div> <!-- /.container-fluid -->
    </section>
@endsection
