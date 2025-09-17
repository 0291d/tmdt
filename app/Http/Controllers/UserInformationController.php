<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;

class UserInformationController extends Controller
{
    // Controller thong tin nguoi dung: show/update customer profile
    public function show()
    {
        $user = auth()->user();
        // Avoid creating an incomplete customer record on view
        $customer = Customer::firstOrNew(['user_id' => $user->id]);
        return view('pages.user_information', compact('user','customer'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'phone' => ['nullable','regex:/^0\d{9}$/'],
            'address' => ['nullable','string','max:255'],
        ], [
            'phone.regex' => 'Số điện thoại phải gồm 10 số và bắt đầu bằng 0.',
        ]);
        $user = auth()->user();
        $customer = Customer::firstOrCreate(
            ['user_id' => $user->id],
            [
                // Use empty strings to satisfy NOT NULL schema
                'phone' => '',
                'address' => '',
            ]
        );
        $customer->fill($data);
        $customer->save();
        return redirect()->route('user.info')->with('status', 'Cập nhật thông tin thành công');
    }
}
