<?php

use App\Enums\BloodGroup;
use App\Enums\Gender;
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
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('nrc_no')->unique();
            $table->date('date_of_birth');
            $table->enum('gender', Gender::values());
            $table->enum('blood_group', BloodGroup::values())->index();
            $table->decimal('weight', 5, 2);
            $table->date('last_donation_date')->nullable();
            $table->text('remarks')->nullable();
            $table->string('emergency_contact');
            $table->string('emergency_phone', 20);
            $table->text('address');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletesTz();
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
