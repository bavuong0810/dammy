<?php

namespace App\Http\Controllers\Admin;

use App\Constants\BaseConstants;
use App\Models\CoinHistory;
use App\Models\Report;
use App\Models\ReportViewDaily;
use App\Models\UserCoin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;

class ReportController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function index(Request $request)
    {
        $query = Report::with(
            [
                'user' => function ($q) {
                    $q->select('id', 'name');
                },
                'story' => function ($q) {
                    $q->select('id', 'name', 'slug');
                },
                'chapter' => function ($q) {
                    $q->select('id', 'name', 'slug');
                }
            ]
        )
            ->orderBy('created_at', 'DESC');

        $reports = $query->paginate(50);
        return view('admin.report.index', compact('reports'));
    }

    public function detail($id)
    {
        $detail = Report::where('id', $id)->first();
        if ($detail) {
            return view('admin.report.detail', compact('detail'));
        } else {
            return redirect()->route('admin.report.index');
        }
    }

    public function confirm(Request $request)
    {
        $id = $request->id;
        $error = Report::where('id', $id)->first();
        if ($error) {
            $user = User::with(['coin'])->where('id', $error->user_id)->first();
            if ($user) {
                $error->status = Report::StatusType['Confirm'];
                $error->save();

                UserCoin::where('user_id', $error->user_id)
                    ->update(
                        [
                            'coin' => $user->coin->coin + 1000
                        ]
                    );

                CoinHistory::create(
                    [
                        'user_id' => $error->user_id,
                        'coin' => 1000,
                        'type' => CoinHistory::Type['System'],
                        'message' => 'Bạn được cộng ' . number_format(1000) . ' xu qua việc báo cáo lỗi chính xác.',
                        'transaction_type' => CoinHistory::TransactionType['PLUS']
                    ]
                );

                return response()->json(
                    [
                        'success' => true,
                        'message' => 'Xác nhận sửa lỗi thành công và cộng cho người dùng 50 Coin.'
                    ]
                );
            } else {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Không tìm thấy người dùng đã báo cáo lỗi.'
                    ]
                );
            }
        } else {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Không tìm thấy báo cáo lỗi này.'
                ]
            );
        }
    }

    public function cancel(Request $request)
    {
        $id = $request->id;
        $error = Report::where('id', $id)->first();
        if ($error) {
            $user = User::where('id', $error->user_id)->first();
            if ($user) {
                $error->status = Report::StatusType['Cancel'];
                $error->save();

                return response()->json(
                    [
                        'success' => true,
                        'message' => 'Huỷ bỏ báo cáo lỗi thành công.'
                    ]
                );
            } else {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Không tìm thấy người dùng đã báo cáo lỗi.'
                    ]
                );
            }
        } else {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Không tìm thấy báo cáo lỗi này.'
                ]
            );
        }
    }

    public function viewDaily()
    {
        $reportViewDaily = ReportViewDaily::orderBy('id', 'DESC')->paginate(31);
        return view('admin.report.view-daily', compact('reportViewDaily'));
    }
}
