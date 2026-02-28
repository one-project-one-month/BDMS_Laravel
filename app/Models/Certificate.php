<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    protected $fillable = ['user_id', 'typeOfCerti', 'certificate_title', 'certificate_description', 'certificate_date'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
