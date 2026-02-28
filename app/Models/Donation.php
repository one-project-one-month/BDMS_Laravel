<?php

namespace App\Models;

use App\Models\Donor;
use App\Models\Hospital;
use App\Models\MedicalRecord;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    protected $fillable = ['donor_id', 'request_id', 'hospital_id', 'donation_code', 'status', 'unit_donated', 'donation_date', 'approved_by', 'approved_at'];

    public function donor()
    {
        return $this->belongsTo(Donor::class);
    }

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    public function medicalRecord()
    {
        return $this->hasOne(MedicalRecord::class);
    }
}
