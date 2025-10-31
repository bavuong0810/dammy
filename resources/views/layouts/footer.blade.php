<!-- search modal -->
<div class="modal" id="SearchModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-md-down">
        <div class="modal-content">
            <div class="modal-header gap-2">
                <div class="position-relative popup-search w-100">
                    <form action="{{ route('search') }}" method="GET">
                        <input class="form-control search-input form-control-lg ps-5 border border-3 border-primary" type="search"
                               placeholder="Search" name="search" id="searchInputMobile">
                        <span class="position-absolute top-50 search-show ms-3 translate-middle-y start-0 top-50 fs-4"><i
                                class='bx bx-search'></i></span>
                    </form>
                </div>
                <button type="button" class="btn-close d-md-none" data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ul id="search-autocomplete-mobile" class="p-0"></ul>
            </div>
        </div>
    </div>
</div>
<!-- end search modal -->


<!--start overlay-->
<div class="overlay toggle-icon"></div>
<!--end overlay-->
{{--
<!--Start Back To Top Button--> <a href="javaScript:;" class="back-to-top"><i class='bx bxs-up-arrow-alt'></i></a>
<!--End Back To Top Button-->
--}}

@if (Route::currentRouteName() == 'chapter.detail' && isset($chapter) && $list_chapters)
    <div class="chapter-footer bg-control-chapter">
        <div class="d-flex justify-content-center">
            <a href="javascript:void(0)" onclick="openSetting()" class="btn btn-sm btn-white m-1">
                <i class="bx bx-font-family me-0"></i>
            </a>
            <a href="{{ route('story.detail', $chapter->story->slug) }}" class="btn btn-sm btn-white m-1">
                <i class="bx bx-info-circle me-0"></i>
            </a>
            @if($prev_chapter != "")
                <a href="javascript:void(0)" id="prev_chapter_btn" style="line-height: 1.3" class="btn btn-sm btn-white m-1"
                   onclick="actionChangeChapter('{{ $prev_chapter['storySlug'] }},{{ $prev_chapter['chapterSlug'] }}')">
                    <<
                </a>
            @else
                <a href="javascript:void(0)" id="prev_chapter_btn" class="btn btn-sm btn-white m-1 disabled">
                    <<
                </a>
            @endif

            <select name="selected_chapter" id="selected_chapter" class="form-select m-1">
                @foreach($list_chapters as $item)
                    <option value="{{ $chapter->story->slug }},{{ $item->slug }}"
                            @if($item->id == $chapter->id) selected @endif>
                        {{ $item->name }}
                    </option>
                @endforeach
            </select>

            @if($next_chapter != "")
                <a href="javascript:void(0)" id="next_chapter_btn" class="btn btn-sm btn-white m-1" style="line-height: 1.3"
                   onclick="actionChangeChapter('{{ $next_chapter['storySlug'] }},{{ $next_chapter['chapterSlug'] }}')">
                    >>
                </a>
            @else
                <a href="javascript:void(0)" id="next_chapter_btn" class="btn btn-sm btn-white m-1 disabled" style="line-height: 1.3">
                    >>
                </a>
            @endif
        </div>
    </div>
@endif
<footer class="page-footer">
    <div class="container py-2" style="text-align: left">
        <div class="row">
            <div class="col-md-6">
                <div class="footer-logo text-center">
                    <a href="{{ route('index') }}">
                        <img src="{{ asset($settings->where('type', 'logo')->first()->value) }}" alt="{{ $settings->where('type', 'seo_title')->first()->value }}" width="300" height="80">
                    </a>
                </div>
                <div class="footer-content my-3">
                    {!! Helpers::get_option_by_key($getOptions, 'footer-content') !!}
                </div>
                <div class="footer-contact mb-3">
                    {!! Helpers::get_option_by_key($getOptions, 'footer-contact') !!}
                </div>
                <div>
                    {!! Helpers::get_option_by_key($getOptions, 'dmca-script') !!}
                </div>
            </div>
            <div class="col-md-6">
                <div class="footer-privacy">
                    <p>{!! Helpers::get_option_by_key($getOptions, 'footer-privacy') !!}</p>
                </div>
            </div>
        </div>
    </div>
    <p class="mb-0 border-top pt-1">
        <a href="{{ route('page.detail', 'chinh-sach-va-quy-dinh-chung') }}"><b>Chính sách và quy định chung</b></a> - <a href="{{ route('page.detail', 'chinh-sach-bao-mat') }}"><b>Chính sách bảo mật</b></a> - <a href="{{ route('sitemap.index') }}"><b>Sitemap</b></a><br>
        Copyright © 2024. All right reserved.
    </p>
</footer>
