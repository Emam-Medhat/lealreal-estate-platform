<?php

namespace App\Http\Controllers;

use App\Models\EncryptedData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DataEncryptionController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $encryptionStats = [
            'total_encrypted_files' => EncryptedData::where('user_id', $user->id)->count(),
            'storage_used' => $this->calculateStorageUsed($user->id),
            'encryption_level' => $this->getEncryptionLevel($user->id),
            'recent_encryptions' => EncryptedData::where('user_id', $user->id)
                ->where('created_at', '>=', now()->subDays(7))
                ->count(),
        ];

        $encryptedFiles = EncryptedData::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('security.encryption.index', compact('encryptionStats', 'encryptedFiles'));
    }

    public function create()
    {
        return view('security.encryption.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'data_type' => 'required|in:text,file,json,xml',
            'data_content' => 'required_without:data_file|string',
            'data_file' => 'required_without:data_content|file|max:10240',
            'encryption_algorithm' => 'required|in:aes-256-gcm,chacha20-poly1305,aes-256-cbc',
            'password' => 'required|string|min:12',
            'description' => 'nullable|string|max:500',
            'tags' => 'nullable|array',
            'access_level' => 'required|in:private,restricted,public',
            'expiry_date' => 'nullable|date|after:today',
        ]);

        $data = $this->processDataForEncryption($validated);
        $encryptedContent = $this->encryptData($data['content'], $validated['password'], $validated['encryption_algorithm']);
        
        $filePath = null;
        if ($request->hasFile('data_file')) {
            $filePath = $this->storeEncryptedFile($request->file('data_file'), $encryptedContent);
        }

        $encryptedData = EncryptedData::create([
            'user_id' => Auth::id(),
            'data_type' => $validated['data_type'],
            'original_filename' => $request->hasFile('data_file') ? $request->file('data_file')->getClientOriginalName() : null,
            'encrypted_content' => $encryptedContent,
            'file_path' => $filePath,
            'encryption_algorithm' => $validated['encryption_algorithm'],
            'encryption_key_hash' => hash('sha256', $validated['password']),
            'file_size' => strlen($encryptedContent),
            'description' => $validated['description'],
            'tags' => json_encode($validated['tags'] ?? []),
            'access_level' => $validated['access_level'],
            'expiry_date' => $validated['expiry_date'],
            'checksum' => hash('sha256', $encryptedContent),
        ]);

        return redirect()->route('security.encryption.show', $encryptedData)
            ->with('success', 'تم تشفير البيانات بنجاح');
    }

    public function show(EncryptedData $encryptedData)
    {
        $this->authorize('view', $encryptedData);
        
        return view('security.encryption.show', compact('encryptedData'));
    }

    public function decrypt(Request $request, EncryptedData $encryptedData)
    {
        $this->authorize('decrypt', $encryptedData);

        $validated = $request->validate([
            'password' => 'required|string',
        ]);

        try {
            $decryptedContent = $this->decryptData(
                $encryptedData->encrypted_content,
                $validated['password'],
                $encryptedData->encryption_algorithm
            );

            // Verify checksum
            if (hash('sha256', $decryptedContent) !== $encryptedData->checksum) {
                throw new \Exception('فشل التحقق من سلامة البيانات');
            }

            return view('security.encryption.decrypted', [
                'encryptedData' => $encryptedData,
                'decryptedContent' => $decryptedContent,
            ]);

        } catch (\Exception $e) {
            return back()->with('error', 'فشل فك التشفير: ' . $e->getMessage());
        }
    }

    public function download(EncryptedData $encryptedData)
    {
        $this->authorize('download', $encryptedData);

        if ($encryptedData->file_path) {
            return Storage::download($encryptedData->file_path, $encryptedData->original_filename);
        }

        return response()->streamDownload(function () use ($encryptedData) {
            echo $encryptedData->encrypted_content;
        }, $encryptedData->original_filename ?? 'encrypted_data.txt');
    }

    public function destroy(EncryptedData $encryptedData)
    {
        $this->authorize('delete', $encryptedData);

        // Delete file if exists
        if ($encryptedData->file_path) {
            Storage::delete($encryptedData->file_path);
        }

        $encryptedData->delete();

        return redirect()->route('security.encryption.index')
            ->with('success', 'تم حذف البيانات المشفرة بنجاح');
    }

    public function bulkEncrypt(Request $request)
    {
        $validated = $request->validate([
            'files.*' => 'required|file|max:10240',
            'encryption_algorithm' => 'required|in:aes-256-gcm,chacha20-poly1305,aes-256-cbc',
            'password' => 'required|string|min:12',
            'access_level' => 'required|in:private,restricted,public',
        ]);

        $results = [];
        $errors = [];

        foreach ($request->file('files') as $file) {
            try {
                $content = file_get_contents($file->getPathname());
                $encryptedContent = $this->encryptData($content, $validated['password'], $validated['encryption_algorithm']);
                $filePath = $this->storeEncryptedFile($file, $encryptedContent);

                $encryptedData = EncryptedData::create([
                    'user_id' => Auth::id(),
                    'data_type' => 'file',
                    'original_filename' => $file->getClientOriginalName(),
                    'encrypted_content' => $encryptedContent,
                    'file_path' => $filePath,
                    'encryption_algorithm' => $validated['encryption_algorithm'],
                    'encryption_key_hash' => hash('sha256', $validated['password']),
                    'file_size' => strlen($encryptedContent),
                    'access_level' => $validated['access_level'],
                    'checksum' => hash('sha256', $encryptedContent),
                ]);

                $results[] = [
                    'filename' => $file->getClientOriginalName(),
                    'status' => 'success',
                    'encrypted_data_id' => $encryptedData->id,
                ];

            } catch (\Exception $e) {
                $errors[] = [
                    'filename' => $file->getClientOriginalName(),
                    'error' => $e->getMessage(),
                ];
            }
        }

        return response()->json([
            'results' => $results,
            'errors' => $errors,
            'total_files' => count($request->file('files')),
            'successful' => count($results),
            'failed' => count($errors),
        ]);
    }

    public function generateKey()
    {
        $key = Str::random(32);
        $keyId = Str::uuid();
        
        return response()->json([
            'key' => $key,
            'key_id' => $keyId,
            'created_at' => now(),
        ]);
    }

    public function validateEncryption(Request $request)
    {
        $validated = $request->validate([
            'encrypted_data_id' => 'required|exists:encrypted_data,id',
            'password' => 'required|string',
        ]);

        $encryptedData = EncryptedData::findOrFail($validated['encrypted_data_id']);
        
        try {
            $decryptedContent = $this->decryptData(
                $encryptedData->encrypted_content,
                $validated['password'],
                $encryptedData->encryption_algorithm
            );

            $isValid = hash('sha256', $decryptedContent) === $encryptedData->checksum;

            return response()->json([
                'valid' => $isValid,
                'message' => $isValid ? 'البيانات صالحة' : 'البيانات تالفة',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'valid' => false,
                'message' => 'فشل فك التشفير',
            ]);
        }
    }

    private function processDataForEncryption($validated)
    {
        $content = null;
        
        if (isset($validated['data_content'])) {
            $content = $validated['data_content'];
        } elseif (isset($validated['data_file'])) {
            $content = file_get_contents($validated['data_file']->getPathname());
        }

        return [
            'content' => $content,
            'size' => strlen($content),
        ];
    }

    private function encryptData($data, $password, $algorithm)
    {
        try {
            switch ($algorithm) {
                case 'aes-256-gcm':
                    return $this->encryptAES256GCM($data, $password);
                
                case 'chacha20-poly1305':
                    return $this->encryptChaCha20Poly1305($data, $password);
                
                case 'aes-256-cbc':
                    return $this->encryptAES256CBC($data, $password);
                
                default:
                    throw new \Exception('خوارزمية التشفير غير مدعومة');
            }
        } catch (\Exception $e) {
            throw new \Exception('فشل تشفير البيانات: ' . $e->getMessage());
        }
    }

    private function decryptData($encryptedData, $password, $algorithm)
    {
        try {
            switch ($algorithm) {
                case 'aes-256-gcm':
                    return $this->decryptAES256GCM($encryptedData, $password);
                
                case 'chacha20-poly1305':
                    return $this->decryptChaCha20Poly1305($encryptedData, $password);
                
                case 'aes-256-cbc':
                    return $this->decryptAES256CBC($encryptedData, $password);
                
                default:
                    throw new \Exception('خوارزمية التشفير غير مدعومة');
            }
        } catch (\Exception $e) {
            throw new \Exception('فشل فك تشفير البيانات: ' . $e->getMessage());
        }
    }

    private function encryptAES256GCM($data, $password)
    {
        $key = hash('sha256', $password, true);
        $iv = random_bytes(16);
        $tag = '';
        
        $encrypted = openssl_encrypt($data, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag);
        
        return base64_encode($iv . $tag . $encrypted);
    }

    private function decryptAES256GCM($encryptedData, $password)
    {
        $key = hash('sha256', $password, true);
        $decoded = base64_decode($encryptedData);
        
        $iv = substr($decoded, 0, 16);
        $tag = substr($decoded, 16, 16);
        $encrypted = substr($decoded, 32);
        
        return openssl_decrypt($encrypted, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag);
    }

    private function encryptChaCha20Poly1305($data, $password)
    {
        // For demonstration, using AES-256-GCM as fallback
        return $this->encryptAES256GCM($data, $password);
    }

    private function decryptChaCha20Poly1305($encryptedData, $password)
    {
        // For demonstration, using AES-256-GCM as fallback
        return $this->decryptAES256GCM($encryptedData, $password);
    }

    private function encryptAES256CBC($data, $password)
    {
        $key = hash('sha256', $password, true);
        $iv = random_bytes(16);
        
        $encrypted = openssl_encrypt($data, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
        
        return base64_encode($iv . $encrypted);
    }

    private function decryptAES256CBC($encryptedData, $password)
    {
        $key = hash('sha256', $password, true);
        $decoded = base64_decode($encryptedData);
        
        $iv = substr($decoded, 0, 16);
        $encrypted = substr($decoded, 16);
        
        return openssl_decrypt($encrypted, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
    }

    private function storeEncryptedFile($file, $encryptedContent)
    {
        $filename = 'encrypted_' . Str::uuid() . '.enc';
        $path = 'encrypted_files/' . $filename;
        
        Storage::put($path, $encryptedContent);
        
        return $path;
    }

    private function calculateStorageUsed($userId)
    {
        return EncryptedData::where('user_id', $userId)
            ->sum('file_size');
    }

    private function getEncryptionLevel($userId)
    {
        $files = EncryptedData::where('user_id', $userId)->get();
        
        if ($files->isEmpty()) {
            return 'none';
        }

        $hasStrongEncryption = $files->contains('encryption_algorithm', 'aes-256-gcm');
        $hasMediumEncryption = $files->contains('encryption_algorithm', 'aes-256-cbc');
        
        if ($hasStrongEncryption) {
            return 'high';
        } elseif ($hasMediumEncryption) {
            return 'medium';
        } else {
            return 'low';
        }
    }
}
