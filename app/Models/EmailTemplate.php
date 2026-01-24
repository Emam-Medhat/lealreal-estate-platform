<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'subject',
        'content',
        'variables',
        'is_active',
        'created_by'
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean'
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function processContent(array $data = []): string
    {
        $content = $this->content;
        
        // Default variables
        $defaultVariables = [
            'current_date' => now()->format('Y-m-d'),
            'current_time' => now()->format('H:i:s'),
            'company_name' => config('app.name', 'Real Estate Platform'),
            'support_email' => config('mail.from.address', 'support@example.com')
        ];

        $variables = array_merge($defaultVariables, $data);

        foreach ($variables as $key => $value) {
            $content = str_replace('{' . $key . '}', $value, $content);
        }

        return $content;
    }

    public function processSubject(array $data = []): string
    {
        $subject = $this->subject;
        
        $defaultVariables = [
            'current_date' => now()->format('Y-m-d')
        ];

        $variables = array_merge($defaultVariables, $data);

        foreach ($variables as $key => $value) {
            $subject = str_replace('{' . $key . '}', $value, $subject);
        }

        return $subject;
    }

    public function getVariableList(): string
    {
        if (empty($this->variables)) {
            return 'No variables defined';
        }

        return implode(', ', array_map(function($var) {
            return '{' . $var . '}';
        }, $this->variables));
    }

    public function getPreview(array $data = []): array
    {
        return [
            'subject' => $this->processSubject($data),
            'content' => $this->processContent($data)
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCreator($query, $userId)
    {
        return $query->where('created_by', $userId);
    }
}
