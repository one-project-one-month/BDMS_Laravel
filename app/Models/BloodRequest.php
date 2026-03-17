<?php

namespace App\Models;

use App\Enums\BloodGroup;
use App\Enums\BloodRequestStatus;
use App\Enums\Urgency;
use App\Models\Appointment;
use App\Models\Hospital;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BloodRequest extends Model
{
    use SoftDeletes;
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
        'reason',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'required_date' => 'date',
        'approved_at' => 'datetime',
        'units_required' => 'integer',
        'blood_group' => BloodGroup::class,
        'urgency' => Urgency::class,
        'status' => BloodRequestStatus::class,
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
