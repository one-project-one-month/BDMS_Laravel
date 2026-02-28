<?php

namespace App\Models;

use App\Models\Donor;
use Illuminate\Database\Eloquent\Model;

class DonorRequestMatch extends Model
{
    protected $table = 'donor_request_match';

    protected $fillable = ['donor_id', 'request_id', 'status', 'notified_at', 'responded_at'];

    public function donor()
    {
        return $this->belongsTo(Donor::class);
    }
}
