<?php
use Carbon\Carbon;
?>
<a class="nav-link dropdown-toggle dropdown-toggle-nocaret position-relative" href="#"
   data-bs-toggle="dropdown"><span class="alert-count">{{ $unread_notification }}</span>
    <i class='bx bx-bell'></i>
</a>
<div class="dropdown-menu dropdown-menu-end">
    <a href="javascript:;">
        <div class="msg-header">
            <p class="msg-header-title">Thông báo</p>
            <p class="msg-header-badge">{{ $unread_notification }} tin mới</p>
        </div>
    </a>
    <div class="header-notifications-list">
        <input type="hidden" id="total_current_noti" value="{{ count($notifications) }}">
        @if (count($notifications))
            @foreach ($notifications as $notification)
                    <?php
                    Carbon::setLocale('vi');
                    $created = $notification->created_at;
                    $created_at = Carbon::parse($created)->diffForHumans(Carbon::now());
                    ?>
                <a class="dropdown-item position-relative" href="javascript:;"
                   data-href="{{ $notification->link }}" onclick="readNotification(this, {{ $notification->id }})">
                    <div class="d-flex align-items-center">
                        <div class="user-online">
                            <img src="{{ $notification->image }}" class="msg-avatar"
                                 alt="">
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="msg-name">{{ $notification->title }}
                                <span class="msg-time float-end">{{ $created_at }}</span>
                            </h6>
                            <p class="msg-info">{{ $notification->description }}</p>
                        </div>
                    </div>

                    @if($notification->unread)
                        <div class="new-noti">
                            <span class="dots"></span>
                        </div>
                    @endif
                </a>
            @endforeach
        @else
            <div class="text-center my-2">
                Chưa có thông báo nào.
            </div>
        @endif
    </div>
    @if ($unread_notification > 0)
        <a href="javascript:void(0)">
            <div class="text-center msg-footer">
                <button class="btn btn-primary w-100" onclick="makeAllRead()">
                    Đánh dấu tất cả đã đọc
                </button>
            </div>
        </a>
    @endif
</div>
