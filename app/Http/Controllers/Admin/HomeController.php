<?php

namespace App\Http\Controllers\Admin;

use App\Constants\BaseConstants;
use App\Models\Story;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function __construct()
    {
        // $this->middleware('guest:admin')->except('logout');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('admin.home');
    }

    public function testShopeeAff()
    {
        return view('admin.test-shopee');
    }

    public function changeUserType()
    {
        $translateTeams = User::where('type', User::UserType['TranslateTeam'])
            ->where('active', BaseConstants::ACTIVE)
            ->orderBy('total_view', 'DESC')
            ->get();

        $teamChange = [];
        foreach ($translateTeams as $team) {
            $ngay_bat_dau = $team->team_accept_time;
            $ngay_ket_thuc = date('Y-m-d H:i:s');

            $hieu_so = abs(strtotime($ngay_ket_thuc) - strtotime($ngay_bat_dau));

            $nam = floor($hieu_so / (365 * 60 * 60 * 24));
            $thang = floor(($hieu_so - $nam * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
            $ngay = floor(($hieu_so - $nam * 365 * 60 * 60 * 24 - $thang * 30 * 60 * 60 * 24) / (60 * 60 * 24));

            if (Story::where('user_id', $team->id)->count() == 0 && $ngay >= 7) {
                $teamChange[] = $team->id;
            }
        }

        User::whereIn('id', $teamChange)->update(
            [
                'request_change_type' => BaseConstants::INACTIVE,
                'type' => User::UserType['User']
            ]
        );
    }
}
