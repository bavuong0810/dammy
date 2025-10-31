@extends('admin.layouts.app')
@section('seo')
    <?php
    $data_seo = array(
        'title' => 'Thống kê view ngày | ' . $settings->where('type', 'seo_title')->first()->value,
        'keywords' => $settings->where('type', 'seo_keyword')->first()->value,
        'description' => $settings->where('type', 'seo_description')->first()->value,
        'og_title' => 'Thống kê view ngày | ' . $settings->where('type', 'seo_title')->first()->value,
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
                    <h1 class="m-0 text-dark">Thống kê view ngày</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Thống kê view ngày</li>
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
                            <h3 class="card-title">Thống kê view ngày</h3>
                        </div> <!-- /.card-header -->
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="users-table">
                                    <thead>
                                    <tr>
                                        <th class="text-center font-weight-bold">ID</th>
                                        <th class="text-center font-weight-bold">Thời gian</th>
                                        <th class="text-center font-weight-bold">Tổng view</th>
                                        <th class="text-center font-weight-bold">Tổng chi phí</th>
                                        <th class="text-center font-weight-bold">View ngày</th>
                                        <th class="text-center font-weight-bold">Chi phí</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($reportViewDaily as $item)
                                        <tr>
                                            <td class="text-center">{{ $item->id }}</td>
                                            <td class="text-center">{{ $item->created_at }}</td>
                                            <td class="text-center">{{ number_format($item->total) }}</td>
                                            <td class="text-center">{{ number_format($item->total_money, 0, ',', '.') }}</td>
                                            <td class="text-center">
                                                {{ number_format($item->day) }}
                                            </td>
                                            <td class="text-center">
                                                {{ number_format($item->cost, 0, ',', '.') }} VNĐ
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="pagination">
                                {{ $reportViewDaily->links() }}
                            </div>
                        </div> <!-- /.card-body -->
                    </div><!-- /.card -->
                </div> <!-- /.col -->
            </div> <!-- /.row -->
        </div> <!-- /.container-fluid -->
    </section>
@endsection
