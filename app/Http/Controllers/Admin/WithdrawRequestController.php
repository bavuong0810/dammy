<?php

namespace App\Http\Controllers\Admin;

use App\Constants\BaseConstants;
use App\Libraries\Helpers;
use App\Models\CoinHistory;
use App\Models\Notification;
use App\Models\WithdrawRequest;
use App\Models\User;
use App\Models\UserCoin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class WithdrawRequestController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function index()
    {
        $list = WithdrawRequest::with(['user', 'user.coin'])
            ->orderBy('status', 'ASC')
            ->orderBy('created_at', 'DESC')
            ->paginate(20);
        return view('admin.withdraw-request.index', compact('list'));
    }

    public function detail($id)
    {
        $detail = WithdrawRequest::with(['user'])->where('id', $id)->first();
        if ($detail) {
            return view('admin.withdraw-request.detail', compact('detail'));
        } else{
            return redirect()->route('admin.withdrawRequest.index');
        }
    }

    public function cancel(Request $request)
    {
        $code = $request->code;
        $withdrawRequest = WithdrawRequest::where('code', $code)->first();
        if ($withdrawRequest) {
            $withdrawRequest->status = WithdrawRequest::StatusType['Cancel'];
            $withdrawRequest->save();

            Notification::create(
                [
                    'user_id' => $withdrawRequest->user_id,
                    'title' => "Yêu cầu rút xu",
                    'description' => 'Yêu cầu ' . $withdrawRequest->code . ' đã bị huỷ.',
                    'image' => asset('img/avatar-admin.png'),
                    'link' => route('user.withdraw'),
                    'unread' => BaseConstants::ACTIVE,
                ]
            );
            Cache::pull('list_noti_' . $withdrawRequest->user_id);
            return response()->json(
                [
                    'success' => true,
                    'message' => 'Huỷ yêu cầu rút xu ' . $code . ' thành công.'
                ]
            );
        } else {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Không tìm thấy yêu cầu rút xu này.'
                ]
            );
        }
    }

    public function confirm(Request $request)
    {
        $code = $request->code;
        $withdrawRequest = WithdrawRequest::where('code', $code)->first();
        if ($withdrawRequest) {
            $user = User::with(['coin'])->where('id', $withdrawRequest->user_id)->first();
            if ($user) {
                $checkCoin = $user->coin->coin - $withdrawRequest->coin;
                if ($checkCoin >= 0) {
                    $withdrawRequest->status = WithdrawRequest::StatusType['Confirm'];
                    $withdrawRequest->save();

                    UserCoin::where('user_id', $withdrawRequest->user_id)
                        ->update(
                            [
                                'coin' => $user->coin->coin - $withdrawRequest->coin,
                            ]
                        );

                    CoinHistory::create(
                        [
                            'user_id' => $withdrawRequest->user_id,
                            'coin' => $withdrawRequest->coin,
                            'type' => CoinHistory::Type['Withdraw'],
                            'message' => 'Rút thành công ' . number_format($withdrawRequest->coin) . ' xu.',
                            'transaction_type' => CoinHistory::TransactionType['MINUS']
                        ]
                    );

                    Notification::create(
                        [
                            'user_id' => $withdrawRequest->user_id,
                            'title' => "Rút xu",
                            'description' => "Yêu cầu rút xu " . $withdrawRequest->code . ' đã được duyệt',
                            'image' => asset('img/avatar-admin.png'),
                            'link' => route('user.withdraw'),
                            'unread' => BaseConstants::ACTIVE,
                        ]
                    );
                    Cache::pull('list_noti_' . $withdrawRequest->user_id);

                    return response()->json(
                        [
                            'success' => true,
                            'message' => 'Xử lý yêu cầu rút xu ' . $code . ' thành công.'
                        ]
                    );
                } else {
                    return response()->json(
                        [
                            'success' => false,
                            'message' => 'Số dư không đủ để xử lý yêu cầu rút xu này.'
                        ]
                    );
                }
            } else {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Không tìm thấy người yêu cầu xu này'
                    ]
                );
            }
        } else {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Không tìm thấy yêu cầu này'
                ]
            );
        }
    }
}
