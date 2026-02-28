<?php

namespace App\Models;

use App\Models\Appointment;
use App\Models\BloodInventory;
use App\Models\BloodRequest;
use App\Models\Donation;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Hospital extends Model
{
    protected $fillable = [
        'hospital_name',
        'address',
        'phone',
        'email',
        'license_no',
        'is_verified'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function donations()
    {
        return $this->hasMany(Donation::class);
    }

    public function bloodInventory()
    {
        return $this->hasMany(BloodInventory::class);
    }

    public function bloodRequests()
    {
        return $this->hasMany(BloodRequest::class);
    }
}
