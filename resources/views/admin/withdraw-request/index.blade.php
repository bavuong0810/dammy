@extends('admin.layouts.app')
@section('seo')
    <?php
    $data_seo = array(
        'title' => 'Yêu cầu rút xu | ' . $settings->where('type', 'seo_title')->first()->value,
        'keywords' => $settings->where('type', 'seo_keyword')->first()->value,
        'description' => $settings->where('type', 'seo_description')->first()->value,
        'og_title' => 'Yêu cầu rút xu | ' . $settings->where('type', 'seo_title')->first()->value,
        'og_description' => $settings->where('type', 'seo_description')->first()->value,
        'og_url' => Request::url(),
        'og_img' => asset($settings->where('type', 'seo_image')->first()->value),
        'current_url' => Request::url(),
        'current_url_amp' => ''
    );
    $seo = WebService::getSEO($data_seo);
    ?>
    @include('admin.partials.seo')
@endsection
@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Yêu cầu rút xu</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Yêu cầu rút xu</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Yêu cầu rút xu</h3>
                        </div> <!-- /.card-header -->
                        <div class="card-body">
                            <?php
                            $templateId = env('VIETQR_TEMPLATE_ID');
                            ?>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th class="text-center"><b>Mã giao dịch</b></th>
                                        <th class="text-center"><b>Xu cần rút</b></th>
                                        <th class="text-center"><b>Người rút</b></th>
                                        <th class="text-center"><b>Trạng thái</b></th>
                                        <th class="text-center"><b>Ngân hàng</b></th>
                                        <th class="text-center"><b>Hành động</b></th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    @foreach($list as $item)
                                        @if($item->user)
                                            <tr>
                                                <td class="text-center">
                                                    {{ $item->code }}
                                                </td>
                                                <td class="text-center">
                                                    {{ number_format($item->coin) }}
                                                </td>
                                                <td class="text-center">
                                                    {{ $item->user->name }}
                                                    <br> Xu hiện có: <b>{{ number_format($item->user->coin->coin) }}</b>
                                                </td>
                                                <td class="text-center">
                                                        <?php
                                                        switch ($item->status) {
                                                            case 0:
                                                                $status ='<span class="text-success bg-light-success">Mới</span>';
                                                                break;
                                                            case 1:
                                                                $status = '<span class="text-primary bg-light-primary">Đã xử lý</span>';
                                                                break;
                                                            default:
                                                                $status = '<span class="text-danger bg-light-danger">Đã huỷ</span>';
                                                                break;
                                                        }
                                                        ?>
                                                    <b>Ngày gửi: </b> {{ $item->created_at }}<br>
                                                    <b>Trạng thái: </b> {!! $status !!}
                                                </td>
                                                <td class="text-center">
                                                        <?php
                                                        $coin = (int)$item->coin;
                                                        $amount = $coin;
                                                        if ($item->status === 0) {
                                                            $bank = json_decode($item->user->bank_account, true);
                                                            $accountName = $bank['account_name'];
                                                            $qrcode = '';
                                                            if ($bank['bank_bin'] !== '') {
                                                                $qrcode = "https://img.vietqr.io/image/" . $bank['bank_bin'] . "-" . $bank['account_number'] . "-" . $templateId . ".png?amount=" . $amount . "&&accountName=" . $accountName . "&addInfo=" . $item->code;
                                                            } else {
                                                                $qrcode = "https://qrcode.tec-it.com/API/QRCode?data=2|99|" . $bank['account_number'] . "|0|0|0|0|" . $amount . "|" . $item->code . "&choe=UTF-8";
                                                            }
                                                            echo '<b>Số tiền rút được: </b>' . number_format($amount, 0, ',', '.') . '</br><img src="' . $qrcode . '" style="margin-top: 10px; max-width: 250px"></br>' . $bank['account_number'] . '</br>' . $accountName;
                                                        } else {
                                                            echo '<b>Số tiền đã rút: </b>' . number_format($amount, 0, ',', '.');
                                                        }
                                                        ?>
                                                </td>
                                                <td class="text-center">
                                                        <?php
                                                        if ($item->status == 0) {
                                                            echo '<button class="btn btn-sm btn-success mr-2 mb-2" onclick="confirmWithdrawRequest(\'' . $item->code . '\')">Đã xử lý</button>' .
                                                                '<button class="btn btn-sm btn-danger mr-2 mb-2" onclick="cancelWithdrawRequest(\'' . $item->code . '\')">Huỷ yêu cầu</button>';
                                                        } else {
                                                            echo '<span class="text-primary bg-light-primary">Đã xử lý</span>';
                                                        }
                                                        ?>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="fr">
                                {!! $list->links() !!}
                            </div>
                        </div> <!-- /.card-body -->
                    </div><!-- /.card -->
                </div> <!-- /.col -->
            </div> <!-- /.row -->
        </div> <!-- /.container-fluid -->
    </section>
@endsection

