<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TwoFactorAuthentication extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'secret_code',
        'backup_codes',
        'enabled',
        'verified_at',
        'last_used_at',
    ];

    protected $casts = [
        'backup_codes' => 'array',
        'enabled' => 'boolean',
        'verified_at' => 'datetime',
        'last_used_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isEnabled()
    {
        return $this->enabled && $this->verified_at;
    }

    public function generateBackupCodes()
    {
        $codes = [];
        for ($i = 0; $i < 8; $i++) {
            $codes[] = strtoupper(\Str::random(8));
        }
        $this->backup_codes = $codes;
        $this->save();
        return $codes;
    }

    public function verifyBackupCode($code)
    {
        if (!$this->backup_codes) {
            return false;
        }

        $index = array_search(strtoupper($code), $this->backup_codes);
        if ($index !== false) {
            unset($this->backup_codes[$index]);
            $this->backup_codes = array_values($this->backup_codes);
            $this->save();
            return true;
        }

        return false;
    }
}
