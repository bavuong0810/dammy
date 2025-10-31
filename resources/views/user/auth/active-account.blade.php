@extends('layouts.app')
@section('seo')
    <?php
    $title = 'Xác thực tài khoản - ' . Helpers::get_setting('seo_title');
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
                                        <div class="my-4">
                                            <p class="mb-0">Xin chào, {{ Auth::user()->name }}</p>
                                            <div class="account_infomation">
                                                @if(Session::has('success_msg'))
                                                    <div class="mgt-10  alert alert-success alert-dismissible fade in" role="alert">
                                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                                        {{ Session::get('success_msg') }}
                                                    </div>
                                                @endif
                                                <div class="email-validate">
                                                    Tài khoản của bạn chưa được xác thực. <br/>
                                                    Nếu bạn chưa nhận được mail xác thực, <a href="{{ route('user.resendActiveEmail') }}">nhấn vào đây để gửi lại</a>.
                                                    <br>
                                                    Nếu vẫn không tìm thấy email, bạn vui lòng kiểm tra email trong hộp thư <b>Spam</b>.
                                                </div>
                                            </div>
                                        </div>
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
