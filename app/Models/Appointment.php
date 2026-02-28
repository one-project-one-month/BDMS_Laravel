<?php

namespace App\Models;

use App\Models\Donor;
use App\Models\Hospital;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = ['donor_id', 'hospital_id', 'request_id', 'appointment_type', 'appointment_date', 'status'];

    public function donor()
    {
        return $this->belongsTo(Donor::class);
    }

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }
}
