<?php

namespace App\Http\Controllers\Admin;

use App\Constants\BaseConstants;
use App\Models\CoinHistory;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\User;
use App\Models\UserCoin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class PaymentController extends Controller
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
        $list = Payment::with(['user'])
            ->whereNotIn('id', [584, 587, 589, 945])
            ->orderBy('created_at', 'DESC')
            ->take(30)
            ->get();
        return view('admin.payment.index', compact('list'));
    }

    public function detail($id)
    {
        $detail = Payment::where('id', $id)->first();
        if($detail){
            return view('admin.payment.detail', compact('detail'));
        } else{
            return redirect()->route('admin.payment.index');
        }
    }

    public function cancel(Request $request)
    {
        $code = $request->code;
        $payment = Payment::where('code', $code)->first();
        if ($payment) {
            $payment->status = Payment::Status['Cancel'];
            $payment->save();

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Huỷ giao dịch ' . $code . ' thành công.'
                ]
            );
        } else {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Không tìm thấy giao dịch này'
                ]
            );
        }
    }

    public function confirm(Request $request)
    {
        $code = $request->code;
        $payment = Payment::where('code', $code)->first();
        if ($payment) {
            $user = User::with(['coin'])->where('id', $payment->user_id)->first();
            if ($user) {
                $payment->status = Payment::Status['Confirm'];
                $payment->save();

                UserCoin::where('user_id', $payment->user_id)
                    ->update(
                        [
                            'coin' => $user->coin->coin + $payment->amount,
                            'total_coin' => $user->coin->total_coin + $payment->amount,
                        ]
                    );

                CoinHistory::create(
                    [
                        'user_id' => $payment->user_id,
                        'coin' => $payment->amount,
                        'type' => CoinHistory::Type['Recharge'],
                        'message' => 'Nạp thành công ' . number_format($payment->amount) . ' xu.',
                        'transaction_type' => CoinHistory::TransactionType['PLUS']
                    ]
                );

                Notification::create(
                    [
                        'user_id' => $payment->user_id,
                        'title' => "Đam Mỹ",
                        'description' => 'Giao dịch ' . $payment->code . ' đã được xử lý thành công.',
                        'image' => asset('img/avatar-admin.png'),
                        'link' => route('index'),
                        'unread' => BaseConstants::ACTIVE,
                    ]
                );
                Cache::pull('list_noti_' . $payment->user_id);

                return response()->json(
                    [
                        'success' => true,
                        'message' => 'Cộng xu cho giao dịch ' . $code . ' thành công.'
                    ]
                );
            } else {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Không tìm thấy người nạp xu này'
                    ]
                );
            }
        } else {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Không tìm thấy giao dịch này'
                ]
            );
        }
    }
}
