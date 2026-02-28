<?php

namespace App\Models;

use App\Models\Appointment;
use App\Models\Hospital;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class BloodRequest extends Model
{
    protected $fillable = [
        'user_id',
        'hospital_id',
        'patient_name',
        'blood_group',
        'units_required',
        'contact_phone',
        'urgency',
        'required_date',
        'status',
        'notes',
        'approved_by',
        'approved_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'request_id');
    }
}
