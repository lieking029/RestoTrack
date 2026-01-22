<?php

namespace App\Models;

use App\Enums\UserType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasUuids, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'password',
        'user_type',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'full_name',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'user_type' => UserType::class,
        ];
    }

    protected function fullName(): Attribute
    {
        return Attribute::get(function () {
            $parts = array_filter([
                $this->first_name,
                $this->middle_name,
                $this->last_name
            ]);
            
            return trim(implode(' ', $parts));
        });
    }

    public function getInitialsAttribute(): string
    {
        $firstInitial = $this->first_name ? strtoupper(substr($this->first_name, 0, 1)) : '';
        $lastInitial = $this->last_name ? strtoupper(substr($this->last_name, 0, 1)) : '';
        
        return $firstInitial . $lastInitial;
    }

    public function isAdmin(): bool
    {
        return $this->user_type->value === UserType::Admin;
    }

    public function isManager(): bool
    {
        return $this->user_type->value === UserType::Manager;
    }

    public function isEmployee(): bool
    {
        return $this->user_type->value === UserType::Employee;
    }

    public function getRoleNameAttribute(): string
    {
        return $this->user_type->description;
    }

    public function scopeAdmins($query)
    {
        return $query->where('user_type', UserType::Admin);
    }

    public function scopeManagers($query)
    {
        return $query->where('user_type', UserType::Manager);
    }

    public function scopeEmployees($query)
    {
        return $query->where('user_type', UserType::Employee);
    }

    public function scopeNonAdmins($query)
    {
        return $query->whereNot('user_type', UserType::Admin);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('first_name', 'like', "%{$search}%")
              ->orWhere('last_name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
        });
    }

    public function processedPayments()
{
    return $this->hasMany(Payment::class, 'processed_by');
}
}