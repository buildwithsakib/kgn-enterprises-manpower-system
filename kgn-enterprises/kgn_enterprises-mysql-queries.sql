-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 11, 2025 at 04:24 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kgn_enterprises`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetMonthlyStats` (IN `year_param` INT)   BEGIN
    SELECT 
        MONTH(submitted_at) as month,
        COUNT(*) as total_submissions,
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
        SUM(CASE WHEN status = 'contacted' THEN 1 ELSE 0 END) as contacted,
        SUM(CASE WHEN status = 'resolved' THEN 1 ELSE 0 END) as resolved
    FROM contact_submissions
    WHERE YEAR(submitted_at) = year_param
    GROUP BY MONTH(submitted_at)
    ORDER BY month;
END$$

--
-- Functions
--
CREATE DEFINER=`root`@`localhost` FUNCTION `CalculateAverageRating` () RETURNS DECIMAL(3,2) DETERMINISTIC BEGIN
    DECLARE avg_rating DECIMAL(3,2);
    SELECT AVG(rating) INTO avg_rating FROM testimonials WHERE is_approved = TRUE;
    RETURN IFNULL(avg_rating, 0.00);
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `admin_activity_log`
--

CREATE TABLE `admin_activity_log` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `activity_type` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('super_admin','admin','editor') DEFAULT 'admin',
  `is_active` tinyint(1) DEFAULT 1,
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_login_ip` varchar(45) DEFAULT NULL,
  `login_count` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `password_hash`, `full_name`, `email`, `role`, `is_active`, `last_login`, `created_at`, `updated_at`, `last_login_ip`, `login_count`) VALUES
(1, 'sakib2006', '$2y$10$R1yjDv1zQyOVGcaZAWfNc.8CAGOrT9kd9CqrzRifCJRpUEpdXOMq.', 'Sakib Shaikh', 'sakibbhaisk7@gmail.com', 'super_admin', 1, '2025-12-11 19:41:11', '2025-12-11 09:30:31', '2025-12-11 14:11:11', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `industry` varchar(100) DEFAULT NULL,
  `testimonial` text DEFAULT NULL,
  `rating` decimal(3,2) DEFAULT 0.00,
  `website` varchar(255) DEFAULT NULL,
  `since_date` date DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT 0,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`id`, `name`, `logo`, `industry`, `testimonial`, `rating`, `website`, `since_date`, `is_featured`, `display_order`, `created_at`, `updated_at`) VALUES
(1, 'NIYATI PVT. LTD', NULL, 'Manufacturing', 'Provided excellent manpower support for our project. Professional and reliable service.', 5.00, NULL, NULL, 1, 1, '2025-12-11 09:30:31', '2025-12-11 09:30:31'),
(2, 'Tejas Pawale Industries', NULL, 'Manufacturing', 'Quick deployment of skilled workers. Great replacement policy and professional team.', 4.50, NULL, NULL, 1, 2, '2025-12-11 09:30:31', '2025-12-11 09:30:31'),
(3, 'Rohit Kinker Solutions', NULL, 'IT Services', 'Best manpower service in Pune. They understand client requirements perfectly.', 5.00, NULL, NULL, 1, 3, '2025-12-11 09:30:31', '2025-12-11 09:30:31'),
(4, 'Kute Healthcare Group', NULL, 'Healthcare', 'Professional housekeeping staff that maintain high hygiene standards in our hospitals.', 4.80, NULL, NULL, 1, 4, '2025-12-11 09:30:31', '2025-12-11 09:30:31'),
(5, 'Avej Logistics', '', 'Logistics', 'Reliable workforce for our warehouse operations. Good communication and support.', 4.70, '', NULL, 1, 5, '2025-12-11 09:30:31', '2025-12-11 12:54:08'),
(6, 'Sarthak Manufacturing Co.', NULL, 'Industrial', 'Consistent quality in skilled manpower supply for our production unit.', 4.60, NULL, NULL, 1, 6, '2025-12-11 09:30:31', '2025-12-11 09:30:31'),
(7, 'Tech Solutions India', NULL, 'IT Services', 'Excellent IT professionals with relevant skills and good work ethics.', 4.90, NULL, NULL, 1, 7, '2025-12-11 09:30:31', '2025-12-11 09:30:31'),
(8, 'City Hospital Pune', NULL, 'Healthcare', 'Professional facility management team that understands hospital requirements.', 4.80, NULL, NULL, 1, 8, '2025-12-11 09:30:31', '2025-12-11 09:30:31');

-- --------------------------------------------------------

--
-- Table structure for table `contact_submissions`
--

CREATE TABLE `contact_submissions` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `subject` varchar(200) DEFAULT NULL,
  `message` text NOT NULL,
  `service_type` varchar(100) DEFAULT NULL,
  `source` varchar(100) DEFAULT 'direct',
  `status` enum('pending','contacted','resolved','spam') DEFAULT 'pending',
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_submissions`
--

INSERT INTO `contact_submissions` (`id`, `name`, `email`, `phone`, `subject`, `message`, `service_type`, `source`, `status`, `submitted_at`, `notes`) VALUES
(6, 'sakib shaikh', 'kgnenterprises9670@gmail.com', '9899099878', 'skjdcsdc', 'ksmdcknjcsk', 'facility', 'gallery', 'spam', '2025-12-11 15:16:29', 'yes');

-- --------------------------------------------------------

--
-- Table structure for table `gallery`
--

CREATE TABLE `gallery` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(255) NOT NULL,
  `category` varchar(100) DEFAULT 'general',
  `location` varchar(200) DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gallery`
--

INSERT INTO `gallery` (`id`, `title`, `description`, `image_url`, `category`, `location`, `display_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Team Meeting', 'Our team planning and strategy session for upcoming projects', 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=600&h=400&fit=crop', 'events', '', 1, 1, '2025-12-11 09:30:32', '2025-12-11 12:55:57'),
(2, 'Industrial Workforce', 'Skilled workers at manufacturing plant deployment', 'https://images.unsplash.com/photo-1577962917302-cd874c4e31d2?w=600&h=400&fit=crop', 'workforce', NULL, 2, 1, '2025-12-11 09:30:32', '2025-12-11 09:30:32'),
(3, 'Facility Management', 'Professional facility management team at work', 'https://images.unsplash.com/photo-1560472354-b33ff0c44a43?w=600&h=400&fit=crop', 'facility', NULL, 3, 1, '2025-12-11 09:30:32', '2025-12-11 09:30:32'),
(4, 'Office Administration', 'Our skilled office administration professionals', 'https://images.unsplash.com/photo-1559028012-481c04fa702d?w=600&h=400&fit=crop', 'team', NULL, 4, 1, '2025-12-11 09:30:32', '2025-12-11 09:30:32'),
(5, 'Training Session', 'Skill development training for workforce', 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=600&h=400&fit=crop', 'events', NULL, 5, 1, '2025-12-11 09:30:32', '2025-12-11 09:30:32');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `slug` varchar(200) DEFAULT NULL,
  `description` text NOT NULL,
  `job_type` enum('full_time','part_time','contract','internship') DEFAULT 'full_time',
  `location` varchar(200) NOT NULL,
  `experience` varchar(100) NOT NULL,
  `qualification` varchar(200) NOT NULL,
  `salary_range` varchar(100) NOT NULL,
  `deadline` date DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `posted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`id`, `title`, `slug`, `description`, `job_type`, `location`, `experience`, `qualification`, `salary_range`, `deadline`, `is_active`, `posted_at`, `updated_at`) VALUES
(5, 'Cook- Semiskilled', 'cook-semiskilled', 'Cook for reputed company\'s Guest house in Pune...', 'full_time', 'Pune', '1-3 years', 'High School', '18000-20000', NULL, 1, '2025-12-11 09:48:54', '2025-12-11 09:48:54'),
(6, 'Data Entry Operator', 'data-entry-operator', 'Immediate openings for Data Entry Operators with good typing speed....', 'full_time', 'Pune', '0-1 year', 'Intermediate', '12000-18000', NULL, 1, '2025-12-11 09:48:54', '2025-12-11 09:48:54'),
(7, 'HR Executive', 'hr-executive', 'We are looking for an experienced HR Executive to manage payroll of employees....', 'full_time', 'Pune', '2-4 years', 'Graduate', '20000', NULL, 1, '2025-12-11 09:48:54', '2025-12-11 09:48:54'),
(8, 'Programmer', 'programmer', 'Programmer position available with required skills for development tasks...', 'full_time', 'Pune', '1-3 years', 'Bachelor in Computer Science', '25000', NULL, 1, '2025-12-11 09:48:54', '2025-12-11 09:48:54'),
(9, 'PHP PROGRAMMER', 'php-programmer', 'URGENT REQUIREMENT', 'contract', 'Pune', '0-1 years', 'Any Graduate', '12000-18000', NULL, 1, '2025-12-11 14:23:25', '2025-12-11 14:58:50');

-- --------------------------------------------------------

--
-- Table structure for table `job_applications`
--

CREATE TABLE `job_applications` (
  `id` int(11) NOT NULL,
  `job_id` int(11) DEFAULT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `position` varchar(100) NOT NULL,
  `experience` varchar(50) DEFAULT NULL,
  `qualification` varchar(100) DEFAULT NULL,
  `current_ctc` varchar(50) DEFAULT NULL,
  `expected_ctc` varchar(50) DEFAULT NULL,
  `resume_path` varchar(255) DEFAULT NULL,
  `cover_letter` text DEFAULT NULL,
  `application_status` enum('pending','under_review','shortlisted','rejected','hired') DEFAULT 'pending',
  `applied_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','reviewed','shortlisted','rejected') NOT NULL DEFAULT 'pending',
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `attempt_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `slug` varchar(200) DEFAULT NULL,
  `description` text NOT NULL,
  `features` text DEFAULT NULL,
  `icon` varchar(100) DEFAULT 'fas fa-briefcase',
  `category` varchar(100) DEFAULT 'General',
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `title`, `slug`, `description`, `features`, `icon`, `category`, `display_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Skilled Manpower', 'skilled-manpower', 'Highly trained and experienced professionals for specialized roles including technicians, engineers, supervisors, and skilled workers with technical expertise.', 'Technical Staff & Engineers,IT Professionals,Administrative Staff,Supervisory Roles', 'fas fa-user-graduate', 'Skilled', 1, 1, '2025-12-11 09:30:31', '2025-12-11 09:30:31'),
(2, 'Unskilled Manpower', 'unskilled-manpower', 'Reliable workforce for general labor, production, packaging, and other entry-level positions across various industries with quick deployment.', 'General Laborers,Production Workers,Packaging Staff,Warehouse Workers', 'fas fa-users', 'General', 2, 1, '2025-12-11 09:30:31', '2025-12-11 09:30:31'),
(3, 'Facility Management', 'facility-management', 'Comprehensive facility management services including maintenance, security, and administrative support for smooth operations of your premises.', 'Housekeeping Staff,Security Personnel,Maintenance Crew,Administrative Support', 'fas fa-building', 'Facility', 3, 1, '2025-12-11 09:30:31', '2025-12-11 09:30:31'),
(4, 'IT Professionals', 'it-professionals', 'Qualified IT professionals including developers, network engineers, support staff, and technical specialists for your technology needs and projects.', 'Software Developers,IT Support Staff,Network Engineers,Technical Specialists', 'fas fa-laptop-code', 'IT', 4, 1, '2025-12-11 09:30:31', '2025-12-11 09:30:31'),
(5, 'Housekeeping Services', 'housekeeping-services', 'Professional housekeeping and sanitation staff for maintaining hygiene and cleanliness in offices, hospitals, and industrial premises.', 'Office Cleaning,Hospital Housekeeping,Industrial Cleaning,Sanitation Staff', 'fas fa-broom', 'Cleaning', 5, 1, '2025-12-11 09:30:31', '2025-12-11 09:30:31'),
(6, 'Industrial Workers', 'industrial-workers', 'Skilled and semi-skilled workers for manufacturing, production, warehouse, and logistics operations across various industries.', 'Factory Workers,Production Line,Warehouse Staff,Quality Control', 'fas fa-industry', 'Industrial', 6, 1, '2025-12-11 09:30:31', '2025-12-11 10:51:57');

-- --------------------------------------------------------

--
-- Table structure for table `team_members`
--

CREATE TABLE `team_members` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `designation` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `social_linkedin` varchar(255) DEFAULT NULL,
  `social_instagram` varchar(255) DEFAULT NULL,
  `social_twitter` varchar(255) DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `testimonials`
--

CREATE TABLE `testimonials` (
  `id` int(11) NOT NULL,
  `client_name` varchar(200) NOT NULL,
  `client_designation` varchar(200) DEFAULT NULL,
  `company` varchar(200) DEFAULT NULL,
  `testimonial` text NOT NULL,
  `rating` int(11) DEFAULT 5 CHECK (`rating` between 1 and 5),
  `service_type` varchar(100) DEFAULT NULL,
  `client_image` varchar(255) DEFAULT NULL,
  `is_approved` tinyint(1) DEFAULT 0,
  `featured` tinyint(1) DEFAULT 0,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `testimonials`
--

INSERT INTO `testimonials` (`id`, `client_name`, `client_designation`, `company`, `testimonial`, `rating`, `service_type`, `client_image`, `is_approved`, `featured`, `display_order`, `created_at`, `updated_at`, `email`, `phone`) VALUES
(1, 'OMKAR UGLE', 'Manager', 'NIYATI PVT. LTD', 'Provided excellent manpower support for our project. Professional and reliable service.', 5, 'Skilled Manpower', NULL, 1, 1, 0, '2025-12-11 09:30:31', '2025-12-11 09:30:31', NULL, NULL),
(2, 'TEJAS PAWALE', 'HR Manager', 'Manufacturing Unit', 'Quick deployment of skilled workers. Great replacement policy and professional team.', 5, 'Industrial Workers', NULL, 1, 1, 0, '2025-12-11 09:30:31', '2025-12-11 09:30:31', NULL, NULL),
(3, 'ROHIT KINKER', 'Operations Head', 'IT Company', 'Best manpower service in Pune. They understand client requirements perfectly.', 5, 'IT Professionals', NULL, 1, 1, 0, '2025-12-11 09:30:31', '2025-12-11 09:30:31', NULL, NULL),
(4, 'VIVEK KUTE', 'HR Manager', 'IT Services Pune', 'The IT professionals provided by KGN ENTERPRISES are well-qualified and skilled.', 5, 'IT Staffing', NULL, 1, 1, 0, '2025-12-11 09:30:31', '2025-12-11 09:30:31', NULL, NULL),
(5, 'SARTHAK ARUDE', 'Production Head', 'Pune Manufacturing Unit', 'KGN ENTERPRISES has been our reliable manpower partner for 3 years. Their skilled workforce helped us increase production efficiency by 40%.', 5, 'Industrial Manpower', NULL, 1, 1, 0, '2025-12-11 09:30:31', '2025-12-11 09:30:31', NULL, NULL),
(6, 'AVEJ SHAIKH', 'Facility Manager', 'Hospital Chain', 'The housekeeping and facility management staff provided by KGN are well-trained and professional. They maintain high hygiene standards.', 5, 'Facility Management', NULL, 1, 1, 0, '2025-12-11 09:30:31', '2025-12-11 12:51:00', 'avej9670@gmail.com', '9689436470');

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_dashboard_stats`
-- (See below for the actual view)
--
CREATE TABLE `vw_dashboard_stats` (
`pending_contacts` bigint(21)
,`pending_applications` bigint(21)
,`pending_testimonials` bigint(21)
,`total_clients` bigint(21)
,`active_jobs` bigint(21)
,`active_services` bigint(21)
);

-- --------------------------------------------------------

--
-- Table structure for table `website_settings`
--

CREATE TABLE `website_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_type` enum('text','textarea','image','boolean','number') DEFAULT 'text',
  `category` varchar(100) DEFAULT 'general',
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `website_settings`
--

INSERT INTO `website_settings` (`id`, `setting_key`, `setting_value`, `setting_type`, `category`, `display_order`, `created_at`, `updated_at`) VALUES
(1, 'company_name', 'KGN ENTERPRISES', 'text', 'general', 0, '2025-12-11 09:30:32', '2025-12-11 09:30:32'),
(2, 'company_email', 'kgnenterprises9670@gmail.com', 'text', 'contact', 0, '2025-12-11 09:30:32', '2025-12-11 09:30:32'),
(3, 'contact_phone1', '+91 9881901568', 'text', 'contact', 0, '2025-12-11 09:30:32', '2025-12-11 09:30:32'),
(4, 'contact_phone2', '+91 9423042591', 'text', 'contact', 0, '2025-12-11 09:30:32', '2025-12-11 09:30:32'),
(5, 'company_address', '185 Ground, Javed Turuk, Shendewadi Phata, Kadus, Khed, Pune - 412404, India', 'textarea', 'contact', 0, '2025-12-11 09:30:32', '2025-12-11 09:30:32'),
(6, 'about_us', 'skdjfdsmfdmfmd', 'textarea', 'about', 0, '2025-12-11 09:30:32', '2025-12-11 13:00:20'),
(7, 'linkedin_url', 'https://www.linkedin.com/in/sakib-shaikh-b5755b397', 'text', 'social', 0, '2025-12-11 09:30:32', '2025-12-11 09:30:32'),
(8, 'instagram_url', 'https://www.instagram.com/sakib__.2006', 'text', 'social', 0, '2025-12-11 09:30:32', '2025-12-11 09:30:32'),
(10, 'whatsapp_number', '+919881901568', 'text', 'contact', 0, '2025-12-11 09:30:32', '2025-12-11 09:30:32'),
(11, 'google_maps_embed', '<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3780.349493835528!2d73.85694967497264!3d18.651545382480434!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bc2c9c9b8c5a5a5%3A0x8b8c5a5a5a5a5a5!2sPune%2C%20Maharashtra!5e0!3m2!1sen!2sin!4v1647851234567!5m2!1sen!2sin\" width=\"100%\" height=\"100%\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>', 'textarea', 'contact', 0, '2025-12-11 09:30:32', '2025-12-11 09:30:32'),
(12, 'meta_description', 'KGN ENTERPRISES provides premium manpower services, job placement, and contract staffing with 5+ years experience. We supply skilled and unskilled workforce, facility management, and industrial labour across Pune and Maharashtra.', 'textarea', 'seo', 0, '2025-12-11 09:30:32', '2025-12-11 09:30:32'),
(13, 'meta_keywords', 'manpower services Pune, job placement, labour supply, contract staffing, workforce provider, recruitment agency, manpower consultancy, facility management services, staffing services in Pune, industrial labour supply', 'textarea', 'seo', 0, '2025-12-11 09:30:32', '2025-12-11 09:30:32'),
(14, 'logo_path', 'uploads/settings/logo_path_1765458325.png', 'image', 'general', 0, '2025-12-11 09:30:32', '2025-12-11 13:05:25'),
(15, 'favicon_path', 'uploads/settings/favicon_path_1765458325.png', 'image', 'general', 0, '2025-12-11 09:30:32', '2025-12-11 13:05:25'),
(16, 'github_url', 'https://github.com/sakib92s', 'text', 'social', 0, '2025-12-11 13:03:07', '2025-12-11 13:03:07');

-- --------------------------------------------------------

--
-- Structure for view `vw_dashboard_stats`
--
DROP TABLE IF EXISTS `vw_dashboard_stats`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_dashboard_stats`  AS SELECT (select count(0) from `contact_submissions` where `contact_submissions`.`status` = 'pending') AS `pending_contacts`, (select count(0) from `job_applications` where `job_applications`.`status` = 'pending') AS `pending_applications`, (select count(0) from `testimonials` where `testimonials`.`is_approved` = 0) AS `pending_testimonials`, (select count(0) from `clients`) AS `total_clients`, (select count(0) from `jobs` where `jobs`.`is_active` = 1) AS `active_jobs`, (select count(0) from `services` where `services`.`is_active` = 1) AS `active_services` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_activity_log`
--
ALTER TABLE `admin_activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_admin_activity` (`admin_id`,`created_at`);

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_featured_order` (`is_featured`,`display_order`);

--
-- Indexes for table `contact_submissions`
--
ALTER TABLE `contact_submissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status_date` (`status`,`submitted_at`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_contact_submissions_source` (`source`);

--
-- Indexes for table `gallery`
--
ALTER TABLE `gallery`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_category_active` (`category`,`is_active`,`display_order`),
  ADD KEY `idx_gallery_created_at` (`created_at`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_active_deadline` (`is_active`,`deadline`),
  ADD KEY `idx_jobs_posted_at` (`posted_at`);

--
-- Indexes for table `job_applications`
--
ALTER TABLE `job_applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `job_id` (`job_id`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_status` (`application_status`),
  ADD KEY `idx_applied_at` (`applied_at`);

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ip_time` (`ip_address`,`attempt_time`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_active_order` (`is_active`,`display_order`);

--
-- Indexes for table `team_members`
--
ALTER TABLE `team_members`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_active_order` (`is_active`,`display_order`);

--
-- Indexes for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_approved_featured` (`is_approved`,`featured`,`display_order`),
  ADD KEY `idx_testimonials_created_at` (`created_at`);

--
-- Indexes for table `website_settings`
--
ALTER TABLE `website_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`),
  ADD KEY `idx_category_key` (`category`,`setting_key`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_activity_log`
--
ALTER TABLE `admin_activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `contact_submissions`
--
ALTER TABLE `contact_submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `gallery`
--
ALTER TABLE `gallery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `job_applications`
--
ALTER TABLE `job_applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `team_members`
--
ALTER TABLE `team_members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `testimonials`
--
ALTER TABLE `testimonials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `website_settings`
--
ALTER TABLE `website_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_activity_log`
--
ALTER TABLE `admin_activity_log`
  ADD CONSTRAINT `admin_activity_log_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admin_users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `job_applications`
--
ALTER TABLE `job_applications`
  ADD CONSTRAINT `job_applications_ibfk_1` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
