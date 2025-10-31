@extends('layouts.app')
@section('seo')
    <?php
    $seo_title = $settings->where('type', 'seo_title')->first()->value;
    $seo_description = $settings->where('type', 'seo_description')->first()->value;
    $data_seo = array(
        'title' => 'Đóng góp cùng Đam Mỹ | ' . $seo_title,
        'keywords' => '',
        'description' => $seo_description,
        'og_title' => 'Đóng góp cùng Đam Mỹ | ' . $seo_title,
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
                    <h6 class="mb-0 text-uppercase">Đóng góp cùng Đam Mỹ</h6>
                    <hr/>
                    <div class="card">
                        <div class="card-body">
                            <div class="card bg-primary text-center">
                                <div class="card-body">
                                    <div class="p-2 rounded">
                                        Trở thành tác giả/nhóm dịch cùng đóng góp truyện với Đam Mỹ
                                        <br>
                                        Sau khi gửi yêu cầu vui lòng gửi tin nhắn đến <a href="https://www.facebook.com/dammy.me" target="_blank">Fanpage Đam Mỹ</a> để xác thực tài khoản.
                                    </div>
                                </div>
                            </div>

                            <?php
                            $user = Auth::user();
                            $request_change_type = $user->request_change_type;
                            ?>

                            @if(Session::has('success_msg'))
                                <div class="mgt-10  alert alert-success alert-dismissible" role="alert">
                                    {{ Session::get('success_msg') }}
                                </div>
                            @endif

                            @if($request_change_type)
                                <div class="card bg-info">
                                    <div class="card-body">
                                        <div class="p-2 text-dark rounded">
                                            <p class="mb-2">
                                                Bạn đã gửi yêu cầu đăng ký cho Đam Mỹ, vui lòng chờ team xem xét và duyệt yêu cầu của bạn!
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <form action="{{ route('user.requestChangeUserType.process') }}" method="POST">
                                    @csrf
                                    @if ($errors->any())
                                        <div class="mgt-10 alert alert-danger">
                                            <ul>
                                                @foreach ($errors->all() as $error)
                                                    <li>{!! $error !!}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    <div class="mb-3">
                                        <input type="text" name="phone" value="{!! old('phone') !!}" class="form-control"
                                               placeholder="Số điện thoại" required>
                                    </div>
                                    <div class="mb-3">
                                        <input type="text" name="facebook" value="{!! old('facebook') !!}" class="form-control"
                                               placeholder="Link FB hoặc Fanpage" required>
                                    </div>
                                    <div class="mb-4">
                                        <textarea name="note" id="note" cols="30" rows="5" class="form-control"
                                                  placeholder="Lời nhắn cho Đam Mỹ" required>{!! old('note') !!}</textarea>
                                    </div>
                                    <div class="input-group mb-3 justify-content-center">
                                        <button type="submit" class="btn btn-primary px-5">Gửi yêu cầu</button>
                                    </div>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <!--end row-->
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        addEventListener("submit", (event) => {
            let body = $('body');
            body.addClass("loading");
        });
    </script>
@endsection
