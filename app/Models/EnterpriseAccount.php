<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EnterpriseAccount extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_name',
        'company_type',
        'industry',
        'size',
        'website',
        'phone',
        'address',
        'city',
        'country',
        'postal_code',
        'tax_id',
        'registration_number',
        'contact_person',
        'contact_email',
        'contact_phone',
        'billing_address',
        'payment_method',
        'subscription_plan',
        'status',
        'upgraded_at',
        'subscription_id',
        'tenant_id',
        'tenant_name',
        'subscription_plan_id',
        'max_users',
        'storage_limit',
        'bandwidth_limit',
        'api_calls_limit',
        'trial_expires_at',
        'notes',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'upgraded_at' => 'datetime',
        'trial_expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function tenant()
    {
        return $this->hasOne(Tenant::class, 'enterprise_id');
    }

    public function subscription()
    {
        return $this->hasOne(Subscription::class, 'enterprise_id');
    }

    public function adminUser()
    {
        return $this->hasOne(User::class, 'enterprise_id')->where('role', 'enterprise_admin');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'enterprise_id');
    }
}
