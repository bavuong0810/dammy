<?php

namespace App\Http\Controllers\User;

use App\Constants\BaseConstants;
use App\Libraries\Helpers;
use App\Models\Chapter;
use App\Models\CoinHistory;
use App\Models\Donate;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\Story;
use App\Models\User;
use App\Models\UserChapter;
use App\Models\UserCoin;
use App\Models\WithdrawRequest;
use App\Tasks\TelegramTask;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class CoinController extends Controller
{
    private $telegramTask;
    public function __construct(){
        $this->telegramTask = new TelegramTask();
    }

    public function recharge(Request $request)
    {
        $query = Payment::where('user_id', Auth::user()->id)
            ->orderBy('created_at', 'DESC');
        if ($request->code) {
            $query->where('code', 'LIKE', '%' . $request->code . '%');
        }

        $payments = $query->paginate(24);
        return view('user.coin.recharge', compact('payments'));
    }

    public function processRecharge(Request $request)
    {
        $code = Helpers::generatePaymentCode();
        $user_id = Auth::user()->id;

        $amount = (int)str_replace(',', '', $request->amount);
        $payment = Payment::create(
            [
                'user_id' => $user_id,
                'code' => $code,
                'amount' => $amount,
                'status' => Payment::Status['New'],
            ]
        );

        if ($payment) {
            return redirect()->route('user.coin.waitTransfer', $payment->code);
        } else {
            return redirect()
                ->back()
                ->withErrors('Xãy ra lỗi trong quá trình tạo thanh toán! Vui lòng thử lại.')
                ->withInput();
        }
    }

    public function waitTransfer($code)
    {
        $payment = Payment::where('code', $code)->first();
        if ($payment) {
            return view('user.coin.wait-transfer', compact('payment'));
        } else {
            return redirect()->route('user.coin.recharge');
        }
    }

    public function confirmTransfer($code)
    {
        $payment = Payment::with(['user'])
            ->where('code', $code)
            ->first();
        if ($payment) {
            $payment->status = Payment::Status['ConfirmTransfer'];
            $payment->save();

            if (env('APP_ENV') == 'production') {
                $adminLink = route('admin.payment.index');
                $user_name = $payment->user->name;
//                $text = "<b>[" . Helpers::get_setting('company_name') . "]</b> Xác nhận chuyển khoản\n"
//                    . "<b>Code: </b> " . $payment->code . "\n"
//                    . "<b>Trạng thái: </b> " . Payment::StatusType[1] . "\n"
//                    . "<b>Họ và tên: </b>$user_name\n"
//                    . "<b>Số tiền: </b>" . number_format($payment->amount) . "\n"
//                    . "<b>Thời gian: </b> " . $payment->created_at . "\n"
//                    . "<b>Admin link: </b>$adminLink\n";
                $amount = number_format($payment->amount);
                $text = <<<EOT
                        **[Đam Mỹ] Xác nhận chuyển khoản**
                        **Code:**  $payment->code
                        **Họ và tên:**  $user_name
                        **Số tiền:**  $amount
                        **Thời gian:**  $payment->created_at
                        **Admin link:**  $adminLink
                        EOT;
                $this->telegramTask->sendMessage($text);
            }
            return response()->json([
                'success' => true,
                'message' => 'Xác nhận chuyển khoản thành công. Vui lòng đợi từ 6-12 tiếng để đội ngũ xử lý cộng xu vào tài khoản.'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy mã thanh toán này.'
            ]);
        }
    }

    public function coinHistories(Request $request)
    {
        $histories = CoinHistory::where('user_id', Auth::user()->id)
            ->orderBy('created_at', 'DESC')
            ->paginate(24);
        return view('user.coin.coin-history', compact('histories'));
    }

    public function paymentHistory(Request $request)
    {
        $query = Payment::where('user_id', Auth::user()->id)
            ->orderBy('created_at', 'DESC');
        if ($request->code) {
            $query->where('code', 'LIKE', '%' . $request->code . '%');
        }

        $payments = $query->paginate(24);
        return view('user.coin.payment-history', compact('payments'));
    }

    public function donate(Request $request)
    {
        $amount = strip_tags($request->amount);
        $amount = (int)str_replace(',', '', $amount);
        $receiver_id = $request->receiver;
        $story_id = $request->story_id;
        $story = Story::where('id', $story_id)->first();
        if (!$story) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Không tìm thấy truyện.'
                ]
            );
        }
        $user_id = Auth::user()->id;
        $user = User::with(['coin'])->where('id', $user_id)->first();
        $receiver = User::with(['coin'])->where('id', $receiver_id)->first();
        if ($user_id == $receiver_id) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Không thể tự donate cho bản thân mình.'
                ]
            );
        }

        if ($user && $receiver) {
            $user_coin = $user->coin->coin;
            if ($amount > 0 && $user_coin >= $amount) {
                $donate = Donate::create(
                    [
                        'user_id' => $user_id,
                        'receiver_id' => $receiver_id,
                        'story_id' => $story_id,
                        'coin' => $amount,
                    ]
                );
                if ($donate) {
                    User::where('id', $user->id)->update(
                        [
                            'total_donate' => $user->total_donate + $amount
                        ]
                    );

                    //Trừ coin của người Donate
                    UserCoin::where('user_id', $user->id)->update(
                        [
                            'coin' => $user->coin->coin - $amount,
                        ]
                    );
                    CoinHistory::create(
                        [
                            'user_id' => $user->id,
                            'coin' => $amount,
                            'type' => CoinHistory::Type['Donate'],
                            'message' => 'Bạn đã donate ' . number_format($amount) . ' xu cho truyện ' . $story->name . '.',
                            'transaction_type' => CoinHistory::TransactionType['MINUS']
                        ]
                    );

                    //Cộng coin cho người nhận
                    UserCoin::where('user_id', $receiver->id)->update(
                        [
                            'coin' => $receiver->coin->coin + $amount,
                        ]
                    );
                    CoinHistory::create(
                        [
                            'user_id' => $receiver->id,
                            'coin' => $amount,
                            'type' => CoinHistory::Type['Donated'],
                            'message' => 'Bạn nhận được ' . number_format($amount) . ' xu từ truyện ' . $story->name . '.',
                            'transaction_type' => CoinHistory::TransactionType['PLUS']
                        ]
                    );

                    if ($story->thumbnail == '') {
                        $thumbnail = asset('img/no-image.png');
                    } else {
                        $thumbnail = asset('images/story/thumbs/230/' . $story->thumbnail);
                    }
                    Notification::create(
                        [
                            'user_id' => $story->user_id,
                            'title' => "Độc giả đã donate cho bạn",
                            'description' => 'Đã nhận donate từ truyện ' . $story->name,
                            'image' => $thumbnail,
                            'link' => route('story.detail', $story->slug),
                            'unread' => BaseConstants::ACTIVE,
                        ]
                    );
                    Cache::pull('list_noti_' . $story->user_id);

                    return response()->json(
                        [
                            'success' => true,
                            'message' => 'Donate thành công! Cảm ơn bạn đã ủng hộ cho team ' . number_format($amount) . ' coin.'
                        ]
                    );
                }

                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Lỗi không tạo được giao dịch ủng hộ team. Vui lòng thử lại.'
                    ]
                );
            }

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Xu trong tài khoản không đủ để thực hiện. Bạn có thể nạp thêm để tiếp tục ủng hộ team!'
                ]
            );
        } else {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Lỗi không tìm thấy tài khoản của người nhận hoặc người gửi.'
                ]
            );
        }
    }

    public function donateToAuthor(Request $request)
    {
        $amount = strip_tags($request->amount);
        $amount = (int)str_replace(',', '', $amount);
        $author_id = $request->author_id;
        $user = Auth::user();
        $user_id = $user->id;
        $user = User::with(['coin'])->where('id', $user_id)->first();
        $receiver = User::with(['coin'])->where('id', $author_id)->first();
        if ($user_id == $author_id) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Không thể tự donate cho bản thân mình.'
                ]
            );
        }

        if ($user && $receiver) {
            $user_coin = $user->coin->coin;
            if ($amount > 0 && $user_coin >= $amount) {
                $donate = Donate::create(
                    [
                        'user_id' => $user_id,
                        'receiver_id' => $author_id,
                        'story_id' => 0,
                        'coin' => $amount,
                    ]
                );
                if ($donate) {
                    User::where('id', $user->id)->update(
                        [
                            'total_donate' => $user->total_donate + $amount
                        ]
                    );

                    //Trừ coin của người Donate
                    UserCoin::where('user_id', $user->id)->update(
                        [
                            'coin' => $user->coin->coin - $amount,
                        ]
                    );
                    CoinHistory::create(
                        [
                            'user_id' => $user->id,
                            'coin' => $amount,
                            'type' => CoinHistory::Type['Donate'],
                            'message' => 'Bạn đã donate ' . number_format($amount) . ' xu cho tác giả ' . $receiver->name . '.',
                            'transaction_type' => CoinHistory::TransactionType['MINUS']
                        ]
                    );

                    //Cộng coin cho người nhận
                    UserCoin::where('user_id', $receiver->id)->update(
                        [
                            'coin' => $receiver->coin->coin + $amount,
                        ]
                    );
                    CoinHistory::create(
                        [
                            'user_id' => $receiver->id,
                            'coin' => $amount,
                            'type' => CoinHistory::Type['Donated'],
                            'message' => 'Bạn nhận được ' . number_format($amount) . ' xu từ ' . $user->name . '.',
                            'transaction_type' => CoinHistory::TransactionType['PLUS']
                        ]
                    );

                    if ($user->avatar == '') {
                        $thumbnail = asset('img/avata.png');
                    } else {
                        $thumbnail = asset('images/avatar/thumbs/230/' . $user->avatar);
                    }
                    Notification::create(
                        [
                            'user_id' => $receiver->id,
                            'title' => "Độc giả đã donate cho bạn",
                            'description' => 'Đã nhận donate từ độc giả ' . $user->name,
                            'image' => $thumbnail,
                            'link' => route('user.dashboard'),
                            'unread' => BaseConstants::ACTIVE,
                        ]
                    );
                    Cache::pull('list_noti_' . $receiver->user_id);

                    return response()->json(
                        [
                            'success' => true,
                            'message' => 'Donate thành công! Cảm ơn bạn đã ủng hộ cho team ' . number_format($amount) . ' coin.'
                        ]
                    );
                }

                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Lỗi không tạo được giao dịch ủng hộ team. Vui lòng thử lại.'
                    ]
                );
            }

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Xu trong tài khoản không đủ để thực hiện. Bạn có thể nạp thêm để tiếp tục ủng hộ team!'
                ]
            );
        } else {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Lỗi không tìm thấy tài khoản của người nhận hoặc người gửi.'
                ]
            );
        }
    }

    public function donateHistory()
    {
        $donates = Donate::with(['receiver', 'story'])
            ->where('user_id', Auth::user()->id)
            ->orderBy('created_at', 'DESC')
            ->paginate(50);
        return view('user.coin.donate-history', compact('donates'));
    }

    public function donatedHistory()
    {
        $donates = Donate::with(['user', 'story'])
            ->where('receiver_id', Auth::user()->id)
            ->orderBy('created_at', 'DESC')
            ->paginate(50);
        return view('user.coin.donated-history', compact('donates'));
    }

    public function buyChapter(Request $request)
    {
        $chapter_id = $request->chapter_id;
        $story_id = $request->story_id;
        $chapter = Chapter::where('id', $chapter_id)
            ->where('story_id', $story_id)
            ->first();
        if ($chapter) {
            $user_id = Auth::user()->id;
            //check user buy or not
            $check = UserChapter::where('user_id', $user_id)
                ->where('story_id', $story_id)
                ->where('chapter_id', $chapter_id)
                ->first();
            if ($check) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Bạn đã từng mở khoá chương này.'
                    ]
                );
            } else {
                $user = User::with(['coin'])
                    ->where('id', $user_id)
                    ->first();
                $story = Story::where('id', $story_id)
                    ->first();
                $receiver = User::with(['coin'])->where('id', $story->user_id)->first();
                if ($user_id == $receiver->id) {
                    return response()->json(
                        [
                            'success' => false,
                            'message' => 'Không thể mở khoá chương truyện của chính mình.'
                        ]
                    );
                }
                $amount = $chapter->coin;
                if ($user && $receiver) {
                    $user_coin = $user->coin->coin;
                    if ($amount > 0 && $user_coin > $amount) {
                        $userChapter = UserChapter::create(
                            [
                                'user_id' => $user_id,
                                'chapter_id' => $chapter_id,
                                'story_id' => $story_id,
                                'coin' => $amount,
                            ]
                        );

                        if ($userChapter) {
                            //Trừ coin của người mua
                            UserCoin::where('user_id', $user->id)->update(
                                [
                                    'coin' => $user->coin->coin - $amount,
                                ]
                            );
                            CoinHistory::create(
                                [
                                    'user_id' => $user->id,
                                    'coin' => $amount,
                                    'type' => CoinHistory::Type['Donate'],
                                    'message' => 'Bạn đã dùng ' . number_format($amount) . ' xu mở khoá ' . $chapter->name . ' của truyện ' . $story->name . '.',
                                    'transaction_type' => CoinHistory::TransactionType['MINUS']
                                ]
                            );

                            //Cộng coin cho người nhận
                            UserCoin::where('user_id', $receiver->id)->update(
                                [
                                    'coin' => $receiver->coin->coin + $amount,
                                ]
                            );
                            CoinHistory::create(
                                [
                                    'user_id' => $receiver->id,
                                    'coin' => $amount,
                                    'type' => CoinHistory::Type['Donated'],
                                    'message' => $user->name . ' đã dùng ' . number_format($amount) . ' xu mở khoá ' . $chapter->name . ' của truyện ' . $story->name . '.',
                                    'transaction_type' => CoinHistory::TransactionType['PLUS']
                                ]
                            );

                            if ($user->avatar == '') {
                                $thumbnail = asset('img/avata.png');
                            } else {
                                $thumbnail = asset('images/avatar/thumbs/230/' . $user->avatar);
                            }
                            Notification::create(
                                [
                                    'user_id' => $story->user_id,
                                    'title' => "Độc giả đã mở khoá chương của bạn",
                                    'description' => $story->name . ' của truyện ' . $story->name,
                                    'image' => $thumbnail,
                                    'link' => route('chapter.detail', [$story->slug, $chapter->slug]),
                                    'unread' => BaseConstants::ACTIVE,
                                ]
                            );
                            Cache::pull('user_chapters_' . $user->id . '_story_id_' . $story->id);
                            Cache::pull('list_noti_' . $story->user_id);

                            return response()->json(
                                [
                                    'success' => true,
                                    'message' => 'Mở khoá chương thành công!'
                                ]
                            );
                        }

                        return response()->json(
                            [
                                'success' => false,
                                'message' => 'Lỗi! Không tạo được giao dịch mua chương truyện. Vui lòng thử lại.'
                            ]
                        );
                    }

                    return response()->json(
                        [
                            'success' => false,
                            'message' => 'Xu trong tài khoản không đủ để thực hiện. Bạn có thể nạp thêm để mở khoá chương này!'
                        ]
                    );
                } else {
                    return response()->json(
                        [
                            'success' => false,
                            'message' => 'Lỗi! Không tìm thấy tài khoản của người nhận hoặc người gửi.'
                        ]
                    );
                }
            }
        } else {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Không tìm thấy chương truyện.'
                ]
            );
        }
    }

    public function withdraw()
    {
        $withdrawRequests = WithdrawRequest::where('user_id', Auth::user()->id)
            ->orderBy('created_at', 'DESC')
            ->paginate(50);
        return view('user.coin.withdraw', compact('withdrawRequests'));
    }

    public function withdrawProcess(Request $request)
    {
        $withdraw_coin = strip_tags($request->withdraw_coin);
        $withdraw_coin = (int)str_replace(',', '', $withdraw_coin);
        $user = Auth::user();
        $user_coin = $user->coin->coin;
        if ($withdraw_coin < 100000) {
            return redirect()->back()->withInput()->withErrors("Rút tối thiểu 100,000 xu một lần.");
        }
        if ($withdraw_coin > $user_coin) {
            return redirect()->back()->withInput()->withErrors("Số xu rút lớn hơn số xu hiện có.");
        }

        $code = Helpers::generateWithdrawCode();
        $withdrawRequest = WithdrawRequest::create(
            [
                'user_id' => $user->id,
                'code' => $code,
                'coin' => $withdraw_coin,
                'status' => WithdrawRequest::StatusType['New'],
                'message' => '',
            ]
        );

        if ($withdrawRequest) {
            if (env('APP_ENV') == 'production') {
                $adminLink = route('admin.withdrawRequest.index');
                $user_name = $user->name;
//                $text = "<b>[" . Helpers::get_setting('company_name') . "]</b> Yêu cầu rút xu\n"
//                    . "<b>Code: </b> " . $withdrawRequest->code . "\n"
//                    . "<b>Trạng thái: </b> Mới\n"
//                    . "<b>Họ và tên: </b>$user_name\n"
//                    . "<b>Số tiền: </b>" . number_format($withdrawRequest->coin) . "\n"
//                    . "<b>Thời gian: </b> " . $withdrawRequest->created_at . "\n"
//                    . "<b>Admin link: </b>$adminLink\n";
                $amount = number_format($withdrawRequest->coin);
                $text = <<<EOT
                        **[Đam Mỹ] Yêu cầu rút xu**
                        **Code:**  $withdrawRequest->code
                        **Họ và tên:**  $user_name
                        **Số tiền:**  $amount
                        **Thời gian:**  $withdrawRequest->created_at
                        **Admin link:**  $adminLink
                        EOT;
                $this->telegramTask->sendMessage($text);
            }

            return redirect()
                ->back()
                ->with(
                    'success_msg',
                    'Gửi yêu cầu rút xu thành công. Yêu cầu của bạn sẽ được team xem xét và duyệt trong thời gian sớm nhất.'
                );
        } else {
            return redirect()->back()->withInput()->withErrors("Lỗi! Không tạo được yêu cầu rút xu. Vui lòng thử lại.");
        }
    }
}
