<?php

use App\Constants\BaseConstants;
use App\Tasks\Admin\RoleTask;
use Illuminate\Support\Facades\Request;

$segment_check = Request::segment(2);
$user_role = Request()->user_role;
$user_role_id = Request()->user_role['role_id'];
$is_super_admin = ($user_role_id == BaseConstants::SUPER_ADMIN_ROLE_ID) ? true : false;

$pageModulePermission = app(RoleTask::class)
    ->checkPermission('page-management', [BaseConstants::READ_PERMISSION], $user_role);
$userModulePermission = app(RoleTask::class)
    ->checkPermission('user-management', [BaseConstants::READ_PERMISSION], $user_role);
$storyModulePermission = app(RoleTask::class)
    ->checkPermission('story-management', [BaseConstants::READ_PERMISSION], $user_role);
$storyCategoryModulePermission = app(RoleTask::class)
    ->checkPermission('category-management', [BaseConstants::READ_PERMISSION], $user_role);
$paymentPermission = app(RoleTask::class)
    ->checkPermission('payment-management', [BaseConstants::READ_PERMISSION], $user_role);
$reportPermission = app(RoleTask::class)
    ->checkPermission('report-management', [BaseConstants::READ_PERMISSION], $user_role);
$chatPermission = app(RoleTask::class)
    ->checkPermission('chat-management', [BaseConstants::READ_PERMISSION], $user_role);
$requestChangeTypePermission = app(RoleTask::class)
    ->checkPermission('request-change-user-type', [BaseConstants::READ_PERMISSION], $user_role);
$reviewPermission = app(RoleTask::class)
    ->checkPermission('review-management', [BaseConstants::READ_PERMISSION], $user_role);
?>
<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{route('admin.dashboard')}}" class="brand-link">
        <img src="{{asset('img/avatar-admin.png')}}" alt="{!! $settings->where('type', 'company_name')->first()->value !!}"
             class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">{!! $settings->where('type', 'company_name')->first()->value !!}</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                @if(Request()->admin_info->avatar == '')
                    <img src="{{ asset('img/avatar-admin.png') }}" class="img-circle elevation-2" alt="{{ Request()->admin_info->name }}">
                @else
                    <img src="{{ asset('images/avatar/' . Request()->admin_info->avatar) }}" class="img-circle elevation-2" alt="{{ Request()->admin_info->name }}">
                @endif
            </div>
            <div class="info">
                <a href="javascript:void(0)" class="d-block">{{ Request()->admin_info->name }}</a>
            </div>
        </div>

    <?php /* <!-- SidebarSearch Form -->
      <div class="form-inline">
        <div class="input-group" data-widget="sidebar-search">
          <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
          <div class="input-group-append">
            <button class="btn btn-sidebar">
              <i class="fas fa-search fa-fw"></i>
            </button>
          </div>
        </div>
      </div> */ ?>

    <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
                     with font-awesome or any other icon font library -->

                <li class="nav-item has-treeview">
                    <a href="{{route('admin.dashboard')}}" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li>
                <li class="nav-item has-treeview">
                    <a href="{{route('index')}}" target="_blank" class="nav-link">
                        <i class="nav-icon fas fa-home"></i>
                        <p>
                            Xem trang chủ
                        </p>
                    </a>
                </li>
                @if($pageModulePermission)
                    <li class="nav-item has-treeview <?php if (in_array(
                        $segment_check,
                        array('list-pages', 'page', 'edit-page')
                    )) {
                        echo 'menu-open';
                    } ?>">
                        <a href="javascript:void(0)"
                           class="nav-link <?php if (in_array($segment_check, array('list-pages', 'page', 'edit-page'))) {
                               echo 'active';
                           } ?>">
                            <i class="nav-icon fas fa-file"></i>
                            <p>
                                Page
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{route('admin.pages')}}"
                                   class="nav-link <?php if (in_array($segment_check, array('list-pages'))) {
                                       echo 'active';
                                   } ?>">
                                    <i class="fas fa-angle-right nav-icon"></i>
                                    <p>List Page</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

                @if($storyModulePermission || $storyCategoryModulePermission)
                    <li class="nav-item has-treeview <?php if (in_array(
                        $segment_check,
                        [
                            'stories',
                            'story',
                            'categories',
                            'category',
                            'chapter'
                        ]
                    )) {
                        echo 'menu-open';
                    } ?>">
                        <a href="javascript:void(0)" class="nav-link <?php if (in_array(
                            $segment_check,
                            [
                                'stories',
                                'story',
                                'categories',
                                'category',
                                'chapter'
                            ]
                        )) {
                            echo 'active';
                        } ?>">
                            <i class="nav-icon fas fa-book"></i>
                            <p>
                                Truyện
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @if($storyModulePermission)
                                <li class="nav-item">
                                    <a href="{{route('admin.story.index')}}"
                                       class="nav-link <?php if (in_array($segment_check, ['stories', 'story'])) {
                                           echo 'active';
                                       } ?>">
                                        <i class="fas fa-angle-right nav-icon"></i>
                                        <p>Tất cả truyện</p>
                                    </a>
                                </li>
                            @endif
                            @if($storyCategoryModulePermission)
                                <li class="nav-item">
                                    <a href="{{route('admin.category.index')}}" class="nav-link <?php if (in_array(
                                        $segment_check,
                                        ['categories', 'category']
                                    )) {
                                        echo 'active';
                                    } ?>">
                                        <i class="fas fa-angle-right nav-icon"></i>
                                        <p>Thể loại truyện</p>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif

                @if($paymentPermission)
                    <li class="nav-item">
                        <a href="{{route('admin.payment.index')}}"
                           class="nav-link @if(in_array($segment_check, ['payments', 'payment'])) active @endif">
                            <i class="nav-icon fas fa-coins"></i>
                            <p>Nạp Xu</p>
                        </a>
                    </li>
                @endif

                @if($is_super_admin)
                    <li class="nav-item">
                        <a href="{{route('admin.withdrawRequest.index')}}"
                           class="nav-link @if(in_array($segment_check, ['withdraw-requests', 'withdraw-request'])) active @endif">
                            <i class="nav-icon fas fa-coins"></i>
                            <p>Rút Xu</p>
                        </a>
                    </li>
                @endif

                {{--
                @if($chatPermission)
                    <li class="nav-item">
                        <a href="{{route('admin.chat.index')}}"
                           class="nav-link @if(in_array($segment_check, ['chats'])) active @endif">
                            <i class="nav-icon fas fa-comments"></i>
                            <p>Quản lý Chat</p>
                        </a>
                    </li>
                @endif
                --}}

                @if($reportPermission)
                    <li class="nav-item">
                        <a href="{{route('admin.report.index')}}"
                           class="nav-link @if(in_array($segment_check, ['reports', 'report'])) active @endif">
                            <i class="nav-icon fas fa-exclamation-triangle"></i>
                            <p>Báo cáo lỗi</p>
                        </a>
                    </li>
                @endif

                @if($reviewPermission)
                    <li class="nav-item">
                        <a href="{{route('admin.review.index')}}"
                           class="nav-link @if(in_array($segment_check, ['reviews', 'review'])) active @endif">
                            <i class="nav-icon fas fa-star"></i>
                            <p>Đánh giá</p>
                        </a>
                    </li>
                @endif

                @if($requestChangeTypePermission)
                    <li class="nav-item">
                        <a href="{{route('admin.requestChangeUserType.index')}}"
                           class="nav-link @if(in_array($segment_check, ['request-change-user-types'])) active @endif">
                            <i class="nav-icon  fas fa-handshake"></i>
                            <p>Yêu cầu đóng góp truyện</p>
                        </a>
                    </li>
                @endif

                @if($userModulePermission)

                    <li class="nav-item">
                        <a href="{{route('admin.user.index')}}"
                           class="nav-link @if(in_array($segment_check, ['users', 'user'])) active @endif">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Quản lý người dùng</p>
                        </a>
                    </li>
                @endif

                @if($is_super_admin)
                    <li class="nav-item">
                        <a href="{{route('admin.translateTeam.index')}}"
                           class="nav-link @if(in_array($segment_check, ['translate-teams'])) active @endif">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Quản lý nhóm dịch</p>
                        </a>
                    </li>
                @endif

                @if($is_super_admin)
                    <li class="nav-item">
                        <a href="{{route('admin.report.viewDaily')}}"
                           class="nav-link @if(in_array($segment_check, ['report-view-daily'])) active @endif">
                            <i class="nav-icon fas fa-chart-bar"></i>
                            <p>Thống kê view ngày</p>
                        </a>
                    </li>
                    <li class="nav-item has-treeview @if(in_array($segment_check, array('list-manager', 'manager'))) menu-open @endif">
                        <a href="javascript:void(0)" class="nav-link @if (in_array($segment_check, array('list-manager', 'manager'))) active @endif">
                            <i class="nav-icon fas fa-users"></i>
                            <p>
                                Quản lý tài khoản
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('admin.listManagers') }}" class="nav-link @if(in_array($segment_check, array('list-manager'))) active @endif">
                                    <i class="fas fa-angle-right nav-icon"></i>
                                    <p>Danh sách tài khoản</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-header">Phân Quyền</li>
                    <li class="nav-item">
                        <a href="{{ route('admin.listModules') }}" class="nav-link @if(in_array($segment_check, array('list-modules', 'module'))) active @endif">
                            <i class="nav-icon fas fa-cubes"></i>
                            <p>
                                Danh sách chức năng
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.listRoles') }}" class="nav-link @if(in_array($segment_check, array('list-roles', 'role'))) active @endif">
                            <i class="nav-icon fas fa-user-check"></i>
                            <p>
                                Danh sách vai trò
                            </p>
                        </a>
                    </li>
                    <!-- Setting -->
                    <li class="nav-header">Cài đặt</li>
                    <li class="nav-item has-treeview
                        @if(in_array($segment_check, array(
                            'theme-option',
                            'general-setting',
                            'menu',
                            'social-setting',
                            'smtp-setting',
                            'shipping-setting',
                            ))) menu-open @endif">
                        <a href="javascript:void(0)" class="nav-link
                            @if(in_array($segment_check, array('theme-option', 'general-setting', 'shipping-setting'))) active @endif">
                            <i class="nav-icon fas fa-wrench"></i>
                            <p>
                                Cài đặt và cấu hình
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('admin.generalSetting') }}" class="nav-link
                                    @if(in_array($segment_check, array('general-setting'))) active @endif">
                                    <i class="fas fa-angle-right nav-icon"></i>
                                    <p>Cài đặt chung</p>
                                </a>
                            </li>
                            {{--
                            <li class="nav-item">
                                <a href="{{ route('admin.menu') }}" class="nav-link
                                    @if(in_array($segment_check, array('menu'))) active @endif">
                                    <i class="fas fa-angle-right nav-icon"></i>
                                    <p>Menu</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.shippingSetting') }}" class="nav-link
                                    @if(in_array($segment_check, array('shipping-setting'))) active @endif">
                                    <i class="fas fa-angle-right nav-icon"></i>
                                    <p>Cài đặt vận chuyển</p>
                                </a>
                            </li>
                            --}}
                            <li class="nav-item">
                                <a href="{{ route('admin.smtpSetting') }}" class="nav-link
                                    @if(in_array($segment_check, array('smtp-setting'))) active @endif">
                                    <i class="fas fa-angle-right nav-icon"></i>
                                    <p>Cài đặt SMTP</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.socialSetting') }}" class="nav-link
                                    @if(in_array($segment_check, array('social-setting'))) active @endif">
                                    <i class="fas fa-angle-right nav-icon"></i>
                                    <p>Cài đặt mạng xã hội</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.themeOption') }}" class="nav-link
                                    @if(in_array($segment_check, array('theme-option'))) active @endif">
                                    <i class="fas fa-angle-right nav-icon"></i>
                                    <p>Cài đặt mở rộng</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a href="{{route('admin.clearCache')}}" class="nav-link">
                            <i class="nav-icon fas fa-eraser"></i>
                            <p>
                                Xoá cache trang
                            </p>
                        </a>
                    </li>
                @endif
                <li class="nav-item">
                    <a href="{{ route('admin.accountInformation') }}" class="nav-link {{in_array($segment_check, array('account-information')) ? 'active' : ''}}">
                        <i class="nav-icon far fa-user-circle"></i>
                        <p>
                            Thông tin cá nhân
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{route('admin.changePassword')}}" class="nav-link">
                        <i class="nav-icon fas fa-unlock-alt"></i>
                        <p>
                            Đổi mật khẩu
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{route('admin.logout')}}" class="nav-link">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>
                            Logout
                        </p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
