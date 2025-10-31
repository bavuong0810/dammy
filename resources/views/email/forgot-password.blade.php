<p>Xin chào {{ $data['name'] }}</p>

<p>Bạn đã yêu cầu thay đổi mật khẩu tại <b>{!! Helpers::get_setting('company_name') !!}</b>. Click vào link dưới đây để tiếp tục quá trình thay đổi mật khẩu.</p>

<p>
    Link: <a href="{{ route('resetPasswordView', $data['token']) }}" target="_blank">
        {{ route('resetPasswordView', $data['token']) }}</a>
</p>

<p>Nếu bạn không thực hiện đặt lại mật khẩu, hành động này không cần thiết.</p>

<p>{!! Helpers::get_setting('company_name') !!}!</p>
