<?php

namespace App\Models;

use App\Models\Appointment;
use App\Models\Donation;
use App\Models\DonorRequestMatch;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Donor extends Model
{
    protected $fillable = ['user_id', 'nrc_no', 'blood_group', 'gender', 'address', 'is_active', 'date_of_birth', 'last_donation_date', 'total_donations', 'emergency_contact', 'emergency_phone'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function donations()
    {
        return $this->hasMany(Donation::class);
    }

    public function matches()
    {
        return $this->hasMany(DonorRequestMatch::class);
    }
}
