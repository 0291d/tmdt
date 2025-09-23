<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Tham chiếu người dùng và đơn hàng
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->uuid('order_id')->nullable();

            // Thông tin giao dịch chung
            $table->string('provider')->default('vnpay');
            $table->string('currency', 10)->default('VND');
            $table->decimal('amount', 15, 2)->default(0);

            // Thông tin định danh giao dịch
            $table->string('txn_ref')->index(); // vnp_TxnRef
            $table->string('gateway_txn_no')->nullable(); // vnp_TransactionNo

            // Trạng thái và chi tiết kết quả
            $table->string('status')->default('pending'); // pending|success|failed
            $table->string('response_code', 10)->nullable(); // vnp_ResponseCode
            $table->string('bank_code', 50)->nullable(); // vnp_BankCode
            $table->string('card_type', 50)->nullable(); // vnp_CardType
            $table->string('secure_hash', 255)->nullable(); // vnp_SecureHash
            $table->timestamp('paid_at')->nullable();

            // Lưu toàn bộ payload trả về từ cổng thanh toán (nếu cần đối soát)
            $table->json('payload')->nullable();

            $table->timestamps();

            // FK tới orders (UUID)
            $table->foreign('order_id')->references('id')->on('orders')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};

