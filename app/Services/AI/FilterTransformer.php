<?php

namespace App\Services\AI;

/**
 * Filter Transformer
 *
 * Converts the raw parsed filter array (from SmartSearchService / the AI response)
 * into clean parameters the Eloquent query builder can consume.
 *
 * Input (from AI):
 * [
 *   "property_type" => "شقة",
 *   "city"          => "القاهرة الجديدة",
 *   "bedrooms"      => 3,
 *   "min_price"     => 500000,
 *   "furnished"     => true,
 *   ...
 * ]
 *
 * Output (clean):
 * [
 *   "property_type" => "apartment",
 *   "city"          => "القاهرة الجديدة",
 *   "bedrooms"      => 3,
 *   "min_price"     => 500000.0,
 *   "furnished"     => true,
 *   ...
 * ]
 */
class FilterTransformer
{
    /** Arabic → English property type mapping */
    private const TYPE_MAP = [
        // Apartment
        'شقة' => 'apartment',
        'شقق' => 'apartment',
        'apartment' => 'apartment',
        'apt' => 'apartment',
        'flat' => 'apartment',

        // Villa
        'فيلا' => 'villa',
        'فيلل' => 'villa',
        'villa' => 'villa',

        // Office
        'مكتب' => 'office',
        'office' => 'office',

        // Land / Plot
        'أرض' => 'land',
        'قطعة أرض' => 'land',
        'land' => 'land',
        'plot' => 'land',

        // Shop
        'محل' => 'shop',
        'محلات' => 'shop',
        'shop' => 'shop',
        'retail' => 'shop',

        // Warehouse
        'مخزن' => 'warehouse',
        'مستودع' => 'warehouse',
        'warehouse' => 'warehouse',

        // Duplex
        'دوبلكس' => 'duplex',
        'duplex' => 'duplex',

        // Studio
        'استوديو' => 'studio',
        'studio' => 'studio',

        // Townhouse
        'تاون هاوس' => 'townhouse',
        'townhouse' => 'townhouse',

        // Penthouse
        'بنتهاوس' => 'penthouse',
        'penthouse' => 'penthouse',
    ];

    /** Arabic → English listing type */
    private const LISTING_TYPE_MAP = [
        'للبيع' => 'sale',
        'بيع' => 'sale',
        'sale' => 'sale',
        'buy' => 'sale',
        'للإيجار' => 'rent',
        'إيجار' => 'rent',
        'rent' => 'rent',
        'rental' => 'rent',
    ];

    // ─────────────────────────────────────────────────────────────────────────

    public function transform(array $raw): array
    {
        $clean = [];

        // Property type normalisation
        if (!empty($raw['property_type'])) {
            $clean['property_type'] = $this->normalizePropertyType($raw['property_type']);
        }

        // Location fields – pass through as-is (Arabic is fine for LIKE queries)
        foreach (['city', 'district', 'compound'] as $field) {
            if (!empty($raw[$field])) {
                $clean[$field] = trim($raw[$field]);
            }
        }

        // Numeric fields
        foreach (['bedrooms', 'bathrooms'] as $field) {
            if (isset($raw[$field]) && is_numeric($raw[$field])) {
                $clean[$field] = (int) $raw[$field];
            }
        }

        // Price fields
        foreach (['min_price', 'max_price'] as $field) {
            if (isset($raw[$field]) && is_numeric($raw[$field])) {
                $clean[$field] = (float) $raw[$field];
            }
        }

        // Boolean
        if (isset($raw['furnished'])) {
            $clean['furnished'] = (bool) $raw['furnished'];
        }

        // Array fields
        if (!empty($raw['special_features']) && is_array($raw['special_features'])) {
            $clean['special_features'] = array_filter(
                array_map('strval', $raw['special_features'])
            );
        }

        // Listing type
        if (!empty($raw['listing_type'])) {
            $clean['listing_type'] = $this->normalizeListing($raw['listing_type']);
        }

        return array_filter($clean, fn($v) => $v !== null && $v !== '' && $v !== []);
    }

    // ─────────────────────────────────────────────────────────────────────────

    private function normalizePropertyType(string $type): ?string
    {
        $lower = mb_strtolower(trim($type));
        return self::TYPE_MAP[$lower] ?? $lower;
    }

    private function normalizeListing(string $type): ?string
    {
        $lower = mb_strtolower(trim($type));
        return self::LISTING_TYPE_MAP[$lower] ?? $lower;
    }
}
