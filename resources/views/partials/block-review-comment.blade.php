<?php
use Carbon\Carbon;

Carbon::setLocale('vi');
?>
@if(count($comments))
    @foreach($comments as $comment)
            <?php
            $created = date('Y-m-d H:i:s', strtotime($comment->created_at));
            $comment->created = Carbon::parse($created)->diffForHumans(Carbon::now());
            $usersComment = $comment->user;
            if ($usersComment->avatar != "") {
                $thumbnail = asset('images/avatar/thumbs/100/' . $usersComment->avatar);
            } else {
                $thumbnail = asset('img/avata.png');
            }
            ?>
        <div class="reivew-comment-item ms-4 space-y-4">
            <div class="p-3 border radius-10 rounded-lg bg-card text-card-foreground">
                <div class="d-flex justify-content-start space-x-4">
                    <div class="flex-shrink-0">
                        <img class="w-10 h-10 rounded-circle" src="{{ $thumbnail }}" alt=""
                             style="width: 40px; height: 40px; object-fit: cover;"/>
                    </div>
                    <div class="flex-1">
                        <div class="d-flex align-items-center justify-content-between">
                            <h4 class="text-lg font-semibold">{{ $usersComment->name }}</h4>
                            <span class="text-muted-foreground">{{ $comment->created }}</span>
                        </div>
                        <p class="mt-2 text-sm">
                            {!! htmlspecialchars_decode($comment->content) !!}
                        </p>
                        <div class="d-flex align-items-center mt-2 space-x-4 text-sm text-muted-foreground">
                            <button class="d-flex align-items-center space-x-1 border-0 bg-transparent">
                                <span>💬</span>
                                <span onclick="replyReview('{{ $usersComment->name }}', {{ $comment->review_id }})">Trả lời {{ $usersComment->name }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endif

<div class="ms-4 space-y-4" id="blockReplyReview-{{ $review_id }}">
    <div class="input-group">
        <input type="text" class="form-control" placeholder="Trả lời" aria-label="Trả lời" aria-describedby="button-addon2" id="replyReviewChild-{{ $review_id }}">
        <button class="btn btn-outline-success" type="button" id="button-addon2" onclick="sentReplyReview({{ $review_id }})">Gửi</button>
    </div>
</div>

