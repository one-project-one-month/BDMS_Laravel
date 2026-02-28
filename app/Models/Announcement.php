<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = ['user_id', 'title', 'content', 'is_active', 'expired_at'];

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
