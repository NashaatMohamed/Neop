<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number', 50)->unique();
            $table->foreignId('from_account_id')->nullable()->constrained('accounts')->onDelete('cascade');
            $table->foreignId('to_account_id')->nullable()->constrained('accounts')->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->decimal('from_balance_before', 15, 2)->nullable();
            $table->decimal('from_balance_after', 15, 2)->nullable();
            $table->decimal('to_balance_before', 15, 2)->nullable();
            $table->decimal('to_balance_after', 15, 2)->nullable();
            $table->enum('status', ['success', 'failed']);
            $table->string('description')->nullable();
            $table->text('failure_reason')->nullable();
            $table->timestamp('created_at')->useCurrent();

            // Indexes
            $table->index('from_account_id');
            $table->index('to_account_id');
            $table->index('created_at');
            $table->index(['status', 'created_at']);
        });

        DB::statement('ALTER TABLE transactions ADD CONSTRAINT transactions_amount_positive CHECK (amount > 0)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
