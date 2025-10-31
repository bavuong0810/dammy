@extends('layouts.app')
@section('seo')
<?php
$seo_title = $settings->where('type', 'seo_title')->first()->value;
$seo_description = $settings->where('type', 'seo_description')->first()->value;
$data_seo = array(
    'title' => 'Thông tin tài khoản | ' . $seo_title,
    'keywords' => $settings->where('type', 'seo_keyword')->first()->value,
    'description' => $seo_description,
    'og_title' => 'Thông tin tài khoản | ' . $seo_title,
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
    <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}"/>
    <style>
        .avatar-wrapper {
            position: relative;
            height: 200px;
            width: 200px;
            margin: 50px auto;
            border-radius: 50%;
            overflow: hidden;
            box-shadow: 1px 1px 15px -5px black;
            transition: all .3s ease;
        }

        .avatar-wrapper:hover {
            transform: scale(1.05);
            cursor: pointer;
        }

        .avatar-wrapper:hover .profile-pic {
            opacity: .5;
        }

        .avatar-wrapper .profile-pic {
            height: 100%;
            width: 100%;
            transition: all .3s ease;
        }

        .avatar-wrapper .profile-pic:after {
            font-family: 'boxicons' !important;
            content: "\e9c9";
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            position: absolute;
            font-size: 130px;
            background: #ecf0f1;
            color: #34495e;
            font-weight: 900;
            text-align: center;
        }

        .upload-button {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            width: 100%;
        }

        .upload-button .bx-cloud-upload {
            position: absolute;
            font-size: 200px;
            top: 50%;
            left: 50%;
            text-align: center;
            opacity: 0;
            transition: all .3s ease;
            color: #34495e;
            transform: translate(-50%, -50%);
        }

        .upload-button:hover .bx-cloud-upload {
            opacity: .9;
        }
    </style>
    <!--start page wrapper -->
    <div class="page-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-xl-6 mx-auto">
                    <h6 class="mb-0 text-uppercase">Thông tin tài khoản</h6>
                    <hr/>
                    <div class="card">
                        <div class="card-body">
                            @if(Auth::user()->register_with_social == 1 && Auth::user()->is_change_password == 0)
                                <div class="alert alert-danger border-0 bg-primary alert-dismissible fade show py-3 my-3">
                                    <div class="d-flex align-items-center">
                                        <div class="font-35 text-white"><i class="bx bx-bell"></i>
                                        </div>
                                        <div class="ms-3">
                                            <h6 class="mb-2 text-white font-18">Chú ý</h6>
                                            <div class="text-white">Vì bạn đã đăng nhập thông qua Google, vui lòng cập nhật mật khẩu riêng của mình để quá trình sử dụng không bị gián đoạn</div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($user->type == \App\Models\User::UserType['TranslateTeam'] && ($user->facebook == '' || $user->phone == ''))
                                <div class="alert alert-danger border-0 bg-danger alert-dismissible fade show py-3 my-3">
                                    <div class="d-flex align-items-center">
                                        <div class="font-35 text-white"><i class="bx bx-bell"></i>
                                        </div>
                                        <div class="ms-3">
                                            <h6 class="mb-2 text-white font-18">Chú ý</h6>
                                            <div class="text-white">
                                                Để có thể tiếp tục đăng truyện, chương mới, các team bắt buộc phải cung cấp Facebook và Số điện thoại của mình để tiện cho Đam Mỹ theo dõi và đưa ra các thông báo kịp thời đến team.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <form action="{{ route('user.updateProfile') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @if(Session::has('success_msg'))
                                    <div class="mgt-10  alert alert-success alert-dismissible" role="alert">
                                        {{ Session::get('success_msg') }}
                                    </div>
                                @endif

                                @if ($errors->any())
                                    <div class="mgt-10 alert alert-danger">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{!! $error !!}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <?php
                                    if ($user->avatar == '') {
                                        $avatar = asset('img/avata.png');
                                    } else {
                                        $avatar = asset('images/avatar/' . $user->avatar);
                                    }
                                ?>

                                <div class="form-group text-center">
                                    <div class="avatar-wrapper mb-3">
                                        <img class="profile-pic" width="150"
                                             src="{{ $avatar }}"/>
                                        <div class="upload-button">
                                            <i class="bx bx-cloud-upload"></i>
                                        </div>
                                        <input class="file-upload" type="file" name="avatar" accept="image/*"/>
                                        <input type="hidden" name="avatar_file_link"
                                               value="{{ $user->avatar }}">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="name" class="form-label">Tên hiển thị:</label>
                                    <input type="text" class="form-control" name="name" id="name"
                                           value="{{ $user->name }}">
                                </div>

                                <div class="mb-3">
                                    <label for="username" class="form-label">Username:</label>
                                    <input type="text" class="form-control" name="username" id="username"
                                           value="{{ $user->username }}">
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">Email:</label>
                                    <input type="email" class="form-control" name="email" id="email"
                                           value="{{ $user->email }}" @if($user->email != '') disabled readonly @endif>
                                </div>

                                <div class="mb-3">
                                    <label for="facebook" class="form-label">Facebook:</label>
                                    <input type="text" class="form-control" name="facebook" id="facebook"
                                           @if($user->type == \App\Models\User::UserType['TranslateTeam']) required @endif
                                           value="{{ $user->facebook }}">
                                </div>

                                @if($user->type == \App\Models\User::UserType['TranslateTeam'])
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Số điện thoại:</label>
                                        <input type="text" class="form-control" name="phone" id="phone" required
                                               value="{{ $user->phone }}">
                                    </div>
                                @endif

                                <div class="mb-4">
                                    <label for="about_me" class="form-label">Tiểu sử:</label>
                                    <textarea name="about_me" id="about_me" cols="30" rows="5"
                                              class="form-control">{!! $user->about_me !!}</textarea>
                                </div>

                                @if($user->type == \App\Models\User::UserType['TranslateTeam'])
                                    <?php
                                    $bankInfo = ($user->bank_account != '') ? json_decode($user->bank_account, true) : '';
                                    $bankName = '';
                                    $accountNumber = '';
                                    $accountName = '';
                                    $useMomo = false;
                                    $phoneNumber = '';
                                    $momoAccountName = '';
                                    if ($bankInfo != '') {
                                        $bankName = $bankInfo['bank_name'];
                                        if ($bankName == 'momo') {
                                            $useMomo = true;
                                            $phoneNumber = $bankInfo['account_number'];
                                            $momoAccountName = $bankInfo['account_name'];
                                        } else {
                                            $accountNumber = $bankInfo['account_number'];
                                            $accountName = $bankInfo['account_name'];
                                        }
                                    }
                                    $banks = Helpers::getBanks();
                                    ?>
                                    <hr>
                                    <div class="mb-4" id="signature">
                                        <label for="team_signature" class="form-label"><b>Chữ ký team:</b></label>
                                        <textarea name="team_signature" id="team_signature" cols="30" rows="5"
                                                  class="form-control">{!! $user->team_signature !!}</textarea>
                                    </div>
                                    <hr>
                                    <h5 class="text-center">Phương thức thanh toán</h5>
                                    <div class="mb-3">
                                        <label for="payment_method" class="form-label">Chọn phương thức thanh toán:</label>
                                        <select name="payment_method" id="payment_method" class="form-control">
                                            <option value="bank" @if(!$useMomo) selected @endif>Ngân hàng</option>
                                            <option value="momo" @if($useMomo) selected @endif>MoMo</option>
                                        </select>
                                    </div>

                                    <div class="bank_method" @if($useMomo) style="display: none" @endif>
                                        <div class="mb-3">
                                            <label for="bank_name" class="form-label">Ngân hàng:</label>
                                            <select name="bank_name" id="bank_name" class="form-control">
                                                <option value="">Chọn ngân hàng</option>
                                                @foreach($banks as $bank)
                                                    <option value="{{ $bank->shortName }}" @if($bank->shortName == $bankName) selected @endif>
                                                        {{ $bank->shortName }} - {{ $bank->code }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="account_number" class="form-label">Số tài khoản:</label>
                                            <input type="text" name="account_number" id="account_number"
                                                   class="form-control" value="{{ $accountNumber }}">
                                        </div>
                                        <div class="mb-3">
                                            <label for="account_name" class="form-label">Tên chủ tài khoản:</label>
                                            <input type="text" name="account_name" id="account_name"
                                                   class="form-control" value="{{ $accountName }}">
                                        </div>
                                    </div>
                                    <div class="momo_method" @if(!$useMomo) style="display: none" @endif>
                                        <div class="mb-3">
                                            <label for="phone_number" class="form-label">Số điện thoại:</label>
                                            <input type="text" name="phone_number" id="phone_number"
                                                   class="form-control" value="{{ $phoneNumber }}">
                                        </div>
                                        <div class="mb-3">
                                            <label for="momo_account_name" class="form-label">Tên chủ tài khoản:</label>
                                            <input type="text" name="momo_account_name" id="momo_account_name"
                                                   class="form-control" value="{{ $momoAccountName }}">
                                        </div>
                                    </div>
                                @endif
                                <div class="input-group mb-3 justify-content-center">
                                    <button type="submit" class="btn btn-primary px-5">Cập nhật</button>
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
    <script src="{{ asset('plugins/select2/js/select2.min.js') }}"></script>
    <script>
        jQuery(document).ready(function ($) {
            addEventListener("submit", (event) => {
                let body = $('body');
                body.addClass("loading");
            });

            $('#bank_name').select2();
            //js upload avatar admin
            var readURL = function (input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();

                    reader.onload = function (e) {
                        $('.profile-pic').attr('src', e.target.result);
                    }

                    reader.readAsDataURL(input.files[0]);
                }
            }

            $(".file-upload").on('change', function () {
                readURL(this);
            });

            $(".upload-button").on('click', function () {
                $(".file-upload").click();
            });

            $('#payment_method').on('change', function () {
                if ($('#payment_method').val() === 'momo') {
                    $('.momo_method').show();
                    $('.bank_method').hide();
                } else {
                    $('.momo_method').hide();
                    $('.bank_method').show();
                }
            });
        });
    </script>
@endsection
