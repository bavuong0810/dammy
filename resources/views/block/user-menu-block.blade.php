<a class="d-flex align-items-center nav-link dropdown-toggle gap-3 dropdown-toggle-nocaret"
   href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
    <?php
    $avatar = asset('images/avatar/thumbs/100/' . $user->avatar);
    if ($user->avatar == '') {
        $avatar = asset('img/avata.png');
    }
    ?>
    <img src="{{ $avatar }}" class="user-img" alt="{{ $user->name }}" onerror="this.src='{{ asset('img/avata.png') }}';">
</a>
<ul class="dropdown-menu dropdown-menu-end">
    {{--
    <li>
        <a class="dropdown-item d-flex align-items-center" href="javascript:void(0)">
            <i class="bx bx-coin-stack fs-5"></i><span>{{ number_format($user_coin) }}</span>
        </a>
    </li>
    <li>
        <div class="dropdown-divider mb-0"></div>
    </li>
    --}}
    @if($user->type == 1)
        <li><a class="dropdown-item d-flex align-items-center" href="{{ route('translateTeam.detail', $user->id) }}"><i
                    class="bx bx-user fs-5"></i><span>Trang cá nhân</span></a>
        </li>
        {{--
        <li><a class="dropdown-item d-flex align-items-center" href="{{ route('user.registerRecommendedStory') }}"><i
                    class="bx bx-pen fs-5"></i><span>Đăng ký đề cử</span></a>
        </li>
        --}}
        <li><a class="dropdown-item d-flex align-items-center" href="{{ route('user.areBeingInterested') }}"><i
                    class="bx bx-show fs-5"></i><span>Truyện được quan tâm</span></a>
        </li>
    @endif
    @if($user->type != 1)
        <li><a class="dropdown-item d-flex align-items-center" href="{{ route('user.requestChangeUserType') }}"><i
                    class="bx bx-chevrons-up fs-5"></i><span>Đăng truyện</span></a>
        </li>
    @endif
    <li><a class="dropdown-item d-flex align-items-center" href="{{ route('user.following') }}"><i
                class="bx bx-bell fs-5"></i><span>Đang theo dõi</span></a>
    </li>
    @if($user->type == 1)
        <li><a class="dropdown-item d-flex align-items-center" href="{{ route('user.story.index') }}"><i
                    class="bx bx-dock-left fs-5"></i><span>Truyện của bạn</span></a>
        </li>
        <li><a class="dropdown-item d-flex align-items-center" href="{{ route('user.story.create') }}"><i
                    class="bx bx-plus fs-5"></i><span>Đăng truyện</span></a>
        </li>
    @endif
    <li><a class="dropdown-item d-flex align-items-center" href="{{ route('user.readHistories') }}"><i
                class="bx bx-show fs-5"></i><span>Truyện đã đọc</span></a>
    </li>
    <li><a class="dropdown-item d-flex align-items-center" href="{{ route('user.bookmark') }}"><i
                class="bx bx-bookmark-alt fs-5"></i><span>Truyện đã lưu</span></a>
    </li>
    @if($user->type == 1)
        <li><a class="dropdown-item d-flex align-items-center" href="{{ route('user.dashboard') }}"><i
                    class="bx bx-bar-chart-alt-2 fs-5"></i><span>Thống kê</span></a>
        </li>
    @endif
    <li><a class="dropdown-item d-flex align-items-center" href="{{ route('user.profile') }}"><i
                class="bx bx-user fs-5"></i><span>Thông tin</span></a>
    </li>
    <li><a class="dropdown-item d-flex align-items-center" href="{{ route('user.changePassword') }}"><i
                class="bx bx-cog fs-5"></i><span>Đổi mật khẩu</span></a>
    </li>
    <li>
        <div class="dropdown-divider mb-0"></div>
    </li>
    <li>
        <a class="dropdown-item d-flex align-items-center" href="{{ route('user.logout') }}">
            <i class="bx bx-log-out-circle"></i><span>Đăng xuất</span>
        </a>
    </li>
</ul>
