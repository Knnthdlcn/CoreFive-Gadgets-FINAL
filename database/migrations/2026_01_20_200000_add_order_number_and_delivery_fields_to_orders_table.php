<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'order_number')) {
                $table->string('order_number', 32)->nullable()->after('id');
                $table->unique('order_number');
            }
            if (!Schema::hasColumn('orders', 'delivered_at')) {
                $table->timestamp('delivered_at')->nullable()->after('status');
            }
            if (!Schema::hasColumn('orders', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('delivered_at');
            }
        });

        // Expand status enum to include 'delivered' (MySQL). Laravel schema builder cannot alter enums reliably.
        // Keep the existing values, add delivered.
        DB::statement("ALTER TABLE `orders` MODIFY `status` ENUM('pending','processing','shipped','delivered','completed','cancelled') NOT NULL DEFAULT 'pending'");

        // Backfill order_number for existing orders.
        // Format: CFG-YYYYMMDD-000001 (based on created_at + id)
        DB::table('orders')
            ->whereNull('order_number')
            ->orderBy('id')
            ->chunkById(200, function ($orders) {
                foreach ($orders as $order) {
                    $date = $order->created_at ? date('Ymd', strtotime($order->created_at)) : date('Ymd');
                    $orderNumber = 'CFG-' . $date . '-' . str_pad((string) $order->id, 6, '0', STR_PAD_LEFT);
                    DB::table('orders')->where('id', $order->id)->update(['order_number' => $orderNumber]);
                }
            });
    }

    public function down(): void
    {
        // Revert enum (drop delivered)
        DB::statement("ALTER TABLE `orders` MODIFY `status` ENUM('pending','processing','shipped','completed','cancelled') NOT NULL DEFAULT 'pending'");

        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'completed_at')) {
                $table->dropColumn('completed_at');
            }
            if (Schema::hasColumn('orders', 'delivered_at')) {
                $table->dropColumn('delivered_at');
            }
            if (Schema::hasColumn('orders', 'order_number')) {
                $table->dropUnique(['order_number']);
                $table->dropColumn('order_number');
            }
        });
    }
};
