<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppointmentDocument extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'appointment_id',
        'agent_id',
        'title',
        'description',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'category',
        'is_public',
        'version',
        'uploaded_by',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'is_public' => 'boolean',
        'version' => 'integer',
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
