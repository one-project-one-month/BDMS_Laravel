<?php

use App\Enums\BloodGroup;
use App\Enums\InfectionResult;
use App\Enums\ScreeningStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('medical_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donation_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('hospital_id')->constrained()->cascadeOnDelete()->index();
            $table->decimal('hemoglobin_level', 4, 2)->nullable();

            $table->enum('hiv_result', InfectionResult::values())->nullable();
            $table->enum('hepatitis_b_result', InfectionResult::values())->nullable();
            $table->enum('hepatitis_c_result', InfectionResult::values())->nullable();
            $table->enum('malaria_result', InfectionResult::values())->nullable();
            $table->enum('syphilis_result', InfectionResult::values())->nullable();

            $table->enum('blood_group', BloodGroup::values())->index();

            $table->enum('screening_status', ScreeningStatus::values())->default(ScreeningStatus::PENDING->value)
                ->index();

            $table->text('screening_notes')->nullable();

            $table->foreignId('screened_by')->nullable()
                ->constrained('users');

            $table->timestamp('screening_at')->nullable();

            $table->timestamps();
            $table->softDeletesTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_records');
    }
};
