<?php

namespace App\Models;

use App\Enums\BloodGroup;
use App\Enums\DonationStatus;
use App\Models\BloodInventory;
use App\Models\BloodRequest;
use App\Models\Donor;
use App\Models\Hospital;
use App\Models\MedicalRecord;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Donation extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'donor_id',
        'hospital_id',
        'blood_request_id',
        'created_by',
        'blood_group',
        'units_donated',
        'donation_date',
        'status',
        'approved_by',
        'approved_at',
        'remarks',
    ];

    protected $casts = [
        'donation_date' => 'date',
        'approved_at' => 'datetime',
        'units_donated' => 'integer',
        'blood_group' => BloodGroup::class,
        'status' => DonationStatus::class,
    ];

    public function donor()
    {
        return $this->belongsTo(Donor::class);
    }

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    public function bloodRequest()
    {
        return $this->belongsTo(BloodRequest::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function medicalRecord()
    {
        return $this->hasOne(MedicalRecord::class);
    }

    public function inventory()
    {
        return $this->hasOne(BloodInventory::class);
    }
}
