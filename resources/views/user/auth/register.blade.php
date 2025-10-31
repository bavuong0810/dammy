@extends('layouts.app')
@section('seo')
    <?php
    $title = 'Đăng ký - ' . Helpers::get_setting('seo_title');
    $description = $title . ' - ' . Helpers::get_setting('seo_description');
    $keyword = Helpers::get_setting('seo_keyword');
    $thumb_img_seo = asset(Helpers::get_setting('seo_image'));
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
    <!--start page wrapper -->
    <div class="page-wrapper">
        <div class="page-content">
            <div class="d-flex align-items-center justify-content-center my-5">
                <div class="container-fluid">
                    <div class="row row-cols-1 row-cols-lg-2 row-cols-xl-3">
                        <div class="col mx-auto">
                            <div class="card mb-0">
                                <div class="card-body">
                                    <div class="p-4">
                                        <div class="mb-3 text-center">
                                            <img src="{{ asset(Helpers::get_setting('logo')) }}" width="120" alt="" />
                                        </div>
                                        <div class="text-center mb-4">
                                            <p class="mb-0">Điền thông tin bên dưới để tạo tài khoản</p>
                                        </div>
                                        <div class="form-body">
                                            <form class="row g-3" action="{{ route('postRegisterUser') }}" method="POST">
                                                @csrf
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

                                                <div class="col-12">
                                                    <label for="inputUsername" class="form-label">Email</label>
                                                    <input type="email" class="form-control" name="email" id="inputUsername" placeholder="example@user.com">
                                                </div>
                                                <div class="col-12">
                                                    <label for="inputEmailAddress" class="form-label">Tên hiển thị</label>
                                                    <input type="text" class="form-control" name="name" id="inputEmailAddress" placeholder="Tên của bạn">
                                                </div>
                                                <div class="col-12">
                                                    <label for="inputChoosePassword" class="form-label">Mật khẩu</label>
                                                    <div class="input-group" id="show_hide_password">
                                                        <input type="password" class="form-control border-end-0" name="password"
                                                               id="inputChoosePassword" placeholder="Mật khẩu của bạn">
                                                        <a href="javascript:;" class="input-group-text bg-transparent"><i class='bx bx-hide'></i>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <label for="inputConfirmPassword" class="form-label">Nhập lại mật khẩu</label>
                                                    <div class="input-group" id="show_hide_confirm_password">
                                                        <input type="password" name="password_confirmation" class="form-control border-end-0"
                                                               id="inputConfirmPassword" placeholder="Nhập lại mật khẩu">
                                                        <a href="javascript:;" class="input-group-text bg-transparent"><i class='bx bx-hide'></i></a>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="d-grid">
                                                        <button type="submit" class="btn btn-primary">Đăng ký</button>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="text-center ">
                                                        <p class="mb-0">Đã có tài khoản? <a href="{{ route('login') }}">Đăng nhập ngay</a></p>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        {{--
                                        <div class="login-separater text-center mb-5"> <span>Hoặc đăng ký với</span>
                                            <hr/>
                                        </div>
                                        <div class="list-inline contacts-social text-center">
                                            <a href="javascript:;" class="list-inline-item bg-google text-white border-0 rounded-3"><i class="bx bxl-google"></i></a>
                                        </div>
                                        --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end row-->
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <!--Password show & hide js -->
    <script>
        $(document).ready(function () {

            $("#show_hide_confirm_password a").on('click', function (event) {
                event.preventDefault();
                if ($('#show_hide_confirm_password input').attr("type") == "text") {
                    $('#show_hide_confirm_password input').attr('type', 'password');
                    $('#show_hide_confirm_password i').addClass("bx-hide");
                    $('#show_hide_confirm_password i').removeClass("bx-show");
                } else if ($('#show_hide_confirm_password input').attr("type") == "password") {
                    $('#show_hide_confirm_password input').attr('type', 'text');
                    $('#show_hide_confirm_password i').removeClass("bx-hide");
                    $('#show_hide_confirm_password i').addClass("bx-show");
                }
            });

            $("#show_hide_password a").on('click', function (event) {
                event.preventDefault();
                if ($('#show_hide_password input').attr("type") == "text") {
                    $('#show_hide_password input').attr('type', 'password');
                    $('#show_hide_password i').addClass("bx-hide");
                    $('#show_hide_password i').removeClass("bx-show");
                } else if ($('#show_hide_password input').attr("type") == "password") {
                    $('#show_hide_password input').attr('type', 'text');
                    $('#show_hide_password i').removeClass("bx-hide");
                    $('#show_hide_password i').addClass("bx-show");
                }
            });
        });
    </script>
@endsection
