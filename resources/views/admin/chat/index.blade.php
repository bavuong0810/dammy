@extends('admin.layouts.app')
@section('seo')
    <?php
    $data_seo = array(
        'title' => 'Quản lý Chat | ' . $settings->where('type', 'seo_title')->first()->value,
        'keywords' => $settings->where('type', 'seo_keyword')->first()->value,
        'description' => $settings->where('type', 'seo_description')->first()->value,
        'og_title' => 'Quản lý Chat | ' . $settings->where('type', 'seo_title')->first()->value,
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
                    <h1 class="m-0 text-dark">Quản lý Chat</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Quản lý Chat</li>
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
                            <h3 class="card-title">Quản lý Chat</h3>
                        </div> <!-- /.card-header -->
                        <div class="card-body">
                            <div class="clear">
                                <div class="fr">
                                    <form method="GET" action="{{route('admin.chat.index')}}" id="frm-filter-post" class="form-inline">
                                        <input type="text" class="form-control mr-2" name="user_name" id="search_user_name"
                                               placeholder="Tên người dùng" value="{{ Request::get('user_name') }}">
                                        <input type="text" class="form-control" name="content" id="search_content"
                                               placeholder="Nội dung chat" value="{{ Request::get('content') }}">
                                        <button type="submit" class="btn btn-primary ml-2">Tìm kiếm</button>
                                    </form>
                                </div>
                            </div>
                            <br>
                            <div class="table-responsive">
                                <table class="table table-bordered" id="users-table">
                                    <thead>
                                    <tr>
                                        <th class="text-center font-weight-bold">ID</th>
                                        <th class="text-center font-weight-bold">Người dùng</th>
                                        <th class="text-center font-weight-bold">Nội dung</th>
                                        <th class="text-center font-weight-bold">Ngày khởi tạo</th>
                                        <th class="text-center font-weight-bold">Hành động</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($chats as $chat)
                                        <tr id="chat_id_{{ $chat->id }}">
                                            <td>{{ $chat->id }}</td>
                                            <td>{{ $chat->user->name }}</td>
                                            <td>{{ $chat->content }}</td>
                                            <td>
                                                {{ $chat->created_at }}
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-sm btn-danger mr-2" onclick="deleteChat({{ $chat->id }})">Xoá</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="pagination">
                                {{ $chats->links() }}
                            </div>
                        </div> <!-- /.card-body -->
                    </div><!-- /.card -->
                </div> <!-- /.col -->
            </div> <!-- /.row -->
        </div> <!-- /.container-fluid -->
    </section>
    <script type="text/javascript">
        function deleteChat(id) {
            (function ($) {
                Swal.fire({
                    title: 'Xoá chat',
                    text: 'Bạn muốn xoá chat này?',
                    icon: 'question',
                    showCancelButton: true,
                    cancelButtonText: 'Huỷ',
                    confirmButtonColor: '#0d6efd',
                    confirmButtonText: 'OK',
                }).then((result) => {
                    if (result.isConfirmed) {
                        var arr = {
                            "_token": getMetaContentByName("csrf-token"),
                        };

                        $.ajax({
                            type: "DELETE",
                            url: admin_url + "/chat/" + id,
                            data: arr,
                            cache: false,
                            beforeSend: function () {
                            },
                            success: function (result) {
                                if (result.success) {
                                    Swal.fire({
                                        icon: "success",
                                        title: "Đã xoá chat",
                                        text: result.message,
                                        showConfirmButton: false,
                                        timer: 1500
                                    });
                                    $("#chat_id_" + id).remove();
                                } else {
                                    Swal.fire(
                                        'Oops...',
                                        result.message,
                                        'error'
                                    );
                                }
                            }
                        });
                    }
                })
            })(jQuery);
        }
    </script>
@endsection
