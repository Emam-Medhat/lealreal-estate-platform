<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EncryptedData extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'data_type',
        'original_filename',
        'encrypted_content',
        'file_path',
        'encryption_algorithm',
        'encryption_key_hash',
        'file_size',
        'checksum',
        'access_level',
        'expiry_date',
        'description',
        'tags',
        'metadata',
        'encryption_method',
        'key_derivation_method',
        'iv_vector',
        'salt',
        'iterations',
        'compression_enabled',
        'compression_algorithm',
        'backup_enabled',
        'backup_locations',
        'access_log',
        'decryption_count',
        'last_accessed_at',
        'access_count',
        'security_level',
        'data_classification',
        'retention_period',
        'auto_delete_date',
        'compliance_flags',
        'audit_trail',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'encryption_method' => 'array',
        'key_derivation_method' => 'array',
        'backup_locations' => 'array',
        'access_log' => 'array',
        'metadata' => 'array',
        'tags' => 'array',
        'compliance_flags' => 'array',
        'audit_trail' => 'array',
        'compression_enabled' => 'boolean',
        'backup_enabled' => 'boolean',
        'expiry_date' => 'datetime',
        'last_accessed_at' => 'datetime',
        'auto_delete_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $hidden = [
        'encrypted_content',
        'encryption_key_hash',
        'iv_vector',
        'salt',
        'iterations',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function accessLogs(): HasMany
    {
        return $this->hasMany(DataAccessLog::class, 'encrypted_data_id');
    }

    public function decryptionAttempts(): HasMany
    {
        return $this->hasMany(DecryptionAttempt::class, 'encrypted_data_id');
    }

    // Scopes
    public function scopeByType($query, $type)
    {
        return $query->where('data_type', $type);
    }

    public function scopeByAccessLevel($query, $level)
    {
        return $query->where('access_level', $level);
    }

    public function scopeExpired($query)
    {
        return $query->where('expiry_date', '<', now());
    }

    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at')
            ->where(function ($q) {
                $q->whereNull('expiry_date')
                  ->orWhere('expiry_date', '>', now());
            });
    }

    public function scopeBySecurityLevel($query, $level)
    {
        return $query->where('security_level', $level);
    }

    public function scopeByClassification($query, $classification)
    {
        return $query->where('data_classification', $classification);
    }

    // Methods
    public function encryptData(string $data, string $password, string $algorithm = 'aes-256-gcm'): array
    {
        $encryptionResult = [
            'encrypted_content' => '',
            'algorithm' => $algorithm,
            'key_hash' => '',
            'iv_vector' => '',
            'salt' => '',
            'iterations' => 100000,
            'checksum' => '',
        ];

        switch ($algorithm) {
            case 'aes-256-gcm':
                $encryptionResult = $this->encryptAES256GCM($data, $password);
                break;
            case 'chacha20-poly1305':
                $encryptionResult = $this->encryptChaCha20Poly1305($data, $password);
                break;
            case 'aes-256-cbc':
                $encryptionResult = $this->encryptAES256CBC($data, $password);
                break;
            default:
                throw new \InvalidArgumentException('Unsupported encryption algorithm');
        }

        return $encryptionResult;
    }

    public function decryptData(string $encryptedData, string $password, string $algorithm): string
    {
        switch ($algorithm) {
            case 'aes-256-gcm':
                return $this->decryptAES256GCM($encryptedData, $password);
            case 'chacha20-poly1305':
                return $this->decryptChaCha20Poly1305($encryptedData, $password);
            case 'aes-256-cbc':
                return $this->decryptAES256CBC($encryptedData, $password);
            default:
                throw new \InvalidArgumentException('Unsupported encryption algorithm');
        }
    }

    public function verifyIntegrity(): bool
    {
        if (!$this->encrypted_content || !$this->checksum) {
            return false;
        }

        $calculatedChecksum = hash('sha256', $this->encrypted_content);
        return hash_equals($this->checksum, $calculatedChecksum);
    }

    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function isAccessible(): bool
    {
        return !$this->isExpired() && !$this->isDeleted();
    }

    public function canAccess($userId): bool
    {
        // Check if user has access based on access level and permissions
        if ($this->user_id === $userId) {
            return true;
        }

        // Implement additional access control logic
        return $this->checkAccessPermissions($userId);
    }

    public function logAccess($userId, string $action, array $details = []): void
    {
        $accessLog = $this->access_log ?? [];
        
        $accessLog[] = [
            'user_id' => $userId,
            'action' => $action,
            'timestamp' => now()->toISOString(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'details' => $details,
        ];

        $this->update([
            'access_log' => $accessLog,
            'last_accessed_at' => now(),
            'access_count' => $this->access_count + 1,
        ]);

        // Create separate access log record
        DataAccessLog::create([
            'encrypted_data_id' => $this->id,
            'user_id' => $userId,
            'action' => $action,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'details' => json_encode($details),
            'success' => true,
        ]);
    }

    public function logDecryptionAttempt($userId, bool $success, string $reason = ''): void
    {
        DecryptionAttempt::create([
            'encrypted_data_id' => $this->id,
            'user_id' => $userId,
            'success' => $success,
            'reason' => $reason,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        if (!$success) {
            // Log failed decryption attempt
            \Log::warning('Failed decryption attempt', [
                'encrypted_data_id' => $this->id,
                'user_id' => $userId,
                'reason' => $reason,
            ]);
        }
    }

    public function createBackup(): array
    {
        if (!$this->backup_enabled) {
            throw new \Exception('Backup is not enabled for this data');
        }

        $backupLocations = [];
        $backupId = Str::uuid();

        foreach ($this->backup_locations as $location) {
            $backupPath = $this->generateBackupPath($location, $backupId);
            
            // Store backup to specified location
            $this->storeBackup($backupPath, $location);
            
            $backupLocations[] = [
                'location' => $location,
                'path' => $backupPath,
                'created_at' => now(),
                'size' => $this->file_size,
                'checksum' => $this->checksum,
            ];
        }

        return [
            'backup_id' => $backupId,
            'locations' => $backupLocations,
            'created_at' => now(),
        ];
    }

    public function scheduleAutoDeletion(): void
    {
        if ($this->retention_period) {
            $deletionDate = now()->addDays($this->retention_period);
            $this->update(['auto_delete_date' => $deletionDate]);
        }
    }

    public function checkCompliance(): array
    {
        $complianceIssues = [];
        $complianceScore = 100;

        // Check encryption compliance
        if (!$this->encrypted_content) {
            $complianceIssues[] = 'Data is not encrypted';
            $complianceScore -= 30;
        }

        // Check access control compliance
        if ($this->access_level === 'public' && $this->data_classification === 'confidential') {
            $complianceIssues[] = 'Public access level for confidential data';
            $complianceScore -= 25;
        }

        // Check retention compliance
        if ($this->retention_period && $this->retention_period > 365 * 7) { // 7 years
            $complianceIssues[] = 'Retention period exceeds compliance limits';
            $complianceScore -= 20;
        }

        // Check audit trail compliance
        if (!$this->audit_trail || empty($this->audit_trail)) {
            $complianceIssues[] = 'Missing audit trail';
            $complianceScore -= 15;
        }

        return [
            'compliant' => $complianceScore >= 80,
            'score' => $complianceScore,
            'issues' => $complianceIssues,
            'recommendations' => $this->generateComplianceRecommendations($complianceIssues),
        ];
    }

    public function generateMetadata(): array
    {
        return [
            'file_info' => [
                'original_filename' => $this->original_filename,
                'file_size' => $this->file_size,
                'file_type' => $this->getFileType(),
                'mime_type' => $this->getMimeType(),
            ],
            'encryption_info' => [
                'algorithm' => $this->encryption_algorithm,
                'method' => $this->encryption_method,
                'security_level' => $this->security_level,
                'key_derivation' => $this->key_derivation_method,
            ],
            'access_info' => [
                'access_level' => $this->access_level,
                'access_count' => $this->access_count,
                'last_accessed' => $this->last_accessed_at,
                'expiry_date' => $this->expiry_date,
            ],
            'compliance_info' => [
                'classification' => $this->data_classification,
                'retention_period' => $this->retention_period,
                'auto_delete_date' => $this->auto_delete_date,
                'compliance_score' => $this->checkCompliance()['score'],
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    // Private methods
    private function encryptAES256GCM(string $data, string $password): array
    {
        $key = hash('sha256', $password, true);
        $iv = random_bytes(16);
        $tag = '';
        
        $encrypted = openssl_encrypt($data, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag);
        
        return [
            'encrypted_content' => base64_encode($iv . $tag . $encrypted),
            'key_hash' => hash('sha256', $password),
            'iv_vector' => base64_encode($iv),
            'salt' => '',
            'iterations' => 100000,
            'checksum' => hash('sha256', $data),
        ];
    }

    private function decryptAES256GCM(string $encryptedData, string $password): string
    {
        $key = hash('sha256', $password, true);
        $decoded = base64_decode($encryptedData);
        
        $iv = substr($decoded, 0, 16);
        $tag = substr($decoded, 16, 16);
        $encrypted = substr($decoded, 32);
        
        return openssl_decrypt($encrypted, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag);
    }

    private function encryptChaCha20Poly1305(string $data, string $password): array
    {
        // For demonstration, using AES-256-GCM as fallback
        return $this->encryptAES256GCM($data, $password);
    }

    private function decryptChaCha20Poly1305(string $encryptedData, string $password): string
    {
        // For demonstration, using AES-256-GCM as fallback
        return $this->decryptAES256GCM($encryptedData, $password);
    }

    private function encryptAES256CBC(string $data, string $password): array
    {
        $key = hash('sha256', $password, true);
        $iv = random_bytes(16);
        
        $encrypted = openssl_encrypt($data, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
        
        return [
            'encrypted_content' => base64_encode($iv . $encrypted),
            'key_hash' => hash('sha256', $password),
            'iv_vector' => base64_encode($iv),
            'salt' => '',
            'iterations' => 100000,
            'checksum' => hash('sha256', $data),
        ];
    }

    private function decryptAES256CBC(string $encryptedData, string $password): string
    {
        $key = hash('sha256', $password, true);
        $decoded = base64_decode($encryptedData);
        
        $iv = substr($decoded, 0, 16);
        $encrypted = substr($decoded, 16);
        
        return openssl_decrypt($encrypted, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
    }

    private function checkAccessPermissions($userId): bool
    {
        // Implement access permission checking logic
        // This would typically check against an access control system
        return true; // Placeholder
    }

    private function generateBackupPath(string $location, string $backupId): string
    {
        $filename = "backup_{$backupId}_" . $this->original_filename;
        return "{$location}/{$filename}";
    }

    private function storeBackup(string $backupPath, string $location): void
    {
        // Implement backup storage logic based on location type
        // This could be local storage, cloud storage, etc.
        \Storage::put($backupPath, $this->encrypted_content);
    }

    private function generateComplianceRecommendations(array $issues): array
    {
        $recommendations = [];

        foreach ($issues as $issue) {
            switch ($issue) {
                case 'Data is not encrypted':
                    $recommendations[] = 'Enable encryption for all sensitive data';
                    break;
                case 'Public access level for confidential data':
                    $recommendations[] = 'Change access level to restricted or private';
                    break;
                case 'Retention period exceeds compliance limits':
                    $recommendations[] = 'Reduce retention period to comply with regulations';
                    break;
                case 'Missing audit trail':
                    $recommendations[] = 'Enable audit trail for all data operations';
                    break;
            }
        }

        return $recommendations;
    }

    private function getFileType(): string
    {
        if ($this->original_filename) {
            return pathinfo($this->original_filename, PATHINFO_EXTENSION);
        }
        
        return 'unknown';
    }

    private function getMimeType(): string
    {
        $extension = $this->getFileType();
        
        $mimeTypes = [
            'txt' => 'text/plain',
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'json' => 'application/json',
            'xml' => 'application/xml',
        ];

        return $mimeTypes[$extension] ?? 'application/octet-stream';
    }
}
