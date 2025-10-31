@extends('layouts.app')
@section('seo')
<?php
$seo_title = $settings->where('type', 'seo_title')->first()->value;
$seo_description = $settings->where('type', 'seo_description')->first()->value;
$data_seo = array(
    'title' => 'Đổi mật khẩu | ' . $seo_title,
    'keywords' => '',
    'description' => $seo_description,
    'og_title' => 'Đổi mật khẩu | ' . $seo_title,
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
            <div class="row">
                <div class="col-xl-6 mx-auto">
                    <h6 class="mb-0 text-uppercase">Đổi mật khẩu</h6>
                    <hr/>
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('user.storeChangePassword') }}" method="POST">
                                @csrf
                                @if(Session::has('success_msg'))
                                    <div class="mgt-10  alert alert-success alert-dismissible" role="alert">
                                        {{ Session::get('success_msg') }}
                                    </div>
                                @endif

                                @if ($errors->any())
                                    <div class="mgt-10 alert alert-danger">
                                        @foreach ($errors->all() as $error)
                                            <p class="mb-1">{!! $error !!}</p>
                                        @endforeach
                                    </div>
                                @endif

                                @if((Auth::user()->register_with_social == 1 && Auth::user()->is_change_password == 1) || Auth::user()->register_with_social == 0)
                                    <div class="input-group mb-3">
                                        <div class="input-group" id="show_hide_current_password">
                                            <input type="password" class="form-control border-end-0" name="current_password"
                                                   id="inputCurrentPassword" placeholder="Mật khẩu hiện tại">
                                            <a href="javascript:;" class="input-group-text bg-transparent"><i class='bx bx-hide'></i>
                                            </a>
                                        </div>
                                    </div>
                                @endif

                                <div class="input-group mb-3">
                                    <div class="input-group" id="show_hide_password">
                                        <input type="password" class="form-control border-end-0" name="password"
                                               id="inputChoosePassword" placeholder="Mật khẩu mới">
                                        <a href="javascript:;" class="input-group-text bg-transparent"><i class='bx bx-hide'></i>
                                        </a>
                                    </div>
                                </div>

                                <div class="input-group mb-3">
                                    <div class="input-group" id="show_hide_confirm_password">
                                        <input type="password" name="password_confirmation" class="form-control border-end-0"
                                               id="inputConfirmPassword" placeholder="Nhập lại mật khẩu">
                                        <a href="javascript:;" class="input-group-text bg-transparent"><i class='bx bx-hide'></i></a>
                                    </div>
                                </div>

                                <div class="input-group mb-3 justify-content-center">
                                    <button type="submit" class="btn btn-primary px-5">Đổi mật khẩu</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!--end row-->
        </div>
    </div>
@endsection

@section('script')
    <!--Password show & hide js -->
    <script>
        $(document).ready(function () {
            addEventListener("submit", (event) => {
                let body = $('body');
                body.addClass("loading");
            });

            $("#show_hide_current_password a").on('click', function (event) {
                event.preventDefault();
                if ($('#show_hide_current_password input').attr("type") == "text") {
                    $('#show_hide_current_password input').attr('type', 'password');
                    $('#show_hide_current_password i').addClass("bx-hide");
                    $('#show_hide_current_password i').removeClass("bx-show");
                } else if ($('#show_hide_current_password input').attr("type") == "password") {
                    $('#show_hide_current_password input').attr('type', 'text');
                    $('#show_hide_current_password i').removeClass("bx-hide");
                    $('#show_hide_current_password i').addClass("bx-show");
                }
            });

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
