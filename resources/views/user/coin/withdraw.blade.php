@extends('layouts.app')
@section('seo')
    <?php
    use App\Models\WithdrawRequest;
    $seo_title = $settings->where('type', 'seo_title')->first()->value;
    $seo_description = $settings->where('type', 'seo_description')->first()->value;
    $data_seo = array(
        'title' => 'Rút xu | ' . $seo_title,
        'keywords' => '',
        'description' => $seo_description,
        'og_title' => 'Rút xu | ' . $seo_title,
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
                            <li class="breadcrumb-item active" aria-current="page">Rút xu</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!--end breadcrumb-->
            <div class="row">
                <div class="col-xl-6 mx-auto">
                    <h6 class="mb-0 text-uppercase">Rút xu</h6>
                    <hr/>
                    <div class="card">
                        <div class="card-body">
                            <div class="withdraw-info">
                                <div class="info"></div>
                                <div class="form-withdraw">
                                    <?php
                                    $bankInfo = (Auth::user()->bank_account != '') ? json_decode(Auth::user()->bank_account, true) : '';
                                    $bankName = '';
                                    $accountNumber = '';
                                    $accountName = '';
                                    if ($bankInfo != '') {
                                        $bankName = $bankInfo['bank_name'];
                                        $accountNumber = $bankInfo['account_number'];
                                        $accountName = $bankInfo['account_name'];
                                    }
                                    ?>
                                    @if($bankName != '' && $accountName != '' && $accountNumber != '')
                                        <form action="{{ route('user.withdraw.process') }}" method="POST">
                                            @csrf
                                            <div class="card bg-warning text-center">
                                                <div class="card-body">
                                                    <div class="p-2 text-dark rounded">
                                                        <p>Xu hiện có: <b>{{ number_format(Auth::user()->coin->coin) }}</b> xu</p>
                                                        <p><b>Lưu ý: </b> Rút tối thiểu 100.000 xu một lần rút.</p>
                                                    </div>
                                                </div>
                                            </div>

                                            @if ($errors->any())
                                                <div class="mgt-10 alert alert-danger">
                                                    <ul>
                                                        @foreach ($errors->all() as $error)
                                                            <li>{{ $error }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif

                                            @if(Session::has('success_msg'))
                                                <div class="mgt-10  alert alert-success alert-dismissible" role="alert">
                                                    {{ Session::get('success_msg') }}
                                                </div>
                                            @endif

                                            <div class="mb-3">
                                                <label for="withdraw_coin" class="form-label">Số xu cần rút</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="bx bx-coin-stack"></i></span>
                                                    <input type="text" name="withdraw_coin" id="withdraw_coin" data-type="currency"
                                                           class="form-control" placeholder="Xu" value="{{ old('withdraw_coin') }}">
                                                </div>
                                            </div>
                                            <div class="mb-3 text-center">
                                                <button type="submit" class="btn btn-success">RÚT XU</button>
                                            </div>
                                        </form>
                                    @else
                                        <div class="card bg-danger text-center">
                                            <div class="card-body">
                                                <div class="p-2 text-white rounded">
                                                    Bạn cần cài đặt thông tin ngân hàng trước khi yêu cầu rút xu.
                                                    <a href="{{ route('user.profile') }}" style="color: #FFF"><b>Cài đặt ngay.</b></a>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    @if (count($withdrawRequests))
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table mb-0">
                                        <thead class="table-light">
                                        <tr>
                                            <th>Thời gian</th>
                                            <th>Code</th>
                                            <th>Số xu</th>
                                            <th>Trạng thái</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($withdrawRequests as $withdrawRequest)
                                            <tr>
                                                <td>
                                                    {{ date('d/m/Y H:i:s', strtotime($withdrawRequest->created_at)) }}
                                                </td>
                                                <td>{{ $withdrawRequest->code }}</td>
                                                <td><b>{{ number_format($withdrawRequest->coin) }}</b> xu</td>
                                                <td>
                                                    @if($withdrawRequest->status == WithdrawRequest::StatusType['New'])
                                                        <div class="badge rounded-pill text-success bg-light-success p-2 text-uppercase px-3">
                                                            <i class="bx bxs-circle me-1"></i>Chờ duyệt
                                                        </div>
                                                    @elseif($withdrawRequest->status == WithdrawRequest::StatusType['Confirm'])
                                                        <div class="badge rounded-pill text-info bg-light-info p-2 text-uppercase px-3">
                                                            <i class="bx bxs-circle align-middle me-1"></i>Đã duyệt
                                                        </div>
                                                    @else
                                                        <div class="badge rounded-pill text-danger bg-light-danger p-2 text-uppercase px-3">
                                                            <i class="bx bxs-circle align-middle me-1"></i>Đã huỷ
                                                        </div>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="pagination mt-4 justify-content-center">
                                    {!! $withdrawRequests->withQueryString()->links('vendor.pagination.rocker-pagination') !!}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            <!--end row-->
        </div>
    </div>
@endsection

@section('script')
    <script>

    </script>
@endsection
