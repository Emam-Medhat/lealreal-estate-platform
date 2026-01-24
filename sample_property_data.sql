-- Create Sample Property Data
-- Property Record
INSERT INTO properties (agent_id, property_code, title, description, property_type, listing_type, price, currency, area, area_unit, bedrooms, bathrooms, floors, year_built, status, featured, premium, address, city, state, country, postal_code, latitude, longitude, views_count, favorites_count, inquiries_count, created_at, updated_at) 
VALUES (
    1, 
    'PROP-001', 
    'Luxury Villa in Riyadh', 
    'Beautiful luxury villa with modern amenities and spacious living areas', 
    'villa', 
    'sale', 
    2500000.00, 
    'SAR', 
    500.00, 
    'sq_m', 
    5, 
    4, 
    2, 
    2020, 
    'active', 
    1, 
    1, 
    'King Abdullah Road', 
    'Riyadh', 
    'Riyadh Province', 
    'Saudi Arabia', 
    '12345', 
    24.7136, 
    46.6753, 
    0, 
    0, 
    0, 
    NOW(), 
    NOW()
);

-- Get the property ID (assuming it's 1, adjust if needed)
SET @property_id = LAST_INSERT_ID();

-- Property Location Record
INSERT INTO property_locations (property_id, address, city, state, country, postal_code, latitude, longitude, neighborhood, district, created_at, updated_at) 
VALUES (
    @property_id, 
    'King Abdullah Road', 
    'Riyadh', 
    'Riyadh Province', 
    'Saudi Arabia', 
    '12345', 
    24.7136, 
    46.6753, 
    'Al-Malaz', 
    'Central', 
    NOW(), 
    NOW()
);

-- Property Price Record
INSERT INTO property_prices (property_id, price, currency, price_type, price_per_sqm, is_negotiable, includes_vat, vat_rate, service_charges, maintenance_fees, effective_date, is_active, created_at, updated_at) 
VALUES (
    @property_id, 
    2500000.00, 
    'SAR', 
    'sale', 
    5000.00, 
    0, 
    1, 
    15.00, 
    5000.00, 
    2000.00, 
    CURDATE(), 
    1, 
    NOW(), 
    NOW()
);

-- Property Details Record
INSERT INTO property_details (property_id, bedrooms, bathrooms, floors, parking_spaces, year_built, area, area_unit, land_area, land_area_unit, specifications, materials, interior_features, exterior_features, created_at, updated_at) 
VALUES (
    @property_id, 
    5, 
    4, 
    2, 
    3, 
    2020, 
    500.00, 
    'sq_m', 
    600.00, 
    'sq_m', 
    '{"construction": "Concrete", "roof_type": "Flat", "foundation": "Reinforced Concrete"}', 
    '{"walls": "Brick", "floors": "Marble", "windows": "Aluminum"}', 
    'Marble floors, central AC, modern kitchen, built-in wardrobes', 
    'Garden, swimming pool, garage, security system', 
    NOW(), 
    NOW()
);

-- Sample Amenities (if amenities table exists)
INSERT IGNORE INTO property_amenities (name, slug, icon, description, category, is_active, sort_order, created_at, updated_at) VALUES
('Swimming Pool', 'swimming-pool', 'fas fa-swimming-pool', 'Private swimming pool', 'recreation', 1, 1, NOW(), NOW()),
('Parking', 'parking', 'fas fa-parking', 'Covered parking spaces', 'facilities', 1, 2, NOW(), NOW()),
('Security', 'security', 'fas fa-shield-alt', '24/7 security system', 'safety', 1, 3, NOW(), NOW()),
('Garden', 'garden', 'fas fa-tree', 'Private garden area', 'outdoor', 1, 4, NOW(), NOW());

-- Attach amenities to property (if pivot table exists)
INSERT IGNORE INTO property_amenity_property (property_id, property_amenity_id, created_at, updated_at)
SELECT @property_id, id, NOW(), NOW() FROM property_amenities WHERE slug IN ('swimming-pool', 'parking', 'security', 'garden');

-- Sample Features (if features table exists)
INSERT IGNORE INTO property_features (name, slug, icon, description, category, is_premium, is_active, sort_order, created_at, updated_at) VALUES
('Luxury', 'luxury', 'fas fa-gem', 'Premium luxury features', 'quality', 1, 1, 1, NOW(), NOW()),
('Modern', 'modern', 'fas fa-home', 'Modern design and architecture', 'style', 0, 1, 2, NOW(), NOW()),
('Spacious', 'spacious', 'fas fa-expand', 'Spacious living areas', 'size', 0, 1, 3, NOW(), NOW());

-- Attach features to property (if pivot table exists)
INSERT IGNORE INTO property_feature_property (property_id, property_feature_id, created_at, updated_at)
SELECT @property_id, id, NOW(), NOW() FROM property_features WHERE slug IN ('luxury', 'modern', 'spacious');
