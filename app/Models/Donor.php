<?php

namespace App\Models;

use App\Enums\BloodGroup;
use App\Enums\Gender;
use App\Models\Appointment;
use App\Models\Donation;
use App\Models\DonorRequestMatch;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Donor extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'user_id',
        'nrc_no',
        'date_of_birth',
        'gender',
        'blood_group',
        'weight',
        'last_donation_date',
        'remarks',
        'emergency_contact',
        'emergency_phone',
        'address',
        'is_active',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'last_donation_date' => 'date',
        'weight' => 'decimal:2',
        'is_active' => 'boolean',
        'gender' => Gender::class,
        'blood_group' => BloodGroup::class,
    ];

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
