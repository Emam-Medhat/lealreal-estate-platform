<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'language',
        'timezone',
        'currency',
        'date_format',
        'time_format',
        'theme',
        'email_notifications',
        'push_notifications',
        'sms_notifications',
        'marketing_emails',
        'security_alerts',
        'newsletter_subscription',
        'profile_visibility',
        'show_online_status',
        'allow_friend_requests',
        'auto_save_drafts',
        'two_factor_enabled',
        'biometric_enabled',
        'session_timeout',
        'privacy_settings',
        'display_settings',
        'notification_settings',
        'security_settings',
        'metadata',
    ];

    protected $casts = [
        'email_notifications' => 'boolean',
        'push_notifications' => 'boolean',
        'sms_notifications' => 'boolean',
        'marketing_emails' => 'boolean',
        'security_alerts' => 'boolean',
        'newsletter_subscription' => 'boolean',
        'show_online_status' => 'boolean',
        'allow_friend_requests' => 'boolean',
        'auto_save_drafts' => 'boolean',
        'two_factor_enabled' => 'boolean',
        'biometric_enabled' => 'boolean',
        'privacy_settings' => 'json',
        'display_settings' => 'json',
        'notification_settings' => 'json',
        'security_settings' => 'json',
        'metadata' => 'json',
    ];

    protected $attributes = [
        'language' => 'en',
        'timezone' => 'UTC',
        'currency' => 'USD',
        'date_format' => 'Y-m-d',
        'time_format' => '24h',
        'theme' => 'light',
        'email_notifications' => true,
        'push_notifications' => true,
        'sms_notifications' => false,
        'marketing_emails' => false,
        'security_alerts' => true,
        'newsletter_subscription' => false,
        'profile_visibility' => 'public',
        'show_online_status' => true,
        'allow_friend_requests' => true,
        'auto_save_drafts' => true,
        'two_factor_enabled' => false,
        'biometric_enabled' => false,
        'session_timeout' => 120,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFormattedDateAttribute(): string
    {
        return now()->setTimezone($this->timezone)->format($this->date_format);
    }

    public function getFormattedTimeAttribute(): string
    {
        $format = $this->time_format === '12h' ? 'h:i A' : 'H:i';
        return now()->setTimezone($this->timezone)->format($format);
    }

    public function getFormattedDateTimeAttribute(): string
    {
        $timeFormat = $this->time_format === '12h' ? 'h:i A' : 'H:i';
        return now()->setTimezone($this->timezone)->format($this->date_format . ' ' . $timeFormat);
    }

    public function enableNotification(string $type): void
    {
        if (property_exists($this, $type)) {
            $this->update([$type => true]);
        }
    }

    public function disableNotification(string $type): void
    {
        if (property_exists($this, $type)) {
            $this->update([$type => false]);
        }
    }

    public function setPrivacySetting(string $key, $value): void
    {
        $privacySettings = $this->privacy_settings ?? [];
        $privacySettings[$key] = $value;
        $this->update(['privacy_settings' => $privacySettings]);
    }

    public function getPrivacySetting(string $key, $default = null)
    {
        $privacySettings = $this->privacy_settings ?? [];
        return $privacySettings[$key] ?? $default;
    }

    public function setDisplaySetting(string $key, $value): void
    {
        $displaySettings = $this->display_settings ?? [];
        $displaySettings[$key] = $value;
        $this->update(['display_settings' => $displaySettings]);
    }

    public function getDisplaySetting(string $key, $default = null)
    {
        $displaySettings = $this->display_settings ?? [];
        return $displaySettings[$key] ?? $default;
    }

    public function setNotificationSetting(string $key, $value): void
    {
        $notificationSettings = $this->notification_settings ?? [];
        $notificationSettings[$key] = $value;
        $this->update(['notification_settings' => $notificationSettings]);
    }

    public function getNotificationSetting(string $key, $default = null)
    {
        $notificationSettings = $this->notification_settings ?? [];
        return $notificationSettings[$key] ?? $default;
    }

    public function setSecuritySetting(string $key, $value): void
    {
        $securitySettings = $this->security_settings ?? [];
        $securitySettings[$key] = $value;
        $this->update(['security_settings' => $securitySettings]);
    }

    public function getSecuritySetting(string $key, $default = null)
    {
        $securitySettings = $this->security_settings ?? [];
        return $securitySettings[$key] ?? $default;
    }

    public function scopeByLanguage($query, string $language)
    {
        return $query->where('language', $language);
    }

    public function scopeByCurrency($query, string $currency)
    {
        return $query->where('currency', $currency);
    }

    public function scopeByTheme($query, string $theme)
    {
        return $query->where('theme', $theme);
    }

    public function scopeWithNotificationsEnabled($query)
    {
        return $query->where('email_notifications', true)
                    ->orWhere('push_notifications', true)
                    ->orWhere('sms_notifications', true);
    }
}
