<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

/**
 * @method bool isAdmin()
 * @method bool isManager()
 * @method bool isEmployee()
 * @method bool isManagerOrAdmin()
 * @property string $role_name
 * @property string $role_display
 * @property array  $permission_names
 */

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, SoftDeletes;



    protected $keyType   = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'name',
        'email',
        'password',
        'remaining_days',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $hidden = ['password', 'remember_token'];


    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'remaining_days'    => 'integer',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) $model->id = Str::random(10);
        });
    }

    // ── JWT ──
    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [];
    }

    // ── Relationships ──
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_role')
            ->withPivot('assigned_by', 'assigned_at');
    }

    public function leaveApplications()
    {
        return $this->hasMany(LeaveApplication::class);
    }

    // ── Permission helpers ──
    /**
     * Kiểm tra user có permission không (cache eager loaded roles).
     */
    public function hasPermission(string $permission): bool
    {
        return $this->roles
            ->flatMap(fn($role) => $role->permissions)
            ->pluck('name')
            ->contains($permission);
    }

    public function hasRole(string $roleName): bool
    {
        return $this->roles->pluck('name')->contains($roleName);
    }

    public function hasAnyRole(array $roleNames): bool
    {
        return $this->roles->pluck('name')->intersect($roleNames)->isNotEmpty();
    }


    // ── Shortcut getters ───
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isManager(): bool
    {
        return $this->hasRole('manager');
    }

    public function isEmployee(): bool
    {
        return $this->hasRole('employee');
    }

    public function isManagerOrAdmin(): bool
    {
        return $this->hasAnyRole(['admin', 'manager']);
    }


    public function getRoleNameAttribute(): string
    {
        return $this->roles->first()?->name ?? 'employee';
    }

    public function getRoleDisplayAttribute(): string
    {
        return $this->roles->first()?->display_name ?? 'Nhân viên';
    }

    public function getPermissionNamesAttribute(): array
    {
        return $this->roles
            ->flatMap(fn($r) => $r->permissions)
            ->pluck('name')
            ->unique()
            ->values()
            ->all();
    }
}
