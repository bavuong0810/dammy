@extends('admin.layouts.app')
<?php
$title = $detail->name;
$name = $detail->name;
$email = $detail->email;
$username = $detail->username;
$user_money = $detail->coin->coin;
$loai = $detail->loai;
$active = $detail->active;
$avatar = $detail->avatar;
$date_update = $detail->updated_at;
$id = $detail->user_id;
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
            <form action="{{route('admin.user.store')}}" method="POST" id="frm-create-page" enctype="multipart/form-data">
                @csrf
                @if(Session::has('success_msg'))
                    <div class="mgt-10  alert alert-success alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                        {{ Session::get('success_msg') }}
                    </div>
                @endif
                <input type="hidden" name="id" value="{{$id}}">
                <div class="row">
                    <div class="col-9">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">{{ $title }}</h3>
                            </div> <!-- /.card-header -->
                            <div class="card-body">
                                <!-- show error form -->
                                <div class="errorTxt"></div>
                                <div class="form-group text-center">
                                    @if($avatar != '')
                                        <img src="{{ $avatar }}" alt="" style="max-width: 300px">
                                    @else
                                        <img src="{{ asset('img/avata.png') }}" alt="" style="max-width: 300px">
                                    @endif
                                </div>
                                <div class="form-group text-center">
                                    <b>Coin hiện có: </b> {{ number_format($user_money) }}
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name">Tên hiển thị</label>
                                            <input type="text" class="form-control" id="name" name="name"
                                                   placeholder="Tên hiển thị" value="{{ $name }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="username">Username</label>
                                            <input id="username" type="text" name="username" class="form-control" value="{{ $username }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email">Email</label>
                                            <input id="email" type="email" name="email" class="form-control" value="{{ $email }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="loai">Loại tài khoản</label>
                                            <select name="loai" id="loai" class="form-control">
                                                <option value="0" @if($loai == 0) selected @endif>Độc giả</option>
                                                <option value="1" @if($loai == 1) selected @endif>Nhóm dịch</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="password">Đổi mật khẩu</label>
                                            <input id="password" type="password" name="password" class="form-control" value="">
                                            <input type="checkbox" name="change_password" value="1" id="change_password">
                                            <label for="change_password" class="mt-2" style="color: red">Xác nhận đổi mật khẩu?</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="coin">Cộng Coin cho người dùng</label>
                                            <input id="coin" type="number" name="coin" class="form-control" value="0">
                                            <input type="checkbox" name="add_coin" value="1" id="add_coin">
                                            <label for="add_coin" class="mt-2" style="color: red">Xác nhận cộng Coin?</label>
                                        </div>
                                    </div>
                                </div>
                            </div> <!-- /.card-body -->
                        </div><!-- /.card -->
                    </div> <!-- /.col-9 -->
                    <div class="col-3">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Hành động</h3>
                            </div> <!-- /.card-header -->
                            <div class="card-body">
                                <div class="form-group clearfix">
                                    <div class="icheck-primary d-inline">
                                        <input type="radio" id="radioDraft" name="status" value="0" @if($active == 0) checked @endif>
                                        <label for="radioDraft">Inactive</label>
                                    </div>
                                    <div class="icheck-primary d-inline" style="margin-left: 15px;">
                                        <input type="radio" id="radioPublic" name="status" value="1" @if($active == 1) checked @endif>
                                        <label for="radioPublic">Active</label>
                                    </div>
                                </div>
                                <div class="form-group text-right">
                                    <button type="submit" class="btn btn-success">Lưu</button>
                                </div>
                            </div> <!-- /.card-body -->
                        </div><!-- /.card -->
                    </div> <!-- /.col-9 -->
                </div> <!-- /.row -->
            </form>
        </div> <!-- /.container-fluid -->
    </section>
@endsection
