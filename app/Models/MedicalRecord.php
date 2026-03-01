<?php

namespace App\Models;

use App\Enums\BloodGroup;
use App\Enums\InfectionResult;
use App\Enums\ScreeningStatus;
use App\Models\Donation;
use App\Models\Hospital;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MedicalRecord extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'donation_id',
        'hospital_id',
        'hemoglobin_level',
        'hiv_result',
        'hepatitis_b_result',
        'hepatitis_c_result',
        'malaria_result',
        'syphilis_result',
        'blood_group',
        'screening_status',
        'screening_notes',
        'screened_by',
        'screening_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'hemoglobin_level' => 'decimal:2',
        'screening_at' => 'datetime',
        'hiv_result' => InfectionResult::class,
        'screening_status' => ScreeningStatus::class,
        'blood_group' => BloodGroup::class,
    ];

    public function donation()
    {
        return $this->belongsTo(Donation::class);
    }

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    public function screener()
    {
        return $this->belongsTo(User::class, 'screened_by');
    }
}
