<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pto_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 5, 2);
            $table->enum('type', ['monthly_accrual', 'manual_adjustment', 'carry_over']);
            $table->date('effective_date');
            $table->date('reference_month')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'type', 'reference_month'], 'pto_unique_monthly_accrual');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pto_transactions');
    }
};
