<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Announcement;
use App\Models\BloodRequest;
use App\Models\Certificate;
use App\Models\Donor;
use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'role_id',
        'hospital_id',
        'user_name',
        'email',
        'password',
        'is_active',
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
            'is_active' => 'boolean'
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
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

    public function hasPermission($permission)
    {
        return $this->role?->permissions
            ->contains('name', $permission);
    }
}
