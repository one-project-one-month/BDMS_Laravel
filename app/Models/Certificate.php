<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Certificate extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'user_id',
        'certificate_title',
        'certificate_description',
        'certificate_date',
        'cetificate_image'
    ];

    protected $casts = [
        'certificate_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
