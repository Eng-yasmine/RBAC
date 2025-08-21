<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
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
        'name',
        'email',
        'password',
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
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function permessions()
    {
        return $this->belongsToMany(Permession::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Get all permissions for the user (direct permissions + role permissions)
     *
     * @return array
     */
    public function getAllPermessions(): array
    {
        // Get direct permissions assigned to user
        $directPermessions = $this->permessions->pluck('name')->toArray();

        // Get permissions from all user roles
        $rolePermessions = $this->roles->flatMap(function ($role) {
            return $role->permessions->pluck('name')->toArray();
        })->toArray();

        // Merge and remove duplicates
        return array_unique(array_merge($directPermessions, $rolePermessions));
    }

    /**
     * Check if user has specific permission
     *
     * @param string $permission
     * @return bool
     */
    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->getAllPermessions());
    }

    /**
     * Check if user has any of the given roles
     *
     * @param string|array $roles
     * @return bool
     */
    public function hasRole(string|array $roles): bool
    {
        $userRoles = $this->roles->pluck('name')->toArray();

        // Convert single role to array
        $roles = is_array($roles) ? $roles : [$roles];

        return !empty(array_intersect($roles, $userRoles));
    }

    /**
     * Check if user has all of the given roles
     *
     * @param array $roles
     * @return bool
     */
    public function hasAllRoles(array $roles): bool
    {
        $userRoles = $this->roles->pluck('name')->toArray();
        return empty(array_diff($roles, $userRoles));
    }
}
