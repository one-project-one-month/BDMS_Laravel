<?php

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
            $table->foreignId('donation_id')->unique()->constrained('donations')->onDelete('cascade');
            $table->decimal('hemoglobin_level', 4, 2);
            $table->enum('hiv_result', ['positive', 'negative', 'inconclusive']);
            $table->string('blood_group');
            $table->enum('hepatitis_b_result', ['positive', 'negative']);
            $table->enum('hepatitis_c_result', ['positive', 'negative']);
            $table->enum('malaria_result', ['positive', 'negative']);
            $table->enum('syphilis_result', ['positive', 'negative']);
            $table->enum('screening_status', ['failed', 'passed']);
            $table->text('screening_notes')->nullable();
            $table->string('screened_by')->nullable();
            $table->date('screening_date');
            $table->timestamps();
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
