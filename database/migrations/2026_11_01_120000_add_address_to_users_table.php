<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * ที่อยู่เริ่มต้นของผู้ใช้ (แก้ได้ในหน้า My Profile, ใช้เติมช่องที่อยู่ตอน Checkout)
     * ที่อยู่จริงของแต่ละออเดอร์ยังเก็บใน orders.shipping_address เหมือนเดิม
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('address')->nullable()->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('address');
        });
    }
};
