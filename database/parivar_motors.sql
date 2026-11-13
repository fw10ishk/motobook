-- Parivar Motors Database Dump
-- Compatible with MySQL 5.7+ / MariaDB 10.x / XAMPP

CREATE DATABASE IF NOT EXISTS `parivar_motors` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `parivar_motors`;

-- --------------------------------------------------------
-- Table structure for `users`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'active',
  `role` varchar(50) NOT NULL DEFAULT 'admin',
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed default admin user (password: admin123)
INSERT INTO `users` (`id`, `email`, `password`, `full_name`, `status`, `role`, `created_at`, `updated_at`) VALUES
(1, 'admin@parivarmotors.com', '$2y$10$wB5Mh7l/U.jC6oN52i1DqOxWqH55L1sP7lWjY74kFz2j4p.e6Bv4O', 'Parivar Motors Admin', 'active', 'admin', NOW(), NOW());

-- --------------------------------------------------------
-- Table structure for `remember_tokens`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `remember_tokens`;
CREATE TABLE `remember_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `is_valid` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `remember_tokens_user_id_foreign` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Lookup Tables
-- --------------------------------------------------------
DROP TABLE IF EXISTS `cities`;
CREATE TABLE `cities` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `cities` (`name`) VALUES
('Ahmedabad'), ('Surat'), ('Vadodara'), ('Rajkot'), ('Bhavnagar'), ('Jamnagar'), ('Gandhinagar');

DROP TABLE IF EXISTS `car_dealers`;
CREATE TABLE `car_dealers` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `car_dealers` (`name`) VALUES
('Parivar Motors Direct'), ('Apex Motors'), ('Landmark Automobiles'), ('Shreeji Motors');

DROP TABLE IF EXISTS `manufacturers`;
CREATE TABLE `manufacturers` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `manufacturers` (`name`) VALUES
('Maruti Suzuki'), ('Hyundai'), ('Tata Motors'), ('Mahindra'), ('Toyota'), ('Honda'), ('Kia');

DROP TABLE IF EXISTS `models`;
CREATE TABLE `models` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `models` (`name`) VALUES
('Swift'), ('Baleno'), ('Brezza'), ('Creta'), ('i20'), ('Nexon'), ('Harrier'), ('Scorpio-N'), ('Thar'), ('Innova Crysta'), ('Fortuner'), ('City'), ('Seltos');

DROP TABLE IF EXISTS `rto`;
CREATE TABLE `rto` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `rto` (`name`) VALUES
('Ahmedabad Subhash Bridge'), ('Ahmedabad Vastral'), ('Surat'), ('Vadodara'), ('Rajkot'), ('Gandhinagar');

DROP TABLE IF EXISTS `rto_codes`;
CREATE TABLE `rto_codes` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `rto_codes` (`name`) VALUES
('GJ-01'), ('GJ-27'), ('GJ-05'), ('GJ-06'), ('GJ-03'), ('GJ-18');

DROP TABLE IF EXISTS `hire_purchase`;
CREATE TABLE `hire_purchase` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `hire_purchase` (`name`) VALUES
('HDFC Bank'), ('ICICI Bank'), ('State Bank of India'), ('Kotak Mahindra Bank'), ('Axis Bank'), ('None / Cash');

DROP TABLE IF EXISTS `insurance_companies`;
CREATE TABLE `insurance_companies` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `insurance_companies` (`name`) VALUES
('ICICI Lombard'), ('Bajaj Allianz'), ('Tata AIG'), ('HDFC ERGO'), ('New India Assurance');

DROP TABLE IF EXISTS `executives`;
CREATE TABLE `executives` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `executives` (`name`) VALUES
('Rahul Sharma'), ('Amit Patel'), ('Priya Shah'), ('Vijay Verma');

DROP TABLE IF EXISTS `executive_branches`;
CREATE TABLE `executive_branches` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `executive_branches` (`name`) VALUES
('Ahmedabad - SG Highway'), ('Ahmedabad - Maninagar'), ('Surat - Ring Road'), ('Vadodara - Alkapuri');

-- --------------------------------------------------------
-- Table structure for `inquiries`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `inquiries`;
CREATE TABLE `inquiries` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `customer_name` varchar(255) DEFAULT NULL,
  `customer_email` varchar(255) DEFAULT NULL,
  `customer_phone` varchar(255) DEFAULT NULL,
  `vehicle_of_interest` varchar(255) DEFAULT NULL,
  `preferred_contact_method` varchar(255) DEFAULT NULL,
  `additional_notes` text DEFAULT NULL,
  `inquiry_source` varchar(255) DEFAULT NULL,
  `referral_person` varchar(255) DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `inquiries` (`customer_name`, `customer_email`, `customer_phone`, `vehicle_of_interest`, `preferred_contact_method`, `additional_notes`, `inquiry_source`, `referral_person`, `status`, `created_at`, `updated_at`) VALUES
('Rajesh Mehta', 'rajesh.mehta@gmail.com', '9876543210', 'Hyundai Creta 2024', 'phone', 'Looking for finance options with 20% down payment', 'Walk-in', NULL, 'pending', NOW(), NOW()),
('Sneha Joshi', 'sneha.joshi@outlook.com', '9825123456', 'Maruti Suzuki Swift VXi', 'email', 'Interested in exchange of old WagonR', 'Website', NULL, 'completed', NOW(), NOW());

-- --------------------------------------------------------
-- Table structure for `new_cars`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `new_cars`;
CREATE TABLE `new_cars` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `docket_number` varchar(255) DEFAULT NULL,
  `entry_type` varchar(255) DEFAULT NULL,
  `booking_date` varchar(255) DEFAULT NULL,
  `delivery_date` varchar(255) DEFAULT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `customer_phone` varchar(255) DEFAULT NULL,
  `customer_address1` text DEFAULT NULL,
  `customer_address2` text DEFAULT NULL,
  `city_name` varchar(255) DEFAULT NULL,
  `pincode` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `customer_dob` varchar(255) DEFAULT NULL,
  `nominee_relationship` varchar(255) DEFAULT NULL,
  `nominee_name` varchar(255) DEFAULT NULL,
  `nominee_dob` varchar(255) DEFAULT NULL,
  `manufacturer_name` varchar(255) DEFAULT NULL,
  `model_name` varchar(255) DEFAULT NULL,
  `color` varchar(255) DEFAULT NULL,
  `registration_no` varchar(255) DEFAULT NULL,
  `chassis_no` varchar(255) DEFAULT NULL,
  `engine_no` varchar(255) DEFAULT NULL,
  `dealer_name` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `hp_name` varchar(255) DEFAULT NULL,
  `manufacture_year` varchar(255) DEFAULT NULL,
  `fuel_type` varchar(255) DEFAULT NULL,
  `executive_branch_name` varchar(255) DEFAULT NULL,
  `executive_name` varchar(255) DEFAULT NULL,
  `insurance_company_name` varchar(255) DEFAULT NULL,
  `rto_name` varchar(255) DEFAULT NULL,
  `rto_code` varchar(255) DEFAULT NULL,
  `parsing_status` varchar(255) DEFAULT NULL,
  `number_plate` varchar(255) DEFAULT NULL,
  `choice_number` varchar(255) DEFAULT NULL,
  `scheme` varchar(255) DEFAULT NULL,
  `broker_name` varchar(255) DEFAULT NULL,
  `other_remarks` text DEFAULT NULL,
  `kyc` varchar(255) DEFAULT NULL,
  `deal_agreement` varchar(255) DEFAULT NULL,
  `main_image` varchar(255) DEFAULT NULL,
  `rto_tax` varchar(255) DEFAULT NULL,
  `insurance` varchar(255) DEFAULT NULL,
  `amc_gmc_tax` varchar(255) DEFAULT NULL,
  `ext_warranty` varchar(255) DEFAULT NULL,
  `accessories` varchar(255) DEFAULT NULL,
  `krunal_accessories` varchar(255) DEFAULT NULL,
  `fast_tag` varchar(255) DEFAULT NULL,
  `tcs` varchar(255) DEFAULT NULL,
  `loyalty_card` varchar(255) DEFAULT NULL,
  `driver_price` varchar(255) DEFAULT NULL,
  `total_on_road` varchar(255) DEFAULT NULL,
  `consumer_offer` varchar(255) DEFAULT NULL,
  `corporate_discount` varchar(255) DEFAULT NULL,
  `other_discount` varchar(255) DEFAULT NULL,
  `net_on_road` varchar(255) DEFAULT NULL,
  `disbursement` varchar(255) DEFAULT NULL,
  `down_payment` varchar(255) DEFAULT NULL,
  `net_short_payment` varchar(255) DEFAULT NULL,
  `ex_showroom_price` varchar(255) DEFAULT NULL,
  `dealer_ex_showroom_price` varchar(255) DEFAULT NULL,
  `dealer_rto_tax` varchar(255) DEFAULT NULL,
  `dealer_insurance` varchar(255) DEFAULT NULL,
  `dealer_amc_gmc_tax` varchar(255) DEFAULT NULL,
  `dealer_ext_warranty` varchar(255) DEFAULT NULL,
  `dealer_accessories` varchar(255) DEFAULT NULL,
  `dealer_krunal_accessories` varchar(255) DEFAULT NULL,
  `dealer_fast_tag` varchar(255) DEFAULT NULL,
  `dealer_tcs` varchar(255) DEFAULT NULL,
  `dealer_loyalty_card` varchar(255) DEFAULT NULL,
  `dealer_driver_price` varchar(255) DEFAULT NULL,
  `dealer_total_on_road` varchar(255) DEFAULT NULL,
  `dealer_consumer_offer` varchar(255) DEFAULT NULL,
  `dealer_corporate_discount` varchar(255) DEFAULT NULL,
  `dealer_other_discount` varchar(255) DEFAULT NULL,
  `dealer_net_on_road` varchar(255) DEFAULT NULL,
  `dealer_disbursement` varchar(255) DEFAULT NULL,
  `dealer_down_payment` varchar(255) DEFAULT NULL,
  `dealer_net_short_payment` varchar(255) DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'available',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `new_car_images`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `new_car_images`;
CREATE TABLE `new_car_images` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `new_car_id` bigint(20) UNSIGNED NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `new_car_images_new_car_id_foreign` (`new_car_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `old_cars`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `old_cars`;
CREATE TABLE `old_cars` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `docket_number` varchar(255) DEFAULT NULL,
  `original_price` varchar(255) DEFAULT NULL,
  `current_price` varchar(255) DEFAULT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `customer_phone` varchar(255) DEFAULT NULL,
  `customer_address1` text DEFAULT NULL,
  `customer_address2` text DEFAULT NULL,
  `city_name` varchar(255) DEFAULT NULL,
  `pincode` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `customer_dob` varchar(255) DEFAULT NULL,
  `manufacturer_name` varchar(255) DEFAULT NULL,
  `model_name` varchar(255) DEFAULT NULL,
  `color` varchar(255) DEFAULT NULL,
  `registration_no` varchar(255) DEFAULT NULL,
  `chassis_no` varchar(255) DEFAULT NULL,
  `engine_no` varchar(255) DEFAULT NULL,
  `insurance_expiry_date` varchar(255) DEFAULT NULL,
  `hp_name` varchar(255) DEFAULT NULL,
  `manufacture_year` varchar(255) DEFAULT NULL,
  `fuel_type` varchar(255) DEFAULT NULL,
  `dealer_name` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `executive_branch_name` varchar(255) DEFAULT NULL,
  `executive_name` varchar(255) DEFAULT NULL,
  `insurance_company_name` varchar(255) DEFAULT NULL,
  `rto_name` varchar(255) DEFAULT NULL,
  `rto_code` varchar(255) DEFAULT NULL,
  `parsing_status` varchar(255) DEFAULT NULL,
  `number_plate` varchar(255) DEFAULT NULL,
  `scheme` varchar(255) DEFAULT NULL,
  `broker_name` varchar(255) DEFAULT NULL,
  `other_remarks` text DEFAULT NULL,
  `rc_book` varchar(255) DEFAULT NULL,
  `insurance_doc` varchar(255) DEFAULT NULL,
  `owner_id_proof` varchar(255) DEFAULT NULL,
  `pan_card` varchar(255) DEFAULT NULL,
  `aadhar_card` varchar(255) DEFAULT NULL,
  `noc` varchar(255) DEFAULT NULL,
  `form_35` varchar(255) DEFAULT NULL,
  `purchase_agreement` varchar(255) DEFAULT NULL,
  `main_image` varchar(255) DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'available',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `old_car_images`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `old_car_images`;
CREATE TABLE `old_car_images` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `old_car_id` bigint(20) UNSIGNED NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `old_car_images_old_car_id_foreign` (`old_car_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `extra_charges`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `extra_charges`;
CREATE TABLE `extra_charges` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `old_car_id` bigint(20) UNSIGNED NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `amount` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `extra_charges_old_car_id_foreign` (`old_car_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `sales`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `sales`;
CREATE TABLE `sales` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `car_type` varchar(50) DEFAULT NULL,
  `car_id` bigint(20) UNSIGNED DEFAULT NULL,
  `sale_type` varchar(50) DEFAULT NULL,
  `total_price` varchar(255) DEFAULT NULL,
  `sale_date` varchar(255) DEFAULT NULL,
  `buyer_name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `old_car_buyers`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `old_car_buyers`;
CREATE TABLE `old_car_buyers` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sale_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `contact` varchar(255) DEFAULT NULL,
  `address_line_1` text DEFAULT NULL,
  `address_line_2` text DEFAULT NULL,
  `city_name` varchar(255) DEFAULT NULL,
  `pincode` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `dob` varchar(255) DEFAULT NULL,
  `pan_card` varchar(255) DEFAULT NULL,
  `aadhar_card` varchar(255) DEFAULT NULL,
  `driving_license` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `old_car_buyers_sale_id_foreign` (`sale_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `full_payment_details`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `full_payment_details`;
CREATE TABLE `full_payment_details` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sale_id` bigint(20) UNSIGNED DEFAULT NULL,
  `total_amount` double NOT NULL DEFAULT 0,
  `amount_paid` double NOT NULL DEFAULT 0,
  `remaining_amount` double NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `full_payment_details_sale_id_foreign` (`sale_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `payment_installments`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `payment_installments`;
CREATE TABLE `payment_installments` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `full_payment_id` bigint(20) UNSIGNED DEFAULT NULL,
  `note` text DEFAULT NULL,
  `amount` double NOT NULL DEFAULT 0,
  `payment_date` datetime DEFAULT NULL,
  `payment_method` varchar(255) DEFAULT NULL,
  `payment_status` varchar(50) NOT NULL DEFAULT 'completed',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payment_installments_full_payment_id_foreign` (`full_payment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `dealer_full_payment_details`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `dealer_full_payment_details`;
CREATE TABLE `dealer_full_payment_details` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sale_id` bigint(20) UNSIGNED DEFAULT NULL,
  `total_amount` double NOT NULL DEFAULT 0,
  `amount_paid` double NOT NULL DEFAULT 0,
  `remaining_amount` double NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dealer_full_payment_details_sale_id_foreign` (`sale_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `dealer_payment_installments`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `dealer_payment_installments`;
CREATE TABLE `dealer_payment_installments` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `dealer_full_payment_id` bigint(20) UNSIGNED DEFAULT NULL,
  `note` text DEFAULT NULL,
  `amount` double NOT NULL DEFAULT 0,
  `payment_date` datetime DEFAULT NULL,
  `payment_method` varchar(255) DEFAULT NULL,
  `payment_status` varchar(50) NOT NULL DEFAULT 'completed',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dealer_payment_installments_id_foreign` (`dealer_full_payment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `loan_details`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `loan_details`;
CREATE TABLE `loan_details` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sale_id` bigint(20) UNSIGNED DEFAULT NULL,
  `loan_amount` double NOT NULL DEFAULT 0,
  `number_of_parts` int(11) NOT NULL DEFAULT 1,
  `installment_amount` double NOT NULL DEFAULT 0,
  `interest_rate` double NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `loan_details_sale_id_foreign` (`sale_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `installments`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `installments`;
CREATE TABLE `installments` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `loan_id` bigint(20) UNSIGNED DEFAULT NULL,
  `installment_number` int(11) NOT NULL DEFAULT 1,
  `due_date` datetime DEFAULT NULL,
  `amount_due` double NOT NULL DEFAULT 0,
  `amount_paid` double DEFAULT NULL,
  `payment_date` datetime DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `installments_loan_id_foreign` (`loan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
