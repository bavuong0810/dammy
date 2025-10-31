<?php
use Carbon\Carbon;

Carbon::setLocale('vi');
?>
@if($reviews)
    @foreach($reviews->data as $review)
        <?php
            $created = date('Y-m-d H:i:s', strtotime($review->created_at));
            $review->created = Carbon::parse($created)->diffForHumans(Carbon::now());
            $usersReview = $review->user;
            if ($usersReview->avatar != "") {
                $thumbnail = asset('images/avatar/thumbs/100/' . $usersReview->avatar);
            } else {
                $thumbnail = asset('img/avata.png');
            }
        ?>
        <div id="review-{{ $review->id }}" class="review-item space-y-4 mt-3">
            <div class="p-3 border radius-10 rounded-lg bg-card text-card-foreground">
                <div class="d-flex justify-content-start space-x-4">
                    <div class="flex-shrink-0">
                        <img class="w-10 h-10 rounded-circle" src="{{ $thumbnail }}" alt=""
                             style="width: 40px; height: 40px; object-fit: cover;"/>
                    </div>
                    <div class="flex-1">
                        <div class="d-flex align-items-center justify-content-between">
                            <h4 class="text-lg font-semibold">{{ $usersReview->name }}</h4>
                            <span class="text-muted-foreground">{{ $review->created }}</span>
                        </div>
                        <div class="d-flex align-items-center space-x-2 text-sm text-muted-foreground">
                            <span>⭐ {{ $review->rating }}</span>
                        </div>
                        <p class="mt-2 text-sm">
                            {!! htmlspecialchars_decode($review->content) !!}
                        </p>
                        <div class="d-flex align-items-center mt-2 space-x-4 text-sm text-muted-foreground">
                            <button class="d-flex align-items-center space-x-1 border-0 bg-transparent">
                                <span>💬</span>
                                <span>{{ $review->total_comment }} Trả lời</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="read-comment ms-3" id="read-comment-{{ $review->id }}">
                <a href="javascript:void(0)" onclick="readReviewComment({{ $review->id }})">
                    @if ($review->total_comment > 0)
                        <b>Xem {{ $review->total_comment }} trả lời</b>
                    @else
                        <b>Trả lời</b>
                    @endif
                </a>
            </div>
        </div>
    @endforeach
    @if ($reviews->next_page_url != '')
        <div class="text-center mt-3" id="btnMoreReview">
            <button class="btn-success btn-sm btn" type="button" onclick="loadMoreReview({{ $reviews->current_page + 1 }})">Xem thêm</button>
        </div>
    @endif
@endif
