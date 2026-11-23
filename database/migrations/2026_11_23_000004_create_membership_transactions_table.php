<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('membership_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->restrictOnDelete();
            $table->foreignId('membership_level_id')->constrained()->restrictOnDelete();
            $table->string('reference_number')->unique();
            $table->enum('type', ['application', 'renewal']);
            $table->enum('status', ['pending', 'paid', 'approved', 'expired', 'cancelled'])->default('pending');
            $table->decimal('amount_due', 10, 2);
            $table->date('starts_on')->nullable();
            $table->date('expires_on')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['member_id', 'created_at']);
        });
    }

    public function down(): void { Schema::dropIfExists('membership_transactions'); }
};
