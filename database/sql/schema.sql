-- =============================================================
-- Citizen Forms Platform - Complete SQL Schema
-- MySQL 8+
-- =============================================================

SET FOREIGN_KEY_CHECKS = 0;

-- Users table (extended from Laravel default)
CREATE TABLE IF NOT EXISTS `users` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `email_verified_at` TIMESTAMP NULL DEFAULT NULL,
    `password` VARCHAR(255) NOT NULL,
    `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    `user_type` ENUM('admin', 'citizen') NOT NULL DEFAULT 'citizen',
    `remember_token` VARCHAR(100) NULL DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Roles
CREATE TABLE IF NOT EXISTS `roles` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL UNIQUE,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `description` TEXT NULL DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Permissions
CREATE TABLE IF NOT EXISTS `permissions` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL UNIQUE,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `description` TEXT NULL DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Groups
CREATE TABLE IF NOT EXISTS `groups` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT NULL DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pivot: role_permission
CREATE TABLE IF NOT EXISTS `role_permission` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `role_id` BIGINT UNSIGNED NOT NULL,
    `permission_id` BIGINT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `role_permission_unique` (`role_id`, `permission_id`),
    CONSTRAINT `fk_rp_role` FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_rp_permission` FOREIGN KEY (`permission_id`) REFERENCES `permissions`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pivot: user_role
CREATE TABLE IF NOT EXISTS `user_role` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `role_id` BIGINT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `user_role_unique` (`user_id`, `role_id`),
    CONSTRAINT `fk_ur_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_ur_role` FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pivot: group_user
CREATE TABLE IF NOT EXISTS `group_user` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `group_id` BIGINT UNSIGNED NOT NULL,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `group_user_unique` (`group_id`, `user_id`),
    CONSTRAINT `fk_gu_group` FOREIGN KEY (`group_id`) REFERENCES `groups`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_gu_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pivot: group_permission
CREATE TABLE IF NOT EXISTS `group_permission` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `group_id` BIGINT UNSIGNED NOT NULL,
    `permission_id` BIGINT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `group_permission_unique` (`group_id`, `permission_id`),
    CONSTRAINT `fk_gp_group` FOREIGN KEY (`group_id`) REFERENCES `groups`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_gp_permission` FOREIGN KEY (`permission_id`) REFERENCES `permissions`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- External Forms
CREATE TABLE IF NOT EXISTS `external_forms` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT NULL DEFAULT NULL,
    `group_id` BIGINT UNSIGNED NULL DEFAULT NULL,
    `is_public` TINYINT(1) NOT NULL DEFAULT 0,
    `iframe_code` TEXT NOT NULL,
    `start_date` DATETIME NULL DEFAULT NULL,
    `end_date` DATETIME NULL DEFAULT NULL,
    `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    `created_by` BIGINT UNSIGNED NULL DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_ef_group` FOREIGN KEY (`group_id`) REFERENCES `groups`(`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_ef_creator` FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Internal Forms
CREATE TABLE IF NOT EXISTS `internal_forms` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT NULL DEFAULT NULL,
    `group_id` BIGINT UNSIGNED NULL DEFAULT NULL,
    `is_public` TINYINT(1) NOT NULL DEFAULT 0,
    `structure` JSON NOT NULL,
    `start_date` DATETIME NULL DEFAULT NULL,
    `end_date` DATETIME NULL DEFAULT NULL,
    `status` ENUM('active', 'inactive', 'draft') NOT NULL DEFAULT 'draft',
    `allow_anonymous` TINYINT(1) NOT NULL DEFAULT 0,
    `created_by` BIGINT UNSIGNED NULL DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_if_group` FOREIGN KEY (`group_id`) REFERENCES `groups`(`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_if_creator` FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Internal Form Fields
CREATE TABLE IF NOT EXISTS `internal_form_fields` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `form_id` BIGINT UNSIGNED NOT NULL,
    `type` VARCHAR(255) NOT NULL,
    `label` VARCHAR(255) NOT NULL,
    `placeholder` TEXT NULL DEFAULT NULL,
    `required` TINYINT(1) NOT NULL DEFAULT 0,
    `options` JSON NULL DEFAULT NULL,
    `order` INT NOT NULL DEFAULT 0,
    `validation_rules` JSON NULL DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_iff_form` FOREIGN KEY (`form_id`) REFERENCES `internal_forms`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Internal Form Responses
CREATE TABLE IF NOT EXISTS `internal_form_responses` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `form_id` BIGINT UNSIGNED NOT NULL,
    `user_id` BIGINT UNSIGNED NULL DEFAULT NULL,
    `ip_address` VARCHAR(45) NULL DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_ifr_form` FOREIGN KEY (`form_id`) REFERENCES `internal_forms`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_ifr_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Internal Form Response Values
CREATE TABLE IF NOT EXISTS `internal_form_response_values` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `response_id` BIGINT UNSIGNED NOT NULL,
    `field_id` BIGINT UNSIGNED NOT NULL,
    `value` TEXT NULL DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_ifrv_response` FOREIGN KEY (`response_id`) REFERENCES `internal_form_responses`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_ifrv_field` FOREIGN KEY (`field_id`) REFERENCES `internal_form_fields`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Form Access Logs
CREATE TABLE IF NOT EXISTS `form_access_logs` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `form_type` VARCHAR(255) NOT NULL,
    `form_id` BIGINT UNSIGNED NOT NULL,
    `user_id` BIGINT UNSIGNED NULL DEFAULT NULL,
    `ip_address` VARCHAR(45) NULL DEFAULT NULL,
    `action` VARCHAR(255) NOT NULL DEFAULT 'view',
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    INDEX `idx_form_access` (`form_type`, `form_id`),
    CONSTRAINT `fk_fal_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- =============================================================
-- Default Data
-- =============================================================

-- Permissions
INSERT INTO `permissions` (`name`, `slug`, `description`, `created_at`, `updated_at`) VALUES
('Voir formulaire', 'view_form', 'Permet de voir les formulaires', NOW(), NOW()),
('Répondre formulaire', 'respond_form', 'Permet de répondre aux formulaires', NOW(), NOW()),
('Créer formulaire', 'create_form', 'Permet de créer des formulaires', NOW(), NOW()),
('Modifier formulaire', 'edit_form', 'Permet de modifier les formulaires', NOW(), NOW()),
('Supprimer formulaire', 'delete_form', 'Permet de supprimer les formulaires', NOW(), NOW()),
('Exporter réponses', 'export_responses', 'Permet d\'exporter les réponses', NOW(), NOW()),
('Gérer utilisateurs', 'manage_users', 'Permet de gérer les utilisateurs', NOW(), NOW()),
('Gérer rôles', 'manage_roles', 'Permet de gérer les rôles', NOW(), NOW()),
('Gérer groupes', 'manage_groups', 'Permet de gérer les groupes', NOW(), NOW());

-- Roles
INSERT INTO `roles` (`name`, `slug`, `description`, `created_at`, `updated_at`) VALUES
('Administrateur', 'admin', 'Accès complet à toutes les fonctionnalités', NOW(), NOW()),
('Modérateur', 'moderator', 'Peut gérer les formulaires et voir les réponses', NOW(), NOW()),
('Citoyen', 'citizen', 'Peut voir et répondre aux formulaires', NOW(), NOW());

-- Admin role gets all permissions
INSERT INTO `role_permission` (`role_id`, `permission_id`, `created_at`, `updated_at`)
SELECT r.id, p.id, NOW(), NOW()
FROM `roles` r, `permissions` p
WHERE r.slug = 'admin';

-- Moderator permissions
INSERT INTO `role_permission` (`role_id`, `permission_id`, `created_at`, `updated_at`)
SELECT r.id, p.id, NOW(), NOW()
FROM `roles` r, `permissions` p
WHERE r.slug = 'moderator' AND p.slug IN ('view_form', 'respond_form', 'create_form', 'edit_form', 'export_responses');

-- Citizen permissions
INSERT INTO `role_permission` (`role_id`, `permission_id`, `created_at`, `updated_at`)
SELECT r.id, p.id, NOW(), NOW()
FROM `roles` r, `permissions` p
WHERE r.slug = 'citizen' AND p.slug IN ('view_form', 'respond_form');

-- Default admin user (password: password)
INSERT INTO `users` (`name`, `email`, `email_verified_at`, `password`, `status`, `user_type`, `created_at`, `updated_at`) VALUES
('Admin', 'admin@citizenforms.local', NOW(), '$2y$12$LK.oRDN9RXLbJGjO/kzWceCsg0MQH2lXWOTM2GGiHf50LCdBffAAS', 'active', 'admin', NOW(), NOW());

-- Assign admin role to admin user
INSERT INTO `user_role` (`user_id`, `role_id`, `created_at`, `updated_at`)
SELECT u.id, r.id, NOW(), NOW()
FROM `users` u, `roles` r
WHERE u.email = 'admin@citizenforms.local' AND r.slug = 'admin';
