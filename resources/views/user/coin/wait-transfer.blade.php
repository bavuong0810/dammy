@extends('layouts.app')
@section('seo')
<?php
$seo_title = $settings->where('type', 'seo_title')->first()->value;
$seo_description = $settings->where('type', 'seo_description')->first()->value;
$data_seo = array(
    'title' => 'Xác nhận chuyển khoản | ' . $seo_title,
    'keywords' => '',
    'description' => $seo_description,
    'og_title' => 'Xác nhận chuyển khoản | ' . $seo_title,
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
                            <li class="breadcrumb-item active" aria-current="page">Xác nhận chuyển khoản</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!--end breadcrumb-->
            <div class="row">
                <div class="col-xl-6 mx-auto">
                    <h6 class="mb-0 text-uppercase">Xác nhận chuyển khoản</h6>
                    <hr/>
                    <div class="card">
                        <div class="card-body">
                            <div class="text-center">
                                <h4 class="mb-3">Quét mã QR thanh toán</h4>
                                <?php
                                    $vietQRID = env('VIETQR_ID');
                                    $accountNumber = env('VIETQR_ACCOUNT_NUMBER');
                                    $templateId = env('VIETQR_TEMPLATE_ID');
                                    $accountName = env('VIETQR_ACCOUNT_NAME');
                                    $amount = $payment->amount;
                                    $content = $payment->code;
                                    $qrcode = "https://img.vietqr.io/image/{$vietQRID}-{$accountNumber}-{$templateId}.png?amount={$amount}&addInfo={$content}&accountName={$accountName}";
                                ?>
                                <img src="{{ $qrcode }}"
                                     alt="" width="300" style="border: 2px solid #008cff; border-radius: 5px;">
                                <p class="my-4" style="text-align: left; font-size: 16px">
                                    Hãy chuyển tiền tới tài khoản theo hướng dẫn: <br>
                                    Ngân hàng: <b>{{ env('VIETQR_BANK') }}</b> <br>
                                    Số tài khoản: <b>{{ env('VIETQR_ACCOUNT_NUMBER') }}</b> <br>
                                    Chủ tài khoản: <b>{{ env('VIETQR_ACCOUNT_NAME') }}</b> <br>
                                    Nội dung ghi: <b>{{ $payment->code }}</b> <br>
                                    * Thời gian nhận xu là 24h tính từ lúc chuyển khoản <br>
                                    * Vui lòng ghi đúng nội dung chuyển khoản để tránh nhầm lẫn trong việc nạp xu.
                                </p>
                            </div>
                            @if($payment->status == \App\Models\Payment::Status['New'])
                                <div class="input-group mb-3 justify-content-center">
                                    <button type="button" class="btn btn-primary px-5" onclick="confirmTransfer({{ $payment->id }})">
                                        Xác nhận đã chuyển khoản
                                    </button>
                                </div>
                            @elseif ($payment->status == \App\Models\Payment::Status['ConfirmTransfer'])
                                <div class="alert alert-info border-0 bg-info alert-dismissible fade show py-2">
                                    <div class="d-flex align-items-center">
                                        <div class="font-35 text-dark"><i class="bx bx-info-square"></i>
                                        </div>
                                        <div class="ms-3">
                                            <h6 class="mb-0 text-dark">Đã xác nhận chuyển khoản</h6>
                                            <div class="text-dark">Xu của bạn sẽ được cộng sau khi team quản trị xác nhận giao dịch!</div>
                                        </div>
                                    </div>
                                </div>
                            @elseif ($payment->status == \App\Models\Payment::Status['Confirm'])
                                <div class="alert alert-success border-0 bg-success alert-dismissible fade show py-2">
                                    <div class="d-flex align-items-center">
                                        <div class="font-35 text-white"><i class="bx bxs-check-circle"></i>
                                        </div>
                                        <div class="ms-3">
                                            <h6 class="mb-0 text-white">Nạp xu thành công</h6>
                                            <div class="text-white">Xu đã được cộng vào tài khoản của bạn!</div>
                                        </div>
                                    </div>
                                </div>
                            @elseif ($payment->status == \App\Models\Payment::Status['Cancel'])
                                <div class="alert alert-danger border-0 bg-danger alert-dismissible fade show py-2">
                                    <div class="d-flex align-items-center">
                                        <div class="font-35 text-white"><i class="bx bxs-message-square-x"></i>
                                        </div>
                                        <div class="ms-3">
                                            <h6 class="mb-0 text-white">Giao dịch đã bị huỷ</h6>
                                            <div class="text-white">Giao dịch của bạn đã bị huỷ! Nếu có vấn đề về giao dịch này vui lòng liên hệ team quản trị để được giải quyết</div>
                                        </div>
                                    </div>
                                </div>
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
    <script>
        function confirmTransfer(id) {
            (function ($) {
                var arr = {
                    "_token": getMetaContentByName("_token")
                };
                let body = $("body");
                $.ajax({
                    type: "POST",
                    url: site + "/user/nap-tien/{{ $payment->code }}/xac-nhan-chuyen-khoan",
                    data: arr,
                    cache: false,
                    beforeSend: function () {
                        body.addClass("loading");
                    },
                    success: function (result) {
                        body.removeClass("loading");
                        if (result.success) {
                            Swal.fire({
                                title: 'Thành công',
                                text: result.message,
                                icon: 'success',
                                showCancelButton: false,
                                confirmButtonColor: '#0d6efd',
                                confirmButtonText: 'OK',
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    location.reload();
                                }
                            })
                        } else {
                            Swal.fire(
                                'Oops...',
                                result.message,
                                'error'
                            );
                        }
                    }
                });
            })(jQuery);
        }
    </script>
@endsection
