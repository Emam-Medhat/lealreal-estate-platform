<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'email_or_phone',
        'ip_address',
        'user_agent',
        'successful',
        'failure_reason',
        'user_id',
        'attempted_at',
    ];

    protected $casts = [
        'successful' => 'boolean',
        'attempted_at' => 'datetime',
    ];

    public $timestamps = true;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeSuccessful($query)
    {
        return $query->where('successful', true);
    }

    public function scopeFailed($query)
    {
        return $query->where('successful', false);
    }

    public function scopeByIp($query, $ip)
    {
        return $query->where('ip_address', $ip);
    }

    public function scopeByEmail($query, $email)
    {
        return $query->where('email_or_phone', $email);
    }

    public function scopeRecent($query, $minutes = 15)
    {
        return $query->where('attempted_at', '>=', now()->subMinutes($minutes));
    }

    public function isSuspicious()
    {
        return !$this->successful && $this->failure_reason === 'account_locked';
    }
}
