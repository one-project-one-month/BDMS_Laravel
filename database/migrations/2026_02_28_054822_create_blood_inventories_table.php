<?php

use App\Enums\BloodGroup;
use App\Enums\BloodInventoryStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('blood_inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donation_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('hospital_id')->constrained()->cascadeOnDelete()->index();
            $table->enum('blood_group', BloodGroup::values())->index();
            $table->unsignedSmallInteger('units');
            $table->date('collected_at');
            $table->date('expired_at')->index();
            $table->enum('status', BloodInventoryStatus::values())->default(BloodInventoryStatus::AVAILABLE->value)
                ->index();
            $table->foreignId('blood_request_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
            $table->softDeletesTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blood_inventories');
    }
};
