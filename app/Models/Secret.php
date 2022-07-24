<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Secret extends Model
{
    use HasFactory;

    protected $casts = [
        'expires_at' => 'datetime',
        'remaining_views' => 'integer',
    ];

    protected $primaryKey = 'hash';
    public $incrementing = false;

    protected static function booted()
    {
        static::creating(function ($secret) {
            $secret->hash = sha1(($secret->secret_text ?: 'secret') . Str::uuid());
        });
    }

    public function isExpired(): bool
    {
        return $this->remaining_views <= 0 || (isset($this->expires_at) && $this->expires_at->isPast());
    }
}
