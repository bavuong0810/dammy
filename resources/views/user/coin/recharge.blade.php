@extends('layouts.app')
@section('seo')
<?php
$seo_title = $settings->where('type', 'seo_title')->first()->value;
$seo_description = $settings->where('type', 'seo_description')->first()->value;
$data_seo = array(
    'title' => 'Nạp xu | ' . $seo_title,
    'keywords' => '',
    'description' => $seo_description,
    'og_title' => 'Nạp xu | ' . $seo_title,
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
            <!--breadcrumb-->
            <div class="page-breadcrumb d-flex align-items-center mb-3">
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route('index') }}">Trang chủ</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Nạp xu</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!--end breadcrumb-->
            <div class="row">
                <div class="col-xl-6 mx-auto">
                    <h6 class="mb-0 text-uppercase">Nạp xu</h6>
                    <hr/>
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('user.processRecharge') }}" method="POST">
                                @csrf
                                <div class="card bg-info text-center">
                                    <div class="card-body">
                                        <div class="p-2 text-white rounded">
                                            <p>Xu hiện có: <b>{{ number_format(Auth::user()->coin->coin) }}</b> xu</p>
                                            <p><b>Tỉ lệ quy đổi: </b> 1 xu = 1 VNĐ</p>
                                        </div>
                                    </div>
                                </div>

                                <h6>Chọn nhanh:</h6>
                                <div class="input-group mb-3 justify-content-center">
                                    <button type="button" class="btn btn-sm btn-outline-secondary px-3 radius-30" style="font-size: 13px"
                                            onclick="jQuery('#amount').val('10,000')">10,000</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary px-3 radius-30" style="font-size: 13px"
                                            onclick="jQuery('#amount').val('20,000')">20,000</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary px-3 radius-30" style="font-size: 13px"
                                            onclick="jQuery('#amount').val('50,000')">50,000</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary px-3 radius-30" style="font-size: 13px"
                                            onclick="jQuery('#amount').val('100,000')">100,000</button>
                                </div>
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" name="amount" placeholder="Số tiền bạn muốn nạp" data-type="currency"
                                           aria-label="Số tiền" aria-describedby="basic-addon1" id="amount" required value="{{ old('amount', '') }}">
                                    <span class="input-group-text" id="basic-addon2">VNĐ</span>
                                </div>
                                <div class="input-group mb-3 justify-content-center">
                                    <button type="submit" class="btn btn-primary px-5">Nạp</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="d-lg-flex align-items-center mb-4 gap-3">
                                <div class="position-relative">
                                    <form action="{{ route('user.recharge') }}" method="GET">
                                        <input type="text" name="code" class="form-control ps-5 radius-30" placeholder="Tìm giao dịch...">
                                        <span class="position-absolute top-50 product-show translate-middle-y"><i class="bx bx-search"></i></span>
                                    </form>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table mb-0">
                                    <thead class="table-light">
                                    <tr>
                                        <th>ID#</th>
                                        <th>Code</th>
                                        <th>Số tiền</th>
                                        <th>Trạng thái</th>
                                        <th>Ngày giao dịch</th>
                                        <th>Xem chi tiết</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($payments as $payment)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="ms-2">
                                                        <h6 class="mb-0 font-14">#{{ $payment->id }}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $payment->code }}</td>
                                            <td>{{ number_format($payment->amount) }}</td>
                                            <td>
                                                @if($payment->status == \App\Models\Payment::Status['Confirm'])
                                                    <div class="badge rounded-pill text-info bg-light-info p-2 text-uppercase px-3">
                                                        <i class="bx bxs-circle me-1"></i> Đã xác nhận
                                                    </div>
                                                @elseif($payment->status == \App\Models\Payment::Status['New'])
                                                    <div class="badge rounded-pill text-success bg-light-success p-2 text-uppercase px-3">
                                                        <i class="bx bxs-circle me-1"></i>
                                                        Mới
                                                    </div>
                                                @elseif($payment->status == \App\Models\Payment::Status['ConfirmTransfer'])
                                                    <div class="badge rounded-pill text-warning bg-light-warning p-2 text-uppercase px-3">
                                                        <i class="bx bxs-circle align-middle me-1"></i>Xác nhận chuyển khoản
                                                    </div>
                                                @elseif($payment->status == \App\Models\Payment::Status['Cancel'])
                                                    <div class="badge rounded-pill text-danger bg-light-danger p-2 text-uppercase px-3">
                                                        <i class="bx bxs-circle align-middle me-1"></i>Đã huỷ
                                                    </div>
                                                @endif
                                            </td>
                                            <td>{{ $payment->created_at }}</td>
                                            <td><a href="{{ route('user.coin.waitTransfer', $payment->code) }}" class="btn btn-primary btn-sm radius-30 px-4">Xem chi tiết</a></td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="pagination mt-4 justify-content-center">
                                {!! $payments->withQueryString()->links('vendor.pagination.rocker-pagination') !!}
                            </div>
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
