<?php

namespace App\Models;

use App\Enums\AppointmentStatus;
use App\Models\BloodRequest;
use App\Models\Donation;
use App\Models\Hospital;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'user_id',
        'hospital_id',
        'donation_id',
        'blood_request_id',
        'appointment_date',
        'appointment_time',
        'status',
        'remarks',
    ];

    protected $casts = [
        'appointment_date' => 'date',
        'appointment_time' => 'datetime:H:i', // Time format
        'status' => AppointmentStatus::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    public function donation()
    {
        return $this->belongsTo(Donation::class);
    }

    public function blood_request()
    {
        return $this->belongsTo(BloodRequest::class);
    }
}
