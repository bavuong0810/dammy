<?php

namespace App\Http\Controllers;

use App\Constants\BaseConstants;
use App\Models\UserCoin;
use App\Models\UserProvider;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use App\Models\User;
use Illuminate\Support\Facades\File;
use App\Libraries\Helpers;

class SocialAuthController extends Controller
{
    /**
     * Chuyển hướng người dùng sang OAuth Provider.
     *
     * @return Response
     */
    public function redirectToProvider($provider)
    {
        if(!Session::has('pre_url')){
            Session::put('pre_url', URL::previous());
        }else{
            if(URL::previous() != URL::to('login')) Session::put('pre_url', URL::previous());
        }
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Lấy thông tin từ Provider, kiểm tra nếu người dùng đã tồn tại trong CSDL
     * thì đăng nhập, ngược lại nếu chưa thì tạo người dùng mới trong SCDL.
     *
     * @return Response
     */
    public function handleProviderCallback($provider)
    {
        $user = Socialite::driver($provider)->user();

        $authUser = $this->findOrCreateUser($user, $provider);

        Auth::login($authUser);

        return redirect()->route('index');
    }

    /**
     * @param  $user Socialite user object
     * @param $provider Social auth provider
     * @return  User
     */
    public function findOrCreateUser($user, $provider)
    {
        $userProvider = UserProvider::where('provider_id', $user->id)
            ->where('provider', $provider)
            ->first();
        if ($userProvider) {
            return User::where('id', $userProvider->user_id)->first();
        }

        $username = str_replace(' ', '', Str::slug($user->name));
        $username = strtolower($username . rand(0,10000));
        $check_email = User::where('email', $user->email)->first();
        if ($check_email) {
            UserProvider::firstOrCreate(
                [
                    'user_id' => $check_email->id,
                    'provider' => $provider,
                    'provider_id' => $user->id,
                ]
            );
            return $check_email;
        } else {
            //avatar
            $year = date('Y');
            $month = date('m');
            $date = date('d');
            $path_img = $year . '/' . $month . '/' . $date . '/';
            if (!is_dir(public_path() . "/images/avatar/" . $path_img)) {
                // dir doesn't exist, make it
                mkdir(public_path() . "/images/avatar/" . $path_img, 0777, true);
            }
            $time = time();
            $avatarContent = file_get_contents($user->getAvatar());
            $avatar = '';
            if ($avatarContent) {
                File::put(public_path() . '/images/avatar/' . $path_img . $time . '-' . $user->getId().".jpg", $avatarContent);
                $avatar = $path_img . $time . '-' . $user->getId().".jpg";
                $path = 'images/avatar';
                Helpers::getThumbnail($path, $avatar, 230, null);
                Helpers::getThumbnail($path, $avatar, 100, null);
            }

            $newUser = User::create([
                'name' => $user->name,
                'username' => $username,
                'email' => $user->email,
                'password' => bcrypt($user->token),
                'total_view' => 0,
                'total_donate' => 0,
                'total_view_month' => 0,
                'type' => User::UserType['User'],
                'active' => BaseConstants::ACTIVE,
                'last_login' => date('Y-m-d H:i:s'),
                'email_activation' => '',
                'email_verified_at' => date('Y-m-d H:i:s'),
                'avatar' => $avatar,
                'register_with_social' => BaseConstants::ACTIVE,
            ]);

            if ($newUser) {
                UserProvider::create([
                    'provider' => $provider,
                    'provider_id' => $user->id,
                    'user_id' => $newUser->id,
                ]);

                UserCoin::create(
                    [
                        'user_id' => $newUser->id,
                        'coin' => 0,
                        'total_coin' => 0,
                    ]
                );
                return $newUser;
            } else {
                $msg = "Đã xãy ra lỗi trong quá trình đăng ký tài khoản";
                $result = "";
                $result .= "<script language='javascript'>alert('".$msg."');</script>";
                $result .= "<script language='javascript'>history.go(-1);</script>";
                echo $result;
                exit();
            }
        }
    }
}
