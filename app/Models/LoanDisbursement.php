<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoanDisbursement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'loan_id',
        'disbursement_number',
        'amount',
        'method',
        'reference',
        'recipient_name',
        'recipient_account',
        'recipient_bank',
        'recipient_address',
        'disbursement_date',
        'scheduled_date',
        'status',
        'transaction_hash',
        'confirmation_number',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'amount' => 'decimal:15,2',
        'disbursement_date' => 'datetime',
        'scheduled_date' => 'datetime',
        'loan_id' => 'integer',
        'disbursement_number' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByLoan($query, $loanId)
    {
        return $query->where('loan_id', $loanId);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    // Helper methods
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isScheduled(): bool
    {
        return $this->status === 'scheduled';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function getAmountFormattedAttribute(): string
    {
        return number_format($this->amount, 2);
    }

    public function getStatusDisplayAttribute(): string
    {
        $statuses = [
            'pending' => '🟡 Pending',
            'scheduled' => '🔵 Scheduled',
            'processing' => '🟠 Processing',
            'completed' => '🟢 Completed',
            'failed' => '🔴 Failed',
            'cancelled' => '⚫ Cancelled',
        ];

        return $statuses[$this->status] ?? '❓ Unknown';
    }

    public function getMethodDisplayAttribute(): string
    {
        $methods = [
            'bank_transfer' => 'Bank Transfer',
            'wire_transfer' => 'Wire Transfer',
            'ach' => 'ACH Transfer',
            'check' => 'Check',
            'cash' => 'Cash',
            'crypto' => 'Cryptocurrency',
            'direct_deposit' => 'Direct Deposit',
        ];

        return $methods[$this->method] ?? $this->method;
    }

    public function getDaysUntilDisbursementAttribute(): int
    {
        if (!$this->scheduled_date) return 0;
        return now()->diffInDays($this->scheduled_date, false);
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->isPending() && $this->getDaysUntilDisbursementAttribute() < 0;
    }

    public function getIsDueTodayAttribute(): bool
    {
        return $this->scheduled_date && $this->scheduled_date->isToday();
    }

    public function getIsUpcomingAttribute(): bool
    {
        $days = $this->getDaysUntilDisbursementAttribute();
        return $this->isPending() && $days >= 0 && $days <= 7;
    }

    public function getDisbursementWindowAttribute(): string
    {
        $days = $this->getDaysUntilDisbursementAttribute();
        
        if ($days < 0) {
            return abs($days) . ' days overdue';
        } elseif ($days === 0) {
            return 'Due today';
        } elseif ($days === 1) {
            return 'Due tomorrow';
        } elseif ($days <= 7) {
            return $days . ' days';
        } else {
            return $days . ' days';
        }
    }

    public function getPriorityLevelAttribute(): string
    {
        $days = $this->getDaysUntilDisbursementAttribute();
        
        if ($days < 0) {
            return 'Overdue';
        } elseif ($days <= 1) {
            return 'Urgent';
        } elseif ($days <= 3) {
            return 'High';
        } elseif ($days <= 7) {
            return 'Medium';
        } else {
            return 'Low';
        }
    }

    public function getPriorityColorAttribute(): string
    {
        $colors = [
            'Overdue' => 'text-red-600',
            'Urgent' => 'text-orange-600',
            'High' => 'text-yellow-600',
            'Medium' => 'text-blue-600',
            'Low' => 'text-gray-600',
        ];

        return $colors[$this->getPriorityLevelAttribute()] ?? 'text-gray-600';
    }

    public function getProcessingTimeAttribute(): ?string
    {
        if (!$this->disbursement_date || !$this->scheduled_date) return null;
        
        $processingTime = $this->scheduled_date->diffInHours($this->disbursement_date);
        
        if ($processingTime < 1) {
            return '< 1 hour';
        } elseif ($processingTime < 24) {
            return $processingTime . ' hours';
        } else {
            return round($processingTime / 24, 1) . ' days';
        }
    }

    public function getRecipientInfoAttribute(): array
    {
        return [
            'name' => $this->recipient_name,
            'account' => $this->recipient_account,
            'bank' => $this->recipient_bank,
            'address' => $this->recipient_address,
        ];
    }

    public function getRecipientInfoFormattedAttribute(): string
    {
        $info = [];
        
        if ($this->recipient_name) {
            $info[] = $this->recipient_name;
        }
        
        if ($this->recipient_bank) {
            $info[] = $this->recipient_bank;
        }
        
        if ($this->recipient_account) {
            $info[] = '****' . substr($this->recipient_account, -4);
        }
        
        return implode(' - ', $info);
    }

    public function getTransactionUrlAttribute(): ?string
    {
        if (empty($this->transaction_hash)) return null;
        
        $explorers = [
            'ethereum' => 'https://etherscan.io/tx/',
            'polygon' => 'https://polygonscan.com/tx/',
            'binance' => 'https://bscscan.com/tx/',
            'bitcoin' => 'https://blockstream.info/tx/',
        ];

        $blockchain = 'ethereum'; // Default, should be stored with disbursement
        
        return ($explorers[$blockchain] ?? '') . $this->transaction_hash;
    }

    public function canBeProcessed(): bool
    {
        return $this->isPending() && $this->scheduled_date <= now();
    }

    public function canBeScheduled(): bool
    {
        return $this->isPending() && $this->scheduled_date > now();
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'scheduled']);
    }

    public function canBeRetried(): bool
    {
        return $this->isFailed();
    }

    public function getDisbursementSummaryAttribute(): array
    {
        return [
            'amount' => $this->amount,
            'method' => $this->method,
            'status' => $this->status,
            'scheduled_date' => $this->scheduled_date,
            'disbursement_date' => $this->disbursement_date,
            'recipient' => $this->getRecipientInfoFormattedAttribute(),
            'reference' => $this->reference,
            'confirmation' => $this->confirmation_number,
            'priority' => $this->getPriorityLevelAttribute(),
            'window' => $this->getDisbursementWindowAttribute(),
        ];
    }

    public function getDisbursementTimelineAttribute(): array
    {
        $timeline = [];
        
        if ($this->scheduled_date) {
            $timeline[] = [
                'event' => 'Scheduled',
                'date' => $this->scheduled_date,
                'description' => 'Disbursement scheduled',
                'icon' => '📅',
            ];
        }
        
        if ($this->disbursement_date) {
            $timeline[] = [
                'event' => 'Disbursed',
                'date' => $this->disbursement_date,
                'description' => 'Funds disbursed successfully',
                'icon' => '✅',
            ];
        }
        
        if ($this->confirmation_number) {
            $timeline[] = [
                'event' => 'Confirmed',
                'date' => $this->disbursement_date,
                'description' => 'Transaction confirmed: ' . $this->confirmation_number,
                'icon' => '🔗',
            ];
        }
        
        return $timeline;
    }

    public function getRiskLevelAttribute(): string
    {
        $riskFactors = 0;
        
        // High amount increases risk
        if ($this->amount > 100000) $riskFactors++;
        
        // New recipient increases risk
        if ($this->recipient_account && strlen($this->recipient_account) < 10) $riskFactors++;
        
        // International transfer increases risk
        if ($this->recipient_address && strpos($this->recipient_address, 'USA') === false) $riskFactors++;
        
        // Crypto transfers have different risk profile
        if ($this->method === 'crypto') $riskFactors++;
        
        if ($riskFactors >= 3) return 'High';
        if ($riskFactors >= 2) return 'Medium';
        return 'Low';
    }

    public function getRiskColorAttribute(): string
    {
        $colors = [
            'High' => 'text-red-600',
            'Medium' => 'text-yellow-600',
            'Low' => 'text-green-600',
        ];

        return $colors[$this->getRiskLevelAttribute()] ?? 'text-gray-600';
    }

    public function getRequiresVerificationAttribute(): bool
    {
        return $this->getRiskLevelAttribute() === 'High' || $this->amount > 50000;
    }

    public function getVerificationStatusAttribute(): string
    {
        if (!$this->getRequiresVerificationAttribute()) {
            return 'Not Required';
        }
        
        return $this->isCompleted() ? 'Verified' : 'Pending Verification';
    }

    public function getComplianceChecksAttribute(): array
    {
        $checks = [];
        
        // AML check
        $checks[] = [
            'name' => 'AML Screening',
            'status' => $this->amount > 10000 ? 'Required' : 'Passed',
            'description' => 'Anti-Money Laundering screening',
        ];
        
        // KYC check
        $checks[] = [
            'name' => 'KYC Verification',
            'status' => 'Required',
            'description' => 'Know Your Customer verification',
        ];
        
        // Sanctions check
        $checks[] = [
            'name' => 'Sanctions Check',
            'status' => 'Required',
            'description' => 'International sanctions screening',
        ];
        
        return $checks;
    }
}
