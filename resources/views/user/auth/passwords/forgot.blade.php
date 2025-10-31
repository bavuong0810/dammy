@extends('layouts.app')
@section('seo')
    <?php
    $title = 'Quên mật khẩu - ' . $settings->where('type', 'seo_title')->first()->value;
    $description = $settings->where('type', 'seo_description')->first()->value;
    $keyword = '';
    $thumb_img_seo = asset($settings->where('type', 'seo_image')->first()->value);
    $data_seo = array(
        'title' => $title,
        'keywords' => $keyword,
        'description' => $description,
        'og_title' => $title,
        'og_description' => $description,
        'og_url' => Request::url(),
        'og_img' => $thumb_img_seo,
        'current_url' => Request::url(),
        'current_url_amp' => ''
    );
    $seo = WebService::getSEO($data_seo);
    ?>
    @include('partials.seo')
@endsection
@section('content')
    <!--wrapper-->
    <div class="page-wrapper">
        <div class="section-authentication-cover">
            <div class="">
                <div class="row g-0">
                    <div class="col-12 col-xl-7 col-xxl-8 auth-cover-left align-items-center justify-content-center d-none d-xl-flex">
                        <div class="card shadow-none bg-transparent shadow-none rounded-0 mb-0">
                            <div class="card-body">
                                <img src="{{ asset('assets/images/login-images/forgot-password-cover.svg') }}"
                                     class="img-fluid" width="600" alt=""/>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-xl-5 col-xxl-4 auth-cover-right align-items-center justify-content-center">
                        <div class="card rounded-0 m-3 shadow-none bg-transparent mb-0">
                            <div class="card-body p-sm-5">
                                <div class="p-3">
                                    <div class="text-center">
                                        <img src="{{ asset('assets/images/icons/forgot-2.png') }}" width="100" alt="" />
                                    </div>
                                    <h4 class="mt-5 font-weight-bold">Quên mật khẩu?</h4>
                                    <p class="text-muted">Nhập Email đăng ký của bạn để đặt lại mật khẩu</p>

                                    @if(Session::has('success_msg'))
                                        <div class="mgt-10 alert alert-success alert-dismissible" role="alert">
                                            {{ Session::get('success_msg') }}
                                        </div>
                                    @endif

                                    @if(count($errors))
                                        <div class="mgt-10 alert alert-danger alert-dismissible" role="alert">
                                            @foreach($errors->all() as $error)
                                                <p class="mb-1">{{ $error }}</p>
                                            @endforeach
                                        </div>
                                    @endif

                                    <form action="{{ route('forgotPassword.sendOTP') }}" method="POST">
                                        @csrf
                                        <div class="my-4">
                                            <label class="form-label">Email</label>
                                            <input type="email" name="email" class="form-control" placeholder="example@user.com" required />
                                        </div>
                                        <div class="d-grid gap-2">
                                            <button type="submit" class="btn btn-primary">Đặt lại mật khẩu</button>
                                            <a href="{{ route('login') }}" class="btn btn-light"><i class='bx bx-arrow-back me-1'></i> Quay lại trang đăng nhập</a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!--end row-->
            </div>
        </div>
    </div>
    <!--end wrapper-->
@endsection
