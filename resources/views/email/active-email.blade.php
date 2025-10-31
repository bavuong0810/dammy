<p>Xin chào {{ $data['name'] }}</p>

<p>Xác thực tài khoản email của bạn tại đường dẫn: {{ route('user.getActiveEmailCode', [$data['user_id'], $data['code_active']]) }}</p>

<p>Nếu bạn không thực hiện tạo tài khoản, hành động này không cần thiết.</p>

<p>{!! Helpers::get_setting('company_name') !!}!</p>
