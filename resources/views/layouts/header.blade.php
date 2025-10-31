<?php
use Illuminate\Support\Facades\Auth;
?>
<!--start header wrapper-->
<div class="header-wrapper">
    <!--start header -->
    <header>
        <div class="topbar d-flex align-items-center">
            <nav class="navbar navbar-expand gap-3 container">
                <div class="mobile-toggle-menu d-block d-lg-none" data-bs-toggle="offcanvas"
                     data-bs-target="#offcanvasNavbar"><i class='bx bx-menu'></i></div>
                <div class="topbar-logo-header d-flex">
                    <div class="">
                        <a href="{{ route('index') }}">
                            <img src="{{ asset($settings->where('type', 'logo')->first()->value) }}" class="logo-icon" alt="{{ $settings->where('type', 'seo_title')->first()->value }}" width="120" height="30">
                        </a>
                    </div>
                </div>
                <div class="search-bar d-lg-block d-none">
                    <div class="position-relative popup-search w-100">
                        <form action="{{ route('search') }}" method="GET">
                            <input class="form-control search-input form-control-lg ps-5" type="search"
                                   placeholder="Tìm truyện" name="search" value="" id="searchInputPC"
                                   style="max-height: 40px !important; min-height: inherit !important;">
                            <span class="position-absolute top-50 search-show ms-3 translate-middle-y start-0 top-50 fs-4"><i
                                    class='bx bx-search'></i></span>
                        </form>
                        <ul id="search-autocomplete"></ul>
                    </div>
                </div>
                <div class="top-menu ms-auto">
                    <ul class="navbar-nav align-items-center gap-1">
                        <li class="nav-item mobile-search-icon d-flex d-lg-none" data-bs-toggle="modal"
                            data-bs-target="#SearchModal">
                            <a class="nav-link" href="javascript:;"><i class='bx bx-search'></i>
                            </a>
                        </li>

                        <li class="nav-item dark-mode d-flex">
                            <a class="nav-link dark-mode-icon" href="javascript:;">
                                @if(Session::get('darkmode')) <i class="bx bx-sun"></i> @else <i class='bx bx-moon'></i> @endif
                            </a>
                        </li>

                        @if(Auth::check())
                            <li class="nav-item dropdown dropdown-large" id="userNotification">
                                <img src="{{ asset('img/ajax-loader.gif') }}" alt="" width="25">
                            </li>
                        @endif
                    </ul>
                </div>
                @if(Auth::check())
                    <div class="user-box dropdown px-2" id="userBox">
                        <img src="{{ asset('img/ajax-loader.gif') }}" alt="" width="25">
                    </div>
                @else
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('login') }}" class="btn btn-sm btn-primary" style="margin-right: 5px; font-size: 13px">Đăng nhập</a>
                        <a href="{{ route('registerUser') }}" class="btn btn-sm btn-primary" style="font-size: 13px">Đăng ký</a>
                    </div>
                @endif
            </nav>
        </div>
    </header>
    <!--end header -->
    <!--navigation-->
    <div class="primary-menu">
        <nav class="navbar navbar-expand-lg align-items-center">
            <div class="container">
                <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasNavbar"
                     aria-labelledby="offcanvasNavbarLabel">
                    <div class="offcanvas-header border-bottom">
                        <div class="d-flex align-items-center">
                            <div class="">
                                <a href="{{ route('index') }}">
                                    <img src="{{ asset($settings->where('type', 'logo')->first()->value) }}"
                                         class="logo-icon" alt="{{ $settings->where('type', 'seo_title')->first()->value }}">
                                </a>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body">
                        <ul class="navbar-nav align-items-center flex-grow-1">
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('pageStory') }}">
                                    <div class="menu-title d-flex align-items-center">Truyện mới</div>
                                </a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" href="javascript:;" data-bs-toggle="dropdown">
                                    <div class="menu-title d-flex align-items-center">Thể loại</div>
                                    <div class="ms-auto dropy-icon"><i class='bx bx-chevron-down'></i></div>
                                </a>
                                <ul class="dropdown-menu scroll-menu">
                                    <?php
                                    $categories = Helpers::getListCategories();
                                    ?>
                                    @if(count($categories))
                                        @foreach($categories as $category)
                                            <li>
                                                <a class="dropdown-item" href="{{ route('category.list', $category->slug) }}">
                                                    <i class='bx bx-chevron-right'></i>{{ $category->name }}
                                                </a>
                                            </li>
                                        @endforeach
                                    @endif
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('completedStory') }}">
                                    <div class="menu-title d-flex align-items-center">Truyện Full</div>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('hotStory') }}">
                                    <div class="menu-title d-flex align-items-center">Truyện Hot</div>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('longStory') }}">
                                    <div class="menu-title d-flex align-items-center">
                                        Truyện Dài
                                    </div>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('creativeStory') }}">
                                    <div class="menu-title d-flex align-items-center">
                                        Truyện Sáng Tác
                                    </div>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('translateTeam.index') }}">
                                    <div class="menu-title d-flex align-items-center">Team</div>
                                </a>
                            </li>
                            @if(Auth::check())
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" href="javascript:;"
                                       data-bs-toggle="dropdown">
                                        <div class="menu-title d-flex align-items-center">Tủ truyện</div>
                                        <div class="ms-auto dropy-icon"><i class='bx bx-chevron-down'></i></div>
                                    </a>
                                    <ul class="dropdown-menu">
                                        @if(Auth::user()->type == 1)
                                            <li>
                                                <a class="dropdown-item" href="{{ route('user.story.index') }}">
                                                    <i class='bx bx-book-add'></i> Truyện của bạn
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="{{ route('user.story.create') }}">
                                                    <i class='bx bx-plus'></i> Đăng truyện
                                                </a>
                                            </li>
                                        @endif
                                        <li>
                                            <a class="dropdown-item" href="{{ route('user.readHistories') }}">
                                                <i class='bx bx-show'></i> Truyện đã đọc
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('user.bookmark') }}">
                                                <i class='bx bx-bookmark'></i> Truyện đã đánh dấu
                                            </a>
                                        </li>
                                            <li>
                                                <a class="dropdown-item" href="{{ route('user.following') }}">
                                                    <i class='bx bx-bell'></i> Đang theo dõi team
                                                </a>
                                            </li>
                                    </ul>
                                </li>
                                @if(Auth::user()->type == 1)
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" href="javascript:;"
                                       data-bs-toggle="dropdown">
                                        <div class="menu-title d-flex align-items-center">Ví xu</div>
                                        <div class="ms-auto dropy-icon"><i class='bx bx-chevron-down'></i></div>
                                    </a>
                                    <ul class="dropdown-menu">
                                        {{--
                                        <li>
                                            <a class="dropdown-item" href="{{ route('user.recharge') }}">
                                                <i class='bx bx-bitcoin'></i>Nạp xu
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('user.buyVip') }}">
                                                <i class='bx bx-star'></i>Premium
                                            </a>
                                        </li>
                                        --}}
                                        <li>
                                            <a class="dropdown-item" href="{{ route('user.coinHistories') }}">
                                                <i class='bx bx-history'></i>Lịch sử giao dịch
                                            </a>
                                        </li>

                                        {{--
                                        <li>
                                            <a class="dropdown-item" href="{{ route('user.donatedHistory') }}">
                                                <i class='bx bx-donate-heart'></i>Lịch sử nhận donate
                                            </a>
                                        </li>
                                        --}}
                                        <li>
                                            <a class="dropdown-item" href="{{ route('user.withdraw') }}">
                                                <i class='bx bx-money'></i>Rút xu
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                @endif
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
    </div>
    <!--end navigation-->
</div>
<!--end header wrapper-->

<?php
$pageAlert = Helpers::get_option_by_key($getOptions, 'page-alert');
?>
@if($pageAlert != '')
    <section class="page-alert">
        <div class="container">
            <div class="alert alert-primary border-0 bg-primary alert-dismissible fade show py-3 mt-3 mb-0">
                <div class="d-flex align-items-center">
                    <div class="font-35 text-white"><i class="bx bx-bell"></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-2 text-white font-18">Thông báo</h6>
                        <div class="text-white">{!! $pageAlert !!}</div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif
