@extends('layouts.app')
@section('seo')
    <?php
    $seoTitle = $settings->where('type', 'seo_title')->first()->value;
    $seoDescription = $settings->where('type', 'seo_description')->first()->value;
    $data_seo = array(
        'title' => $seoTitle,
        'keywords' => $settings->where('type', 'seo_keyword')->first()->value,
        'description' => $seoDescription,
        'og_title' => $seoTitle,
        'og_description' => $seoDescription,
        'og_url' => Request::url(),
        'og_img' => asset($settings->where('type', 'seo_image')->first()->value),
        'current_url' => Request::url()
    );
    $seo = WebService::getSEO($data_seo);
    ?>
    @include('partials.seo')
@endsection
@section('content')
    <div class="container my-5">
        <form action="{{ route('pr.getLink') }}?pr=1" method="GET">
            <div class="card">
                <div class="card-body">
                    <input type="hidden" id="pr" name="pr" class="form-control" value="1">
                    <div class="mb-3">
                        <label for="story_id" class="mb-2">ID Truyện</label>
                        <input type="number" id="story_id" name="id" class="form-control" value="{{ request()->get('id') }}">
                    </div>
                    <div class="mb-3 text-center">
                        <button type="submit" class="btn btn-primary">Lấy link</button>
                    </div>
                </div>
            </div>
        </form>

        @if($story)
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ $story->name }}</h5>
                    <p class="card-text"><span id="copyLink"><b>{{ route('story.detail', $story->slug) }}</b></span> <span style="cursor: pointer" class="ml-2" onclick="copyElement('copyLink')"><b><i class="bx bx-copy"></i></b></span></p>
                    <p class="card-text"><b>Chương 1: </b><span id="copyLinkChapter"><b>{{ route('chapter.detail', [$story->slug, $firstChapter->slug]) }}</b></span> <span style="cursor: pointer" class="ml-2" onclick="copyElement('copyLinkChapter')"><b><i class="bx bx-copy"></i></b></span></p>
                    <p class="card-text">
                        <b>Mẫu bình luận:</b> <br>
                        <span id="formCmt">
                            Đã FULL 👉 Cách xem truyện như sau: <br>
                            🌹 Tim hoặc lai bài viết (có tương tác thì linh sẽ dễ hiển thị). <br>
                            ❤️ Sau đó lướt xuống phần bình luận (nếu vẫn chưa thấy thì bật “Xem tất cả bình luận” nhé
                        </span> <span style="cursor: pointer" class="ml-2" onclick="copyElement('formCmt')"><b><i class="bx bx-copy"></i></b></span></p>
                </div>
            </div>
        @endif
    </div>
@endsection
@section('script')
    <script>
        function copyElement(elementId) {
            const element = document.getElementById(elementId);
            if (!element) return;

            navigator.clipboard.writeText(element.innerText)
                .then(() => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Đã copy nội dung!',
                        showConfirmButton: false,
                        timer: 600,
                    });
                })
                .catch(err => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Không thể copy: ' + err,
                        showConfirmButton: false,
                        timer: 600,
                    });
                });
        }
        jQuery(document).ready(function ($) {

        });
    </script>
@endsection

