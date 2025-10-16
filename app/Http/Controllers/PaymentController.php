<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\User;
// mặc định của vnpay
class PaymentController extends Controller
{
    // Khởi tạo thanh toán qua VNPAY từ giỏ hàng trong session và chuyển hướng sang cổng
    public function vnpayPayment(Request $request)
    {
        // truy cập vào auth lấy user
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }
        // mỗi user được tạo sẽ mặc định tạo 1 customer với các trường trống
        // tìm user với các điều kiện để đảm bảo thanh toán
        $customer = Customer::where('user_id', $user->id)->first();
        if (!$customer || !trim((string) $customer->phone) || !trim((string) $customer->address)) {
            return redirect()->route('user.info')->with('error', 'Bạn cần bổ sung số điện thoại và địa chỉ trước khi thanh toán.');
        }
        // giữ sản phẩm trong session giỏ hàng, nếu chưa thêm mặc định tạo mảng trống
        $cart = session('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng trống.');
        }

        // Tính tổng tiền từ giỏ hàng + coupon
        // ghi nhận coupon người dùng đã chèn vào
        // mảng chứa biến giá trị cơ bản để xử lý thanh toán 
        $applied = session('coupon');
        [$subtotal, $percent, $discountAmount, $finalTotal] = Order::totalsFromCart($cart, $applied);
        if ($finalTotal <= 0) {
            return redirect()->route('cart.index')->with('error', 'Số tiền thanh toán không hợp lệ.');
        }

        // Cấu hình VNPAY
        $vnpUrl        = env('VNPAY_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html');
        $vnpTmnCode    = env('VNPAY_TMN_CODE');
        $vnpHashSecret = env('VNPAY_HASH_SECRET');
        $returnUrl     = env('VNPAY_RETURN_URL') ?: route('payment.vnpay.return');
        if (!$vnpTmnCode || !$vnpHashSecret) {
            return redirect()->route('cart.index')->with('error', 'Thiếu cấu hình VNPAY (VNPAY_TMN_CODE, VNPAY_HASH_SECRET).');
        }

        // Ghi nhận payment pending
        $txnRef = strtoupper(Str::random(10));
        session(['vnpay_txn_ref' => $txnRef, 'vnpay_total' => $finalTotal, 'payment_user_id' => $user->id]);
        $payment = Payment::create([
            'user_id' => $user->id,
            'order_id' => null,
            'provider' => 'vnpay',
            'currency' => 'VND',
            'amount' => $finalTotal,
            'txn_ref' => $txnRef,
            'status' => 'pending',
        ]);
        session(['payment_id' => $payment->id]);

        // Tạo query
        $inputData = [
            'vnp_Version'   => '2.1.0',
            'vnp_TmnCode'   => $vnpTmnCode,
            'vnp_Amount'    => $finalTotal * 100,
            'vnp_Command'   => 'pay',
            'vnp_CreateDate'=> date('YmdHis'),
            'vnp_CurrCode'  => 'VND',
            'vnp_IpAddr'    => $request->ip(),
            'vnp_Locale'    => 'vn',
            'vnp_OrderInfo' => 'Thanh toan don hang tai ' . config('app.name'),
            'vnp_OrderType' => 'other',
            'vnp_ReturnUrl' => $returnUrl,
            'vnp_TxnRef'    => $txnRef,
        ];
        ksort($inputData);
        $hashData = '';
        $query    = '';
        foreach ($inputData as $key => $value) {
            $hashData .= ($hashData ? '&' : '') . urlencode($key) . '=' . urlencode((string) $value);
            $query    .= urlencode($key) . '=' . urlencode((string) $value) . '&';
        }
        $secureHash  = hash_hmac('sha512', $hashData, (string) $vnpHashSecret);
        $redirectUrl = $vnpUrl . '?' . $query . 'vnp_SecureHashType=SHA512&vnp_SecureHash=' . $secureHash;

        return redirect()->away($redirectUrl);
    }

    // Xử lý người dùng quay về từ VNPAY
    public function vnpayReturn(Request $request)
    {
        $vnpHashSecret = env('VNPAY_HASH_SECRET');
        if (!$vnpHashSecret) {
            return redirect()->route('cart.index')->with('error', 'Thiếu cấu hình VNPAY.');
        }

        $vnpData = [];
        foreach ($request->query() as $key => $value) {
            if (substr((string) $key, 0, 4) === 'vnp_') {
                $vnpData[$key] = $value;
            }
        }

        $receivedSecureHash = $vnpData['vnp_SecureHash'] ?? '';
        unset($vnpData['vnp_SecureHash'], $vnpData['vnp_SecureHashType']);

        ksort($vnpData);
        $hashData = '';
        foreach ($vnpData as $key => $value) {
            $hashData .= ($hashData ? '&' : '') . urlencode($key) . '=' . urlencode((string) $value);
        }
        $calculatedHash = hash_hmac('sha512', $hashData, (string) $vnpHashSecret);
        if (!$receivedSecureHash || !hash_equals($calculatedHash, $receivedSecureHash)) {
            return redirect()->route('cart.index')->with('error', 'Chữ ký không hợp lệ từ VNPAY.');
        }

        $responseCode = (string) ($vnpData['vnp_ResponseCode'] ?? '');

        $returnedTxnRef = (string) ($vnpData['vnp_TxnRef'] ?? '');
        $payment = Payment::where('txn_ref', $returnedTxnRef)->first();
        if (!$payment) {
            $paymentId = session('payment_id');
            if ($paymentId) {
                $payment = Payment::find($paymentId);
            }
        }
        if (!$payment) {
            $sessRef = (string) session('vnpay_txn_ref');
            if ($sessRef) {
                $payment = Payment::where('txn_ref', $sessRef)->first();
            }
        }
        if (!$payment) {
            return redirect()->route('cart.index')->with('error', 'Không tìm thấy giao dịch đã khởi tạo. Vui lòng thực hiện thanh toán lại.');
        }

        $payment->response_code = $responseCode;
        $payment->bank_code = (string) ($vnpData['vnp_BankCode'] ?? '');
        $payment->card_type = (string) ($vnpData['vnp_CardType'] ?? '');
        $payment->gateway_txn_no = (string) ($vnpData['vnp_TransactionNo'] ?? '');
        $payment->secure_hash = (string) ($receivedSecureHash);
        $payment->payload = $vnpData;

        if ($responseCode !== '00') {
            $payment->status = 'failed';
            $payment->save();
            return redirect()->route('cart.index')->with('error', 'Thanh toán thất bại hoặc bị hủy (mã: ' . $responseCode . ').');
        }
        // kiểm tra các điều kiện đảm bảo thanh toán
        $user = auth()->user();
        if (!$user && $payment && $payment->user_id) {
            $user = User::find($payment->user_id);
        }
        if (!$user) {
            $payment->status = 'failed';
            $payment->save();
            return redirect()->route('cart.index')->with('error', 'Không xác định được người dùng cho giao dịch.');
        }

        $cart = session('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Không tìm thấy giỏ hàng để tạo đơn.');
        }

        $customer = Customer::where('user_id', $user->id)->first();
        if (!$customer) {
            return redirect()->route('user.info')->with('error', 'Thiếu thông tin khách hàng.');
        }

        // Tạo Order và Items
        $applied = session('coupon');
        $order = Order::create([
            'customer_id' => $customer->id,
            'status' => 'paid',
        ]);
        $order->fillTotalsFromCart($cart, $applied);
        $order->save();
        // với một sản phẩm gắn các thuộc tính của sản phẩm đó vào $it
        foreach ($cart as $it) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $it['id'],
                'quantity' => (int) ($it['quantity'] ?? 1),
                'price' => (int) ($it['price'] ?? 0),
            ]);
        }
        // xử lý coupon 
        if ($applied && isset($applied['id'])) {
            $coupon = Coupon::find($applied['id']);
            if ($coupon && Carbon::parse($coupon->expiry_date)->isFuture() && $coupon->used_count < $coupon->max_uses) {
                $order->coupon_id = $coupon->id;
                $order->save();
                Coupon::whereKey($coupon->id)->update(['used_count' => $coupon->used_count + 1]);
            }
        }

        // Hoàn tất payment
        $payment->order_id = $order->id;
        $payment->status = 'success';
        $payment->paid_at = now();
        $payment->save();

        session()->forget('cart');
        session()->forget('coupon');
        session()->forget('vnpay_txn_ref');
        session()->forget('vnpay_total');
        session()->forget('payment_id');
        session()->forget('payment_user_id');

        return redirect()->route('cart.index')->with('status', 'Thanh toán thành công. Cảm ơn bạn!');
    }
}
