@extends('layouts.app')
@section('seo')
<?php
$seo_title = $settings->where('type', 'seo_title')->first()->value;
$seo_description = $settings->where('type', 'seo_description')->first()->value;
$data_seo = array(
    'title' => 'Mua VIP | ' . $seo_title,
    'keywords' => '',
    'description' => $seo_description,
    'og_title' => 'Mua VIP | ' . $seo_title,
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
                    <h6 class="mb-0 text-uppercase">Tài khoản Premium</h6>
                    <hr/>
                    <div class="card">
                        <div class="card-body">
                            <div class="card bg-warning text-center">
                                <div class="card-body">
                                    <div class="p-2 text-dark rounded">
                                        Khi trở thành Premium bạn sẽ được đọc truyện mà không có quảng cáo
                                    </div>
                                </div>
                            </div>

                            <?php
                                $user = Auth::user();
                                $user_money = $user->coin->coin;
                            ?>

                            <div class="card bg-info">
                                <div class="card-body">
                                    <div class="p-2 text-dark rounded">
                                        @if(Auth::user()->premium_date != '')
                                            <p class="mb-2"><b>Ngày hết hạn Premium: </b> {{ Auth::user()->premium_date }}</p>
                                        @endif
                                        <p class="mb-2"><b>Xu hiện có: </b> {{ number_format($user_money) }}</p>
                                    </div>
                                </div>
                            </div>

                            <form action="{{ route('user.processBuyVip') }}" method="POST">
                                @csrf
                                <h5 class="mb-3">
                                    @if(Auth::user()->premium_date != '') Gia hạn Premium: @else Nâng cấp Premium: @endif
                                </h5>

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

                                <div class="mb-4">
                                    @foreach($premiumPrices as $key => $premiumPrice)
                                        <?php
                                            $disable = '';
                                            $message = '';
                                            if ($user_money < $premiumPrice->price) {
                                                $disable = 'disabled';
                                                $message = '<span class="text-danger bg-light-danger p-2 badge rounded-pill">(Tài khoản của bạn không đủ coin)</span>';
                                            }
                                        ?>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="radio" name="premiumPrice" {{ $disable }}
                                                   id="premiumPrice{{ $key }}" value="{{ $premiumPrice->expired }}">
                                            <label class="form-check-label" for="premiumPrice{{ $key }}">
                                                {{ $premiumPrice->name }} - {{ number_format($premiumPrice->price) }} xu
                                            </label>
                                            {!! $message !!}
                                        </div>
                                    @endforeach
                                </div>
                                <div class="input-group mb-3 justify-content-center">
                                    <button type="submit" class="btn btn-primary px-5">Mua ngay</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!--end row-->

            <div class="row my-4">
                <div class="col-xl-6 mx-auto">
                    <h6 class="mb-0 text-uppercase">Lịch sử mua Premium</h6>
                    <hr/>
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table mb-0">
                                    <thead class="table-light">
                                    <tr>
                                        <th>ID#</th>
                                        <th>Thời gian cộng thêm</th>
                                        <th>Giá</th>
                                        <th>Ngày mua</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($premiumHistories as $premiumHistory)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="ms-2">
                                                        <h6 class="mb-0 font-14">#{{ $premiumHistory->id }}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $premiumHistory->time }} ngày</td>
                                            <td><i class="bx bx-coin-stack"></i> {{ number_format($premiumHistory->coin) }}</td>
                                            <td>{{ $premiumHistory->created_at }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="pagination mt-4 justify-content-center">
                                {!! $premiumHistories->withQueryString()->links('vendor.pagination.rocker-pagination') !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
