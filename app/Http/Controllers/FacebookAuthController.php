<?php

namespace App\Http\Controllers;
use Illuminate\Support\Str;
use Socialite, Auth, Redirect, Session, URL, File;
use App\Libraries\Helpers;
use App\Models\User;
class FacebookAuthController extends Controller
{
    public function redirectToProvider()
    {
        if(!Session::has('pre_url')){
            Session::put('pre_url', URL::previous());
        }else{
            if(URL::previous() != URL::to('login')) Session::put('pre_url', URL::previous());
        }
        return Socialite::driver('facebook')->redirect();
    }

    /**
     * Obtain the user information from facebook.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback()
    {
        $user = Socialite::driver('facebook')->user();

        $authUser = $this->findOrCreateUser($user);

        // Chỗ này để check xem nó có chạy hay không
        // dd($user);

        Auth::login($authUser);

        // Auth::login($authUser, true);

        return redirect()->route('index');
    }

    private function findOrCreateUser($facebookUser){
        $authUser = User::where('provider_id', $facebookUser->id)->first();
        if($authUser){
            return $authUser;
        }
        if ($facebookUser->email != '') {
            $email = $facebookUser->email;
        } else{
            $email = $facebookUser->id.'@gmail.com';
        }
        $username = str_replace(' ', '', Str::slug($facebookUser->name));
        $username = strtolower($username.rand(0,10000));
        $check_email = User::where('email', $email)->first();
        if($check_email){
            $msg = "Email Facebook đã được sử dụng để đăng ký tài khoản";
            $result = "";
            $result .= "<script language='javascript'>alert('".$msg."');</script>";
            $result .= "<script language='javascript'>history.go(-1);</script>";
            echo $result;
            exit();
        }else{
            $year = date('Y');
            $month = date('m');
            $date = date('d');
            $path_img = $year . '/' . $month . '/' . $date;
            if (!is_dir(base_path() . "/images/avatar/" . $path_img)) {
                // dir doesn't exist, make it
                mkdir(base_path() . "/images/avatar/" . $path_img, 0777, true);
            }
            $path_img = $year . '/' . $month . '/' . $date . '/';
            $time = time();
            $avatar = file_get_contents($facebookUser->getAvatar());
            File::put(base_path() . '/images/avatar/' . $path_img . $time . '-' . $facebookUser->getId().".jpg", $avatar);
            $name_avatar = $path_img . $time . '-' . $facebookUser->getId().".jpg";
            $result = User::create([
                'name' => $facebookUser->name,
                'password' => bcrypt($facebookUser->token),
                'username' => $username,
                'avatar' => $name_avatar,
                'email' => $email,
                'email_activation' => '',
                'provider_id' => $facebookUser->id,
                'provider' => $facebookUser->id,
            ]);
            return $result;
        }
    }
}
