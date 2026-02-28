<?php

use App\Enums\DonationStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donor_id')->constrained('donors')->onDelete('cascade');
            $table->foreignId('request_id')->nullable()->constrained('blood_requests')->onDelete('set null');
            $table->foreignId('hospital_id')->constrained('hospitals')->onDelete('cascade');
            $table->string('donation_code')->unique();
            $table->integer('unit_donated')->default(1);
            $table->enum('status', DonationStatus::values())->default(DonationStatus::PENDING->value);
            $table->date('donation_date');
            $table->string('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
