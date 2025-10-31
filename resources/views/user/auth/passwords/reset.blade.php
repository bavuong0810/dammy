@extends('layouts.app')
@section('seo')
    <?php
    $title = 'Quên mật khẩu - ' . $settings->where('type', 'seo_title')->first()->value;
    $description = $title . ' - ' . $settings->where('type', 'seo_description')->first()->value;
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
    <!--page-wrapper-->
    <div class="page-wrapper">
        <div class="section-authentication-cover">
            <div class="">
                <div class="row g-0">
                    <div class="col-12 col-xl-7 col-xxl-8 auth-cover-left align-items-center justify-content-center d-none d-xl-flex">
                        <div class="card shadow-none bg-transparent shadow-none rounded-0 mb-0">
                            <div class="card-body">
                                <img src="{{ asset('assets/images/login-images/reset-password-cover.svg') }}"
                                     class="img-fluid" width="600" alt=""/>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-xl-5 col-xxl-4 auth-cover-right align-items-center justify-content-center">
                        <div class="card rounded-0 m-3 shadow-none bg-transparent mb-0">
                            <div class="card-body p-sm-5">
                                <div class="">
                                    <div class="mb-4 text-center">
                                        <img src="{{ asset($settings->where('type', 'logo')->first()->value) }}" width="110" alt="" />
                                    </div>
                                    <div class="text-start mb-4">
                                        <h5 class="">Đặt lại mật khẩu</h5>
                                        <p class="mb-0">Chúng tôi đã nhận được yêu cầu đặt lại mật khẩu của bạn. Hãy nhập mật khẩu mới của bạn</p>
                                    </div>

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

                                    <form action="{{ route('resetPassword', $token) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="token" value="{{ $token }}">
                                        <div class="mb-3 mt-4">
                                            <label class="form-label">Mật khẩu mới</label>
                                            <input type="password" name="password" class="form-control"
                                                   placeholder="Nhập mật khẩu mới" />
                                        </div>
                                        <div class="mb-4">
                                            <label class="form-label">Xác nhận lại mật khẩu</label>
                                            <input type="password" name="password_confirmation" class="form-control"
                                                   placeholder="Nhập mật khẩu xác nhận" />
                                        </div>
                                        <div class="d-grid gap-2">
                                            <button type="submit" class="btn btn-primary">Đổi mật khẩu</button>
                                            <a href="{{ route('login') }}" class="btn btn-light">
                                                <i class='bx bx-arrow-back mr-1'></i>Quay về trang đăng nhập
                                            </a>
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
@endsection
