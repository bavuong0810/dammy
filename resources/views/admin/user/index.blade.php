@extends('admin.layouts.app')
@section('seo')
    <?php
    $data_seo = array(
        'title' => 'Danh sách người dùng | ' . $settings->where('type', 'seo_title')->first()->value,
        'keywords' => $settings->where('type', 'seo_keyword')->first()->value,
        'description' => $settings->where('type', 'seo_description')->first()->value,
        'og_title' => 'Danh sách người dùng | ' . $settings->where('type', 'seo_title')->first()->value,
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
                    <h1 class="m-0 text-dark">Danh sách người dùng</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Danh sách người dùng</li>
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
                            <h3 class="card-title">Danh sách người dùng</h3>
                        </div> <!-- /.card-header -->
                        <div class="card-body">

                            <div class="clear">
                                <div class="fr">
                                    <form method="GET" action="{{route('admin.user.index')}}" id="frm-filter-post" class="form-inline">
                                        <select name="type" id="type" class="form-control mr-2">
                                            <option value="">Tất cả</option>
                                            <option value="0" @if(Request::get('type') === 0) selected @endif>Độc giả</option>
                                            <option value="1" @if(Request::get('type') === 1) selected @endif>Nhóm dịch</option>
                                        </select>
                                        <input type="text" class="form-control mr-2" name="email" id="email"
                                               placeholder="Email" value="{{ Request::get('email') }}">
                                        <input type="text" class="form-control" name="name" id="name"
                                               placeholder="Tên hiển thị" value="{{ Request::get('name') }}">
                                        <button type="submit" class="btn btn-primary ml-2">Tìm kiếm</button>
                                    </form>
                                </div>
                            </div>

                            <div class="table-responsive mt-4">
                                <table class="table table-bordered" id="users-table">
                                    <thead>
                                    <tr>
                                        <th class="text-center font-weight-bold">ID</th>
                                        <th class="text-center font-weight-bold">Tên hiển thị</th>
                                        <th class="text-center font-weight-bold">Username</th>
                                        <th class="text-center font-weight-bold">Email</th>
                                        <th class="text-center font-weight-bold">Coin</th>
                                        <th class="text-center font-weight-bold">Trạng thái</th>
                                        <th class="text-center font-weight-bold">Lần login gần nhất</th>
                                        <th class="text-center font-weight-bold">Hành động</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($users as $user)
                                        <tr>
                                            <td>{{ $user->id }}</td>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->username }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>{{ (isset($user->coin->coin)) ? number_format($user->coin->coin) : 'Không tạo được' }}</td>
                                            <td class="text-center">
                                                @if ($user->active == 1)
                                                    <span class="text-success bg-light-success">Active</span>
                                                @else
                                                    <span class="text-danger bg-light-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td>{{ $user->last_login }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('admin.user.detail', $user->id) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="pagination">
                                {{ $users->withQueryString()->links() }}
                            </div>
                        </div> <!-- /.card-body -->
                    </div><!-- /.card -->
                </div> <!-- /.col -->
            </div> <!-- /.row -->
        </div> <!-- /.container-fluid -->
    </section>
@endsection
