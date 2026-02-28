<?php

namespace App\Models;

use App\Models\Donation;
use Illuminate\Database\Eloquent\Model;

class MedicalRecord extends Model
{
    protected $fillable = ['donation_id', 'hemoglobin_level', 'hiv_result', 'blood_group', 'hepatitis_b_result', 'hepatitis_c_result', 'malaria_result', 'syphilis_result', 'screening_status', 'screening_notes', 'screened_by', 'screening_date'];

    public function donation()
    {
        return $this->belongsTo(Donation::class);
    }
}
