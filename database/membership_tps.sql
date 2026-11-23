-- Puihaha E-learning database export for MySQL 8+ / compatible MariaDB.
-- Import this file into MySQL, then set the matching DB_* values in .env.

CREATE DATABASE IF NOT EXISTS membership_tps
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE membership_tps;

SET NAMES utf8mb4;
SET time_zone = '+00:00';

CREATE TABLE IF NOT EXISTS users (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    email_verified_at TIMESTAMP NULL DEFAULT NULL,
    password VARCHAR(255) NOT NULL,
    remember_token VARCHAR(100) NULL DEFAULT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY users_email_unique (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS membership_categories (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY membership_categories_name_unique (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS membership_levels (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    fee DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    validity_months SMALLINT UNSIGNED NOT NULL DEFAULT 12,
    benefits TEXT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY membership_levels_name_unique (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS members (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    membership_number VARCHAR(255) NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(30) NULL,
    membership_category_id BIGINT UNSIGNED NOT NULL,
    membership_level_id BIGINT UNSIGNED NOT NULL,
    joined_on DATE NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    deleted_at TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY members_membership_number_unique (membership_number),
    UNIQUE KEY members_email_unique (email),
    KEY members_membership_category_id_foreign (membership_category_id),
    KEY members_membership_level_id_foreign (membership_level_id),
    CONSTRAINT members_membership_category_id_foreign FOREIGN KEY (membership_category_id) REFERENCES membership_categories (id) ON DELETE RESTRICT,
    CONSTRAINT members_membership_level_id_foreign FOREIGN KEY (membership_level_id) REFERENCES membership_levels (id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS membership_transactions (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    member_id BIGINT UNSIGNED NOT NULL,
    membership_level_id BIGINT UNSIGNED NOT NULL,
    reference_number VARCHAR(255) NOT NULL,
    type ENUM('application', 'renewal') NOT NULL,
    status ENUM('pending', 'paid', 'approved', 'expired', 'cancelled') NOT NULL DEFAULT 'pending',
    amount_due DECIMAL(10,2) NOT NULL,
    starts_on DATE NULL,
    expires_on DATE NULL,
    created_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY membership_transactions_reference_number_unique (reference_number),
    KEY membership_transactions_member_id_created_at_index (member_id, created_at),
    KEY membership_transactions_membership_level_id_foreign (membership_level_id),
    KEY membership_transactions_created_by_foreign (created_by),
    CONSTRAINT membership_transactions_member_id_foreign FOREIGN KEY (member_id) REFERENCES members (id) ON DELETE RESTRICT,
    CONSTRAINT membership_transactions_membership_level_id_foreign FOREIGN KEY (membership_level_id) REFERENCES membership_levels (id) ON DELETE RESTRICT,
    CONSTRAINT membership_transactions_created_by_foreign FOREIGN KEY (created_by) REFERENCES users (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS payments (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    membership_transaction_id BIGINT UNSIGNED NOT NULL,
    payment_reference VARCHAR(255) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    method VARCHAR(40) NOT NULL,
    paid_at DATETIME NOT NULL,
    recorded_by BIGINT UNSIGNED NULL,
    notes TEXT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY payments_payment_reference_unique (payment_reference),
    KEY payments_membership_transaction_id_foreign (membership_transaction_id),
    KEY payments_recorded_by_foreign (recorded_by),
    CONSTRAINT payments_membership_transaction_id_foreign FOREIGN KEY (membership_transaction_id) REFERENCES membership_transactions (id) ON DELETE RESTRICT,
    CONSTRAINT payments_recorded_by_foreign FOREIGN KEY (recorded_by) REFERENCES users (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS migrations (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    migration VARCHAR(255) NOT NULL,
    batch INT NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Demo password is "password". Change it before using this system beyond local study.
INSERT IGNORE INTO users (name, email, password, created_at, updated_at)
VALUES ('System Administrator', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.', NOW(), NOW());

INSERT IGNORE INTO membership_categories (name, description, is_active, created_at, updated_at) VALUES
('Student', 'Student membership category', 1, NOW(), NOW()),
('Regular', 'Regular membership category', 1, NOW(), NOW()),
('Senior Citizen', 'Senior Citizen membership category', 1, NOW(), NOW()),
('Corporate', 'Corporate membership category', 1, NOW(), NOW());

INSERT IGNORE INTO membership_levels (name, fee, validity_months, benefits, is_active, created_at, updated_at) VALUES
('Basic', 500.00, 12, 'Standard membership access', 1, NOW(), NOW()),
('Silver', 1000.00, 12, 'Standard access and member discounts', 1, NOW(), NOW()),
('Gold', 1800.00, 12, 'Priority access and expanded discounts', 1, NOW(), NOW()),
('Platinum', 3000.00, 12, 'All standard benefits and premium support', 1, NOW(), NOW());

-- Mark these Laravel migrations as already applied so `php artisan migrate --seed` remains safe.
INSERT INTO migrations (migration, batch)
SELECT required_migrations.migration, 1
FROM (
    SELECT '0001_01_01_000000_create_users_table' AS migration UNION ALL
    SELECT '2026_11_23_000001_create_membership_categories_table' UNION ALL
    SELECT '2026_11_23_000002_create_membership_levels_table' UNION ALL
    SELECT '2026_11_23_000003_create_members_table' UNION ALL
    SELECT '2026_11_23_000004_create_membership_transactions_table' UNION ALL
    SELECT '2026_11_23_000005_create_payments_table'
) AS required_migrations
WHERE NOT EXISTS (
    SELECT 1 FROM migrations existing_migration
    WHERE existing_migration.migration = required_migrations.migration
);
