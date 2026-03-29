<?php

use App\Enums\BloodGroup;
use App\Enums\BloodRequestStatus;
use App\Enums\Urgency;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('blood_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('hospital_id')->constrained()->cascadeOnDelete();
            $table->string('patient_name');
            $table->enum('blood_group', BloodGroup::values())->index();
            $table->unsignedSmallInteger('units_required');
            $table->string('contact_phone');
            $table->enum('urgency', Urgency::values())->default(Urgency::PRE_BOOKED->value)->index();
            $table->date('required_date');
            $table->enum('status', BloodRequestStatus::values())
                ->default(BloodRequestStatus::PENDING->value)
                ->index();
            $table->text('reason')->nullable();
            $table->string('relationship_patient')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletesTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blood_requests');
    }
};
