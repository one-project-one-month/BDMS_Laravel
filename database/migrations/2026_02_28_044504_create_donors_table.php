<?php

use App\Enums\BloodGroup;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('donors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nrc_no');
            $table->enum('blood_group', BloodGroup::values());
            $table->enum('gender', ['male', 'female', 'other']);
            $table->string('address');
            $table->date('date_of_birth');
            $table->boolean('is_active')->default(true);
            $table->date('last_donation_date')->nullable();
            $table->integer('total_donations')->default(0);
            $table->text('medical_notes')->nullable();
            $table->string('emergency_contact');
            $table->string('emergency_phone');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donors');
    }
};
