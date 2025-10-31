<h1 class="card-title">{{ $chapter->story->name }} - {{ $chapter->name }}</h1>
<input type="hidden" name="chapter_id" id="chapter_id" value="{{ $chapter->id }}">

@if($chapter->warning || $chapter->story->warning)
    <div class="alert alert-warning border-0 bg-warning alert-dismissible fade show py-2 mt-3">
        <div class="d-flex align-items-center">
            <div class="font-35 text-dark"><i class="bx bx-info-circle"></i>
            </div>
            <div class="ms-3">
                <div class="text-dark">Nội dung chương có thể sử dụng các từ ngữ nhạy cảm, bạo
                    lực,... bạn có thể cân nhắc trước khi đọc truyện!
                </div>
            </div>
        </div>
    </div>
@endif

<p class="bg-light-info p-3 radius-10 mt-3">
    Cập nhật lúc: {{ \Carbon\Carbon::parse($chapter->created_at)->toDateTimeString() }}<br>
    Lượt xem: {{ number_format($chapter->view) }} <br>
</p>

<div class="chapter-content">
    <input type="hidden" name="chapter_id" id="chapter_id" value="{{ $chapter->id }}">
    <input type="hidden" name="chapter_title" id="chapter_title" value="{{ $chapter->name }}">
    <input type="hidden" name="story_id" id="story_id" value="{{ $chapter->story->id }}">
    <div class="content-container mt-4" id="chapter-content-render">
        @if (
            !Auth::check()
            || (Auth::check() && Auth::user()->premium_date == '')
            || (Auth::check() && Auth::user()->premium_date != '' && strtotime(Auth::user()->premium_date) < time())
        )
            <?php
            $linkShopee = Helpers::get_option_by_key($getOptions, 'chapter-links');
            $linkArray = Helpers::parseComplexLinkTextarea($linkShopee, 'chapter-links');
            $linkAff = $linkArray[rand(0, count($linkArray) - 1)];
            $firstChapter = $list_chapters[(count($list_chapters) - 1)]->slug;
            ?>
            @if($firstChapter == $chapter->slug)
                {!! $chapter->content !!}
            @else
                <div class="affClick" style="@if(isset($_COOKIE['affShowInChapter'])) display:none; @else display:block; @endif">
                    <p class="text-center" style="font-size: 16px;margin-bottom: 0.4rem;">Mời Quý độc giả <b>CLICK</b> vào <b class="text-uppercase">liên kết hoặc ảnh</b> bên dưới</p>
                    <p class="text-center" style="font-size: 16px;margin-bottom: 0.4rem;"><span class="text-uppercase text-danger"><b>mở ứng dụng Shopee</b></span> để tiếp tục đọc toàn bộ chương truyện!</p>
                    <p class="text-center" style="font-size: 16px;margin-bottom: 0.4rem;"><a id="affLink" href="{{ $linkAff }}" target="_blank" rel="noopener" onclick="affLinkClick()"><b>{{ $linkAff }}</b></a></p>
                    <p class="text-center" style="font-size: 16px;margin-bottom: 0.4rem;">
                        <a href="{{ $linkAff }}" target="_blank" rel="noopener" onclick="affLinkClick()">
                            <img src="{{ asset('images/click-here-to-unlock.webp') }}" alt="" style="max-width: 410px; width: 100%;">
                        </a>
                    </p>
                    <h4 class="text-center text-primary" style="font-size: 20px;margin-bottom: 0.4rem;">Đam Mỹ và đội ngũ Tác giả/Editor xin chân thành cảm ơn!</h4>
                </div>
                <div class="affActive" style="@if(isset($_COOKIE['affShowInChapter']) && $_COOKIE['affShowInChapter'] == 'true') display:block; @else display:none; @endif">
                    {!! $chapter->content !!}
                </div>
            @endif
        @else
            {!! $chapter->content !!}
        @endif
    </div>
</div>
