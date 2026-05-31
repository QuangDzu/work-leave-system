<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class LeaveApplication extends Model
{
    use HasFactory, SoftDeletes;

    const TYPE_ANNUAL = 'annual';
    const TYPE_SICK   = 'sick';
    const TYPE_UNPAID = 'unpaid';

    const STATUS_NEW       = 'new';
    const STATUS_PENDING   = 'pending';
    const STATUS_APPROVED  = 'approved';
    const STATUS_REJECTED  = 'rejected';
    const STATUS_CANCELLED = 'cancelled';

    protected $keyType   = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'user_id',
        'start_date',
        'end_date',
        'total_days',
        'reason',
        'rejection_reason',
        'type',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'total_days' => 'integer',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) $model->id = Str::random(10);
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function scopeForUser($q, $uid)
    {
        return $q->where('user_id', $uid);
    }
    public function scopeByStatus($q, $s)
    {
        return $q->where('status', $s);
    }
    public function scopeByType($q, $t)
    {
        return $q->where('type', $t);
    }
    public function scopeFromDate($q, $d)
    {
        return $q->where('start_date', '>=', $d);
    }
    public function scopeToDate($q, $d)
    {
        return $q->where('end_date', '<=', $d);
    }

    public function isEditable(): bool
    {
        return in_array($this->status, [self::STATUS_NEW, self::STATUS_PENDING]);
    }
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }
    public function wasApproved(): bool
    {
        return $this->getOriginal('status') === self::STATUS_APPROVED;
    }
}
