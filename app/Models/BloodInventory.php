<?php
namespace App\Models;

use App\Enums\BloodGroup;
use App\Enums\BloodInventoryStatus;
use App\Models\BloodRequest;
use App\Models\Donation;
use App\Models\Hospital;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BloodInventory extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'donation_id',
        'hospital_id',
        'blood_group',
        'units',
        'collected_at',
        'expired_at',
        'status',
        'blood_request_id',
    ];

    protected $casts = [
        'collected_at' => 'date',
        'expired_at'   => 'date',
        'units'        => 'integer',
        'blood_group'  => BloodGroup::class,
        'status'       => BloodInventoryStatus::class,
    ];

    public static function expireIfNeeded(): int
    {
        return self::query()
            ->where('status', BloodInventoryStatus::AVAILABLE->value)
            ->whereNotNull('expired_at')
            ->whereDate('expired_at', '<=', now()->toDateString())
            ->update(['status' => BloodInventoryStatus::EXPIRED->value]);
    }

    public function scopeAvailableUnitsByHospital($query)
    {
        return $query->where('status', BloodInventoryStatus::AVAILABLE)
            ->where(function ($q) {
                $q->whereNull('expired_at')
                    ->orWhereDate('expired_at', '>', now()->toDateString());
            })
            ->selectRaw('hospital_id, SUM(units) as total_units')
            ->groupBy('hospital_id');
    }

    public function donation()
    {
        return $this->belongsTo(Donation::class);
    }

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    public function bloodRequest()
    {
        return $this->belongsTo(BloodRequest::class);
    }
}
