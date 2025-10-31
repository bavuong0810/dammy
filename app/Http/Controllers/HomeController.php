<?php

namespace App\Http\Controllers;

use App\Libraries\Helpers;
use App\Mail\SendEmailForgotPassword;
use App\Models\Bank;
use App\Models\User;
use App\Models\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function forgotPassword()
    {
        return view('user.auth.passwords.forgot');
    }

    public function forgotPasswordSend(Request $request)
    {
        $email = $request->email;
        if ($email != '') {
            $user = User::where('email', $email)
                ->first();
            if ($user) {
                $email_admin = Helpers::get_setting('admin_email');
                $name_admin_email = Helpers::get_setting('company_name');
                $subject_default = 'Bạn đã yêu cầu đặt lại mật khẩu tại ' . Helpers::get_setting('company_name');

                $datetime_now = date('Y-m-d H:i:s');
                $token = base64_encode($datetime_now . $user->email);
                PasswordReset::create(
                    [
                        'email' => $user->email,
                        'token' => $token,
                        'created_at' => $datetime_now,
                    ]
                );

                $data = [
                    'name' => $user->name,
                    'email' => $user->email,
                    'user_id' => $user->id,
                    'token' => $token,
                    'email_admin' => $email_admin,
                    'name_email_admin' => $name_admin_email,
                    'subject_default' => $subject_default
                ];
                Mail::to($user->email)->send(new SendEmailForgotPassword($data));

                return redirect()->back()->with('success_msg', 'Vui lòng kiểm tra email của bạn để có thể tiếp tục đặt lại mật khẩu. Nếu không tìm thấy email vui lòng kiểm tra trong mục Spam hoặc liên hệ fanpage Đam Mỹ để được hỗ trợ.');
            } else {
                return redirect()->back()->withErrors('Không thể tìm thấy email của bạn.');
            }
        }
        return redirect()->back()->withErrors('Email không được bỏ trống.');
    }

    public function resetPasswordView($token)
    {
        $passwordReset = PasswordReset::where('token', $token)->first();
        if ($passwordReset) {
            return view('user.auth.passwords.reset', compact('token'));
        } else {
            return redirect()->route('index');
        }
    }

    public function resetPassword($token, Request $request)
    {
        $validation_rules = [
            'password' => 'required|min:6|confirmed',
        ];
        $messages = [
            'password.required' => 'Hãy nhập mật khẩu',
            'password.min' => 'Mật khẩu tối thiểu 6 ký tự',
            'password.confirmed' => 'Xác nhận mật khẩu không đúng'
        ];
        $validator = Validator::make($request->all(), $validation_rules, $messages);
        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator);
        }

        $passwordReset = PasswordReset::where('token', $request->token)->first();
        if ($passwordReset) {
            $user = User::where('email', $passwordReset->email)
                ->first();
            if ($user) {
                User::where('email', $passwordReset->email)->update(
                    ['password' => bcrypt($request->password)]
                );

                PasswordReset::where('token', $request->token)->delete();
                return redirect()->route('login')->with('success_msg', 'Mật khẩu của bạn đã được đặt lại.');
            } else {
                return redirect()->back()->withErrors('Không thể đặt lại mật khẩu của bạn. Vui lòng liên hệ ban quản trị để được hỗ trợ.');
            }
        } else {
            return redirect()->route('index');
        }
    }

    public function getBank()
    {
        $arrContextOptions = array(
            "ssl" => array(
                "verify_peer" => false,
                "verify_peer_name" => false,
            ),
        );
        $response = Http::get('https://api.vietqr.io/v2/banks');
        $banks = json_decode($response->body(), true);
        foreach ($banks['data'] as $bank) {
            $logo = $bank['logo'];
            $file = file_get_contents($logo, false, stream_context_create($arrContextOptions));
            file_put_contents(public_path() . '/img/banks/' . $bank['code'] . '.png', $file);
            Bank::firstOrCreate(
                [
                    'code' => $bank['code'],
                    'swift_code' => $bank['swift_code'],
                    'name' => $bank['name'],
                ],
                [
                    'bin' => $bank['bin'],
                    'shortName' => $bank['shortName'],
                    'logo' => 'img/banks/' . $bank['code'] . '.png',
                    'transferSupported' => $bank['transferSupported'],
                    'lookupSupported' => $bank['lookupSupported'],
                    'short_name' => $bank['short_name'],
                    'support' => $bank['support'],
                    'isTransfer' => $bank['isTransfer'],
                ]
            );
        }
    }
}
