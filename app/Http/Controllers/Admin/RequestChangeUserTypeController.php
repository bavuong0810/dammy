<?php

namespace App\Http\Controllers\Admin;

use App\Constants\BaseConstants;
use App\Libraries\Helpers;
use App\Models\Notification;
use App\Models\RequestChangeUserType;
use App\Models\UserCoin;
use App\Models\UserView;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class RequestChangeUserTypeController extends Controller
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
        $query = RequestChangeUserType::with(
            [
                'user' => function ($q) {
                    $q->select('id', 'name');
                }
            ]
        )
            ->orderBy('status', 'ASC')
            ->orderBy('created_at', 'DESC');

        $requestChangeUserTypes = $query->paginate(50);
        return view('admin.request-change-user-type.index', compact('requestChangeUserTypes'));
    }

    public function confirm(Request $request)
    {
        $id = $request->id;
        $requestType = RequestChangeUserType::where('id', $id)->first();
        if ($requestType) {
            $user = User::where('id', $requestType->user_id)->first();
            if ($user) {
                $requestType->status = RequestChangeUserType::Status['Confirm'];
                $requestType->save();

                $user->request_change_type = BaseConstants::INACTIVE;
                $user->type = User::UserType['TranslateTeam'];
                $user->team_accept_time = date('Y-m-d H:i:s');
                $user->save();

                UserView::firstOrCreate(
                    [
                        'user_id' => $requestType->user_id
                    ],
                    [
                        'day' => 0,
                        'week' => 0,
                        'month' => 0,
                        'year' => 0,
                        'alltime' => 0,
                    ]
                );

                Notification::create(
                    [
                        'user_id' => $requestType->user_id,
                        'title' => "Đam Mỹ xin chào!",
                        'description' => 'Yêu cầu đóng góp truyện của bạn đã được duyệt.',
                        'image' => asset('img/avatar-admin.png'),
                        'link' => route('user.story.index'),
                        'unread' => BaseConstants::ACTIVE,
                    ]
                );
                Cache::pull('list_noti_' . $requestType->user_id);

                return response()->json(
                    [
                        'success' => true,
                        'message' => 'Đã duyệt người dùng ' . $user->name . ' thành dịch giả.'
                    ]
                );
            } else {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Không tìm thấy người dùng.'
                    ]
                );
            }
        } else {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Không tìm thấy yêu cầu này.'
                ]
            );
        }
    }

    public function cancel(Request $request)
    {
        $id = $request->id;
        $requestType = RequestChangeUserType::where('id', $id)->first();
        if ($requestType) {
            $user = User::where('id', $requestType->user_id)->first();
            if ($user) {
                $requestType->status = RequestChangeUserType::Status['Cancel'];
                $requestType->save();

                $user->request_change_type = BaseConstants::INACTIVE;
                $user->save();

                return response()->json(
                    [
                        'success' => true,
                        'message' => 'Huỷ bỏ yêu cầu đóng góp truyện thành công.'
                    ]
                );
            } else {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Không tìm thấy người dùng.'
                    ]
                );
            }
        } else {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Không tìm thấy yêu cầu này.'
                ]
            );
        }
    }
}
