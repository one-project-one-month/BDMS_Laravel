<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Announcement;
use App\Models\BloodRequest;
use App\Models\Certificate;
use App\Models\Donor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'hospital_id',
        'user_name',
        'phone',
        'password',
        'role',
        'is_active'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    public function donor()
    {
        return $this->hasOne(Donor::class);
    }

    public function bloodRequests()
    {
        return $this->hasMany(BloodRequest::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    public function announcements()
    {
        return $this->hasMany(Announcement::class);
    }
}
