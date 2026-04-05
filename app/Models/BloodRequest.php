<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Enums\BloodGroup;
use App\Enums\Urgency;
use App\Enums\BloodRequestStatus;

class BloodRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'blood_requests';

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
        'relationship_patient',
        'reason',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'blood_group' => BloodGroup::class,
        'urgency' => Urgency::class,
        'status' => BloodRequestStatus::class,
        'required_date' => 'date',
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $attributes = [
        'status' => BloodRequestStatus::PENDING,
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /*
    |--------------------------------------------------------------------------
    | Status Check Helpers Functions
    |--------------------------------------------------------------------------
    */

    public function canBeApproved(): bool
    {
        return $this->status === BloodRequestStatus::PENDING;
    }

    public function canBeRejected(): bool
    {
        return $this->status === BloodRequestStatus::PENDING;
    }

    public function canBeCancelled(): bool
    {
        return $this->status === BloodRequestStatus::PENDING;
    }

    public function canBeFulfilled(): bool
    {
        return $this->status === BloodRequestStatus::APPROVED;
    }

    // Approve Method
    public function approve(int $adminId): void
    {
        if (!$this->canBeApproved()) {
            throw new \Exception('Only pending requests can be approved.');
        }

        $this->update([
            'status' => BloodRequestStatus::APPROVED,
            'approved_by' => $adminId,
            'approved_at' => now(),
        ]);
    }

    // Reject Method
    public function reject(): void
    {
        if (!$this->canBeRejected()) {
            throw new \Exception('Only pending requests can be rejected.');
        }

        $this->update([
            'status' => BloodRequestStatus::REJECTED,
        ]);
    }

    // Cancel Method
    public function cancel(): void
    {
        if (!$this->canBeCancelled()) {
            throw new \Exception('Only pending requests can be cancelled.');
        }

        $this->update([
            'status' => BloodRequestStatus::CANCELLED,
        ]);
    }

    // Fulfill Method (After Donation Completed)
    public function fulfill(): void
    {
        if (!$this->canBeFulfilled()) {
            throw new \Exception('Only approved requests can be fulfilled.');
        }

        $this->update([
            'status' => BloodRequestStatus::FULFILLED,
        ]);
    }
}
