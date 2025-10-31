@extends('admin.layouts.app')
@section('seo')
    <?php
    $data_seo = array(
        'title' => 'Báo cáo lỗi | ' . $settings->where('type', 'seo_title')->first()->value,
        'keywords' => $settings->where('type', 'seo_keyword')->first()->value,
        'description' => $settings->where('type', 'seo_description')->first()->value,
        'og_title' => 'Báo cáo lỗi | ' . $settings->where('type', 'seo_title')->first()->value,
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
                    <h1 class="m-0 text-dark">Báo cáo lỗi</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Báo cáo lỗi</li>
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
                            <h3 class="card-title">Báo cáo lỗi</h3>
                        </div> <!-- /.card-header -->
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="users-table">
                                    <thead>
                                    <tr>
                                        <th class="text-center font-weight-bold">ID</th>
                                        <th class="text-center font-weight-bold">Người dùng</th>
                                        <th class="text-center font-weight-bold">Lỗi</th>
                                        <th class="text-center font-weight-bold">Truyện</th>
                                        <th class="text-center font-weight-bold">Chapter</th>
                                        <th class="text-center font-weight-bold">Mô tả</th>
                                        <th class="text-center font-weight-bold">Trạng thái</th>
                                        <th class="text-center font-weight-bold">Ngày báo cáo</th>
                                        <th class="text-center font-weight-bold">Hành động</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($reports as $report)
                                        <tr>
                                            <td>{{ $report->id }}</td>
                                            <td>@if($report->user) {{ $report->user->name }} @endif</td>
                                            <td>{{ $report->error }}</td>
                                            <td>
                                                @if ($report->story)
                                                    <a href="{{ route('story.detail', $report->story->slug) }}">
                                                        {{ $report->story->name }}
                                                    </a>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($report->chapter)
                                                    <a href="{{ route('chapter.detail', [$report->story->slug, $report->chapter->slug]) }}" target="_blank">
                                                        {{ $report->chapter->name }}
                                                    </a>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $report->note }}
                                            </td>
                                            <td>
                                                @if ($report->status == \App\Models\Report::StatusType['New'])
                                                    <span class="text-warning">Mới</span>
                                                @elseif ($report->status == \App\Models\Report::StatusType['Confirm'])
                                                    <span class="text-success">Đã xử lý</span>
                                                @else
                                                    <span class="text-danger">Đã huỷ</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $report->created_at }}
                                            </td>
                                            <td class="text-center">
                                                @if ($report->status == \App\Models\Report::StatusType['New'])
                                                    <button class="btn btn-sm btn-success mr-2" onclick="confirmError({{ $report->id }})">Xác nhận lỗi</button>
                                                    <button class="btn btn-sm btn-danger mr-2" onclick="cancelError({{ $report->id }})">Huỷ lỗi</button>
                                                @else
                                                    <span class="badge badge-info">Đã xử lý</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="pagination">
                                {{ $reports->links() }}
                            </div>
                        </div> <!-- /.card-body -->
                    </div><!-- /.card -->
                </div> <!-- /.col -->
            </div> <!-- /.row -->
        </div> <!-- /.container-fluid -->
    </section>
@endsection
