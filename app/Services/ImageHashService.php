<?php

namespace App\Services;

use App\Models\PropertyMedia;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * ImageHashService
 *
 * Implements perceptual hashing (dHash & pHash) for duplicate image detection.
 * Works WITHOUT external PHP extensions by using pure GD library.
 *
 * Algorithms:
 *  - dHash (Difference Hash): Fast, simple. Detects visually similar images.
 *  - pHash (Perceptual Hash): More robust against transformations (flip, crop).
 *
 * @see https://www.hackerfactor.com/blog/index.php?/archives/529-Kind-of-Like-That.html
 */
class ImageHashService
{
    private int $hashSize;
    private string $algorithm;
    private int $similarityThreshold;
    private int $maxHammingDistance;

    public function __construct()
    {
        $config = config('fraud_detection.image_hash');
        $this->hashSize = $config['hash_size'] ?? 8;
        $this->algorithm = $config['algorithm'] ?? 'dhash';
        $this->similarityThreshold = $config['similarity_threshold'] ?? 90;
        $this->maxHammingDistance = $config['max_hamming_distance'] ?? 6;
    }

    /**
     * Generate a perceptual hash string for a given image path.
     *
     * @param string $imagePath  Absolute path to the image file
     * @return string|null       64-character hex string or null on failure
     */
    public function generateHash(string $imagePath): ?string
    {
        try {
            if (!file_exists($imagePath)) {
                Log::warning('ImageHashService: File not found', ['path' => $imagePath]);
                return null;
            }

            $image = $this->loadImage($imagePath);
            if ($image === null) {
                return null;
            }

            $hash = match ($this->algorithm) {
                'phash' => $this->computePHash($image),
                default => $this->computeDHash($image),
            };

            imagedestroy($image);
            return $hash;
        } catch (\Throwable $e) {
            Log::error('ImageHashService: Hash generation failed', [
                'path' => $imagePath,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Generate hash for a file stored in Laravel's Storage.
     */
    public function generateHashFromStoragePath(string $storagePath, string $disk = 'public'): ?string
    {
        $absolutePath = Storage::disk($disk)->path($storagePath);
        return $this->generateHash($absolutePath);
    }

    /**
     * Calculate the Hamming distance between two binary hash strings.
     * Lower distance = more similar images.
     *
     * @param string $hash1  Hex hash string
     * @param string $hash2  Hex hash string
     * @return int           Hamming distance (0 = identical)
     */
    public function hammingDistance(string $hash1, string $hash2): int
    {
        if (strlen($hash1) !== strlen($hash2)) {
            return PHP_INT_MAX; // Incomparable hashes
        }

        // Convert hex strings to binary and count differing bits
        $bin1 = $this->hexToBinary($hash1);
        $bin2 = $this->hexToBinary($hash2);
        $distance = 0;

        for ($i = 0; $i < strlen($bin1) && $i < strlen($bin2); $i++) {
            if ($bin1[$i] !== $bin2[$i]) {
                $distance++;
            }
        }

        return $distance;
    }

    /**
     * Calculate similarity percentage between two hashes.
     *
     * @param string $hash1
     * @param string $hash2
     * @return float  0.0 (completely different) to 100.0 (identical)
     */
    public function calculateSimilarity(string $hash1, string $hash2): float
    {
        $totalBits = strlen($this->hexToBinary($hash1));
        if ($totalBits === 0) {
            return 0.0;
        }

        $distance = $this->hammingDistance($hash1, $hash2);
        $similarity = (1 - ($distance / $totalBits)) * 100;

        return round($similarity, 2);
    }

    /**
     * Check if two images are considered duplicates based on configured threshold.
     */
    public function areDuplicates(string $hash1, string $hash2): bool
    {
        $similarity = $this->calculateSimilarity($hash1, $hash2);
        return $similarity >= $this->similarityThreshold;
    }

    /**
     * Find all existing PropertyMedia records that are duplicates of a given hash.
     *
     * @param string $newHash         Hash of the new image to check
     * @param int|null $excludeMediaId Optional media ID to exclude (self)
     * @return \Illuminate\Support\Collection<PropertyMedia>
     */
    public function findDuplicates(string $newHash, ?int $excludeMediaId = null): \Illuminate\Support\Collection
    {
        // Fetch all existing image hashes from the database
        $query = PropertyMedia::whereNotNull('image_hash')
            ->where('media_type', 'image');

        if ($excludeMediaId) {
            $query->where('id', '!=', $excludeMediaId);
        }

        $candidates = $query->get(['id', 'property_id', 'image_hash', 'file_path']);

        $duplicates = collect();
        foreach ($candidates as $candidate) {
            $similarity = $this->calculateSimilarity($newHash, $candidate->image_hash);
            if ($similarity >= $this->similarityThreshold) {
                $candidate->_similarity = $similarity;
                $duplicates->push($candidate);
            }
        }

        return $duplicates->sortByDesc('_similarity');
    }

    /**
     * Store the hash for a PropertyMedia record.
     */
    public function storeHashForMedia(PropertyMedia $media): bool
    {
        $path = $this->resolveMediaPath($media);
        if (!$path) {
            return false;
        }

        $hash = $this->generateHash($path);
        if (!$hash) {
            return false;
        }

        $media->update(['image_hash' => $hash]);
        return true;
    }

    // -------------------------------------------------------------------------
    // Private: Hash computation algorithms
    // -------------------------------------------------------------------------

    /**
     * Compute dHash (Difference Hash).
     *
     * 1. Resize image to (hashSize+1) x hashSize
     * 2. For each row, compare adjacent pixels: bit=1 if left > right
     * 3. Return binary array as hex
     */
    private function computeDHash(\GdImage $image): string
    {
        $w = $this->hashSize + 1;
        $h = $this->hashSize;

        // Resize to small grayscale
        $small = imagecreatetruecolor($w, $h);
        imagecopyresampled($small, $image, 0, 0, 0, 0, $w, $h, imagesx($image), imagesy($image));

        // Convert to grayscale
        imagefilter($small, IMG_FILTER_GRAYSCALE);

        $bits = '';
        for ($y = 0; $y < $h; $y++) {
            for ($x = 0; $x < $this->hashSize; $x++) {
                $left = $this->getPixelBrightness($small, $x, $y);
                $right = $this->getPixelBrightness($small, $x + 1, $y);
                $bits .= $left > $right ? '1' : '0';
            }
        }

        imagedestroy($small);
        return $this->binaryToHex($bits);
    }

    /**
     * Compute pHash (Perceptual Hash) using simplified DCT.
     *
     * 1. Resize to 32x32
     * 2. Compute DCT
     * 3. Take top-left 8x8 of DCT
     * 4. Threshold against median
     */
    private function computePHash(\GdImage $image): string
    {
        $size = 32;
        $targetSize = $this->hashSize;

        // Resize to 32x32 grayscale
        $small = imagecreatetruecolor($size, $size);
        imagecopyresampled($small, $image, 0, 0, 0, 0, $size, $size, imagesx($image), imagesy($image));
        imagefilter($small, IMG_FILTER_GRAYSCALE);

        // Extract pixel values
        $pixels = [];
        for ($y = 0; $y < $size; $y++) {
            for ($x = 0; $x < $size; $x++) {
                $pixels[$y][$x] = $this->getPixelBrightness($small, $x, $y);
            }
        }
        imagedestroy($small);

        // Apply DCT
        $dct = $this->applyDct2D($pixels, $size);

        // Take top-left 8x8 low-frequency components
        $lowFreq = [];
        for ($y = 0; $y < $targetSize; $y++) {
            for ($x = 0; $x < $targetSize; $x++) {
                $lowFreq[] = $dct[$y][$x];
            }
        }

        // Calculate median
        $sorted = $lowFreq;
        sort($sorted);
        $median = $sorted[count($sorted) / 2];

        // Generate hash bits
        $bits = '';
        foreach ($lowFreq as $val) {
            $bits .= $val >= $median ? '1' : '0';
        }

        return $this->binaryToHex($bits);
    }

    /**
     * Simplified 2D DCT computation (NxN input, NxN output).
     */
    private function applyDct2D(array $pixels, int $size): array
    {
        $dct = [];

        for ($u = 0; $u < $size; $u++) {
            for ($v = 0; $v < $size; $v++) {
                $sum = 0.0;

                for ($x = 0; $x < $size; $x++) {
                    for ($y = 0; $y < $size; $y++) {
                        $sum += $pixels[$y][$x]
                            * cos((2 * $x + 1) * $u * M_PI / (2 * $size))
                            * cos((2 * $y + 1) * $v * M_PI / (2 * $size));
                    }
                }

                $cu = ($u === 0) ? 1 / sqrt(2) : 1.0;
                $cv = ($v === 0) ? 1 / sqrt(2) : 1.0;
                $dct[$u][$v] = (2 / $size) * $cu * $cv * $sum;
            }
        }

        return $dct;
    }

    /**
     * Get the grayscale brightness (0-255) of a pixel.
     */
    private function getPixelBrightness(\GdImage $image, int $x, int $y): int
    {
        $rgb = imagecolorat($image, $x, $y);
        $r = ($rgb >> 16) & 0xFF;
        $g = ($rgb >> 8) & 0xFF;
        $b = $rgb & 0xFF;

        // Luminosity formula
        return (int) round(0.299 * $r + 0.587 * $g + 0.114 * $b);
    }

    /**
     * Load an image resource from a file path (supports JPEG, PNG, WebP, GIF).
     */
    private function loadImage(string $path): ?\GdImage
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $image = match ($ext) {
            'jpg', 'jpeg' => @imagecreatefromjpeg($path),
            'png' => @imagecreatefrompng($path),
            'gif' => @imagecreatefromgif($path),
            'webp' => @imagecreatefromwebp($path),
            default => $this->tryLoadAnyFormat($path),
        };

        return ($image instanceof \GdImage) ? $image : null;
    }

    /**
     * Try to load unknown format using imagecreatefromstring.
     */
    private function tryLoadAnyFormat(string $path): ?\GdImage
    {
        $data = @file_get_contents($path);
        if ($data === false) {
            return null;
        }
        $img = @imagecreatefromstring($data);
        return ($img instanceof \GdImage) ? $img : null;
    }

    /**
     * Convert binary string to hex string.
     */
    private function binaryToHex(string $bits): string
    {
        $hex = '';
        foreach (str_split($bits, 4) as $nibble) {
            $hex .= dechex(bindec($nibble));
        }
        return $hex;
    }

    /**
     * Convert hex string to binary string.
     */
    private function hexToBinary(string $hex): string
    {
        $bin = '';
        foreach (str_split($hex) as $char) {
            $bin .= str_pad(decbin(hexdec($char)), 4, '0', STR_PAD_LEFT);
        }
        return $bin;
    }

    /**
     * Resolve the absolute file system path for a PropertyMedia record.
     */
    private function resolveMediaPath(PropertyMedia $media): ?string
    {
        if (!$media->file_path) {
            return null;
        }

        // Try storage path
        $storagePath = storage_path('app/public/' . ltrim($media->file_path, '/'));
        if (file_exists($storagePath)) {
            return $storagePath;
        }

        // Try public path
        $publicPath = public_path('storage/' . ltrim($media->file_path, '/'));
        if (file_exists($publicPath)) {
            return $publicPath;
        }

        return null;
    }
}
