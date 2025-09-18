<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;

    public function sendResetLinkEmail(Request $request)
    {
        $validated = $request->validate(
            ['email' => ['required', 'email']],
            [
                'email.required' => 'Vui lòng nhập email.',
                'email.email' => 'Email không hợp lệ.',
            ]
        );

        $status = Password::sendResetLink(['email' => $validated['email']]);

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status',  'Chúng tôi đã gửi liên kết đặt lại mật khẩu.');
        }

        $message = 'Không tìm thấy tài khoản với địa chỉ email này.';
        if ($status === Password::RESET_THROTTLED) {
            $message = 'Bạn đã gửi quá nhiều yêu cầu. Vui lòng thử lại sau.';
        }

        return back()->withErrors(['email' => $message])->withInput();
    }
}
