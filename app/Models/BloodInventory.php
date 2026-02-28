<?php

namespace App\Models;

use App\Models\Hospital;
use Illuminate\Database\Eloquent\Model;

class BloodInventory extends Model
{
    protected $table = 'blood_inventory';
    protected $fillable = ['hospital_id', 'blood_group', 'units_available', 'units_reserved', 'unit_total', 'expiry_date'];

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }
}
