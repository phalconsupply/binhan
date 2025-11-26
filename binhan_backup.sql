-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th10 24, 2025 lúc 07:49 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `binhan_db`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `activity_log`
--

CREATE TABLE `activity_log` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `log_name` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `subject_type` varchar(255) DEFAULT NULL,
  `event` varchar(255) DEFAULT NULL,
  `subject_id` bigint(20) UNSIGNED DEFAULT NULL,
  `causer_type` varchar(255) DEFAULT NULL,
  `causer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `properties` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`properties`)),
  `batch_uuid` char(36) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `activity_log`
--

INSERT INTO `activity_log` (`id`, `log_name`, `description`, `subject_type`, `event`, `subject_id`, `causer_type`, `causer_id`, `properties`, `batch_uuid`, `created_at`, `updated_at`) VALUES
(1, 'default', 'created', 'App\\Models\\Vehicle', 'created', 1, 'App\\Models\\User', 1, '{\"attributes\":{\"license_plate\":\"51B50614\",\"model\":\"Toyota\",\"driver_name\":\"\\u0110oan\",\"phone\":null,\"status\":\"active\"}}', NULL, '2025-11-21 11:11:29', '2025-11-21 11:11:29'),
(2, 'default', 'created', 'App\\Models\\Vehicle', 'created', 2, 'App\\Models\\User', 1, '{\"attributes\":{\"license_plate\":\"51B51291\",\"model\":\"Toyota\",\"driver_name\":null,\"phone\":null,\"status\":\"active\"}}', NULL, '2025-11-21 11:11:45', '2025-11-21 11:11:45'),
(3, 'default', 'created', 'App\\Models\\Patient', 'created', 1, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"nguyen van a\",\"birth_year\":1999,\"phone\":\"0987654321\",\"gender\":\"male\",\"address\":null}}', NULL, '2025-11-21 11:14:39', '2025-11-21 11:14:39'),
(4, 'default', 'created', 'App\\Models\\Incident', 'created', 1, 'App\\Models\\User', 1, '{\"attributes\":{\"vehicle_id\":1,\"patient_id\":1,\"date\":\"2025-11-21T18:13:00.000000Z\",\"dispatch_by\":1,\"destination\":\"ch\\u1ee3 r\\u1eaby\",\"summary\":null,\"tags\":null}}', NULL, '2025-11-21 11:14:39', '2025-11-21 11:14:39'),
(5, 'default', 'created', 'App\\Models\\Transaction', 'created', 1, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":1,\"vehicle_id\":1,\"type\":\"thu\",\"amount\":\"4500000.00\",\"method\":\"cash\",\"note\":\"Thu t\\u1eeb chuy\\u1ebfn \\u0111i\",\"date\":\"2025-11-21T18:13:00.000000Z\"}}', NULL, '2025-11-21 11:14:39', '2025-11-21 11:14:39'),
(6, 'default', 'created', 'App\\Models\\Transaction', 'created', 2, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":1,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"500000.00\",\"method\":\"cash\",\"note\":\"Chi ph\\u00ed chuy\\u1ebfn \\u0111i\",\"date\":\"2025-11-21T18:13:00.000000Z\"}}', NULL, '2025-11-21 11:14:39', '2025-11-21 11:14:39'),
(7, 'default', 'created', 'App\\Models\\Location', 'created', 1, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"BV Ch\\u1ee3 r\\u1eaby\",\"type\":\"both\",\"address\":\"HCM\",\"note\":null,\"is_active\":true}}', NULL, '2025-11-21 12:02:39', '2025-11-21 12:02:39'),
(8, 'default', 'created', 'App\\Models\\Location', 'created', 2, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"BV nhi \\u0111\\u1ed3ng\",\"type\":\"both\",\"address\":\"HCM\",\"note\":null,\"is_active\":true}}', NULL, '2025-11-21 12:02:51', '2025-11-21 12:02:51'),
(9, 'default', 'created', 'App\\Models\\Location', 'created', 3, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"BV Ph\\u1ea1m Ng\\u1ecdc Th\\u1ea1ch\",\"type\":\"both\",\"address\":null,\"note\":null,\"is_active\":true}}', NULL, '2025-11-21 12:03:02', '2025-11-21 12:03:02'),
(10, 'default', 'created', 'App\\Models\\Location', 'created', 4, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"BV 115\",\"type\":\"both\",\"address\":\"HCM\",\"note\":null,\"is_active\":true}}', NULL, '2025-11-21 12:03:16', '2025-11-21 12:03:16'),
(11, 'default', 'updated', 'App\\Models\\Location', 'updated', 3, 'App\\Models\\User', 1, '{\"attributes\":{\"address\":\"HCM\"},\"old\":{\"address\":null}}', NULL, '2025-11-21 12:03:22', '2025-11-21 12:03:22'),
(12, 'default', 'created', 'App\\Models\\Location', 'created', 5, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"BV Vi\\u1ec7t \\u0110\\u1ee9c HN\",\"type\":\"both\",\"address\":\"HN\",\"note\":null,\"is_active\":true}}', NULL, '2025-11-21 12:03:38', '2025-11-21 12:03:38'),
(13, 'default', 'created', 'App\\Models\\Location', 'created', 6, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"BV \\u0110a khoa L\\u00e2m \\u0110\\u1ed3ng\",\"type\":\"both\",\"address\":\"L\\u00e2m \\u0110\\u1ed3ng\",\"note\":null,\"is_active\":true}}', NULL, '2025-11-21 12:03:51', '2025-11-21 12:03:51'),
(14, 'default', 'created', 'App\\Models\\Location', 'created', 7, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"BV Nam S\\u00e0i G\\u00f2n\",\"type\":\"both\",\"address\":\"HCM\",\"note\":null,\"is_active\":true}}', NULL, '2025-11-21 12:04:07', '2025-11-21 12:04:07'),
(15, 'default', 'created', 'App\\Models\\Location', 'created', 8, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"BV S\\u00e0i G\\u00f2n ITO\",\"type\":\"both\",\"address\":\"Nguy\\u1ec5n Tr\\u1ecdng Tuy\\u1ec3n, Ph\\u00fa Nhu\\u1eadn, HCM\",\"note\":null,\"is_active\":true}}', NULL, '2025-11-21 12:04:33', '2025-11-21 12:04:33'),
(16, 'default', 'created', 'App\\Models\\Location', 'created', 9, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"TTYT \\u0110\\u1ee9c Tr\\u1ecdng\",\"type\":\"both\",\"address\":\"\\u0110\\u1ee9c Tr\\u1ecdng\",\"note\":null,\"is_active\":true}}', NULL, '2025-11-21 12:04:54', '2025-11-21 12:04:54'),
(17, 'default', 'created', 'App\\Models\\AdditionalService', 'created', 1, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"M\\u00e1y th\\u1edf\",\"description\":\"thu\\u00ea m\\u00e1y th\\u1edf theo ng\\u00e0y\",\"default_price\":\"2500000.00\",\"is_active\":true}}', NULL, '2025-11-21 12:05:22', '2025-11-21 12:05:22'),
(18, 'default', 'created', 'App\\Models\\AdditionalService', 'created', 2, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"V\\u1eadt t\\u01b0 y t\\u1ebf\",\"description\":\"g\\u0103ng tay, kh\\u1ea9u trang, c\\u1ed3n g\\u1ea1t\",\"default_price\":\"200000.00\",\"is_active\":true}}', NULL, '2025-11-21 12:05:49', '2025-11-21 12:05:49'),
(19, 'default', 'created', 'App\\Models\\AdditionalService', 'created', 3, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"B\\u00e1c s\\u0129\",\"description\":\"Thu\\u00ea b\\u00e1c s\\u0129 \\u0111i k\\u00e8m (ng\\u00e0y)\",\"default_price\":\"3000000.00\",\"is_active\":true}}', NULL, '2025-11-21 12:06:11', '2025-11-21 12:06:11'),
(20, 'default', 'updated', 'App\\Models\\AdditionalService', 'updated', 3, 'App\\Models\\User', 1, '{\"attributes\":{\"default_price\":\"3500000.00\"},\"old\":{\"default_price\":\"3000000.00\"}}', NULL, '2025-11-21 12:06:24', '2025-11-21 12:06:24'),
(21, 'default', 'created', 'App\\Models\\AdditionalService', 'created', 4, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"Ch\\u1edd t\\u00e1i kh\\u00e1m\",\"description\":\"m\\u1ed7i 1 ti\\u1ebfng ph\\u00e1t sinh sau 2 ti\\u1ebfng \\u0111\\u1ea7u\",\"default_price\":\"150000.00\",\"is_active\":true}}', NULL, '2025-11-21 12:06:59', '2025-11-21 12:06:59'),
(22, 'default', 'created', 'App\\Models\\Partner', 'created', 1, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"Khoa n\\u1ed9i A - BV \\u0110a khoa\",\"type\":\"collaborator\",\"phone\":null,\"email\":null,\"address\":null,\"commission_rate\":null,\"note\":null,\"is_active\":true}}', NULL, '2025-11-21 12:10:15', '2025-11-21 12:10:15'),
(23, 'default', 'created', 'App\\Models\\Partner', 'created', 2, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"Gara ST-28\",\"type\":\"maintenance\",\"phone\":null,\"email\":null,\"address\":null,\"commission_rate\":null,\"note\":null,\"is_active\":true}}', NULL, '2025-11-21 12:10:38', '2025-11-21 12:10:38'),
(24, 'default', 'created', 'App\\Models\\MaintenanceService', 'created', 1, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"Thay nh\\u1edbt\",\"description\":\"\\u0111\\u1ecbnh k\\u1ef3 m\\u1ed7i 5000km\",\"is_active\":true}}', NULL, '2025-11-21 12:11:06', '2025-11-21 12:11:06'),
(25, 'default', 'created', 'App\\Models\\MaintenanceService', 'created', 2, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"Thay v\\u1ecf\",\"description\":null,\"is_active\":true}}', NULL, '2025-11-21 12:11:15', '2025-11-21 12:11:15'),
(26, 'default', 'created', 'App\\Models\\MaintenanceService', 'created', 3, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"R\\u1eeda xe\",\"description\":null,\"is_active\":true}}', NULL, '2025-11-21 12:11:24', '2025-11-21 12:11:24'),
(27, 'default', 'created', 'App\\Models\\VehicleMaintenance', 'created', 1, 'App\\Models\\User', 1, '{\"attributes\":{\"vehicle_id\":1,\"maintenance_service_id\":3,\"partner_id\":2,\"incident_id\":null,\"date\":\"2025-11-21T00:00:00.000000Z\",\"cost\":\"120000.00\",\"mileage\":null,\"description\":\"r\\u1eeda xe\",\"note\":null}}', NULL, '2025-11-21 12:12:00', '2025-11-21 12:12:00'),
(28, 'default', 'created', 'App\\Models\\Patient', 'created', 2, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"nguyen van b\",\"birth_year\":1995,\"phone\":\"0909090909\",\"gender\":\"male\",\"address\":null}}', NULL, '2025-11-21 12:41:12', '2025-11-21 12:41:12'),
(29, 'default', 'created', 'App\\Models\\Incident', 'created', 2, 'App\\Models\\User', 1, '{\"attributes\":{\"vehicle_id\":2,\"patient_id\":2,\"date\":\"2025-11-21T19:39:00.000000Z\",\"dispatch_by\":1,\"destination\":null,\"from_location_id\":6,\"to_location_id\":1,\"partner_id\":1,\"commission_amount\":\"500000.00\",\"summary\":null,\"tags\":null}}', NULL, '2025-11-21 12:41:12', '2025-11-21 12:41:12'),
(30, 'default', 'created', 'App\\Models\\Transaction', 'created', 3, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":2,\"vehicle_id\":2,\"type\":\"thu\",\"amount\":\"4500000.00\",\"method\":\"cash\",\"note\":\"Thu chuy\\u1ebfn \\u0111i\",\"date\":\"2025-11-21T19:39:00.000000Z\"}}', NULL, '2025-11-21 12:41:12', '2025-11-21 12:41:12'),
(31, 'default', 'created', 'App\\Models\\Transaction', 'created', 4, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":2,\"vehicle_id\":2,\"type\":\"thu\",\"amount\":\"2500000.00\",\"method\":\"cash\",\"note\":\"Thu d\\u1ecbch v\\u1ee5: m\\u00e1y th\\u1edf\",\"date\":\"2025-11-21T19:39:00.000000Z\"}}', NULL, '2025-11-21 12:41:12', '2025-11-21 12:41:12'),
(32, 'default', 'created', 'App\\Models\\Transaction', 'created', 5, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":2,\"vehicle_id\":2,\"type\":\"thu\",\"amount\":\"200000.00\",\"method\":\"cash\",\"note\":\"Thu d\\u1ecbch v\\u1ee5: V\\u1eadt t\\u01b0 y t\\u1ebf\",\"date\":\"2025-11-21T19:39:00.000000Z\"}}', NULL, '2025-11-21 12:41:12', '2025-11-21 12:41:12'),
(33, 'default', 'created', 'App\\Models\\Transaction', 'created', 6, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":2,\"vehicle_id\":2,\"type\":\"chi\",\"amount\":\"500000.00\",\"method\":\"cash\",\"note\":\"c\\u00f4ng t\\u00e0i\",\"date\":\"2025-11-21T19:39:00.000000Z\"}}', NULL, '2025-11-21 12:41:12', '2025-11-21 12:41:12'),
(34, 'default', 'created', 'App\\Models\\Transaction', 'created', 7, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":2,\"vehicle_id\":2,\"type\":\"chi\",\"amount\":\"600000.00\",\"method\":\"cash\",\"note\":\"Chi ph\\u00ed: c\\u00f4ng \\u0111i\\u1ec1u d\\u01b0\\u1ee1ng\",\"date\":\"2025-11-21T19:39:00.000000Z\"}}', NULL, '2025-11-21 12:41:12', '2025-11-21 12:41:12'),
(35, 'default', 'created', 'App\\Models\\Transaction', 'created', 8, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":2,\"vehicle_id\":2,\"type\":\"chi\",\"amount\":\"980000.00\",\"method\":\"cash\",\"note\":\"Chi ph\\u00ed: d\\u1ea7u\",\"date\":\"2025-11-21T19:39:00.000000Z\"}}', NULL, '2025-11-21 12:41:12', '2025-11-21 12:41:12'),
(36, 'default', 'created', 'App\\Models\\VehicleMaintenance', 'created', 2, 'App\\Models\\User', 1, '{\"attributes\":{\"vehicle_id\":2,\"maintenance_service_id\":3,\"partner_id\":2,\"incident_id\":2,\"date\":\"2025-11-21T00:00:00.000000Z\",\"cost\":\"120000.00\",\"mileage\":null,\"description\":null,\"note\":\"B\\u1ea3o tr\\u00ec ph\\u00e1t sinh trong chuy\\u1ebfn \\u0111i\"}}', NULL, '2025-11-21 12:41:12', '2025-11-21 12:41:12'),
(37, 'default', 'created', 'App\\Models\\Transaction', 'created', 9, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":2,\"vehicle_id\":2,\"type\":\"chi\",\"amount\":\"120000.00\",\"method\":\"cash\",\"note\":\"B\\u1ea3o tr\\u00ec: R\\u1eeda xe\",\"date\":\"2025-11-21T19:39:00.000000Z\"}}', NULL, '2025-11-21 12:41:12', '2025-11-21 12:41:12'),
(38, 'default', 'updated', 'App\\Models\\Transaction', 'updated', 10, NULL, NULL, '{\"attributes\":{\"note\":\"Hoa h\\u1ed3ng: Khoa n\\u1ed9i A - BV \\u0110a khoa\"},\"old\":{\"note\":\"Hoa h?ng: Khoa n?i A - BV \\u00d0a khoa\"}}', NULL, '2025-11-21 13:14:51', '2025-11-21 13:14:51'),
(39, 'default', 'created', 'App\\Models\\Transaction', 'created', 11, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":2,\"vehicle_id\":2,\"type\":\"chi\",\"amount\":\"550000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng: \",\"date\":\"2025-11-21T19:39:00.000000Z\"}}', NULL, '2025-11-22 00:04:54', '2025-11-22 00:04:54'),
(40, 'default', 'created', 'App\\Models\\Transaction', 'created', 12, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":2,\"vehicle_id\":2,\"type\":\"chi\",\"amount\":\"600000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng: \",\"date\":\"2025-11-21T19:39:00.000000Z\"}}', NULL, '2025-11-22 00:04:54', '2025-11-22 00:04:54'),
(41, 'default', 'created', 'App\\Models\\Transaction', 'created', 13, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":2,\"vehicle_id\":2,\"type\":\"chi\",\"amount\":\"500000.00\",\"method\":\"cash\",\"note\":\"Hoa h\\u1ed3ng: Khoa n\\u1ed9i A - BV \\u0110a khoa\",\"date\":\"2025-11-21T19:39:00.000000Z\"}}', NULL, '2025-11-22 00:04:54', '2025-11-22 00:04:54'),
(42, 'default', 'deleted', 'App\\Models\\Transaction', 'deleted', 3, 'App\\Models\\User', 1, '{\"old\":{\"incident_id\":2,\"vehicle_id\":2,\"type\":\"thu\",\"amount\":\"4500000.00\",\"method\":\"cash\",\"note\":\"Thu chuy\\u1ebfn \\u0111i\",\"date\":\"2025-11-21T19:39:00.000000Z\"}}', NULL, '2025-11-22 00:06:37', '2025-11-22 00:06:37'),
(43, 'default', 'deleted', 'App\\Models\\Transaction', 'deleted', 4, 'App\\Models\\User', 1, '{\"old\":{\"incident_id\":2,\"vehicle_id\":2,\"type\":\"thu\",\"amount\":\"2500000.00\",\"method\":\"cash\",\"note\":\"Thu d\\u1ecbch v\\u1ee5: m\\u00e1y th\\u1edf\",\"date\":\"2025-11-21T19:39:00.000000Z\"}}', NULL, '2025-11-22 00:06:40', '2025-11-22 00:06:40'),
(44, 'default', 'deleted', 'App\\Models\\Transaction', 'deleted', 5, 'App\\Models\\User', 1, '{\"old\":{\"incident_id\":2,\"vehicle_id\":2,\"type\":\"thu\",\"amount\":\"200000.00\",\"method\":\"cash\",\"note\":\"Thu d\\u1ecbch v\\u1ee5: V\\u1eadt t\\u01b0 y t\\u1ebf\",\"date\":\"2025-11-21T19:39:00.000000Z\"}}', NULL, '2025-11-22 00:06:43', '2025-11-22 00:06:43'),
(45, 'default', 'deleted', 'App\\Models\\Transaction', 'deleted', 6, 'App\\Models\\User', 1, '{\"old\":{\"incident_id\":2,\"vehicle_id\":2,\"type\":\"chi\",\"amount\":\"500000.00\",\"method\":\"cash\",\"note\":\"c\\u00f4ng t\\u00e0i\",\"date\":\"2025-11-21T19:39:00.000000Z\"}}', NULL, '2025-11-22 00:06:45', '2025-11-22 00:06:45'),
(46, 'default', 'deleted', 'App\\Models\\Transaction', 'deleted', 7, 'App\\Models\\User', 1, '{\"old\":{\"incident_id\":2,\"vehicle_id\":2,\"type\":\"chi\",\"amount\":\"600000.00\",\"method\":\"cash\",\"note\":\"Chi ph\\u00ed: c\\u00f4ng \\u0111i\\u1ec1u d\\u01b0\\u1ee1ng\",\"date\":\"2025-11-21T19:39:00.000000Z\"}}', NULL, '2025-11-22 00:06:47', '2025-11-22 00:06:47'),
(47, 'default', 'deleted', 'App\\Models\\Transaction', 'deleted', 8, 'App\\Models\\User', 1, '{\"old\":{\"incident_id\":2,\"vehicle_id\":2,\"type\":\"chi\",\"amount\":\"980000.00\",\"method\":\"cash\",\"note\":\"Chi ph\\u00ed: d\\u1ea7u\",\"date\":\"2025-11-21T19:39:00.000000Z\"}}', NULL, '2025-11-22 00:06:48', '2025-11-22 00:06:48'),
(48, 'default', 'deleted', 'App\\Models\\Transaction', 'deleted', 9, 'App\\Models\\User', 1, '{\"old\":{\"incident_id\":2,\"vehicle_id\":2,\"type\":\"chi\",\"amount\":\"120000.00\",\"method\":\"cash\",\"note\":\"B\\u1ea3o tr\\u00ec: R\\u1eeda xe\",\"date\":\"2025-11-21T19:39:00.000000Z\"}}', NULL, '2025-11-22 00:06:50', '2025-11-22 00:06:50'),
(49, 'default', 'deleted', 'App\\Models\\Transaction', 'deleted', 11, 'App\\Models\\User', 1, '{\"old\":{\"incident_id\":2,\"vehicle_id\":2,\"type\":\"chi\",\"amount\":\"550000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng: \",\"date\":\"2025-11-21T19:39:00.000000Z\"}}', NULL, '2025-11-22 00:06:53', '2025-11-22 00:06:53'),
(50, 'default', 'deleted', 'App\\Models\\Transaction', 'deleted', 12, 'App\\Models\\User', 1, '{\"old\":{\"incident_id\":2,\"vehicle_id\":2,\"type\":\"chi\",\"amount\":\"600000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng: \",\"date\":\"2025-11-21T19:39:00.000000Z\"}}', NULL, '2025-11-22 00:06:55', '2025-11-22 00:06:55'),
(51, 'default', 'deleted', 'App\\Models\\Transaction', 'deleted', 13, 'App\\Models\\User', 1, '{\"old\":{\"incident_id\":2,\"vehicle_id\":2,\"type\":\"chi\",\"amount\":\"500000.00\",\"method\":\"cash\",\"note\":\"Hoa h\\u1ed3ng: Khoa n\\u1ed9i A - BV \\u0110a khoa\",\"date\":\"2025-11-21T19:39:00.000000Z\"}}', NULL, '2025-11-22 00:06:57', '2025-11-22 00:06:57'),
(52, 'default', 'deleted', 'App\\Models\\Transaction', 'deleted', 2, 'App\\Models\\User', 1, '{\"old\":{\"incident_id\":1,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"500000.00\",\"method\":\"cash\",\"note\":\"Chi ph\\u00ed chuy\\u1ebfn \\u0111i\",\"date\":\"2025-11-21T18:13:00.000000Z\"}}', NULL, '2025-11-22 00:06:59', '2025-11-22 00:06:59'),
(53, 'default', 'deleted', 'App\\Models\\Transaction', 'deleted', 1, 'App\\Models\\User', 1, '{\"old\":{\"incident_id\":1,\"vehicle_id\":1,\"type\":\"thu\",\"amount\":\"4500000.00\",\"method\":\"cash\",\"note\":\"Thu t\\u1eeb chuy\\u1ebfn \\u0111i\",\"date\":\"2025-11-21T18:13:00.000000Z\"}}', NULL, '2025-11-22 00:07:00', '2025-11-22 00:07:00'),
(54, 'default', 'deleted', 'App\\Models\\Incident', 'deleted', 2, 'App\\Models\\User', 1, '{\"old\":{\"vehicle_id\":2,\"patient_id\":2,\"date\":\"2025-11-21T19:39:00.000000Z\",\"dispatch_by\":1,\"destination\":null,\"from_location_id\":6,\"to_location_id\":1,\"partner_id\":1,\"commission_amount\":\"500000.00\",\"summary\":null,\"tags\":null}}', NULL, '2025-11-22 00:07:27', '2025-11-22 00:07:27'),
(55, 'default', 'deleted', 'App\\Models\\Incident', 'deleted', 1, 'App\\Models\\User', 1, '{\"old\":{\"vehicle_id\":1,\"patient_id\":1,\"date\":\"2025-11-21T18:13:00.000000Z\",\"dispatch_by\":1,\"destination\":\"ch\\u1ee3 r\\u1eaby\",\"from_location_id\":null,\"to_location_id\":null,\"partner_id\":null,\"commission_amount\":null,\"summary\":null,\"tags\":null}}', NULL, '2025-11-22 00:07:29', '2025-11-22 00:07:29'),
(56, 'default', 'created', 'App\\Models\\Incident', 'created', 3, 'App\\Models\\User', 1, '{\"attributes\":{\"vehicle_id\":1,\"patient_id\":1,\"date\":\"2025-11-22T07:20:00.000000Z\",\"dispatch_by\":1,\"destination\":null,\"from_location_id\":6,\"to_location_id\":5,\"partner_id\":1,\"commission_amount\":\"2000000.00\",\"summary\":null,\"tags\":null}}', NULL, '2025-11-22 00:21:50', '2025-11-22 00:21:50'),
(57, 'default', 'created', 'App\\Models\\Transaction', 'created', 14, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":3,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"2000000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng l\\u00e1i xe: Cil \\u0110oan\",\"date\":\"2025-11-22T07:20:00.000000Z\"}}', NULL, '2025-11-22 00:21:50', '2025-11-22 00:21:50'),
(58, 'default', 'created', 'App\\Models\\Transaction', 'created', 15, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":3,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"2000000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng l\\u00e1i xe: Nguy\\u1ec5n C\\u1eefu Ninh\",\"date\":\"2025-11-22T07:20:00.000000Z\"}}', NULL, '2025-11-22 00:21:50', '2025-11-22 00:21:50'),
(59, 'default', 'created', 'App\\Models\\Transaction', 'created', 16, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":3,\"vehicle_id\":1,\"type\":\"thu\",\"amount\":\"20000000.00\",\"method\":\"cash\",\"note\":\"Thu chuy\\u1ebfn \\u0111i\",\"date\":\"2025-11-22T07:20:00.000000Z\"}}', NULL, '2025-11-22 00:21:50', '2025-11-22 00:21:50'),
(60, 'default', 'created', 'App\\Models\\Transaction', 'created', 17, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":3,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"1500000.00\",\"method\":\"cash\",\"note\":\"Chi ph\\u00ed chuy\\u1ebfn \\u0111i\",\"date\":\"2025-11-22T07:20:00.000000Z\"}}', NULL, '2025-11-22 00:21:50', '2025-11-22 00:21:50'),
(61, 'default', 'created', 'App\\Models\\VehicleMaintenance', 'created', 3, 'App\\Models\\User', 1, '{\"attributes\":{\"vehicle_id\":1,\"maintenance_service_id\":3,\"partner_id\":2,\"incident_id\":3,\"date\":\"2025-11-22T00:00:00.000000Z\",\"cost\":\"120000.00\",\"mileage\":null,\"description\":null,\"note\":\"B\\u1ea3o tr\\u00ec ph\\u00e1t sinh trong chuy\\u1ebfn \\u0111i\"}}', NULL, '2025-11-22 00:21:50', '2025-11-22 00:21:50'),
(62, 'default', 'created', 'App\\Models\\Transaction', 'created', 18, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":3,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"120000.00\",\"method\":\"cash\",\"note\":\"B\\u1ea3o tr\\u00ec: R\\u1eeda xe\",\"date\":\"2025-11-22T07:20:00.000000Z\"}}', NULL, '2025-11-22 00:21:50', '2025-11-22 00:21:50'),
(63, 'default', 'created', 'App\\Models\\Transaction', 'created', 19, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":3,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"2000000.00\",\"method\":\"cash\",\"note\":\"Hoa h\\u1ed3ng: Khoa n\\u1ed9i A - BV \\u0110a khoa\",\"date\":\"2025-11-22T07:20:00.000000Z\"}}', NULL, '2025-11-22 00:21:50', '2025-11-22 00:21:50'),
(64, 'default', 'created', 'App\\Models\\Partner', 'created', 3, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"Gara ngo\\u00e0i\",\"type\":\"maintenance\",\"phone\":null,\"email\":null,\"address\":null,\"commission_rate\":null,\"note\":\"\\u00e1p d\\u1ee5ng cho tr\\u01b0\\u1eddng h\\u1ee3p \\u0111\\u1ed9t xu\\u1ea5t tr\\u00ean \\u0111\\u01b0\\u1eddng\",\"is_active\":true}}', NULL, '2025-11-22 00:56:02', '2025-11-22 00:56:02'),
(65, 'default', 'created', 'App\\Models\\Partner', 'created', 4, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"M\\u00f4i gi\\u1edbi HCM\",\"type\":\"collaborator\",\"phone\":null,\"email\":null,\"address\":null,\"commission_rate\":null,\"note\":\"D\\u00f9ng cho c\\u00e1c tr\\u01b0\\u1eddng h\\u1ee3p gi\\u1edbi thi\\u1ec7u l\\u00e0 c\\u00e1 nh\\u00e2n ho\\u1eb7c ng\\u01b0\\u1eddi b\\u00ean ngo\\u00e0i\",\"is_active\":true}}', NULL, '2025-11-22 00:56:56', '2025-11-22 00:56:56'),
(66, 'default', 'deleted', 'App\\Models\\Incident', 'deleted', 3, 'App\\Models\\User', 1, '{\"old\":{\"vehicle_id\":1,\"patient_id\":1,\"date\":\"2025-11-22T07:20:00.000000Z\",\"dispatch_by\":1,\"destination\":null,\"from_location_id\":6,\"to_location_id\":5,\"partner_id\":1,\"commission_amount\":\"2000000.00\",\"summary\":null,\"tags\":null}}', NULL, '2025-11-22 01:12:11', '2025-11-22 01:12:11'),
(67, 'default', 'updated', 'App\\Models\\Vehicle', 'updated', 2, 'App\\Models\\User', 1, '{\"attributes\":{\"driver_name\":\"Ninh\"},\"old\":{\"driver_name\":null}}', NULL, '2025-11-22 01:12:55', '2025-11-22 01:12:55'),
(68, 'default', 'created', 'App\\Models\\Incident', 'created', 4, 'App\\Models\\User', 1, '{\"attributes\":{\"vehicle_id\":1,\"patient_id\":1,\"date\":\"2025-11-22T08:50:00.000000Z\",\"dispatch_by\":1,\"destination\":null,\"from_location_id\":6,\"to_location_id\":5,\"partner_id\":1,\"commission_amount\":\"2000000.00\",\"summary\":null,\"tags\":null}}', NULL, '2025-11-22 01:51:39', '2025-11-22 01:51:39'),
(69, 'default', 'created', 'App\\Models\\Transaction', 'created', 20, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":4,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"3000000.00\",\"method\":\"bank\",\"note\":\"Ti\\u1ec1n c\\u00f4ng l\\u00e1i xe: Nguy\\u1ec5n C\\u1eefu Ninh\",\"date\":\"2025-11-22T08:50:00.000000Z\"}}', NULL, '2025-11-22 01:51:39', '2025-11-22 01:51:39'),
(70, 'default', 'created', 'App\\Models\\Transaction', 'created', 21, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":4,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"3000000.00\",\"method\":\"bank\",\"note\":\"Ti\\u1ec1n c\\u00f4ng l\\u00e1i xe: L\\u00ea Phong\",\"date\":\"2025-11-22T08:50:00.000000Z\"}}', NULL, '2025-11-22 01:51:39', '2025-11-22 01:51:39'),
(71, 'default', 'created', 'App\\Models\\Transaction', 'created', 22, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":4,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"3000000.00\",\"method\":\"bank\",\"note\":\"Ti\\u1ec1n c\\u00f4ng nh\\u00e2n vi\\u00ean y t\\u1ebf: Nguy\\u1ec5n Qu\\u1ed1c V\\u0169\",\"date\":\"2025-11-22T08:50:00.000000Z\"}}', NULL, '2025-11-22 01:51:39', '2025-11-22 01:51:39'),
(72, 'default', 'created', 'App\\Models\\Transaction', 'created', 23, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":4,\"vehicle_id\":1,\"type\":\"thu\",\"amount\":\"20000000.00\",\"method\":\"bank\",\"note\":\"Thu chuy\\u1ebfn \\u0111i\",\"date\":\"2025-11-22T08:50:00.000000Z\"}}', NULL, '2025-11-22 01:51:39', '2025-11-22 01:51:39'),
(73, 'default', 'created', 'App\\Models\\Transaction', 'created', 24, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":4,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"1500000.00\",\"method\":\"bank\",\"note\":\"Chi ph\\u00ed chuy\\u1ebfn \\u0111i\",\"date\":\"2025-11-22T08:50:00.000000Z\"}}', NULL, '2025-11-22 01:51:39', '2025-11-22 01:51:39'),
(74, 'default', 'created', 'App\\Models\\MaintenanceService', 'created', 4, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"v\\u00e1 l\\u1ed1p\",\"description\":null,\"is_active\":true}}', NULL, '2025-11-22 01:51:39', '2025-11-22 01:51:39'),
(75, 'default', 'created', 'App\\Models\\VehicleMaintenance', 'created', 4, 'App\\Models\\User', 1, '{\"attributes\":{\"vehicle_id\":1,\"maintenance_service_id\":4,\"partner_id\":3,\"incident_id\":4,\"date\":\"2025-11-22T00:00:00.000000Z\",\"cost\":\"120000.00\",\"mileage\":null,\"description\":null,\"note\":\"B\\u1ea3o tr\\u00ec ph\\u00e1t sinh trong chuy\\u1ebfn \\u0111i\"}}', NULL, '2025-11-22 01:51:39', '2025-11-22 01:51:39'),
(76, 'default', 'created', 'App\\Models\\Transaction', 'created', 25, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":4,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"120000.00\",\"method\":\"bank\",\"note\":\"B\\u1ea3o tr\\u00ec: v\\u00e1 l\\u1ed1p\",\"date\":\"2025-11-22T08:50:00.000000Z\"}}', NULL, '2025-11-22 01:51:39', '2025-11-22 01:51:39'),
(77, 'default', 'created', 'App\\Models\\Transaction', 'created', 26, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":4,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"2000000.00\",\"method\":\"bank\",\"note\":\"Hoa h\\u1ed3ng: Khoa n\\u1ed9i A - BV \\u0110a khoa\",\"date\":\"2025-11-22T08:50:00.000000Z\"}}', NULL, '2025-11-22 01:51:39', '2025-11-22 01:51:39'),
(78, 'default', 'created', 'App\\Models\\Transaction', 'created', 27, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":4,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"500000.00\",\"method\":\"cash\",\"note\":\"\\u0110i\\u1ec1u ch\\u1ec9nh: Th\\u01b0\\u1edfng - L\\u00ea Phong (#4)\",\"date\":\"2025-11-22T09:11:16.000000Z\"}}', NULL, '2025-11-22 02:11:16', '2025-11-22 02:11:16'),
(79, 'default', 'created', 'App\\Models\\Transaction', 'created', 28, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":4,\"vehicle_id\":1,\"type\":\"thu\",\"amount\":\"1000000.00\",\"method\":\"cash\",\"note\":\"Tr\\u1eeb ti\\u1ec1n: Ph\\u1ea1t - Nguy\\u1ec5n C\\u1eefu Ninh (Chuy\\u1ebfn #4)\",\"date\":\"2025-11-22T09:22:20.000000Z\"}}', NULL, '2025-11-22 02:22:20', '2025-11-22 02:22:20'),
(80, 'default', 'created', 'App\\Models\\Transaction', 'created', 29, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":null,\"vehicle_id\":null,\"type\":\"chi\",\"amount\":\"300000.00\",\"method\":\"cash\",\"note\":\"\\u0110i\\u1ec1u ch\\u1ec9nh: Th\\u01b0\\u1edfng - Nguy\\u1ec5n Qu\\u1ed1c V\\u0169 (t\\u1eeb qu\\u1ef9 c\\u00f4ng ty)\",\"date\":\"2025-11-22T09:25:50.000000Z\"}}', NULL, '2025-11-22 02:25:50', '2025-11-22 02:25:50'),
(81, 'default', 'created', 'App\\Models\\Transaction', 'created', 30, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":null,\"vehicle_id\":null,\"type\":\"thu\",\"amount\":\"2000000.00\",\"method\":\"cash\",\"note\":\"Tr\\u1eeb ti\\u1ec1n: \\u1ee8ng l\\u01b0\\u01a1ng - L\\u00ea Phong\",\"date\":\"2025-11-22T09:33:16.000000Z\"}}', NULL, '2025-11-22 02:33:16', '2025-11-22 02:33:16'),
(82, 'default', 'deleted', 'App\\Models\\Transaction', 'deleted', 30, 'App\\Models\\User', 1, '{\"old\":{\"incident_id\":null,\"vehicle_id\":null,\"type\":\"thu\",\"amount\":\"2000000.00\",\"method\":\"cash\",\"note\":\"Tr\\u1eeb ti\\u1ec1n: \\u1ee8ng l\\u01b0\\u01a1ng - L\\u00ea Phong\",\"date\":\"2025-11-22T09:33:16.000000Z\"}}', NULL, '2025-11-22 02:36:30', '2025-11-22 02:36:30'),
(83, 'default', 'created', 'App\\Models\\Transaction', 'created', 31, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":null,\"vehicle_id\":null,\"type\":\"chi\",\"amount\":\"5500000.00\",\"method\":\"cash\",\"note\":\"\\u1ee8ng l\\u01b0\\u01a1ng - Nguy\\u1ec5n C\\u1eefu Ninh (N\\u1ee3 c\\u00f4ng ty: 500.000\\u0111)\",\"date\":\"2025-11-22T09:50:12.000000Z\"}}', NULL, '2025-11-22 02:50:12', '2025-11-22 02:50:12'),
(84, 'default', 'deleted', 'App\\Models\\Transaction', 'deleted', 31, 'App\\Models\\User', 1, '{\"old\":{\"incident_id\":null,\"vehicle_id\":null,\"type\":\"chi\",\"amount\":\"5500000.00\",\"method\":\"cash\",\"note\":\"\\u1ee8ng l\\u01b0\\u01a1ng - Nguy\\u1ec5n C\\u1eefu Ninh (N\\u1ee3 c\\u00f4ng ty: 500.000\\u0111)\",\"date\":\"2025-11-22T09:50:12.000000Z\"}}', NULL, '2025-11-22 02:55:14', '2025-11-22 02:55:14'),
(85, 'default', 'created', 'App\\Models\\Transaction', 'created', 32, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":null,\"vehicle_id\":null,\"type\":\"chi\",\"amount\":\"550000.00\",\"method\":\"cash\",\"note\":\"\\u1ee8ng l\\u01b0\\u01a1ng - Nguy\\u1ec5n C\\u1eefu Ninh\",\"date\":\"2025-11-22T10:00:47.000000Z\"}}', NULL, '2025-11-22 03:00:47', '2025-11-22 03:00:47'),
(86, 'default', 'created', 'App\\Models\\Transaction', 'created', 33, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":null,\"vehicle_id\":null,\"type\":\"chi\",\"amount\":\"500000.00\",\"method\":\"cash\",\"note\":\"\\u1ee8ng l\\u01b0\\u01a1ng (n\\u1ee3 c\\u00f4ng ty) - Nguy\\u1ec5n C\\u1eefu Ninh\",\"date\":\"2025-11-22T10:26:12.000000Z\"}}', NULL, '2025-11-22 03:26:12', '2025-11-22 03:26:12'),
(87, 'default', 'deleted', 'App\\Models\\Transaction', 'deleted', 29, 'App\\Models\\User', 1, '{\"old\":{\"incident_id\":null,\"vehicle_id\":null,\"type\":\"chi\",\"amount\":\"300000.00\",\"method\":\"cash\",\"note\":\"\\u0110i\\u1ec1u ch\\u1ec9nh: Th\\u01b0\\u1edfng - Nguy\\u1ec5n Qu\\u1ed1c V\\u0169 (t\\u1eeb qu\\u1ef9 c\\u00f4ng ty)\",\"date\":\"2025-11-22T09:25:50.000000Z\"}}', NULL, '2025-11-22 03:34:03', '2025-11-22 03:34:03'),
(88, 'default', 'deleted', 'App\\Models\\Incident', 'deleted', 4, 'App\\Models\\User', 1, '{\"old\":{\"vehicle_id\":1,\"patient_id\":1,\"date\":\"2025-11-22T08:50:00.000000Z\",\"dispatch_by\":1,\"destination\":null,\"from_location_id\":6,\"to_location_id\":5,\"partner_id\":1,\"commission_amount\":\"2000000.00\",\"summary\":null,\"tags\":null}}', NULL, '2025-11-22 03:47:55', '2025-11-22 03:47:55'),
(89, 'default', 'created', 'App\\Models\\Transaction', 'created', 34, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":null,\"vehicle_id\":null,\"type\":\"thu\",\"amount\":\"99241500.00\",\"method\":\"bank\",\"note\":\"k\\u1ebft chuy\\u1ec3n s\\u1ed1 d\\u01b0\",\"date\":\"2025-11-21T10:55:00.000000Z\"}}', NULL, '2025-11-22 03:55:21', '2025-11-22 03:55:21'),
(90, 'default', 'deleted', 'App\\Models\\Transaction', 'deleted', 34, 'App\\Models\\User', 1, '{\"old\":{\"incident_id\":null,\"vehicle_id\":null,\"type\":\"thu\",\"amount\":\"99241500.00\",\"method\":\"bank\",\"note\":\"k\\u1ebft chuy\\u1ec3n s\\u1ed1 d\\u01b0\",\"date\":\"2025-11-21T10:55:00.000000Z\"}}', NULL, '2025-11-22 04:11:47', '2025-11-22 04:11:47'),
(91, 'default', 'created', 'App\\Models\\Transaction', 'created', 35, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":null,\"vehicle_id\":null,\"type\":\"thu\",\"amount\":\"147241500.00\",\"method\":\"bank\",\"note\":\"k\\u1ebft chuy\\u1ec3n s\\u1ed1 d\\u01b0 T10\",\"date\":\"2025-11-22T11:12:00.000000Z\"}}', NULL, '2025-11-22 04:12:34', '2025-11-22 04:12:34'),
(92, 'default', 'created', 'App\\Models\\Transaction', 'created', 36, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":null,\"vehicle_id\":null,\"type\":\"du_kien_chi\",\"amount\":\"48000000.00\",\"method\":\"bank\",\"note\":\"Gi\\u1eef l\\u1ea1i tr\\u01b0\\u1edbc khi tr\\u00edch l\\u1ee3i nhu\\u1eadn , sau khi x\\u1eed l\\u00fd vi ph\\u1ea1m, thi\\u1ebfu tr\\u00edch th\\u00eam, d\\u01b0 tr\\u1ea3 l\\u1ea1i theo t\\u1ef7 l\\u1ec7 chia l\\u1ee3i nhu\\u1eadn\",\"date\":\"2025-11-22T11:12:00.000000Z\"}}', NULL, '2025-11-22 04:12:53', '2025-11-22 04:12:53'),
(93, 'default', 'created', 'App\\Models\\Transaction', 'created', 37, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":null,\"vehicle_id\":null,\"type\":\"chi\",\"amount\":\"18280284.30\",\"method\":\"bank\",\"note\":\"Chia c\\u1ed5 t\\u1ee9c 100% - Tr\\u1ea7n \\u0110\\u1ee9c Anh (V\\u1ed1n g\\u00f3p: 18.42%) - chia h\\u1ebft l\\u1ee3i nhu\\u1eadn th\\u00e1ng 10\",\"date\":\"2025-11-22T11:46:13.000000Z\"}}', NULL, '2025-11-22 04:46:13', '2025-11-22 04:46:13'),
(94, 'default', 'created', 'App\\Models\\Transaction', 'created', 38, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":null,\"vehicle_id\":null,\"type\":\"chi\",\"amount\":\"18280284.30\",\"method\":\"bank\",\"note\":\"Chia c\\u1ed5 t\\u1ee9c 100% - A Th\\u00e0nh (V\\u1ed1n g\\u00f3p: 18.42%) - chia h\\u1ebft l\\u1ee3i nhu\\u1eadn th\\u00e1ng 10\",\"date\":\"2025-11-22T11:46:13.000000Z\"}}', NULL, '2025-11-22 04:46:13', '2025-11-22 04:46:13'),
(95, 'default', 'created', 'App\\Models\\Transaction', 'created', 39, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":null,\"vehicle_id\":null,\"type\":\"chi\",\"amount\":\"62680931.40\",\"method\":\"bank\",\"note\":\"Chia c\\u1ed5 t\\u1ee9c 100% - A Trung (V\\u1ed1n g\\u00f3p: 63.16%) - chia h\\u1ebft l\\u1ee3i nhu\\u1eadn th\\u00e1ng 10\",\"date\":\"2025-11-22T11:46:13.000000Z\"}}', NULL, '2025-11-22 04:46:13', '2025-11-22 04:46:13'),
(96, 'default', 'created', 'App\\Models\\Vehicle', 'created', 3, 'App\\Models\\User', 1, '{\"attributes\":{\"license_plate\":\"86A31384\",\"model\":\"Ford Transit\",\"driver_name\":\"L\\u00ea Phong\",\"phone\":null,\"status\":\"active\"}}', NULL, '2025-11-22 04:54:03', '2025-11-22 04:54:03'),
(97, 'default', 'created', 'App\\Models\\Vehicle', 'created', 4, 'App\\Models\\User', 1, '{\"attributes\":{\"license_plate\":\"49B08879\",\"model\":\"Ford Transit\",\"driver_name\":null,\"phone\":null,\"status\":\"active\"}}', NULL, '2025-11-22 04:54:24', '2025-11-22 04:54:24'),
(98, 'default', 'updated', 'App\\Models\\Vehicle', 'updated', 4, 'App\\Models\\User', 1, '{\"attributes\":{\"driver_name\":\"Nova\"},\"old\":{\"driver_name\":null}}', NULL, '2025-11-22 04:56:00', '2025-11-22 04:56:00'),
(99, 'default', 'created', 'App\\Models\\Incident', 'created', 5, 'App\\Models\\User', 1, '{\"attributes\":{\"vehicle_id\":4,\"patient_id\":1,\"date\":\"2025-11-22T11:58:00.000000Z\",\"dispatch_by\":1,\"destination\":null,\"from_location_id\":6,\"to_location_id\":1,\"partner_id\":1,\"commission_amount\":\"500000.00\",\"summary\":null,\"tags\":null}}', NULL, '2025-11-22 05:00:32', '2025-11-22 05:00:32'),
(100, 'default', 'created', 'App\\Models\\Transaction', 'created', 40, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":5,\"vehicle_id\":4,\"type\":\"chi\",\"amount\":\"550000.00\",\"method\":\"bank\",\"note\":\"Ti\\u1ec1n c\\u00f4ng l\\u00e1i xe: Nguy\\u1ec5n C\\u1eefu Ninh\",\"date\":\"2025-11-22T11:58:00.000000Z\"}}', NULL, '2025-11-22 05:00:32', '2025-11-22 05:00:32'),
(101, 'default', 'created', 'App\\Models\\Transaction', 'created', 41, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":5,\"vehicle_id\":4,\"type\":\"chi\",\"amount\":\"600000.00\",\"method\":\"bank\",\"note\":\"Ti\\u1ec1n c\\u00f4ng nh\\u00e2n vi\\u00ean y t\\u1ebf: Nguy\\u1ec5n Qu\\u1ed1c V\\u0169\",\"date\":\"2025-11-22T11:58:00.000000Z\"}}', NULL, '2025-11-22 05:00:32', '2025-11-22 05:00:32'),
(102, 'default', 'created', 'App\\Models\\Transaction', 'created', 42, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":5,\"vehicle_id\":4,\"type\":\"thu\",\"amount\":\"4500000.00\",\"method\":\"bank\",\"note\":\"Thu chuy\\u1ebfn \\u0111i\",\"date\":\"2025-11-22T11:58:00.000000Z\"}}', NULL, '2025-11-22 05:00:32', '2025-11-22 05:00:32'),
(103, 'default', 'created', 'App\\Models\\Transaction', 'created', 43, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":5,\"vehicle_id\":4,\"type\":\"chi\",\"amount\":\"1050000.00\",\"method\":\"bank\",\"note\":\"D\\u1ea7u\",\"date\":\"2025-11-22T11:58:00.000000Z\"}}', NULL, '2025-11-22 05:00:32', '2025-11-22 05:00:32'),
(104, 'default', 'created', 'App\\Models\\Transaction', 'created', 44, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":5,\"vehicle_id\":4,\"type\":\"chi\",\"amount\":\"500000.00\",\"method\":\"bank\",\"note\":\"Hoa h\\u1ed3ng: Khoa n\\u1ed9i A - BV \\u0110a khoa\",\"date\":\"2025-11-22T11:58:00.000000Z\"}}', NULL, '2025-11-22 05:00:32', '2025-11-22 05:00:32'),
(105, 'default', 'created', 'App\\Models\\Patient', 'created', 3, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"nguyen van b\",\"birth_year\":2000,\"phone\":null,\"gender\":\"male\",\"address\":null}}', NULL, '2025-11-22 05:05:06', '2025-11-22 05:05:06'),
(106, 'default', 'created', 'App\\Models\\Incident', 'created', 6, 'App\\Models\\User', 1, '{\"attributes\":{\"vehicle_id\":4,\"patient_id\":3,\"date\":\"2025-11-21T12:00:00.000000Z\",\"dispatch_by\":1,\"destination\":null,\"from_location_id\":6,\"to_location_id\":2,\"partner_id\":null,\"commission_amount\":null,\"summary\":null,\"tags\":null}}', NULL, '2025-11-22 05:05:06', '2025-11-22 05:05:06'),
(107, 'default', 'created', 'App\\Models\\Transaction', 'created', 45, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":6,\"vehicle_id\":4,\"type\":\"chi\",\"amount\":\"550000.00\",\"method\":\"bank\",\"note\":\"Ti\\u1ec1n c\\u00f4ng l\\u00e1i xe: Nguy\\u1ec5n C\\u1eefu Ninh\",\"date\":\"2025-11-21T12:00:00.000000Z\"}}', NULL, '2025-11-22 05:05:06', '2025-11-22 05:05:06'),
(108, 'default', 'created', 'App\\Models\\Transaction', 'created', 46, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":6,\"vehicle_id\":4,\"type\":\"chi\",\"amount\":\"600000.00\",\"method\":\"bank\",\"note\":\"Ti\\u1ec1n c\\u00f4ng nh\\u00e2n vi\\u00ean y t\\u1ebf: Nguy\\u1ec5n Qu\\u1ed1c V\\u0169\",\"date\":\"2025-11-21T12:00:00.000000Z\"}}', NULL, '2025-11-22 05:05:06', '2025-11-22 05:05:06'),
(109, 'default', 'created', 'App\\Models\\Transaction', 'created', 47, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":6,\"vehicle_id\":4,\"type\":\"thu\",\"amount\":\"4500000.00\",\"method\":\"bank\",\"note\":\"Thu chuy\\u1ebfn \\u0111i\",\"date\":\"2025-11-21T12:00:00.000000Z\"}}', NULL, '2025-11-22 05:05:06', '2025-11-22 05:05:06'),
(110, 'default', 'created', 'App\\Models\\Transaction', 'created', 48, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":6,\"vehicle_id\":4,\"type\":\"chi\",\"amount\":\"1150000.00\",\"method\":\"bank\",\"note\":\"D\\u1ea7u\",\"date\":\"2025-11-21T12:00:00.000000Z\"}}', NULL, '2025-11-22 05:05:06', '2025-11-22 05:05:06'),
(111, 'default', 'created', 'App\\Models\\MaintenanceService', 'created', 5, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"V\\u00e9 m\\u00e1y bay \\u0110L - HN\",\"description\":null,\"is_active\":true}}', NULL, '2025-11-22 09:11:02', '2025-11-22 09:11:02'),
(112, 'default', 'created', 'App\\Models\\VehicleMaintenance', 'created', 5, 'App\\Models\\User', 1, '{\"attributes\":{\"vehicle_id\":4,\"maintenance_service_id\":5,\"partner_id\":null,\"incident_id\":null,\"date\":\"2025-11-14T00:00:00.000000Z\",\"cost\":\"1666000.00\",\"mileage\":null,\"description\":\"v\\u00e9 mb cho nh\\u00e2n s\\u1ef1 \\u0111i ra l\\u1ea5y xe\",\"note\":null}}', NULL, '2025-11-22 09:11:02', '2025-11-22 09:11:02'),
(113, 'default', 'created', 'App\\Models\\VehicleMaintenance', 'created', 6, 'App\\Models\\User', 1, '{\"attributes\":{\"vehicle_id\":4,\"maintenance_service_id\":null,\"partner_id\":null,\"incident_id\":null,\"date\":\"2025-11-22T00:00:00.000000Z\",\"cost\":\"518298.00\",\"mileage\":null,\"description\":\"ks 1 \\u0111\\u00eam cho l\\u00e1i xe\",\"note\":null}}', NULL, '2025-11-22 09:14:18', '2025-11-22 09:14:18'),
(114, 'default', 'created', 'App\\Models\\Partner', 'created', 5, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"C - bone Hotel\",\"type\":\"maintenance\",\"phone\":null,\"email\":null,\"address\":null,\"commission_rate\":null,\"note\":null,\"is_active\":true}}', NULL, '2025-11-22 09:15:16', '2025-11-22 09:15:16'),
(115, 'default', 'updated', 'App\\Models\\VehicleMaintenance', 'updated', 6, 'App\\Models\\User', 1, '{\"attributes\":{\"partner_id\":5,\"date\":\"2025-11-07T00:00:00.000000Z\"},\"old\":{\"partner_id\":null,\"date\":\"2025-11-22T00:00:00.000000Z\"}}', NULL, '2025-11-22 09:15:16', '2025-11-22 09:15:16'),
(116, 'default', 'created', 'App\\Models\\Partner', 'created', 6, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"Vietjet air\",\"type\":\"maintenance\",\"phone\":null,\"email\":null,\"address\":null,\"commission_rate\":null,\"note\":null,\"is_active\":true}}', NULL, '2025-11-22 09:18:34', '2025-11-22 09:18:34'),
(117, 'default', 'updated', 'App\\Models\\VehicleMaintenance', 'updated', 5, 'App\\Models\\User', 1, '{\"attributes\":{\"partner_id\":6},\"old\":{\"partner_id\":null}}', NULL, '2025-11-22 09:18:34', '2025-11-22 09:18:34'),
(118, 'default', 'created', 'App\\Models\\MaintenanceService', 'created', 6, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"kh\\u00e1ch s\\u1ea1n\",\"description\":null,\"is_active\":true}}', NULL, '2025-11-22 09:18:40', '2025-11-22 09:18:40'),
(119, 'default', 'updated', 'App\\Models\\VehicleMaintenance', 'updated', 6, 'App\\Models\\User', 1, '{\"attributes\":{\"maintenance_service_id\":6},\"old\":{\"maintenance_service_id\":null}}', NULL, '2025-11-22 09:18:40', '2025-11-22 09:18:40'),
(120, 'default', 'created', 'App\\Models\\MaintenanceService', 'created', 7, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"\\u0111\\u1ed5 d\\u1ea7u\",\"description\":null,\"is_active\":true}}', NULL, '2025-11-22 09:20:47', '2025-11-22 09:20:47'),
(121, 'default', 'created', 'App\\Models\\Partner', 'created', 7, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"Petrolimex\",\"type\":\"maintenance\",\"phone\":null,\"email\":null,\"address\":null,\"commission_rate\":null,\"note\":null,\"is_active\":true}}', NULL, '2025-11-22 09:20:47', '2025-11-22 09:20:47'),
(122, 'default', 'created', 'App\\Models\\VehicleMaintenance', 'created', 7, 'App\\Models\\User', 1, '{\"attributes\":{\"vehicle_id\":4,\"maintenance_service_id\":7,\"partner_id\":7,\"incident_id\":null,\"date\":\"2025-11-13T00:00:00.000000Z\",\"cost\":\"500000.00\",\"mileage\":null,\"description\":\"\\u0110\\u1ed5 d\\u1ea7u HN v\\u1ec1 TQ\",\"note\":null}}', NULL, '2025-11-22 09:20:47', '2025-11-22 09:20:47'),
(123, 'default', 'created', 'App\\Models\\VehicleMaintenance', 'created', 8, 'App\\Models\\User', 1, '{\"attributes\":{\"vehicle_id\":4,\"maintenance_service_id\":1,\"partner_id\":3,\"incident_id\":null,\"date\":\"2025-11-14T00:00:00.000000Z\",\"cost\":\"850000.00\",\"mileage\":1600,\"description\":\"Thay nh\\u1edbt l\\u1ea7n \\u0111\\u1ea7u (1600km)\",\"note\":null}}', NULL, '2025-11-22 09:21:26', '2025-11-22 09:21:26'),
(124, 'default', 'created', 'App\\Models\\MaintenanceService', 'created', 8, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"D\\u00e1n xe\",\"description\":null,\"is_active\":true}}', NULL, '2025-11-22 09:22:05', '2025-11-22 09:22:05'),
(125, 'default', 'created', 'App\\Models\\Partner', 'created', 8, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"C\\u1eeda h\\u00e0ng ngo\\u00e0i\",\"type\":\"maintenance\",\"phone\":null,\"email\":null,\"address\":null,\"commission_rate\":null,\"note\":null,\"is_active\":true}}', NULL, '2025-11-22 09:22:05', '2025-11-22 09:22:05'),
(126, 'default', 'created', 'App\\Models\\VehicleMaintenance', 'created', 9, 'App\\Models\\User', 1, '{\"attributes\":{\"vehicle_id\":4,\"maintenance_service_id\":8,\"partner_id\":8,\"incident_id\":null,\"date\":\"2025-11-14T00:00:00.000000Z\",\"cost\":\"450000.00\",\"mileage\":null,\"description\":\"D\\u00e1n logo cty\",\"note\":null}}', NULL, '2025-11-22 09:22:05', '2025-11-22 09:22:05'),
(127, 'default', 'created', 'App\\Models\\MaintenanceService', 'created', 9, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"D\\u00e1n epass\",\"description\":null,\"is_active\":true}}', NULL, '2025-11-22 09:22:40', '2025-11-22 09:22:40'),
(128, 'default', 'created', 'App\\Models\\Partner', 'created', 9, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"Viettel\",\"type\":\"maintenance\",\"phone\":null,\"email\":null,\"address\":null,\"commission_rate\":null,\"note\":null,\"is_active\":true}}', NULL, '2025-11-22 09:22:40', '2025-11-22 09:22:40'),
(129, 'default', 'created', 'App\\Models\\VehicleMaintenance', 'created', 10, 'App\\Models\\User', 1, '{\"attributes\":{\"vehicle_id\":4,\"maintenance_service_id\":9,\"partner_id\":9,\"incident_id\":null,\"date\":\"2025-11-14T00:00:00.000000Z\",\"cost\":\"100000.00\",\"mileage\":null,\"description\":\"D\\u00e1n th\\u1ebb Epass\",\"note\":null}}', NULL, '2025-11-22 09:22:40', '2025-11-22 09:22:40'),
(130, 'default', 'created', 'App\\Models\\MaintenanceService', 'created', 10, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"L\\u1eafp \\u0111\\u1eb7t thi\\u1ebft b\\u1ecb\",\"description\":null,\"is_active\":true}}', NULL, '2025-11-22 09:23:09', '2025-11-22 09:23:09'),
(131, 'default', 'created', 'App\\Models\\Partner', 'created', 10, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"Mai Anh GPS\",\"type\":\"maintenance\",\"phone\":null,\"email\":null,\"address\":null,\"commission_rate\":null,\"note\":null,\"is_active\":true}}', NULL, '2025-11-22 09:23:09', '2025-11-22 09:23:09'),
(132, 'default', 'created', 'App\\Models\\VehicleMaintenance', 'created', 11, 'App\\Models\\User', 1, '{\"attributes\":{\"vehicle_id\":4,\"maintenance_service_id\":10,\"partner_id\":10,\"incident_id\":null,\"date\":\"2025-11-14T00:00:00.000000Z\",\"cost\":\"3800000.00\",\"mileage\":null,\"description\":\"L\\u1eafp cam h\\u00e0nh tr\\u00ecnh, \\u0111\\u1ecbnh v\\u1ecb\",\"note\":null}}', NULL, '2025-11-22 09:23:09', '2025-11-22 09:23:09'),
(133, 'default', 'created', 'App\\Models\\MaintenanceService', 'created', 11, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"\\u00c9p bi\\u1ec3n s\\u1ed1\",\"description\":null,\"is_active\":true}}', NULL, '2025-11-22 09:23:53', '2025-11-22 09:23:53'),
(134, 'default', 'created', 'App\\Models\\Partner', 'created', 11, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"Th\\u1ee3 ngo\\u00e0i\",\"type\":\"maintenance\",\"phone\":null,\"email\":null,\"address\":null,\"commission_rate\":null,\"note\":null,\"is_active\":true}}', NULL, '2025-11-22 09:23:53', '2025-11-22 09:23:53'),
(135, 'default', 'created', 'App\\Models\\VehicleMaintenance', 'created', 12, 'App\\Models\\User', 1, '{\"attributes\":{\"vehicle_id\":4,\"maintenance_service_id\":11,\"partner_id\":11,\"incident_id\":null,\"date\":\"2025-11-14T00:00:00.000000Z\",\"cost\":\"350000.00\",\"mileage\":null,\"description\":\"\\u00c9p bi\\u1ec3n s\\u1ed1\",\"note\":null}}', NULL, '2025-11-22 09:23:53', '2025-11-22 09:23:53'),
(136, 'default', 'created', 'App\\Models\\Partner', 'created', 12, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"Petrolimex\",\"type\":\"maintenance\",\"phone\":null,\"email\":null,\"address\":null,\"commission_rate\":null,\"note\":null,\"is_active\":true}}', NULL, '2025-11-22 09:24:30', '2025-11-22 09:24:30'),
(137, 'default', 'created', 'App\\Models\\VehicleMaintenance', 'created', 13, 'App\\Models\\User', 1, '{\"attributes\":{\"vehicle_id\":4,\"maintenance_service_id\":7,\"partner_id\":12,\"incident_id\":null,\"date\":\"2025-11-14T00:00:00.000000Z\",\"cost\":\"1020000.00\",\"mileage\":null,\"description\":\"\\u0110\\u1ed5 d\\u1ea7u TQ - T\\u00fay Loan\",\"note\":null}}', NULL, '2025-11-22 09:24:30', '2025-11-22 09:24:30'),
(138, 'default', 'created', 'App\\Models\\MaintenanceService', 'created', 12, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"\\u0111\\u1ed5 d\\u1ea7u\",\"description\":null,\"is_active\":true}}', NULL, '2025-11-22 09:25:14', '2025-11-22 09:25:14'),
(139, 'default', 'created', 'App\\Models\\VehicleMaintenance', 'created', 14, 'App\\Models\\User', 1, '{\"attributes\":{\"vehicle_id\":4,\"maintenance_service_id\":12,\"partner_id\":7,\"incident_id\":null,\"date\":\"2025-11-15T00:00:00.000000Z\",\"cost\":\"1300000.00\",\"mileage\":null,\"description\":\"\\u0110\\u1ed5 d\\u1ea7u T\\u00fay loan - L\\u00e2m \\u0110\\u1ed3ng\",\"note\":null}}', NULL, '2025-11-22 09:25:14', '2025-11-22 09:25:14'),
(140, 'default', 'created', 'App\\Models\\MaintenanceService', 'created', 13, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"\\u0102n u\\u1ed1ng\",\"description\":null,\"is_active\":true}}', NULL, '2025-11-22 09:25:53', '2025-11-22 09:25:53'),
(141, 'default', 'created', 'App\\Models\\Partner', 'created', 13, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"Qu\\u00e1n c\\u01a1m\",\"type\":\"maintenance\",\"phone\":null,\"email\":null,\"address\":null,\"commission_rate\":null,\"note\":null,\"is_active\":true}}', NULL, '2025-11-22 09:25:53', '2025-11-22 09:25:53'),
(142, 'default', 'created', 'App\\Models\\VehicleMaintenance', 'created', 15, 'App\\Models\\User', 1, '{\"attributes\":{\"vehicle_id\":4,\"maintenance_service_id\":13,\"partner_id\":13,\"incident_id\":null,\"date\":\"2025-11-15T00:00:00.000000Z\",\"cost\":\"500000.00\",\"mileage\":null,\"description\":\"\\u0102n u\\u1ed1ng \\u0111i \\u0111\\u01b0\\u1eddng ship xe v\\u00e0o\",\"note\":null}}', NULL, '2025-11-22 09:25:53', '2025-11-22 09:25:53'),
(143, 'default', 'created', 'App\\Models\\MaintenanceService', 'created', 14, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"C\\u00f4ng t\\u00e0i\",\"description\":null,\"is_active\":true}}', NULL, '2025-11-22 09:26:24', '2025-11-22 09:26:24');
INSERT INTO `activity_log` (`id`, `log_name`, `description`, `subject_type`, `event`, `subject_id`, `causer_type`, `causer_id`, `properties`, `batch_uuid`, `created_at`, `updated_at`) VALUES
(144, 'default', 'created', 'App\\Models\\Partner', 'created', 14, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"L\\u00e1i xe\",\"type\":\"maintenance\",\"phone\":null,\"email\":null,\"address\":null,\"commission_rate\":null,\"note\":null,\"is_active\":true}}', NULL, '2025-11-22 09:26:24', '2025-11-22 09:26:24'),
(145, 'default', 'created', 'App\\Models\\VehicleMaintenance', 'created', 16, 'App\\Models\\User', 1, '{\"attributes\":{\"vehicle_id\":4,\"maintenance_service_id\":14,\"partner_id\":14,\"incident_id\":null,\"date\":\"2025-11-15T00:00:00.000000Z\",\"cost\":\"2000000.00\",\"mileage\":null,\"description\":\"C\\u00f4ng l\\u00e1i xe\",\"note\":null}}', NULL, '2025-11-22 09:26:24', '2025-11-22 09:26:24'),
(146, 'default', 'created', 'App\\Models\\MaintenanceService', 'created', 15, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"Ph\\u00ed qua tr\\u1ea1m\",\"description\":null,\"is_active\":true}}', NULL, '2025-11-22 09:27:02', '2025-11-22 09:27:02'),
(147, 'default', 'created', 'App\\Models\\Partner', 'created', 15, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"BOT\",\"type\":\"maintenance\",\"phone\":null,\"email\":null,\"address\":null,\"commission_rate\":null,\"note\":null,\"is_active\":true}}', NULL, '2025-11-22 09:27:02', '2025-11-22 09:27:02'),
(148, 'default', 'created', 'App\\Models\\VehicleMaintenance', 'created', 17, 'App\\Models\\User', 1, '{\"attributes\":{\"vehicle_id\":4,\"maintenance_service_id\":15,\"partner_id\":15,\"incident_id\":null,\"date\":\"2025-11-15T00:00:00.000000Z\",\"cost\":\"792315.00\",\"mileage\":null,\"description\":\"Ph\\u00ed qua tr\\u1ea1m BOT\",\"note\":null}}', NULL, '2025-11-22 09:27:02', '2025-11-22 09:27:02'),
(149, 'default', 'created', 'App\\Models\\VehicleMaintenance', 'created', 18, 'App\\Models\\User', 1, '{\"attributes\":{\"vehicle_id\":4,\"maintenance_service_id\":15,\"partner_id\":15,\"incident_id\":null,\"date\":\"2025-11-17T00:00:00.000000Z\",\"cost\":\"362252.00\",\"mileage\":null,\"description\":\"Ph\\u00ed qua tr\\u1ea1m chuy\\u1ec3n vi\\u1ec7n \\u0110L - SG 17\\/11\",\"note\":null}}', NULL, '2025-11-22 09:27:49', '2025-11-22 09:27:49'),
(150, 'default', 'created', 'App\\Models\\MaintenanceService', 'created', 16, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"Ph\\u00ed qua tr\\u1ea1m\",\"description\":null,\"is_active\":true}}', NULL, '2025-11-22 09:28:27', '2025-11-22 09:28:27'),
(151, 'default', 'created', 'App\\Models\\Partner', 'created', 16, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"BOT\",\"type\":\"maintenance\",\"phone\":null,\"email\":null,\"address\":null,\"commission_rate\":null,\"note\":null,\"is_active\":true}}', NULL, '2025-11-22 09:28:27', '2025-11-22 09:28:27'),
(152, 'default', 'created', 'App\\Models\\VehicleMaintenance', 'created', 19, 'App\\Models\\User', 1, '{\"attributes\":{\"vehicle_id\":4,\"maintenance_service_id\":16,\"partner_id\":16,\"incident_id\":null,\"date\":\"2025-11-19T00:00:00.000000Z\",\"cost\":\"870000.00\",\"mileage\":null,\"description\":\"N\\u1ea1p v\\u00e9 th\\u00e1ng tr\\u1ea1m \\u0111\\u1ecbnh an\",\"note\":null}}', NULL, '2025-11-22 09:28:27', '2025-11-22 09:28:27'),
(153, 'default', 'created', 'App\\Models\\MaintenanceService', 'created', 17, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"Ph\\u00ed qua tr\\u1ea1m\",\"description\":null,\"is_active\":true}}', NULL, '2025-11-22 09:29:49', '2025-11-22 09:29:49'),
(154, 'default', 'created', 'App\\Models\\Partner', 'created', 17, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"BOT\",\"type\":\"maintenance\",\"phone\":null,\"email\":null,\"address\":null,\"commission_rate\":null,\"note\":null,\"is_active\":true}}', NULL, '2025-11-22 09:29:49', '2025-11-22 09:29:49'),
(155, 'default', 'created', 'App\\Models\\VehicleMaintenance', 'created', 20, 'App\\Models\\User', 1, '{\"attributes\":{\"vehicle_id\":4,\"maintenance_service_id\":17,\"partner_id\":17,\"incident_id\":null,\"date\":\"2025-11-19T00:00:00.000000Z\",\"cost\":\"1208000.00\",\"mileage\":null,\"description\":\"mua v\\u00e9 th\\u00e1ng qua tr\\u1ea1m li\\u00ean \\u0111\\u1ea7m\",\"note\":null}}', NULL, '2025-11-22 09:29:49', '2025-11-22 09:29:49'),
(156, 'default', 'deleted', 'App\\Models\\VehicleMaintenance', 'deleted', 3, 'App\\Models\\User', 1, '{\"old\":{\"vehicle_id\":1,\"maintenance_service_id\":3,\"partner_id\":2,\"incident_id\":null,\"date\":\"2025-11-22T00:00:00.000000Z\",\"cost\":\"120000.00\",\"mileage\":null,\"description\":null,\"note\":\"B\\u1ea3o tr\\u00ec ph\\u00e1t sinh trong chuy\\u1ebfn \\u0111i\"}}', NULL, '2025-11-22 09:32:39', '2025-11-22 09:32:39'),
(157, 'default', 'deleted', 'App\\Models\\VehicleMaintenance', 'deleted', 4, 'App\\Models\\User', 1, '{\"old\":{\"vehicle_id\":1,\"maintenance_service_id\":4,\"partner_id\":3,\"incident_id\":null,\"date\":\"2025-11-22T00:00:00.000000Z\",\"cost\":\"120000.00\",\"mileage\":null,\"description\":null,\"note\":\"B\\u1ea3o tr\\u00ec ph\\u00e1t sinh trong chuy\\u1ebfn \\u0111i\"}}', NULL, '2025-11-22 09:32:45', '2025-11-22 09:32:45'),
(158, 'default', 'deleted', 'App\\Models\\VehicleMaintenance', 'deleted', 1, 'App\\Models\\User', 1, '{\"old\":{\"vehicle_id\":1,\"maintenance_service_id\":3,\"partner_id\":2,\"incident_id\":null,\"date\":\"2025-11-21T00:00:00.000000Z\",\"cost\":\"120000.00\",\"mileage\":null,\"description\":\"r\\u1eeda xe\",\"note\":null}}', NULL, '2025-11-22 09:32:46', '2025-11-22 09:32:46'),
(159, 'default', 'deleted', 'App\\Models\\VehicleMaintenance', 'deleted', 2, 'App\\Models\\User', 1, '{\"old\":{\"vehicle_id\":2,\"maintenance_service_id\":3,\"partner_id\":2,\"incident_id\":null,\"date\":\"2025-11-21T00:00:00.000000Z\",\"cost\":\"120000.00\",\"mileage\":null,\"description\":null,\"note\":\"B\\u1ea3o tr\\u00ec ph\\u00e1t sinh trong chuy\\u1ebfn \\u0111i\"}}', NULL, '2025-11-22 09:32:51', '2025-11-22 09:32:51'),
(160, 'default', 'created', 'App\\Models\\Transaction', 'created', 49, NULL, NULL, '{\"attributes\":{\"incident_id\":null,\"vehicle_id\":4,\"type\":\"chi\",\"amount\":\"1666000.00\",\"method\":\"cash\",\"note\":\"[B\\u1ea3o tr\\u00ec] V\\u00e9 m\\u00e1y bay \\u0110L - HN - Vietjet air - v\\u00e9 mb cho nh\\u00e2n s\\u1ef1 \\u0111i ra l\\u1ea5y xe\",\"date\":\"2025-11-14T00:00:00.000000Z\"}}', NULL, '2025-11-22 10:10:31', '2025-11-22 10:10:31'),
(161, 'default', 'created', 'App\\Models\\Transaction', 'created', 50, NULL, NULL, '{\"attributes\":{\"incident_id\":null,\"vehicle_id\":4,\"type\":\"chi\",\"amount\":\"518298.00\",\"method\":\"cash\",\"note\":\"[B\\u1ea3o tr\\u00ec] kh\\u00e1ch s\\u1ea1n - C - bone Hotel - ks 1 \\u0111\\u00eam cho l\\u00e1i xe\",\"date\":\"2025-11-07T00:00:00.000000Z\"}}', NULL, '2025-11-22 10:10:31', '2025-11-22 10:10:31'),
(162, 'default', 'created', 'App\\Models\\Transaction', 'created', 51, NULL, NULL, '{\"attributes\":{\"incident_id\":null,\"vehicle_id\":4,\"type\":\"chi\",\"amount\":\"500000.00\",\"method\":\"cash\",\"note\":\"[B\\u1ea3o tr\\u00ec] \\u0111\\u1ed5 d\\u1ea7u - Petrolimex - \\u0110\\u1ed5 d\\u1ea7u HN v\\u1ec1 TQ\",\"date\":\"2025-11-13T00:00:00.000000Z\"}}', NULL, '2025-11-22 10:10:31', '2025-11-22 10:10:31'),
(163, 'default', 'created', 'App\\Models\\Transaction', 'created', 52, NULL, NULL, '{\"attributes\":{\"incident_id\":null,\"vehicle_id\":4,\"type\":\"chi\",\"amount\":\"850000.00\",\"method\":\"cash\",\"note\":\"[B\\u1ea3o tr\\u00ec] Thay nh\\u1edbt - Gara ngo\\u00e0i - Thay nh\\u1edbt l\\u1ea7n \\u0111\\u1ea7u (1600km)\",\"date\":\"2025-11-14T00:00:00.000000Z\"}}', NULL, '2025-11-22 10:10:31', '2025-11-22 10:10:31'),
(164, 'default', 'created', 'App\\Models\\Transaction', 'created', 53, NULL, NULL, '{\"attributes\":{\"incident_id\":null,\"vehicle_id\":4,\"type\":\"chi\",\"amount\":\"450000.00\",\"method\":\"cash\",\"note\":\"[B\\u1ea3o tr\\u00ec] D\\u00e1n xe - C\\u1eeda h\\u00e0ng ngo\\u00e0i - D\\u00e1n logo cty\",\"date\":\"2025-11-14T00:00:00.000000Z\"}}', NULL, '2025-11-22 10:10:31', '2025-11-22 10:10:31'),
(165, 'default', 'created', 'App\\Models\\Transaction', 'created', 54, NULL, NULL, '{\"attributes\":{\"incident_id\":null,\"vehicle_id\":4,\"type\":\"chi\",\"amount\":\"100000.00\",\"method\":\"cash\",\"note\":\"[B\\u1ea3o tr\\u00ec] D\\u00e1n epass - Viettel - D\\u00e1n th\\u1ebb Epass\",\"date\":\"2025-11-14T00:00:00.000000Z\"}}', NULL, '2025-11-22 10:10:31', '2025-11-22 10:10:31'),
(166, 'default', 'created', 'App\\Models\\Transaction', 'created', 55, NULL, NULL, '{\"attributes\":{\"incident_id\":null,\"vehicle_id\":4,\"type\":\"chi\",\"amount\":\"3800000.00\",\"method\":\"cash\",\"note\":\"[B\\u1ea3o tr\\u00ec] L\\u1eafp \\u0111\\u1eb7t thi\\u1ebft b\\u1ecb - Mai Anh GPS - L\\u1eafp cam h\\u00e0nh tr\\u00ecnh, \\u0111\\u1ecbnh v\\u1ecb\",\"date\":\"2025-11-14T00:00:00.000000Z\"}}', NULL, '2025-11-22 10:10:31', '2025-11-22 10:10:31'),
(167, 'default', 'created', 'App\\Models\\Transaction', 'created', 56, NULL, NULL, '{\"attributes\":{\"incident_id\":null,\"vehicle_id\":4,\"type\":\"chi\",\"amount\":\"350000.00\",\"method\":\"cash\",\"note\":\"[B\\u1ea3o tr\\u00ec] \\u00c9p bi\\u1ec3n s\\u1ed1 - Th\\u1ee3 ngo\\u00e0i - \\u00c9p bi\\u1ec3n s\\u1ed1\",\"date\":\"2025-11-14T00:00:00.000000Z\"}}', NULL, '2025-11-22 10:10:31', '2025-11-22 10:10:31'),
(168, 'default', 'created', 'App\\Models\\Transaction', 'created', 57, NULL, NULL, '{\"attributes\":{\"incident_id\":null,\"vehicle_id\":4,\"type\":\"chi\",\"amount\":\"1020000.00\",\"method\":\"cash\",\"note\":\"[B\\u1ea3o tr\\u00ec] \\u0111\\u1ed5 d\\u1ea7u - Petrolimex - \\u0110\\u1ed5 d\\u1ea7u TQ - T\\u00fay Loan\",\"date\":\"2025-11-14T00:00:00.000000Z\"}}', NULL, '2025-11-22 10:10:31', '2025-11-22 10:10:31'),
(169, 'default', 'created', 'App\\Models\\Transaction', 'created', 58, NULL, NULL, '{\"attributes\":{\"incident_id\":null,\"vehicle_id\":4,\"type\":\"chi\",\"amount\":\"1300000.00\",\"method\":\"cash\",\"note\":\"[B\\u1ea3o tr\\u00ec] \\u0111\\u1ed5 d\\u1ea7u - Petrolimex - \\u0110\\u1ed5 d\\u1ea7u T\\u00fay loan - L\\u00e2m \\u0110\\u1ed3ng\",\"date\":\"2025-11-15T00:00:00.000000Z\"}}', NULL, '2025-11-22 10:10:31', '2025-11-22 10:10:31'),
(170, 'default', 'created', 'App\\Models\\Transaction', 'created', 59, NULL, NULL, '{\"attributes\":{\"incident_id\":null,\"vehicle_id\":4,\"type\":\"chi\",\"amount\":\"500000.00\",\"method\":\"cash\",\"note\":\"[B\\u1ea3o tr\\u00ec] \\u0102n u\\u1ed1ng - Qu\\u00e1n c\\u01a1m - \\u0102n u\\u1ed1ng \\u0111i \\u0111\\u01b0\\u1eddng ship xe v\\u00e0o\",\"date\":\"2025-11-15T00:00:00.000000Z\"}}', NULL, '2025-11-22 10:10:31', '2025-11-22 10:10:31'),
(171, 'default', 'created', 'App\\Models\\Transaction', 'created', 60, NULL, NULL, '{\"attributes\":{\"incident_id\":null,\"vehicle_id\":4,\"type\":\"chi\",\"amount\":\"2000000.00\",\"method\":\"cash\",\"note\":\"[B\\u1ea3o tr\\u00ec] C\\u00f4ng t\\u00e0i - L\\u00e1i xe - C\\u00f4ng l\\u00e1i xe\",\"date\":\"2025-11-15T00:00:00.000000Z\"}}', NULL, '2025-11-22 10:10:31', '2025-11-22 10:10:31'),
(172, 'default', 'created', 'App\\Models\\Transaction', 'created', 61, NULL, NULL, '{\"attributes\":{\"incident_id\":null,\"vehicle_id\":4,\"type\":\"chi\",\"amount\":\"792315.00\",\"method\":\"cash\",\"note\":\"[B\\u1ea3o tr\\u00ec] Ph\\u00ed qua tr\\u1ea1m - BOT - Ph\\u00ed qua tr\\u1ea1m BOT\",\"date\":\"2025-11-15T00:00:00.000000Z\"}}', NULL, '2025-11-22 10:10:31', '2025-11-22 10:10:31'),
(173, 'default', 'created', 'App\\Models\\Transaction', 'created', 62, NULL, NULL, '{\"attributes\":{\"incident_id\":null,\"vehicle_id\":4,\"type\":\"chi\",\"amount\":\"362252.00\",\"method\":\"cash\",\"note\":\"[B\\u1ea3o tr\\u00ec] Ph\\u00ed qua tr\\u1ea1m - BOT - Ph\\u00ed qua tr\\u1ea1m chuy\\u1ec3n vi\\u1ec7n \\u0110L - SG 17\\/11\",\"date\":\"2025-11-17T00:00:00.000000Z\"}}', NULL, '2025-11-22 10:10:31', '2025-11-22 10:10:31'),
(174, 'default', 'created', 'App\\Models\\Transaction', 'created', 63, NULL, NULL, '{\"attributes\":{\"incident_id\":null,\"vehicle_id\":4,\"type\":\"chi\",\"amount\":\"870000.00\",\"method\":\"cash\",\"note\":\"[B\\u1ea3o tr\\u00ec] Ph\\u00ed qua tr\\u1ea1m - BOT - N\\u1ea1p v\\u00e9 th\\u00e1ng tr\\u1ea1m \\u0111\\u1ecbnh an\",\"date\":\"2025-11-19T00:00:00.000000Z\"}}', NULL, '2025-11-22 10:10:31', '2025-11-22 10:10:31'),
(175, 'default', 'created', 'App\\Models\\Transaction', 'created', 64, NULL, NULL, '{\"attributes\":{\"incident_id\":null,\"vehicle_id\":4,\"type\":\"chi\",\"amount\":\"1208000.00\",\"method\":\"cash\",\"note\":\"[B\\u1ea3o tr\\u00ec] Ph\\u00ed qua tr\\u1ea1m - BOT - mua v\\u00e9 th\\u00e1ng qua tr\\u1ea1m li\\u00ean \\u0111\\u1ea7m\",\"date\":\"2025-11-19T00:00:00.000000Z\"}}', NULL, '2025-11-22 10:10:31', '2025-11-22 10:10:31'),
(176, 'default', 'deleted', 'App\\Models\\Incident', 'deleted', 5, 'App\\Models\\User', 1, '{\"old\":{\"vehicle_id\":4,\"patient_id\":1,\"date\":\"2025-11-22T11:58:00.000000Z\",\"dispatch_by\":1,\"destination\":null,\"from_location_id\":6,\"to_location_id\":1,\"partner_id\":1,\"commission_amount\":\"500000.00\",\"summary\":null,\"tags\":null}}', NULL, '2025-11-22 10:25:31', '2025-11-22 10:25:31'),
(177, 'default', 'deleted', 'App\\Models\\Incident', 'deleted', 6, 'App\\Models\\User', 1, '{\"old\":{\"vehicle_id\":4,\"patient_id\":3,\"date\":\"2025-11-21T12:00:00.000000Z\",\"dispatch_by\":1,\"destination\":null,\"from_location_id\":6,\"to_location_id\":2,\"partner_id\":null,\"commission_amount\":null,\"summary\":null,\"tags\":null}}', NULL, '2025-11-22 10:25:33', '2025-11-22 10:25:33'),
(178, 'default', 'created', 'App\\Models\\Partner', 'created', 18, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"Khoa HSTC - BV \\u0110KL\\u0110\",\"type\":\"collaborator\",\"phone\":null,\"email\":null,\"address\":null,\"commission_rate\":null,\"note\":null,\"is_active\":true}}', NULL, '2025-11-22 10:28:45', '2025-11-22 10:28:45'),
(179, 'default', 'created', 'App\\Models\\Partner', 'created', 19, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"Khoa n\\u1ed9i B - BV \\u0110KL\\u0110\",\"type\":\"collaborator\",\"phone\":null,\"email\":null,\"address\":null,\"commission_rate\":null,\"note\":null,\"is_active\":true}}', NULL, '2025-11-22 10:29:07', '2025-11-22 10:29:07'),
(180, 'default', 'created', 'App\\Models\\Partner', 'created', 20, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"Khoa Ngo\\u1ea1i TK - BV \\u0110KL\\u0110\",\"type\":\"collaborator\",\"phone\":null,\"email\":null,\"address\":null,\"commission_rate\":null,\"note\":null,\"is_active\":true}}', NULL, '2025-11-22 10:29:24', '2025-11-22 10:29:24'),
(181, 'default', 'created', 'App\\Models\\Partner', 'created', 21, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"Khoa Ngo\\u1ea1i CT - BV \\u0110KL\\u0110\",\"type\":\"collaborator\",\"phone\":null,\"email\":null,\"address\":null,\"commission_rate\":null,\"note\":null,\"is_active\":true}}', NULL, '2025-11-22 10:29:34', '2025-11-22 10:29:34'),
(182, 'default', 'created', 'App\\Models\\Partner', 'created', 22, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"kKhoa Ngo\\u1ea1i TH - BV \\u0110KL\\u0110\",\"type\":\"collaborator\",\"phone\":null,\"email\":null,\"address\":null,\"commission_rate\":null,\"note\":null,\"is_active\":true}}', NULL, '2025-11-22 10:29:56', '2025-11-22 10:29:56'),
(183, 'default', 'created', 'App\\Models\\Partner', 'created', 23, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"Khoa Ph\\u1ed5i - BV \\u0110KL\\u0110\",\"type\":\"collaborator\",\"phone\":null,\"email\":null,\"address\":null,\"commission_rate\":null,\"note\":null,\"is_active\":true}}', NULL, '2025-11-22 10:30:04', '2025-11-22 10:30:04'),
(184, 'default', 'created', 'App\\Models\\Partner', 'created', 24, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"Khoa Ung b\\u01b0\\u1edbu - BV \\u0110KL\\u0110\",\"type\":\"collaborator\",\"phone\":null,\"email\":null,\"address\":null,\"commission_rate\":null,\"note\":null,\"is_active\":true}}', NULL, '2025-11-22 10:30:14', '2025-11-22 10:30:14'),
(185, 'default', 'created', 'App\\Models\\Partner', 'created', 25, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"Khoa C\\u1ea5p c\\u1ee9u - BV \\u0110KL\\u0110\",\"type\":\"collaborator\",\"phone\":null,\"email\":null,\"address\":null,\"commission_rate\":null,\"note\":null,\"is_active\":true}}', NULL, '2025-11-22 10:30:23', '2025-11-22 10:30:23'),
(186, 'default', 'created', 'App\\Models\\Partner', 'created', 26, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"\\u0110\\u1ee9c Anh - khoa HSTC  - BV \\u0110KL\\u0110\",\"type\":\"collaborator\",\"phone\":null,\"email\":null,\"address\":null,\"commission_rate\":null,\"note\":null,\"is_active\":true}}', NULL, '2025-11-22 10:30:51', '2025-11-22 10:30:51'),
(187, 'default', 'created', 'App\\Models\\Location', 'created', 10, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"Khoa HSTC - BV \\u0110KL\\u0110\",\"type\":\"from\",\"address\":null,\"note\":null,\"is_active\":true}}', NULL, '2025-11-22 10:38:48', '2025-11-22 10:38:48'),
(188, 'default', 'created', 'App\\Models\\Location', 'created', 11, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"\\u0110\\u1ee9c Tr\\u1ecdng\",\"type\":\"to\",\"address\":null,\"note\":null,\"is_active\":true}}', NULL, '2025-11-22 10:38:48', '2025-11-22 10:38:48'),
(189, 'default', 'created', 'App\\Models\\Patient', 'created', 4, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"pham trong nghia\",\"birth_year\":1989,\"phone\":\"0985108558\",\"gender\":\"male\",\"address\":null}}', NULL, '2025-11-22 10:38:48', '2025-11-22 10:38:48'),
(190, 'default', 'created', 'App\\Models\\Incident', 'created', 7, 'App\\Models\\User', 1, '{\"attributes\":{\"vehicle_id\":2,\"patient_id\":4,\"date\":\"2025-11-01T17:36:00.000000Z\",\"dispatch_by\":1,\"destination\":null,\"from_location_id\":10,\"to_location_id\":11,\"partner_id\":26,\"commission_amount\":\"150000.00\",\"summary\":null,\"tags\":null}}', NULL, '2025-11-22 10:38:48', '2025-11-22 10:38:48'),
(191, 'default', 'created', 'App\\Models\\Transaction', 'created', 65, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":7,\"vehicle_id\":2,\"type\":\"chi\",\"amount\":\"250000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng l\\u00e1i xe: Nguy\\u1ec5n C\\u1eefu Ninh\",\"date\":\"2025-11-01T17:36:00.000000Z\"}}', NULL, '2025-11-22 10:38:48', '2025-11-22 10:38:48'),
(192, 'default', 'created', 'App\\Models\\Transaction', 'created', 66, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":7,\"vehicle_id\":2,\"type\":\"chi\",\"amount\":\"250000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng nh\\u00e2n vi\\u00ean y t\\u1ebf: Nguy\\u1ec5n Qu\\u1ed1c V\\u0169\",\"date\":\"2025-11-01T17:36:00.000000Z\"}}', NULL, '2025-11-22 10:38:48', '2025-11-22 10:38:48'),
(193, 'default', 'created', 'App\\Models\\Transaction', 'created', 67, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":7,\"vehicle_id\":2,\"type\":\"thu\",\"amount\":\"1500000.00\",\"method\":\"cash\",\"note\":\"Thu chuy\\u1ebfn \\u0111i\",\"date\":\"2025-11-01T17:36:00.000000Z\"}}', NULL, '2025-11-22 10:38:48', '2025-11-22 10:38:48'),
(194, 'default', 'created', 'App\\Models\\Transaction', 'created', 68, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":7,\"vehicle_id\":2,\"type\":\"chi\",\"amount\":\"150000.00\",\"method\":\"cash\",\"note\":\"Hoa h\\u1ed3ng: \\u0110\\u1ee9c Anh - khoa HSTC  - BV \\u0110KL\\u0110\",\"date\":\"2025-11-01T17:36:00.000000Z\"}}', NULL, '2025-11-22 10:38:48', '2025-11-22 10:38:48'),
(195, 'default', 'created', 'App\\Models\\Patient', 'created', 5, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"Kh\\u00f4ng c\\u00f3 t\\u00ean\",\"birth_year\":null,\"phone\":\"0386927627\",\"gender\":\"male\",\"address\":null}}', NULL, '2025-11-22 10:43:13', '2025-11-22 10:43:13'),
(196, 'default', 'created', 'App\\Models\\Incident', 'created', 8, 'App\\Models\\User', 1, '{\"attributes\":{\"vehicle_id\":1,\"patient_id\":5,\"date\":\"2025-11-22T17:38:00.000000Z\",\"dispatch_by\":1,\"destination\":null,\"from_location_id\":11,\"to_location_id\":6,\"partner_id\":null,\"commission_amount\":null,\"summary\":null,\"tags\":null}}', NULL, '2025-11-22 10:43:13', '2025-11-22 10:43:13'),
(197, 'default', 'created', 'App\\Models\\Transaction', 'created', 69, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":8,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"200000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng l\\u00e1i xe: L\\u00ea Phong\",\"date\":\"2025-11-22T17:38:00.000000Z\"}}', NULL, '2025-11-22 10:43:13', '2025-11-22 10:43:13'),
(198, 'default', 'created', 'App\\Models\\Transaction', 'created', 70, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":8,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"500000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n d\\u1ea7u\",\"date\":\"2025-11-22T17:38:00.000000Z\"}}', NULL, '2025-11-22 10:43:13', '2025-11-22 10:43:13'),
(199, 'default', 'created', 'App\\Models\\Transaction', 'created', 71, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":null,\"vehicle_id\":null,\"type\":\"chi\",\"amount\":\"100000.00\",\"method\":\"cash\",\"note\":\"L\\u00e0m ch\\u00eca kho\\u00e1 v\\u00e0o c\\u1ed5ng BV \\u0111a khoa\",\"date\":\"2025-11-02T17:44:00.000000Z\"}}', NULL, '2025-11-22 10:44:41', '2025-11-22 10:44:41'),
(200, 'default', 'updated', 'App\\Models\\Incident', 'updated', 8, 'App\\Models\\User', 1, '{\"attributes\":{\"date\":\"2025-11-02T17:38:00.000000Z\"},\"old\":{\"date\":\"2025-11-22T17:38:00.000000Z\"}}', NULL, '2025-11-22 10:45:03', '2025-11-22 10:45:03'),
(201, 'default', 'created', 'App\\Models\\Transaction', 'created', 72, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":8,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"200000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng: \",\"date\":\"2025-11-02T17:38:00.000000Z\"}}', NULL, '2025-11-22 10:45:03', '2025-11-22 10:45:03'),
(202, 'default', 'deleted', 'App\\Models\\Incident', 'deleted', 8, 'App\\Models\\User', 1, '{\"old\":{\"vehicle_id\":1,\"patient_id\":5,\"date\":\"2025-11-02T17:38:00.000000Z\",\"dispatch_by\":1,\"destination\":null,\"from_location_id\":11,\"to_location_id\":6,\"partner_id\":null,\"commission_amount\":null,\"summary\":null,\"tags\":null}}', NULL, '2025-11-22 10:46:20', '2025-11-22 10:46:20'),
(203, 'default', 'created', 'App\\Models\\Incident', 'created', 9, 'App\\Models\\User', 1, '{\"attributes\":{\"vehicle_id\":1,\"patient_id\":5,\"date\":\"2025-11-02T17:43:00.000000Z\",\"dispatch_by\":1,\"destination\":null,\"from_location_id\":11,\"to_location_id\":6,\"partner_id\":null,\"commission_amount\":null,\"summary\":null,\"tags\":null}}', NULL, '2025-11-22 10:47:25', '2025-11-22 10:47:25'),
(204, 'default', 'created', 'App\\Models\\Transaction', 'created', 73, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":9,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"200000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng l\\u00e1i xe: L\\u00ea Phong\",\"date\":\"2025-11-02T17:43:00.000000Z\"}}', NULL, '2025-11-22 10:47:25', '2025-11-22 10:47:25'),
(205, 'default', 'created', 'App\\Models\\Transaction', 'created', 74, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":9,\"vehicle_id\":1,\"type\":\"thu\",\"amount\":\"1500000.00\",\"method\":\"cash\",\"note\":\"Thu chuy\\u1ebfn \\u0111i\",\"date\":\"2025-11-02T17:43:00.000000Z\"}}', NULL, '2025-11-22 10:47:25', '2025-11-22 10:47:25'),
(206, 'default', 'created', 'App\\Models\\Transaction', 'created', 75, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":9,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"500000.00\",\"method\":\"cash\",\"note\":\"\\u0110\\u1ed5 d\\u1ea7u\",\"date\":\"2025-11-02T17:43:00.000000Z\"}}', NULL, '2025-11-22 10:47:25', '2025-11-22 10:47:25'),
(207, 'default', 'created', 'App\\Models\\Location', 'created', 12, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"\\u0110am R\\u00f4ng\",\"type\":\"to\",\"address\":null,\"note\":null,\"is_active\":true}}', NULL, '2025-11-22 10:50:14', '2025-11-22 10:50:14'),
(208, 'default', 'created', 'App\\Models\\Patient', 'created', 6, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"Li\\u00eang hot ha khi\\u00eam\",\"birth_year\":1972,\"phone\":\"0325692873\",\"gender\":\"male\",\"address\":null}}', NULL, '2025-11-22 10:50:14', '2025-11-22 10:50:14'),
(209, 'default', 'created', 'App\\Models\\Incident', 'created', 10, 'App\\Models\\User', 1, '{\"attributes\":{\"vehicle_id\":1,\"patient_id\":6,\"date\":\"2025-11-02T17:47:00.000000Z\",\"dispatch_by\":1,\"destination\":null,\"from_location_id\":10,\"to_location_id\":12,\"partner_id\":26,\"commission_amount\":\"150000.00\",\"summary\":null,\"tags\":null}}', NULL, '2025-11-22 10:50:14', '2025-11-22 10:50:14'),
(210, 'default', 'created', 'App\\Models\\Transaction', 'created', 76, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":10,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"450000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng l\\u00e1i xe: L\\u00ea Phong\",\"date\":\"2025-11-02T17:47:00.000000Z\"}}', NULL, '2025-11-22 10:50:14', '2025-11-22 10:50:14'),
(211, 'default', 'created', 'App\\Models\\Transaction', 'created', 77, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":10,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"450000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng nh\\u00e2n vi\\u00ean y t\\u1ebf: Nguy\\u1ec5n Qu\\u1ed1c V\\u0169\",\"date\":\"2025-11-02T17:47:00.000000Z\"}}', NULL, '2025-11-22 10:50:14', '2025-11-22 10:50:14'),
(212, 'default', 'created', 'App\\Models\\Transaction', 'created', 78, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":10,\"vehicle_id\":1,\"type\":\"thu\",\"amount\":\"3000000.00\",\"method\":\"cash\",\"note\":\"Thu chuy\\u1ebfn \\u0111i\",\"date\":\"2025-11-02T17:47:00.000000Z\"}}', NULL, '2025-11-22 10:50:14', '2025-11-22 10:50:14'),
(213, 'default', 'created', 'App\\Models\\Transaction', 'created', 79, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":10,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"700000.00\",\"method\":\"cash\",\"note\":\"D\\u1ea7u\",\"date\":\"2025-11-02T17:47:00.000000Z\"}}', NULL, '2025-11-22 10:50:14', '2025-11-22 10:50:14'),
(214, 'default', 'created', 'App\\Models\\Transaction', 'created', 80, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":10,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"150000.00\",\"method\":\"cash\",\"note\":\"Hoa h\\u1ed3ng: \\u0110\\u1ee9c Anh - khoa HSTC  - BV \\u0110KL\\u0110\",\"date\":\"2025-11-02T17:47:00.000000Z\"}}', NULL, '2025-11-22 10:50:14', '2025-11-22 10:50:14'),
(215, 'default', 'updated', 'App\\Models\\Incident', 'updated', 10, 'App\\Models\\User', 1, '{\"attributes\":{\"date\":\"2025-11-03T17:47:00.000000Z\"},\"old\":{\"date\":\"2025-11-02T17:47:00.000000Z\"}}', NULL, '2025-11-22 10:52:47', '2025-11-22 10:52:47'),
(216, 'default', 'created', 'App\\Models\\Transaction', 'created', 81, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":10,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"450000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng: \",\"date\":\"2025-11-03T17:47:00.000000Z\"}}', NULL, '2025-11-22 10:52:47', '2025-11-22 10:52:47'),
(217, 'default', 'created', 'App\\Models\\Transaction', 'created', 82, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":10,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"450000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng: \",\"date\":\"2025-11-03T17:47:00.000000Z\"}}', NULL, '2025-11-22 10:52:47', '2025-11-22 10:52:47'),
(218, 'default', 'created', 'App\\Models\\Transaction', 'created', 83, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":10,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"150000.00\",\"method\":\"cash\",\"note\":\"Hoa h\\u1ed3ng: \\u0110\\u1ee9c Anh - khoa HSTC  - BV \\u0110KL\\u0110\",\"date\":\"2025-11-03T17:47:00.000000Z\"}}', NULL, '2025-11-22 10:52:47', '2025-11-22 10:52:47'),
(221, 'default', 'created', 'App\\Models\\Location', 'created', 14, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"V\\u00f5 Tr\\u01b0\\u1eddng To\\u1ea3n - \\u0110\\u00e0 L\\u1ea1t\",\"type\":\"from\",\"address\":null,\"note\":null,\"is_active\":true}}', NULL, '2025-11-22 10:58:48', '2025-11-22 10:58:48'),
(222, 'default', 'created', 'App\\Models\\Patient', 'created', 8, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"Kh\\u00f4ng c\\u00f3 t\\u00ean\",\"birth_year\":null,\"phone\":null,\"gender\":\"male\",\"address\":null}}', NULL, '2025-11-22 10:58:48', '2025-11-22 10:58:48'),
(223, 'default', 'created', 'App\\Models\\Incident', 'created', 11, 'App\\Models\\User', 1, '{\"attributes\":{\"vehicle_id\":2,\"patient_id\":8,\"date\":\"2025-11-03T17:50:00.000000Z\",\"dispatch_by\":1,\"destination\":null,\"from_location_id\":14,\"to_location_id\":6,\"partner_id\":null,\"commission_amount\":null,\"summary\":null,\"tags\":null}}', NULL, '2025-11-22 10:58:48', '2025-11-22 10:58:48'),
(224, 'default', 'created', 'App\\Models\\Transaction', 'created', 84, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":11,\"vehicle_id\":2,\"type\":\"chi\",\"amount\":\"200000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng nh\\u00e2n vi\\u00ean y t\\u1ebf: L\\u00e2m\",\"date\":\"2025-11-03T17:50:00.000000Z\"}}', NULL, '2025-11-22 10:58:48', '2025-11-22 10:58:48'),
(225, 'default', 'created', 'App\\Models\\Transaction', 'created', 85, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":11,\"vehicle_id\":2,\"type\":\"thu\",\"amount\":\"600000.00\",\"method\":\"cash\",\"note\":\"Thu chuy\\u1ebfn \\u0111i\",\"date\":\"2025-11-03T17:50:00.000000Z\"}}', NULL, '2025-11-22 10:58:48', '2025-11-22 10:58:48'),
(226, 'default', 'created', 'App\\Models\\Location', 'created', 15, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"L\\u00e2m H\\u00e0\",\"type\":\"to\",\"address\":null,\"note\":null,\"is_active\":true}}', NULL, '2025-11-22 11:00:30', '2025-11-22 11:00:30'),
(227, 'default', 'created', 'App\\Models\\Patient', 'created', 9, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"ngo hai\",\"birth_year\":1968,\"phone\":\"0365991068\",\"gender\":null,\"address\":null}}', NULL, '2025-11-22 11:00:30', '2025-11-22 11:00:30'),
(228, 'default', 'created', 'App\\Models\\Incident', 'created', 12, 'App\\Models\\User', 1, '{\"attributes\":{\"vehicle_id\":2,\"patient_id\":9,\"date\":\"2025-11-03T17:58:00.000000Z\",\"dispatch_by\":1,\"destination\":null,\"from_location_id\":10,\"to_location_id\":15,\"partner_id\":26,\"commission_amount\":\"150000.00\",\"summary\":null,\"tags\":null}}', NULL, '2025-11-22 11:00:30', '2025-11-22 11:00:30'),
(229, 'default', 'created', 'App\\Models\\Transaction', 'created', 86, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":12,\"vehicle_id\":2,\"type\":\"chi\",\"amount\":\"300000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng l\\u00e1i xe: Nguy\\u1ec5n C\\u1eefu Ninh\",\"date\":\"2025-11-03T17:58:00.000000Z\"}}', NULL, '2025-11-22 11:00:30', '2025-11-22 11:00:30'),
(230, 'default', 'created', 'App\\Models\\Transaction', 'created', 87, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":12,\"vehicle_id\":2,\"type\":\"chi\",\"amount\":\"300000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng nh\\u00e2n vi\\u00ean y t\\u1ebf: L\\u00e2m\",\"date\":\"2025-11-03T17:58:00.000000Z\"}}', NULL, '2025-11-22 11:00:30', '2025-11-22 11:00:30'),
(231, 'default', 'created', 'App\\Models\\Transaction', 'created', 88, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":12,\"vehicle_id\":2,\"type\":\"thu\",\"amount\":\"1700000.00\",\"method\":\"cash\",\"note\":\"Thu chuy\\u1ebfn \\u0111i\",\"date\":\"2025-11-03T17:58:00.000000Z\"}}', NULL, '2025-11-22 11:00:30', '2025-11-22 11:00:30'),
(232, 'default', 'created', 'App\\Models\\Transaction', 'created', 89, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":12,\"vehicle_id\":2,\"type\":\"chi\",\"amount\":\"500000.00\",\"method\":\"cash\",\"note\":\"D\\u1ea7u\",\"date\":\"2025-11-03T17:58:00.000000Z\"}}', NULL, '2025-11-22 11:00:30', '2025-11-22 11:00:30'),
(233, 'default', 'created', 'App\\Models\\Transaction', 'created', 90, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":12,\"vehicle_id\":2,\"type\":\"chi\",\"amount\":\"150000.00\",\"method\":\"cash\",\"note\":\"Hoa h\\u1ed3ng: \\u0110\\u1ee9c Anh - khoa HSTC  - BV \\u0110KL\\u0110\",\"date\":\"2025-11-03T17:58:00.000000Z\"}}', NULL, '2025-11-22 11:00:30', '2025-11-22 11:00:30'),
(234, 'default', 'created', 'App\\Models\\Patient', 'created', 10, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"L\\u00ea \\u0110\\u01b0\\u1edbc C\\u01b0\\u1eddng\",\"birth_year\":1935,\"phone\":\"0386122168\",\"gender\":\"male\",\"address\":null}}', NULL, '2025-11-22 11:01:42', '2025-11-22 11:01:42'),
(235, 'default', 'created', 'App\\Models\\Incident', 'created', 13, 'App\\Models\\User', 1, '{\"attributes\":{\"vehicle_id\":1,\"patient_id\":10,\"date\":\"2025-11-04T18:00:00.000000Z\",\"dispatch_by\":1,\"destination\":null,\"from_location_id\":null,\"to_location_id\":null,\"partner_id\":26,\"commission_amount\":\"150000.00\",\"summary\":null,\"tags\":null}}', NULL, '2025-11-22 11:01:42', '2025-11-22 11:01:42'),
(236, 'default', 'created', 'App\\Models\\Transaction', 'created', 91, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":13,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"300000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng l\\u00e1i xe: L\\u00ea Phong\",\"date\":\"2025-11-04T18:00:00.000000Z\"}}', NULL, '2025-11-22 11:01:42', '2025-11-22 11:01:42'),
(237, 'default', 'created', 'App\\Models\\Transaction', 'created', 92, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":13,\"vehicle_id\":1,\"type\":\"thu\",\"amount\":\"1800000.00\",\"method\":\"cash\",\"note\":\"Thu chuy\\u1ebfn \\u0111i\",\"date\":\"2025-11-04T18:00:00.000000Z\"}}', NULL, '2025-11-22 11:01:42', '2025-11-22 11:01:42'),
(238, 'default', 'created', 'App\\Models\\Transaction', 'created', 93, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":13,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"150000.00\",\"method\":\"cash\",\"note\":\"Hoa h\\u1ed3ng: \\u0110\\u1ee9c Anh - khoa HSTC  - BV \\u0110KL\\u0110\",\"date\":\"2025-11-04T18:00:00.000000Z\"}}', NULL, '2025-11-22 11:01:42', '2025-11-22 11:01:42'),
(239, 'default', 'created', 'App\\Models\\Location', 'created', 16, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"Ka \\u0110\\u00f4 - \\u0110\\u01a1n D\\u01b0\\u01a1ng\",\"type\":\"to\",\"address\":null,\"note\":null,\"is_active\":true}}', NULL, '2025-11-22 11:03:46', '2025-11-22 11:03:46'),
(240, 'default', 'created', 'App\\Models\\Patient', 'created', 11, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"Ya Nh\\u00e2t\",\"birth_year\":1970,\"phone\":\"0862833804\",\"gender\":\"male\",\"address\":null}}', NULL, '2025-11-22 11:03:46', '2025-11-22 11:03:46'),
(241, 'default', 'created', 'App\\Models\\Incident', 'created', 14, 'App\\Models\\User', 1, '{\"attributes\":{\"vehicle_id\":1,\"patient_id\":11,\"date\":\"2025-11-05T18:01:00.000000Z\",\"dispatch_by\":1,\"destination\":null,\"from_location_id\":10,\"to_location_id\":16,\"partner_id\":26,\"commission_amount\":\"150000.00\",\"summary\":null,\"tags\":null}}', NULL, '2025-11-22 11:03:46', '2025-11-22 11:03:46'),
(242, 'default', 'created', 'App\\Models\\Transaction', 'created', 94, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":14,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"300000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng l\\u00e1i xe: L\\u00ea Phong\",\"date\":\"2025-11-05T18:01:00.000000Z\"}}', NULL, '2025-11-22 11:03:46', '2025-11-22 11:03:46'),
(243, 'default', 'created', 'App\\Models\\Transaction', 'created', 95, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":14,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"300000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng nh\\u00e2n vi\\u00ean y t\\u1ebf: Nguy\\u1ec5n Qu\\u1ed1c V\\u0169\",\"date\":\"2025-11-05T18:01:00.000000Z\"}}', NULL, '2025-11-22 11:03:46', '2025-11-22 11:03:46'),
(244, 'default', 'created', 'App\\Models\\Transaction', 'created', 96, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":14,\"vehicle_id\":1,\"type\":\"thu\",\"amount\":\"2000000.00\",\"method\":\"cash\",\"note\":\"Thu chuy\\u1ebfn \\u0111i\",\"date\":\"2025-11-05T18:01:00.000000Z\"}}', NULL, '2025-11-22 11:03:46', '2025-11-22 11:03:46'),
(245, 'default', 'created', 'App\\Models\\Transaction', 'created', 97, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":14,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"150000.00\",\"method\":\"cash\",\"note\":\"Hoa h\\u1ed3ng: \\u0110\\u1ee9c Anh - khoa HSTC  - BV \\u0110KL\\u0110\",\"date\":\"2025-11-05T18:01:00.000000Z\"}}', NULL, '2025-11-22 11:03:46', '2025-11-22 11:03:46'),
(246, 'default', 'created', 'App\\Models\\Location', 'created', 17, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"\\u0110\\u01a1n D\\u01b0\\u01a1ng\",\"type\":\"to\",\"address\":null,\"note\":null,\"is_active\":true}}', NULL, '2025-11-22 11:05:55', '2025-11-22 11:05:55'),
(247, 'default', 'created', 'App\\Models\\Patient', 'created', 12, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"le thi th\\u01b0a\",\"birth_year\":1935,\"phone\":\"0985714494\",\"gender\":\"female\",\"address\":null}}', NULL, '2025-11-22 11:05:55', '2025-11-22 11:05:55'),
(248, 'default', 'created', 'App\\Models\\Incident', 'created', 15, 'App\\Models\\User', 1, '{\"attributes\":{\"vehicle_id\":1,\"patient_id\":12,\"date\":\"2025-11-08T18:03:00.000000Z\",\"dispatch_by\":1,\"destination\":null,\"from_location_id\":10,\"to_location_id\":17,\"partner_id\":26,\"commission_amount\":\"150000.00\",\"summary\":null,\"tags\":null}}', NULL, '2025-11-22 11:05:55', '2025-11-22 11:05:55'),
(249, 'default', 'created', 'App\\Models\\Transaction', 'created', 98, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":15,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"300000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng l\\u00e1i xe: Nguy\\u1ec5n C\\u1eefu Ninh\",\"date\":\"2025-11-08T18:03:00.000000Z\"}}', NULL, '2025-11-22 11:05:55', '2025-11-22 11:05:55'),
(250, 'default', 'created', 'App\\Models\\Transaction', 'created', 99, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":15,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"300000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng nh\\u00e2n vi\\u00ean y t\\u1ebf: Nguy\\u1ec5n Qu\\u1ed1c V\\u0169\",\"date\":\"2025-11-08T18:03:00.000000Z\"}}', NULL, '2025-11-22 11:05:55', '2025-11-22 11:05:55'),
(251, 'default', 'created', 'App\\Models\\Transaction', 'created', 100, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":15,\"vehicle_id\":1,\"type\":\"thu\",\"amount\":\"1800000.00\",\"method\":\"cash\",\"note\":\"Thu chuy\\u1ebfn \\u0111i\",\"date\":\"2025-11-08T18:03:00.000000Z\"}}', NULL, '2025-11-22 11:05:55', '2025-11-22 11:05:55'),
(252, 'default', 'created', 'App\\Models\\Transaction', 'created', 101, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":15,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"150000.00\",\"method\":\"cash\",\"note\":\"Hoa h\\u1ed3ng: \\u0110\\u1ee9c Anh - khoa HSTC  - BV \\u0110KL\\u0110\",\"date\":\"2025-11-08T18:03:00.000000Z\"}}', NULL, '2025-11-22 11:05:55', '2025-11-22 11:05:55'),
(253, 'default', 'created', 'App\\Models\\Location', 'created', 18, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"Li\\u00ean \\u0110\\u1ea7m - Di Linh\",\"type\":\"to\",\"address\":null,\"note\":null,\"is_active\":true}}', NULL, '2025-11-22 11:08:46', '2025-11-22 11:08:46'),
(254, 'default', 'created', 'App\\Models\\Patient', 'created', 13, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"ka Breis\",\"birth_year\":1965,\"phone\":\"0362720760\",\"gender\":\"female\",\"address\":null}}', NULL, '2025-11-22 11:08:46', '2025-11-22 11:08:46'),
(255, 'default', 'created', 'App\\Models\\Incident', 'created', 16, 'App\\Models\\User', 1, '{\"attributes\":{\"vehicle_id\":1,\"patient_id\":13,\"date\":\"2025-11-10T18:05:00.000000Z\",\"dispatch_by\":1,\"destination\":null,\"from_location_id\":10,\"to_location_id\":18,\"partner_id\":26,\"commission_amount\":\"150000.00\",\"summary\":null,\"tags\":null}}', NULL, '2025-11-22 11:08:46', '2025-11-22 11:08:46'),
(256, 'default', 'created', 'App\\Models\\Transaction', 'created', 102, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":16,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"450000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng l\\u00e1i xe: L\\u00ea Phong\",\"date\":\"2025-11-10T18:05:00.000000Z\"}}', NULL, '2025-11-22 11:08:46', '2025-11-22 11:08:46'),
(257, 'default', 'created', 'App\\Models\\Transaction', 'created', 103, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":16,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"500000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng nh\\u00e2n vi\\u00ean y t\\u1ebf: Nguy\\u1ec5n Qu\\u1ed1c V\\u0169\",\"date\":\"2025-11-10T18:05:00.000000Z\"}}', NULL, '2025-11-22 11:08:46', '2025-11-22 11:08:46'),
(258, 'default', 'created', 'App\\Models\\Transaction', 'created', 104, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":16,\"vehicle_id\":1,\"type\":\"thu\",\"amount\":\"3200000.00\",\"method\":\"cash\",\"note\":\"Thu chuy\\u1ebfn \\u0111i\",\"date\":\"2025-11-10T18:05:00.000000Z\"}}', NULL, '2025-11-22 11:08:46', '2025-11-22 11:08:46'),
(259, 'default', 'created', 'App\\Models\\Transaction', 'created', 105, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":16,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"120000.00\",\"method\":\"cash\",\"note\":\"R\\u1eeda xe\",\"date\":\"2025-11-10T18:05:00.000000Z\"}}', NULL, '2025-11-22 11:08:46', '2025-11-22 11:08:46'),
(260, 'default', 'created', 'App\\Models\\Transaction', 'created', 106, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":16,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"150000.00\",\"method\":\"cash\",\"note\":\"Hoa h\\u1ed3ng: \\u0110\\u1ee9c Anh - khoa HSTC  - BV \\u0110KL\\u0110\",\"date\":\"2025-11-10T18:05:00.000000Z\"}}', NULL, '2025-11-22 11:08:46', '2025-11-22 11:08:46'),
(261, 'default', 'created', 'App\\Models\\Location', 'created', 19, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"B\\u1ea3o L\\u1ed9c\",\"type\":\"to\",\"address\":null,\"note\":null,\"is_active\":true}}', NULL, '2025-11-22 11:11:05', '2025-11-22 11:11:05'),
(262, 'default', 'created', 'App\\Models\\Patient', 'created', 14, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"ngo quoc thanh\",\"birth_year\":1983,\"phone\":\"0352568520\",\"gender\":\"male\",\"address\":null}}', NULL, '2025-11-22 11:11:05', '2025-11-22 11:11:05'),
(263, 'default', 'created', 'App\\Models\\Incident', 'created', 17, 'App\\Models\\User', 1, '{\"attributes\":{\"vehicle_id\":2,\"patient_id\":14,\"date\":\"2025-11-12T18:08:00.000000Z\",\"dispatch_by\":1,\"destination\":null,\"from_location_id\":10,\"to_location_id\":19,\"partner_id\":26,\"commission_amount\":\"200000.00\",\"summary\":null,\"tags\":null}}', NULL, '2025-11-22 11:11:05', '2025-11-22 11:11:05'),
(264, 'default', 'created', 'App\\Models\\Transaction', 'created', 107, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":17,\"vehicle_id\":2,\"type\":\"chi\",\"amount\":\"500000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng l\\u00e1i xe: Nguy\\u1ec5n C\\u1eefu Ninh\",\"date\":\"2025-11-12T18:08:00.000000Z\"}}', NULL, '2025-11-22 11:11:05', '2025-11-22 11:11:05'),
(265, 'default', 'created', 'App\\Models\\Transaction', 'created', 108, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":17,\"vehicle_id\":2,\"type\":\"chi\",\"amount\":\"500000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng nh\\u00e2n vi\\u00ean y t\\u1ebf: Nguy\\u1ec5n Qu\\u1ed1c V\\u0169\",\"date\":\"2025-11-12T18:08:00.000000Z\"}}', NULL, '2025-11-22 11:11:05', '2025-11-22 11:11:05'),
(266, 'default', 'created', 'App\\Models\\Transaction', 'created', 109, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":17,\"vehicle_id\":2,\"type\":\"thu\",\"amount\":\"3200000.00\",\"method\":\"cash\",\"note\":\"Thu chuy\\u1ebfn \\u0111i\",\"date\":\"2025-11-12T18:08:00.000000Z\"}}', NULL, '2025-11-22 11:11:05', '2025-11-22 11:11:05'),
(267, 'default', 'created', 'App\\Models\\Transaction', 'created', 110, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":17,\"vehicle_id\":2,\"type\":\"chi\",\"amount\":\"100000.00\",\"method\":\"cash\",\"note\":\"Oxy\",\"date\":\"2025-11-12T18:08:00.000000Z\"}}', NULL, '2025-11-22 11:11:05', '2025-11-22 11:11:05'),
(268, 'default', 'created', 'App\\Models\\Transaction', 'created', 111, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":17,\"vehicle_id\":2,\"type\":\"chi\",\"amount\":\"200000.00\",\"method\":\"cash\",\"note\":\"Hoa h\\u1ed3ng: \\u0110\\u1ee9c Anh - khoa HSTC  - BV \\u0110KL\\u0110\",\"date\":\"2025-11-12T18:08:00.000000Z\"}}', NULL, '2025-11-22 11:11:05', '2025-11-22 11:11:05'),
(269, 'default', 'created', 'App\\Models\\Location', 'created', 20, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"D\\u1ed1c ga Tr\\u1ea1i m\\u00e1t - \\u0110\\u00e0 L\\u1ea1t\",\"type\":\"to\",\"address\":null,\"note\":null,\"is_active\":true}}', NULL, '2025-11-22 11:13:13', '2025-11-22 11:13:13'),
(270, 'default', 'created', 'App\\Models\\Patient', 'created', 15, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"\\u0110\\u1eb7ng Th\\u1ecb Trang\",\"birth_year\":1954,\"phone\":\"0347900160\",\"gender\":\"female\",\"address\":null}}', NULL, '2025-11-22 11:13:13', '2025-11-22 11:13:13'),
(271, 'default', 'created', 'App\\Models\\Incident', 'created', 18, 'App\\Models\\User', 1, '{\"attributes\":{\"vehicle_id\":1,\"patient_id\":15,\"date\":\"2025-11-12T18:11:00.000000Z\",\"dispatch_by\":1,\"destination\":null,\"from_location_id\":10,\"to_location_id\":20,\"partner_id\":null,\"commission_amount\":null,\"summary\":null,\"tags\":null}}', NULL, '2025-11-22 11:13:13', '2025-11-22 11:13:13'),
(272, 'default', 'created', 'App\\Models\\Transaction', 'created', 112, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":18,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"200000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng l\\u00e1i xe: L\\u00ea Phong\",\"date\":\"2025-11-12T18:11:00.000000Z\"}}', NULL, '2025-11-22 11:13:13', '2025-11-22 11:13:13'),
(273, 'default', 'created', 'App\\Models\\Transaction', 'created', 113, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":18,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"200000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng nh\\u00e2n vi\\u00ean y t\\u1ebf: L\\u00e2m\",\"date\":\"2025-11-12T18:11:00.000000Z\"}}', NULL, '2025-11-22 11:13:13', '2025-11-22 11:13:13'),
(274, 'default', 'created', 'App\\Models\\Transaction', 'created', 114, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":18,\"vehicle_id\":1,\"type\":\"thu\",\"amount\":\"1200000.00\",\"method\":\"cash\",\"note\":\"Thu chuy\\u1ebfn \\u0111i\",\"date\":\"2025-11-12T18:11:00.000000Z\"}}', NULL, '2025-11-22 11:13:13', '2025-11-22 11:13:13'),
(275, 'default', 'created', 'App\\Models\\Patient', 'created', 16, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"\\u0110\\u1ea7u Th\\u1ecb T\\u1ee9\",\"birth_year\":1940,\"phone\":\"0387852096\",\"gender\":\"female\",\"address\":null}}', NULL, '2025-11-22 11:15:13', '2025-11-22 11:15:13'),
(276, 'default', 'created', 'App\\Models\\Incident', 'created', 19, 'App\\Models\\User', 1, '{\"attributes\":{\"vehicle_id\":1,\"patient_id\":16,\"date\":\"2025-11-12T18:13:00.000000Z\",\"dispatch_by\":1,\"destination\":null,\"from_location_id\":10,\"to_location_id\":19,\"partner_id\":26,\"commission_amount\":\"200000.00\",\"summary\":null,\"tags\":null}}', NULL, '2025-11-22 11:15:13', '2025-11-22 11:15:13'),
(277, 'default', 'created', 'App\\Models\\Transaction', 'created', 115, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":19,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"550000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng l\\u00e1i xe: L\\u00ea Phong\",\"date\":\"2025-11-12T18:13:00.000000Z\"}}', NULL, '2025-11-22 11:15:13', '2025-11-22 11:15:13'),
(278, 'default', 'created', 'App\\Models\\Transaction', 'created', 116, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":19,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"600000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng nh\\u00e2n vi\\u00ean y t\\u1ebf: L\\u00e2m\",\"date\":\"2025-11-12T18:13:00.000000Z\"}}', NULL, '2025-11-22 11:15:13', '2025-11-22 11:15:13'),
(279, 'default', 'created', 'App\\Models\\Transaction', 'created', 117, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":19,\"vehicle_id\":1,\"type\":\"thu\",\"amount\":\"3500000.00\",\"method\":\"cash\",\"note\":\"Thu chuy\\u1ebfn \\u0111i\",\"date\":\"2025-11-12T18:13:00.000000Z\"}}', NULL, '2025-11-22 11:15:13', '2025-11-22 11:15:13'),
(280, 'default', 'created', 'App\\Models\\Transaction', 'created', 118, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":19,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"200000.00\",\"method\":\"cash\",\"note\":\"Hoa h\\u1ed3ng: \\u0110\\u1ee9c Anh - khoa HSTC  - BV \\u0110KL\\u0110\",\"date\":\"2025-11-12T18:13:00.000000Z\"}}', NULL, '2025-11-22 11:15:13', '2025-11-22 11:15:13'),
(281, 'default', 'created', 'App\\Models\\Patient', 'created', 17, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"pham van kiem\",\"birth_year\":1936,\"phone\":\"0353930075\",\"gender\":\"male\",\"address\":null}}', NULL, '2025-11-22 11:16:55', '2025-11-22 11:16:55'),
(282, 'default', 'created', 'App\\Models\\Incident', 'created', 20, 'App\\Models\\User', 1, '{\"attributes\":{\"vehicle_id\":2,\"patient_id\":17,\"date\":\"2025-11-13T18:15:00.000000Z\",\"dispatch_by\":1,\"destination\":null,\"from_location_id\":10,\"to_location_id\":15,\"partner_id\":26,\"commission_amount\":\"150000.00\",\"summary\":null,\"tags\":null}}', NULL, '2025-11-22 11:16:55', '2025-11-22 11:16:55');
INSERT INTO `activity_log` (`id`, `log_name`, `description`, `subject_type`, `event`, `subject_id`, `causer_type`, `causer_id`, `properties`, `batch_uuid`, `created_at`, `updated_at`) VALUES
(283, 'default', 'created', 'App\\Models\\Transaction', 'created', 119, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":20,\"vehicle_id\":2,\"type\":\"chi\",\"amount\":\"300000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng l\\u00e1i xe: Nguy\\u1ec5n C\\u1eefu Ninh\",\"date\":\"2025-11-13T18:15:00.000000Z\"}}', NULL, '2025-11-22 11:16:55', '2025-11-22 11:16:55'),
(284, 'default', 'created', 'App\\Models\\Transaction', 'created', 120, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":20,\"vehicle_id\":2,\"type\":\"chi\",\"amount\":\"300000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng nh\\u00e2n vi\\u00ean y t\\u1ebf: Ph\\u00fac\",\"date\":\"2025-11-13T18:15:00.000000Z\"}}', NULL, '2025-11-22 11:16:55', '2025-11-22 11:16:55'),
(285, 'default', 'created', 'App\\Models\\Transaction', 'created', 121, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":20,\"vehicle_id\":2,\"type\":\"thu\",\"amount\":\"2000000.00\",\"method\":\"cash\",\"note\":\"Thu chuy\\u1ebfn \\u0111i\",\"date\":\"2025-11-13T18:15:00.000000Z\"}}', NULL, '2025-11-22 11:16:55', '2025-11-22 11:16:55'),
(286, 'default', 'created', 'App\\Models\\Transaction', 'created', 122, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":20,\"vehicle_id\":2,\"type\":\"chi\",\"amount\":\"500000.00\",\"method\":\"cash\",\"note\":\"D\\u1ea7u\",\"date\":\"2025-11-13T18:15:00.000000Z\"}}', NULL, '2025-11-22 11:16:55', '2025-11-22 11:16:55'),
(287, 'default', 'created', 'App\\Models\\Transaction', 'created', 123, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":20,\"vehicle_id\":2,\"type\":\"chi\",\"amount\":\"150000.00\",\"method\":\"cash\",\"note\":\"Hoa h\\u1ed3ng: \\u0110\\u1ee9c Anh - khoa HSTC  - BV \\u0110KL\\u0110\",\"date\":\"2025-11-13T18:15:00.000000Z\"}}', NULL, '2025-11-22 11:16:55', '2025-11-22 11:16:55'),
(288, 'default', 'created', 'App\\Models\\Patient', 'created', 18, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"Ya \\u0110au\",\"birth_year\":1952,\"phone\":\"0389819624\",\"gender\":\"male\",\"address\":null}}', NULL, '2025-11-22 11:18:15', '2025-11-22 11:18:15'),
(289, 'default', 'created', 'App\\Models\\Incident', 'created', 21, 'App\\Models\\User', 1, '{\"attributes\":{\"vehicle_id\":1,\"patient_id\":18,\"date\":\"2025-11-13T18:16:00.000000Z\",\"dispatch_by\":1,\"destination\":null,\"from_location_id\":10,\"to_location_id\":17,\"partner_id\":null,\"commission_amount\":null,\"summary\":null,\"tags\":null}}', NULL, '2025-11-22 11:18:15', '2025-11-22 11:18:15'),
(290, 'default', 'created', 'App\\Models\\Transaction', 'created', 124, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":21,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"150000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng l\\u00e1i xe: L\\u00ea Phong\",\"date\":\"2025-11-13T18:16:00.000000Z\"}}', NULL, '2025-11-22 11:18:15', '2025-11-22 11:18:15'),
(291, 'default', 'created', 'App\\Models\\Transaction', 'created', 125, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":21,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"150000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng nh\\u00e2n vi\\u00ean y t\\u1ebf: Nguy\\u1ec5n Qu\\u1ed1c V\\u0169\",\"date\":\"2025-11-13T18:16:00.000000Z\"}}', NULL, '2025-11-22 11:18:15', '2025-11-22 11:18:15'),
(292, 'default', 'created', 'App\\Models\\Patient', 'created', 19, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"bonhong\",\"birth_year\":1999,\"phone\":\"0383057831\",\"gender\":\"female\",\"address\":null}}', NULL, '2025-11-22 11:21:17', '2025-11-22 11:21:17'),
(293, 'default', 'created', 'App\\Models\\Incident', 'created', 22, 'App\\Models\\User', 1, '{\"attributes\":{\"vehicle_id\":2,\"patient_id\":19,\"date\":\"2025-11-14T18:18:00.000000Z\",\"dispatch_by\":1,\"destination\":null,\"from_location_id\":10,\"to_location_id\":17,\"partner_id\":null,\"commission_amount\":null,\"summary\":null,\"tags\":null}}', NULL, '2025-11-22 11:21:17', '2025-11-22 11:21:17'),
(294, 'default', 'created', 'App\\Models\\Transaction', 'created', 126, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":22,\"vehicle_id\":2,\"type\":\"chi\",\"amount\":\"150000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng l\\u00e1i xe: Nguy\\u1ec5n C\\u1eefu Ninh\",\"date\":\"2025-11-14T18:18:00.000000Z\"}}', NULL, '2025-11-22 11:21:17', '2025-11-22 11:21:17'),
(295, 'default', 'created', 'App\\Models\\Transaction', 'created', 127, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":22,\"vehicle_id\":2,\"type\":\"chi\",\"amount\":\"150000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng nh\\u00e2n vi\\u00ean y t\\u1ebf: Nguy\\u1ec5n Qu\\u1ed1c V\\u0169\",\"date\":\"2025-11-14T18:18:00.000000Z\"}}', NULL, '2025-11-22 11:21:17', '2025-11-22 11:21:17'),
(296, 'default', 'created', 'App\\Models\\Transaction', 'created', 128, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":22,\"vehicle_id\":2,\"type\":\"thu\",\"amount\":\"1000000.00\",\"method\":\"cash\",\"note\":\"Thu chuy\\u1ebfn \\u0111i\",\"date\":\"2025-11-14T18:18:00.000000Z\"}}', NULL, '2025-11-22 11:21:17', '2025-11-22 11:21:17'),
(297, 'default', 'created', 'App\\Models\\Location', 'created', 21, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"Phan \\u0110\\u00ecnh Ph\\u00f9ng - \\u0110\\u00e0 L\\u1ea1t\",\"type\":\"from\",\"address\":null,\"note\":null,\"is_active\":true}}', NULL, '2025-11-22 11:22:56', '2025-11-22 11:22:56'),
(298, 'default', 'created', 'App\\Models\\Incident', 'created', 23, 'App\\Models\\User', 1, '{\"attributes\":{\"vehicle_id\":1,\"patient_id\":8,\"date\":\"2025-11-14T18:21:00.000000Z\",\"dispatch_by\":1,\"destination\":null,\"from_location_id\":21,\"to_location_id\":6,\"partner_id\":null,\"commission_amount\":null,\"summary\":null,\"tags\":null}}', NULL, '2025-11-22 11:22:56', '2025-11-22 11:22:56'),
(299, 'default', 'created', 'App\\Models\\Transaction', 'created', 129, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":23,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"150000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng l\\u00e1i xe: Cil \\u0110oan\",\"date\":\"2025-11-14T18:21:00.000000Z\"}}', NULL, '2025-11-22 11:22:56', '2025-11-22 11:22:56'),
(300, 'default', 'created', 'App\\Models\\Transaction', 'created', 130, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":23,\"vehicle_id\":1,\"type\":\"thu\",\"amount\":\"500000.00\",\"method\":\"cash\",\"note\":\"Thu chuy\\u1ebfn \\u0111i\",\"date\":\"2025-11-14T18:21:00.000000Z\"}}', NULL, '2025-11-22 11:22:56', '2025-11-22 11:22:56'),
(301, 'default', 'created', 'App\\Models\\Transaction', 'created', 131, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":23,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"150000.00\",\"method\":\"cash\",\"note\":\"Oxy\",\"date\":\"2025-11-14T18:21:00.000000Z\"}}', NULL, '2025-11-22 11:22:56', '2025-11-22 11:22:56'),
(302, 'default', 'created', 'App\\Models\\Location', 'created', 22, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"Ph\\u00fa H\\u1ed9i\",\"type\":\"to\",\"address\":null,\"note\":null,\"is_active\":true}}', NULL, '2025-11-22 11:24:34', '2025-11-22 11:24:34'),
(303, 'default', 'created', 'App\\Models\\Patient', 'created', 20, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"vo thi tho\",\"birth_year\":1947,\"phone\":\"0798666716\",\"gender\":null,\"address\":null}}', NULL, '2025-11-22 11:24:34', '2025-11-22 11:24:34'),
(304, 'default', 'created', 'App\\Models\\Incident', 'created', 24, 'App\\Models\\User', 1, '{\"attributes\":{\"vehicle_id\":2,\"patient_id\":20,\"date\":\"2025-11-15T18:22:00.000000Z\",\"dispatch_by\":1,\"destination\":null,\"from_location_id\":10,\"to_location_id\":22,\"partner_id\":26,\"commission_amount\":\"150000.00\",\"summary\":null,\"tags\":null}}', NULL, '2025-11-22 11:24:34', '2025-11-22 11:24:34'),
(305, 'default', 'created', 'App\\Models\\Transaction', 'created', 132, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":24,\"vehicle_id\":2,\"type\":\"chi\",\"amount\":\"300000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng l\\u00e1i xe: Nguy\\u1ec5n C\\u1eefu Ninh\",\"date\":\"2025-11-15T18:22:00.000000Z\"}}', NULL, '2025-11-22 11:24:34', '2025-11-22 11:24:34'),
(306, 'default', 'created', 'App\\Models\\Transaction', 'created', 133, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":24,\"vehicle_id\":2,\"type\":\"chi\",\"amount\":\"300000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng nh\\u00e2n vi\\u00ean y t\\u1ebf: Nguy\\u1ec5n Qu\\u1ed1c V\\u0169\",\"date\":\"2025-11-15T18:22:00.000000Z\"}}', NULL, '2025-11-22 11:24:34', '2025-11-22 11:24:34'),
(307, 'default', 'created', 'App\\Models\\Transaction', 'created', 134, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":24,\"vehicle_id\":2,\"type\":\"thu\",\"amount\":\"2000000.00\",\"method\":\"cash\",\"note\":\"Thu chuy\\u1ebfn \\u0111i\",\"date\":\"2025-11-15T18:22:00.000000Z\"}}', NULL, '2025-11-22 11:24:34', '2025-11-22 11:24:34'),
(308, 'default', 'created', 'App\\Models\\Transaction', 'created', 135, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":24,\"vehicle_id\":2,\"type\":\"chi\",\"amount\":\"150000.00\",\"method\":\"cash\",\"note\":\"Hoa h\\u1ed3ng: \\u0110\\u1ee9c Anh - khoa HSTC  - BV \\u0110KL\\u0110\",\"date\":\"2025-11-15T18:22:00.000000Z\"}}', NULL, '2025-11-22 11:24:34', '2025-11-22 11:24:34'),
(309, 'default', 'created', 'App\\Models\\MaintenanceService', 'created', 18, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"B\\u1ea3o hi\\u1ec3m v\\u1eadt ch\\u1ea5t\",\"description\":null,\"is_active\":true}}', NULL, '2025-11-22 11:26:43', '2025-11-22 11:26:43'),
(310, 'default', 'created', 'App\\Models\\Partner', 'created', 27, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"Cty b\\u1ea3o hi\\u1ec3m\",\"type\":\"maintenance\",\"phone\":null,\"email\":null,\"address\":null,\"commission_rate\":null,\"note\":null,\"is_active\":true}}', NULL, '2025-11-22 11:26:43', '2025-11-22 11:26:43'),
(311, 'default', 'created', 'App\\Models\\VehicleMaintenance', 'created', 21, 'App\\Models\\User', 1, '{\"attributes\":{\"vehicle_id\":4,\"maintenance_service_id\":18,\"partner_id\":27,\"incident_id\":null,\"date\":\"2025-11-07T00:00:00.000000Z\",\"cost\":\"13563000.00\",\"mileage\":null,\"description\":\"BH th\\u00e2n v\\u1ecf xe \\u0111\\u1ec3 gi\\u1ea3i ng\\u00e2n\",\"note\":null}}', NULL, '2025-11-22 11:26:43', '2025-11-22 11:26:43'),
(312, 'default', 'created', 'App\\Models\\Transaction', 'created', 136, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":null,\"vehicle_id\":4,\"type\":\"chi\",\"amount\":\"13563000.00\",\"method\":\"cash\",\"note\":\"[B\\u1ea3o tr\\u00ec] B\\u1ea3o hi\\u1ec3m v\\u1eadt ch\\u1ea5t - Cty b\\u1ea3o hi\\u1ec3m - BH th\\u00e2n v\\u1ecf xe \\u0111\\u1ec3 gi\\u1ea3i ng\\u00e2n\",\"date\":\"2025-11-07T00:00:00.000000Z\"}}', NULL, '2025-11-22 11:26:43', '2025-11-22 11:26:43'),
(313, 'default', 'created', 'App\\Models\\VehicleMaintenance', 'created', 22, 'App\\Models\\User', 1, '{\"attributes\":{\"vehicle_id\":3,\"maintenance_service_id\":18,\"partner_id\":27,\"incident_id\":null,\"date\":\"2025-11-22T00:00:00.000000Z\",\"cost\":\"13563000.00\",\"mileage\":null,\"description\":null,\"note\":null}}', NULL, '2025-11-22 11:27:11', '2025-11-22 11:27:11'),
(314, 'default', 'created', 'App\\Models\\Transaction', 'created', 137, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":null,\"vehicle_id\":3,\"type\":\"chi\",\"amount\":\"13563000.00\",\"method\":\"cash\",\"note\":\"[B\\u1ea3o tr\\u00ec] B\\u1ea3o hi\\u1ec3m v\\u1eadt ch\\u1ea5t - Cty b\\u1ea3o hi\\u1ec3m\",\"date\":\"2025-11-22T00:00:00.000000Z\"}}', NULL, '2025-11-22 11:27:11', '2025-11-22 11:27:11'),
(315, 'default', 'created', 'App\\Models\\MaintenanceService', 'created', 19, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"B\\u1ea3o hi\\u1ec3m D\\u00e2n s\\u1ef1\",\"description\":null,\"is_active\":true}}', NULL, '2025-11-22 11:27:41', '2025-11-22 11:27:41'),
(316, 'default', 'created', 'App\\Models\\VehicleMaintenance', 'created', 23, 'App\\Models\\User', 1, '{\"attributes\":{\"vehicle_id\":3,\"maintenance_service_id\":19,\"partner_id\":27,\"incident_id\":null,\"date\":\"2025-11-22T00:00:00.000000Z\",\"cost\":\"1311560.00\",\"mileage\":null,\"description\":null,\"note\":null}}', NULL, '2025-11-22 11:27:41', '2025-11-22 11:27:41'),
(317, 'default', 'created', 'App\\Models\\Transaction', 'created', 138, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":null,\"vehicle_id\":3,\"type\":\"chi\",\"amount\":\"1311560.00\",\"method\":\"cash\",\"note\":\"[B\\u1ea3o tr\\u00ec] B\\u1ea3o hi\\u1ec3m D\\u00e2n s\\u1ef1 - Cty b\\u1ea3o hi\\u1ec3m\",\"date\":\"2025-11-22T00:00:00.000000Z\"}}', NULL, '2025-11-22 11:27:41', '2025-11-22 11:27:41'),
(318, 'default', 'created', 'App\\Models\\VehicleMaintenance', 'created', 24, 'App\\Models\\User', 1, '{\"attributes\":{\"vehicle_id\":4,\"maintenance_service_id\":19,\"partner_id\":27,\"incident_id\":null,\"date\":\"2025-11-22T00:00:00.000000Z\",\"cost\":\"1311560.00\",\"mileage\":null,\"description\":null,\"note\":null}}', NULL, '2025-11-22 11:28:01', '2025-11-22 11:28:01'),
(319, 'default', 'created', 'App\\Models\\Transaction', 'created', 139, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":null,\"vehicle_id\":4,\"type\":\"chi\",\"amount\":\"1311560.00\",\"method\":\"cash\",\"note\":\"[B\\u1ea3o tr\\u00ec] B\\u1ea3o hi\\u1ec3m D\\u00e2n s\\u1ef1 - Cty b\\u1ea3o hi\\u1ec3m\",\"date\":\"2025-11-22T00:00:00.000000Z\"}}', NULL, '2025-11-22 11:28:01', '2025-11-22 11:28:01'),
(320, 'default', 'updated', 'App\\Models\\VehicleMaintenance', 'updated', 22, 'App\\Models\\User', 1, '{\"attributes\":{\"date\":\"2025-11-07T00:00:00.000000Z\"},\"old\":{\"date\":\"2025-11-22T00:00:00.000000Z\"}}', NULL, '2025-11-22 11:30:33', '2025-11-22 11:30:33'),
(321, 'default', 'updated', 'App\\Models\\Transaction', 'updated', 137, 'App\\Models\\User', 1, '{\"attributes\":{\"date\":\"2025-11-07T00:00:00.000000Z\"},\"old\":{\"date\":\"2025-11-22T00:00:00.000000Z\"}}', NULL, '2025-11-22 11:30:33', '2025-11-22 11:30:33'),
(322, 'default', 'updated', 'App\\Models\\VehicleMaintenance', 'updated', 23, 'App\\Models\\User', 1, '{\"attributes\":{\"date\":\"2025-11-17T00:00:00.000000Z\"},\"old\":{\"date\":\"2025-11-22T00:00:00.000000Z\"}}', NULL, '2025-11-22 11:30:48', '2025-11-22 11:30:48'),
(323, 'default', 'updated', 'App\\Models\\Transaction', 'updated', 138, 'App\\Models\\User', 1, '{\"attributes\":{\"date\":\"2025-11-17T00:00:00.000000Z\"},\"old\":{\"date\":\"2025-11-22T00:00:00.000000Z\"}}', NULL, '2025-11-22 11:30:48', '2025-11-22 11:30:48'),
(324, 'default', 'updated', 'App\\Models\\VehicleMaintenance', 'updated', 24, 'App\\Models\\User', 1, '{\"attributes\":{\"date\":\"2025-11-17T00:00:00.000000Z\"},\"old\":{\"date\":\"2025-11-22T00:00:00.000000Z\"}}', NULL, '2025-11-22 11:30:57', '2025-11-22 11:30:57'),
(325, 'default', 'updated', 'App\\Models\\Transaction', 'updated', 139, 'App\\Models\\User', 1, '{\"attributes\":{\"date\":\"2025-11-17T00:00:00.000000Z\"},\"old\":{\"date\":\"2025-11-22T00:00:00.000000Z\"}}', NULL, '2025-11-22 11:30:57', '2025-11-22 11:30:57'),
(326, 'default', 'created', 'App\\Models\\Incident', 'created', 25, 'App\\Models\\User', 1, '{\"attributes\":{\"vehicle_id\":1,\"patient_id\":8,\"date\":\"2025-11-16T18:24:00.000000Z\",\"dispatch_by\":1,\"destination\":null,\"from_location_id\":10,\"to_location_id\":21,\"partner_id\":null,\"commission_amount\":null,\"summary\":null,\"tags\":null}}', NULL, '2025-11-22 11:32:32', '2025-11-22 11:32:32'),
(327, 'default', 'created', 'App\\Models\\Transaction', 'created', 140, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":25,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"150000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng l\\u00e1i xe: Cil \\u0110oan\",\"date\":\"2025-11-16T18:24:00.000000Z\"}}', NULL, '2025-11-22 11:32:32', '2025-11-22 11:32:32'),
(328, 'default', 'created', 'App\\Models\\Transaction', 'created', 141, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":25,\"vehicle_id\":1,\"type\":\"thu\",\"amount\":\"500000.00\",\"method\":\"cash\",\"note\":\"Thu chuy\\u1ebfn \\u0111i\",\"date\":\"2025-11-16T18:24:00.000000Z\"}}', NULL, '2025-11-22 11:32:32', '2025-11-22 11:32:32'),
(329, 'default', 'created', 'App\\Models\\Location', 'created', 23, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"\\u0110\\u1ea1 T\\u1ebbh\",\"type\":\"to\",\"address\":null,\"note\":null,\"is_active\":true}}', NULL, '2025-11-22 11:35:18', '2025-11-22 11:35:18'),
(330, 'default', 'created', 'App\\Models\\Patient', 'created', 21, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"Nguy\\u1ec5n v\\u0103n ba\",\"birth_year\":null,\"phone\":null,\"gender\":null,\"address\":null}}', NULL, '2025-11-22 11:35:18', '2025-11-22 11:35:18'),
(331, 'default', 'created', 'App\\Models\\Incident', 'created', 26, 'App\\Models\\User', 1, '{\"attributes\":{\"vehicle_id\":1,\"patient_id\":21,\"date\":\"2025-11-17T18:32:00.000000Z\",\"dispatch_by\":1,\"destination\":null,\"from_location_id\":10,\"to_location_id\":23,\"partner_id\":26,\"commission_amount\":\"200000.00\",\"summary\":null,\"tags\":null}}', NULL, '2025-11-22 11:35:18', '2025-11-22 11:35:18'),
(332, 'default', 'created', 'App\\Models\\Transaction', 'created', 142, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":26,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"500000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng l\\u00e1i xe: Cil \\u0110oan\",\"date\":\"2025-11-17T18:32:00.000000Z\"}}', NULL, '2025-11-22 11:35:18', '2025-11-22 11:35:18'),
(333, 'default', 'created', 'App\\Models\\Transaction', 'created', 143, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":26,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"500000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng nh\\u00e2n vi\\u00ean y t\\u1ebf: Nguy\\u1ec5n Qu\\u1ed1c V\\u0169\",\"date\":\"2025-11-17T18:32:00.000000Z\"}}', NULL, '2025-11-22 11:35:18', '2025-11-22 11:35:18'),
(334, 'default', 'created', 'App\\Models\\Transaction', 'created', 144, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":26,\"vehicle_id\":1,\"type\":\"thu\",\"amount\":\"3500000.00\",\"method\":\"cash\",\"note\":\"Thu chuy\\u1ebfn \\u0111i\",\"date\":\"2025-11-17T18:32:00.000000Z\"}}', NULL, '2025-11-22 11:35:18', '2025-11-22 11:35:18'),
(335, 'default', 'created', 'App\\Models\\Transaction', 'created', 145, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":26,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"700000.00\",\"method\":\"cash\",\"note\":\"D\\u1ea7u\",\"date\":\"2025-11-17T18:32:00.000000Z\"}}', NULL, '2025-11-22 11:35:18', '2025-11-22 11:35:18'),
(336, 'default', 'created', 'App\\Models\\Transaction', 'created', 146, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":26,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"200000.00\",\"method\":\"cash\",\"note\":\"Hoa h\\u1ed3ng: \\u0110\\u1ee9c Anh - khoa HSTC  - BV \\u0110KL\\u0110\",\"date\":\"2025-11-17T18:32:00.000000Z\"}}', NULL, '2025-11-22 11:35:18', '2025-11-22 11:35:18'),
(337, 'default', 'created', 'App\\Models\\Location', 'created', 24, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"Khoa Ph\\u1ed5i - BV\\u0110K\",\"type\":\"from\",\"address\":null,\"note\":null,\"is_active\":true}}', NULL, '2025-11-22 11:37:34', '2025-11-22 11:37:34'),
(338, 'default', 'created', 'App\\Models\\Patient', 'created', 22, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"ka Diuh\",\"birth_year\":null,\"phone\":\"0389253760\",\"gender\":null,\"address\":null}}', NULL, '2025-11-22 11:37:34', '2025-11-22 11:37:34'),
(339, 'default', 'created', 'App\\Models\\Incident', 'created', 27, 'App\\Models\\User', 1, '{\"attributes\":{\"vehicle_id\":3,\"patient_id\":22,\"date\":\"2025-11-17T18:35:00.000000Z\",\"dispatch_by\":1,\"destination\":null,\"from_location_id\":24,\"to_location_id\":1,\"partner_id\":23,\"commission_amount\":\"500000.00\",\"summary\":null,\"tags\":null}}', NULL, '2025-11-22 11:37:34', '2025-11-22 11:37:34'),
(340, 'default', 'created', 'App\\Models\\Transaction', 'created', 147, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":27,\"vehicle_id\":3,\"type\":\"chi\",\"amount\":\"550000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng l\\u00e1i xe: L\\u00ea Phong\",\"date\":\"2025-11-17T18:35:00.000000Z\"}}', NULL, '2025-11-22 11:37:34', '2025-11-22 11:37:34'),
(341, 'default', 'created', 'App\\Models\\Transaction', 'created', 148, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":27,\"vehicle_id\":3,\"type\":\"chi\",\"amount\":\"600000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng nh\\u00e2n vi\\u00ean y t\\u1ebf: Kh\\u00e1nh\",\"date\":\"2025-11-17T18:35:00.000000Z\"}}', NULL, '2025-11-22 11:37:34', '2025-11-22 11:37:34'),
(342, 'default', 'created', 'App\\Models\\Transaction', 'created', 149, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":27,\"vehicle_id\":3,\"type\":\"thu\",\"amount\":\"4500000.00\",\"method\":\"cash\",\"note\":\"Thu chuy\\u1ebfn \\u0111i\",\"date\":\"2025-11-17T18:35:00.000000Z\"}}', NULL, '2025-11-22 11:37:34', '2025-11-22 11:37:34'),
(343, 'default', 'created', 'App\\Models\\Transaction', 'created', 150, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":27,\"vehicle_id\":3,\"type\":\"chi\",\"amount\":\"1970000.00\",\"method\":\"cash\",\"note\":\"D\\u1ea7u\",\"date\":\"2025-11-17T18:35:00.000000Z\"}}', NULL, '2025-11-22 11:37:34', '2025-11-22 11:37:34'),
(344, 'default', 'created', 'App\\Models\\Transaction', 'created', 151, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":27,\"vehicle_id\":3,\"type\":\"chi\",\"amount\":\"500000.00\",\"method\":\"cash\",\"note\":\"Hoa h\\u1ed3ng: Khoa Ph\\u1ed5i - BV \\u0110KL\\u0110\",\"date\":\"2025-11-17T18:35:00.000000Z\"}}', NULL, '2025-11-22 11:37:34', '2025-11-22 11:37:34'),
(345, 'default', 'created', 'App\\Models\\Location', 'created', 25, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"Khoa Ngo\\u1ea1i TH - BV \\u0110a khoa L\\u0110\",\"type\":\"from\",\"address\":null,\"note\":null,\"is_active\":true}}', NULL, '2025-11-22 11:41:05', '2025-11-22 11:41:05'),
(346, 'default', 'created', 'App\\Models\\Location', 'created', 26, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"BV B\\u00ecnh D\\u00e2n\",\"type\":\"to\",\"address\":null,\"note\":null,\"is_active\":true}}', NULL, '2025-11-22 11:41:05', '2025-11-22 11:41:05'),
(347, 'default', 'created', 'App\\Models\\Patient', 'created', 23, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"Nguy\\u1ec5n Th\\u1ecb Hoa\",\"birth_year\":1969,\"phone\":\"0333815585\",\"gender\":\"female\",\"address\":null}}', NULL, '2025-11-22 11:41:05', '2025-11-22 11:41:05'),
(348, 'default', 'created', 'App\\Models\\Incident', 'created', 28, 'App\\Models\\User', 1, '{\"attributes\":{\"vehicle_id\":4,\"patient_id\":23,\"date\":\"2025-11-17T18:37:00.000000Z\",\"dispatch_by\":1,\"destination\":null,\"from_location_id\":25,\"to_location_id\":26,\"partner_id\":null,\"commission_amount\":null,\"summary\":null,\"tags\":null}}', NULL, '2025-11-22 11:41:05', '2025-11-22 11:41:05'),
(349, 'default', 'created', 'App\\Models\\Transaction', 'created', 152, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":28,\"vehicle_id\":4,\"type\":\"chi\",\"amount\":\"550000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng l\\u00e1i xe: Nguy\\u1ec5n C\\u1eefu Ninh\",\"date\":\"2025-11-17T18:37:00.000000Z\"}}', NULL, '2025-11-22 11:41:05', '2025-11-22 11:41:05'),
(350, 'default', 'created', 'App\\Models\\Transaction', 'created', 153, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":28,\"vehicle_id\":4,\"type\":\"chi\",\"amount\":\"600000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng nh\\u00e2n vi\\u00ean y t\\u1ebf: Nguy\\u1ec5n Qu\\u1ed1c V\\u0169\",\"date\":\"2025-11-17T18:37:00.000000Z\"}}', NULL, '2025-11-22 11:41:05', '2025-11-22 11:41:05'),
(351, 'default', 'created', 'App\\Models\\Transaction', 'created', 154, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":28,\"vehicle_id\":4,\"type\":\"thu\",\"amount\":\"4500000.00\",\"method\":\"cash\",\"note\":\"Thu chuy\\u1ebfn \\u0111i\",\"date\":\"2025-11-17T18:37:00.000000Z\"}}', NULL, '2025-11-22 11:41:05', '2025-11-22 11:41:05'),
(352, 'default', 'created', 'App\\Models\\Transaction', 'created', 155, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":28,\"vehicle_id\":4,\"type\":\"chi\",\"amount\":\"1610000.00\",\"method\":\"cash\",\"note\":\"D\\u1ea7u\",\"date\":\"2025-11-17T18:37:00.000000Z\"}}', NULL, '2025-11-22 11:41:05', '2025-11-22 11:41:05'),
(353, 'default', 'created', 'App\\Models\\Transaction', 'created', 156, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":28,\"vehicle_id\":4,\"type\":\"chi\",\"amount\":\"500000.00\",\"method\":\"cash\",\"note\":\"Chi ph\\u00ed: m\\u00f4i gi\\u1edbi ngo\\u00e0i\",\"date\":\"2025-11-17T18:37:00.000000Z\"}}', NULL, '2025-11-22 11:41:05', '2025-11-22 11:41:05'),
(354, 'default', 'created', 'App\\Models\\Patient', 'created', 24, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"Cil Ka Nghi\\u1ec7p\",\"birth_year\":1984,\"phone\":\"0339996201\",\"gender\":\"male\",\"address\":null}}', NULL, '2025-11-22 11:45:07', '2025-11-22 11:45:07'),
(355, 'default', 'created', 'App\\Models\\Incident', 'created', 29, 'App\\Models\\User', 1, '{\"attributes\":{\"vehicle_id\":1,\"patient_id\":24,\"date\":\"2025-11-17T18:41:00.000000Z\",\"dispatch_by\":1,\"destination\":null,\"from_location_id\":25,\"to_location_id\":15,\"partner_id\":26,\"commission_amount\":\"150000.00\",\"summary\":null,\"tags\":null}}', NULL, '2025-11-22 11:45:07', '2025-11-22 11:45:07'),
(356, 'default', 'created', 'App\\Models\\Transaction', 'created', 157, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":29,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"350000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng l\\u00e1i xe: Cil \\u0110oan\",\"date\":\"2025-11-17T18:41:00.000000Z\"}}', NULL, '2025-11-22 11:45:07', '2025-11-22 11:45:07'),
(357, 'default', 'created', 'App\\Models\\Transaction', 'created', 158, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":29,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"350000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng nh\\u00e2n vi\\u00ean y t\\u1ebf: Nguy\\u1ec5n Qu\\u1ed1c V\\u0169\",\"date\":\"2025-11-17T18:41:00.000000Z\"}}', NULL, '2025-11-22 11:45:07', '2025-11-22 11:45:07'),
(358, 'default', 'created', 'App\\Models\\Transaction', 'created', 159, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":29,\"vehicle_id\":1,\"type\":\"thu\",\"amount\":\"2500000.00\",\"method\":\"cash\",\"note\":\"Thu chuy\\u1ebfn \\u0111i\",\"date\":\"2025-11-17T18:41:00.000000Z\"}}', NULL, '2025-11-22 11:45:07', '2025-11-22 11:45:07'),
(359, 'default', 'created', 'App\\Models\\Transaction', 'created', 160, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":29,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"150000.00\",\"method\":\"cash\",\"note\":\"Hoa h\\u1ed3ng: \\u0110\\u1ee9c Anh - khoa HSTC  - BV \\u0110KL\\u0110\",\"date\":\"2025-11-17T18:41:00.000000Z\"}}', NULL, '2025-11-22 11:45:07', '2025-11-22 11:45:07'),
(360, 'default', 'created', 'App\\Models\\Transaction', 'created', 161, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":null,\"vehicle_id\":null,\"type\":\"chi\",\"amount\":\"450000.00\",\"method\":\"cash\",\"note\":\"\\u0110i\\u1ec1u ch\\u1ec9nh: Ph\\u1ee5 c\\u1ea5p tr\\u1ef1c - Cil \\u0110oan (t\\u1eeb qu\\u1ef9 c\\u00f4ng ty)\",\"date\":\"2025-11-22T18:47:15.000000Z\"}}', NULL, '2025-11-22 11:47:15', '2025-11-22 11:47:15'),
(361, 'default', 'created', 'App\\Models\\Location', 'created', 27, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"Di Linh\",\"type\":\"to\",\"address\":null,\"note\":null,\"is_active\":true}}', NULL, '2025-11-22 11:50:50', '2025-11-22 11:50:50'),
(362, 'default', 'created', 'App\\Models\\Patient', 'created', 25, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"K Nghi\\u1ec7p\",\"birth_year\":1975,\"phone\":\"0337700645\",\"gender\":\"male\",\"address\":null}}', NULL, '2025-11-22 11:50:50', '2025-11-22 11:50:50'),
(363, 'default', 'created', 'App\\Models\\Incident', 'created', 30, 'App\\Models\\User', 1, '{\"attributes\":{\"vehicle_id\":1,\"patient_id\":25,\"date\":\"2025-11-17T18:49:00.000000Z\",\"dispatch_by\":1,\"destination\":null,\"from_location_id\":10,\"to_location_id\":27,\"partner_id\":26,\"commission_amount\":\"150000.00\",\"summary\":null,\"tags\":null}}', NULL, '2025-11-22 11:50:50', '2025-11-22 11:50:50'),
(364, 'default', 'created', 'App\\Models\\Transaction', 'created', 162, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":30,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"450000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng l\\u00e1i xe: Cil \\u0110oan\",\"date\":\"2025-11-17T18:49:00.000000Z\"}}', NULL, '2025-11-22 11:50:50', '2025-11-22 11:50:50'),
(365, 'default', 'created', 'App\\Models\\Transaction', 'created', 163, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":30,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"450000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng nh\\u00e2n vi\\u00ean y t\\u1ebf: Danh\",\"date\":\"2025-11-17T18:49:00.000000Z\"}}', NULL, '2025-11-22 11:50:50', '2025-11-22 11:50:50'),
(366, 'default', 'created', 'App\\Models\\Transaction', 'created', 164, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":30,\"vehicle_id\":1,\"type\":\"thu\",\"amount\":\"3000000.00\",\"method\":\"cash\",\"note\":\"Thu chuy\\u1ebfn \\u0111i\",\"date\":\"2025-11-17T18:49:00.000000Z\"}}', NULL, '2025-11-22 11:50:50', '2025-11-22 11:50:50'),
(367, 'default', 'created', 'App\\Models\\Transaction', 'created', 165, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":30,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"150000.00\",\"method\":\"cash\",\"note\":\"Hoa h\\u1ed3ng: \\u0110\\u1ee9c Anh - khoa HSTC  - BV \\u0110KL\\u0110\",\"date\":\"2025-11-17T18:49:00.000000Z\"}}', NULL, '2025-11-22 11:50:50', '2025-11-22 11:50:50'),
(368, 'default', 'created', 'App\\Models\\Location', 'created', 28, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"BV \\u0110H Y D\\u01b0\\u1ee3c HCM\",\"type\":\"to\",\"address\":null,\"note\":null,\"is_active\":true}}', NULL, '2025-11-22 11:57:35', '2025-11-22 11:57:35'),
(369, 'default', 'created', 'App\\Models\\Patient', 'created', 26, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"Ng\\u00f4 Tr\\u1ea7n Th\\u1ebf Ng\\u00e2n\",\"birth_year\":1965,\"phone\":\"0396916647\",\"gender\":\"male\",\"address\":null}}', NULL, '2025-11-22 11:57:35', '2025-11-22 11:57:35'),
(370, 'default', 'created', 'App\\Models\\Incident', 'created', 31, 'App\\Models\\User', 1, '{\"attributes\":{\"vehicle_id\":3,\"patient_id\":26,\"date\":\"2025-11-18T18:50:00.000000Z\",\"dispatch_by\":1,\"destination\":null,\"from_location_id\":24,\"to_location_id\":28,\"partner_id\":23,\"commission_amount\":\"500000.00\",\"summary\":null,\"tags\":null}}', NULL, '2025-11-22 11:57:35', '2025-11-22 11:57:35'),
(371, 'default', 'created', 'App\\Models\\Transaction', 'created', 166, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":31,\"vehicle_id\":3,\"type\":\"chi\",\"amount\":\"550000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng l\\u00e1i xe: L\\u00ea Phong\",\"date\":\"2025-11-18T18:50:00.000000Z\"}}', NULL, '2025-11-22 11:57:35', '2025-11-22 11:57:35'),
(372, 'default', 'created', 'App\\Models\\Transaction', 'created', 167, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":31,\"vehicle_id\":3,\"type\":\"chi\",\"amount\":\"600000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng nh\\u00e2n vi\\u00ean y t\\u1ebf: Nguy\\u1ec5n Qu\\u1ed1c V\\u0169\",\"date\":\"2025-11-18T18:50:00.000000Z\"}}', NULL, '2025-11-22 11:57:35', '2025-11-22 11:57:35'),
(373, 'default', 'created', 'App\\Models\\Transaction', 'created', 168, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":31,\"vehicle_id\":3,\"type\":\"thu\",\"amount\":\"4500000.00\",\"method\":\"cash\",\"note\":\"Thu chuy\\u1ebfn \\u0111i\",\"date\":\"2025-11-18T18:50:00.000000Z\"}}', NULL, '2025-11-22 11:57:35', '2025-11-22 11:57:35'),
(374, 'default', 'created', 'App\\Models\\Transaction', 'created', 169, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":31,\"vehicle_id\":3,\"type\":\"chi\",\"amount\":\"1390000.00\",\"method\":\"cash\",\"note\":\"D\\u1ea7u\",\"date\":\"2025-11-18T18:50:00.000000Z\"}}', NULL, '2025-11-22 11:57:35', '2025-11-22 11:57:35'),
(375, 'default', 'created', 'App\\Models\\Transaction', 'created', 170, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":31,\"vehicle_id\":3,\"type\":\"chi\",\"amount\":\"70000.00\",\"method\":\"cash\",\"note\":\"Chi ph\\u00ed: Oxy\",\"date\":\"2025-11-18T18:50:00.000000Z\"}}', NULL, '2025-11-22 11:57:35', '2025-11-22 11:57:35'),
(376, 'default', 'created', 'App\\Models\\Transaction', 'created', 171, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":31,\"vehicle_id\":3,\"type\":\"chi\",\"amount\":\"500000.00\",\"method\":\"cash\",\"note\":\"Hoa h\\u1ed3ng: Khoa Ph\\u1ed5i - BV \\u0110KL\\u0110\",\"date\":\"2025-11-18T18:50:00.000000Z\"}}', NULL, '2025-11-22 11:57:35', '2025-11-22 11:57:35'),
(377, 'default', 'created', 'App\\Models\\Transaction', 'created', 172, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":30,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"500000.00\",\"method\":\"cash\",\"note\":\"D\\u1ea7u\",\"date\":\"2025-11-22T18:58:00.000000Z\"}}', NULL, '2025-11-22 11:58:40', '2025-11-22 11:58:40'),
(378, 'default', 'created', 'App\\Models\\Transaction', 'created', 173, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":11,\"vehicle_id\":2,\"type\":\"chi\",\"amount\":\"100000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng l\\u00e1i xe: Nguy\\u1ec5n C\\u1eefu Ninh\",\"date\":\"2025-11-03T17:50:00.000000Z\"}}', NULL, '2025-11-23 10:35:26', '2025-11-23 10:35:26'),
(379, 'default', 'created', 'App\\Models\\Transaction', 'created', 174, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":11,\"vehicle_id\":2,\"type\":\"chi\",\"amount\":\"100000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng nh\\u00e2n vi\\u00ean y t\\u1ebf: L\\u00e2m\",\"date\":\"2025-11-03T17:50:00.000000Z\"}}', NULL, '2025-11-23 10:35:26', '2025-11-23 10:35:26'),
(380, 'default', 'created', 'App\\Models\\Transaction', 'created', 175, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":13,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"300000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng l\\u00e1i xe: L\\u00ea Phong\",\"date\":\"2025-11-04T18:00:00.000000Z\"}}', NULL, '2025-11-23 10:37:38', '2025-11-23 10:37:38'),
(381, 'default', 'created', 'App\\Models\\Transaction', 'created', 176, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":13,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"300000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng nh\\u00e2n vi\\u00ean y t\\u1ebf: Nguy\\u1ec5n Qu\\u1ed1c V\\u0169\",\"date\":\"2025-11-04T18:00:00.000000Z\"}}', NULL, '2025-11-23 10:37:38', '2025-11-23 10:37:38'),
(382, 'default', 'created', 'App\\Models\\Transaction', 'created', 177, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":13,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"150000.00\",\"method\":\"cash\",\"note\":\"Hoa h\\u1ed3ng: \\u0110\\u1ee9c Anh - khoa HSTC  - BV \\u0110KL\\u0110\",\"date\":\"2025-11-04T18:00:00.000000Z\"}}', NULL, '2025-11-23 10:37:38', '2025-11-23 10:37:38'),
(383, 'default', 'deleted', 'App\\Models\\Transaction', 'deleted', 94, 'App\\Models\\User', 1, '{\"old\":{\"incident_id\":14,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"300000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng l\\u00e1i xe: L\\u00ea Phong\",\"date\":\"2025-11-05T18:01:00.000000Z\"}}', NULL, '2025-11-23 10:38:34', '2025-11-23 10:38:34'),
(384, 'default', 'created', 'App\\Models\\Transaction', 'created', 178, NULL, NULL, '{\"attributes\":{\"incident_id\":14,\"vehicle_id\":null,\"type\":\"chi\",\"amount\":\"200000.00\",\"method\":\"cash\",\"note\":null,\"date\":\"2025-11-23T17:50:30.000000Z\"}}', NULL, '2025-11-23 10:50:31', '2025-11-23 10:50:31'),
(385, 'default', 'updated', 'App\\Models\\Transaction', 'updated', 178, NULL, NULL, '{\"attributes\":{\"amount\":\"250000.00\",\"method\":\"cash\"},\"old\":{\"amount\":\"200000.00\",\"method\":null}}', NULL, '2025-11-23 10:50:31', '2025-11-23 10:50:31'),
(386, 'default', 'deleted', 'App\\Models\\Transaction', 'deleted', 178, NULL, NULL, '{\"old\":{\"incident_id\":14,\"vehicle_id\":null,\"type\":\"chi\",\"amount\":\"250000.00\",\"method\":null,\"note\":null,\"date\":\"2025-11-23T17:50:30.000000Z\"}}', NULL, '2025-11-23 10:50:31', '2025-11-23 10:50:31'),
(387, 'default', 'created', 'App\\Models\\Transaction', 'created', 179, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":14,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"300000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng l\\u00e1i xe: Nguy\\u1ec5n C\\u1eefu Ninh\",\"date\":\"2025-11-05T18:01:00.000000Z\"}}', NULL, '2025-11-23 10:56:29', '2025-11-23 10:56:29'),
(388, 'default', 'created', 'App\\Models\\Transaction', 'created', 180, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":14,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"300000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng nh\\u00e2n vi\\u00ean y t\\u1ebf: Nguy\\u1ec5n Qu\\u1ed1c V\\u0169\",\"date\":\"2025-11-05T18:01:00.000000Z\"}}', NULL, '2025-11-23 10:56:29', '2025-11-23 10:56:29'),
(389, 'default', 'created', 'App\\Models\\Transaction', 'created', 181, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":14,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"150000.00\",\"method\":\"cash\",\"note\":\"Hoa h\\u1ed3ng: \\u0110\\u1ee9c Anh - khoa HSTC  - BV \\u0110KL\\u0110\",\"date\":\"2025-11-05T18:01:00.000000Z\"}}', NULL, '2025-11-23 10:56:29', '2025-11-23 10:56:29'),
(390, 'default', 'created', 'App\\Models\\Transaction', 'created', 182, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":16,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"475000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng l\\u00e1i xe: L\\u00ea Phong\",\"date\":\"2025-11-10T18:05:00.000000Z\"}}', NULL, '2025-11-23 10:59:20', '2025-11-23 10:59:20'),
(391, 'default', 'created', 'App\\Models\\Transaction', 'created', 183, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":16,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"475000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng nh\\u00e2n vi\\u00ean y t\\u1ebf: Nguy\\u1ec5n Qu\\u1ed1c V\\u0169\",\"date\":\"2025-11-10T18:05:00.000000Z\"}}', NULL, '2025-11-23 10:59:20', '2025-11-23 10:59:20'),
(392, 'default', 'created', 'App\\Models\\Transaction', 'created', 184, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":16,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"150000.00\",\"method\":\"cash\",\"note\":\"Hoa h\\u1ed3ng: \\u0110\\u1ee9c Anh - khoa HSTC  - BV \\u0110KL\\u0110\",\"date\":\"2025-11-10T18:05:00.000000Z\"}}', NULL, '2025-11-23 10:59:20', '2025-11-23 10:59:20'),
(393, 'default', 'created', 'App\\Models\\Transaction', 'created', 185, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":17,\"vehicle_id\":2,\"type\":\"chi\",\"amount\":\"500000.00\",\"method\":\"cash\",\"note\":\"D\\u1ea7u\",\"date\":\"2025-11-23T18:00:00.000000Z\"}}', NULL, '2025-11-23 11:01:21', '2025-11-23 11:01:21'),
(394, 'default', 'created', 'App\\Models\\Transaction', 'created', 186, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":19,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"525000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng l\\u00e1i xe: L\\u00ea Phong\",\"date\":\"2025-11-12T18:13:00.000000Z\"}}', NULL, '2025-11-23 11:03:34', '2025-11-23 11:03:34'),
(395, 'default', 'created', 'App\\Models\\Transaction', 'created', 187, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":19,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"525000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng nh\\u00e2n vi\\u00ean y t\\u1ebf: L\\u00e2m\",\"date\":\"2025-11-12T18:13:00.000000Z\"}}', NULL, '2025-11-23 11:03:34', '2025-11-23 11:03:34'),
(396, 'default', 'created', 'App\\Models\\Transaction', 'created', 188, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":19,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"200000.00\",\"method\":\"cash\",\"note\":\"Hoa h\\u1ed3ng: \\u0110\\u1ee9c Anh - khoa HSTC  - BV \\u0110KL\\u0110\",\"date\":\"2025-11-12T18:13:00.000000Z\"}}', NULL, '2025-11-23 11:03:34', '2025-11-23 11:03:34'),
(397, 'default', 'created', 'App\\Models\\Transaction', 'created', 189, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":21,\"vehicle_id\":1,\"type\":\"thu\",\"amount\":\"1000000.00\",\"method\":\"cash\",\"note\":\"Thu chuy\\u1ebfn \\u0111i\",\"date\":\"2025-11-23T18:05:00.000000Z\"}}', NULL, '2025-11-23 11:05:50', '2025-11-23 11:05:50'),
(398, 'default', 'created', 'App\\Models\\Transaction', 'created', 190, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":23,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"75000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng l\\u00e1i xe: Cil \\u0110oan\",\"date\":\"2025-11-14T18:21:00.000000Z\"}}', NULL, '2025-11-23 11:07:53', '2025-11-23 11:07:53'),
(399, 'default', 'created', 'App\\Models\\Transaction', 'created', 191, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":23,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"75000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng nh\\u00e2n vi\\u00ean y t\\u1ebf: Nguy\\u1ec5n Qu\\u1ed1c V\\u0169\",\"date\":\"2025-11-14T18:21:00.000000Z\"}}', NULL, '2025-11-23 11:07:53', '2025-11-23 11:07:53'),
(400, 'default', 'created', 'App\\Models\\Transaction', 'created', 192, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":25,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"75000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng l\\u00e1i xe: Cil \\u0110oan\",\"date\":\"2025-11-16T18:24:00.000000Z\"}}', NULL, '2025-11-23 11:09:21', '2025-11-23 11:09:21'),
(401, 'default', 'created', 'App\\Models\\Transaction', 'created', 193, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":25,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"75000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng nh\\u00e2n vi\\u00ean y t\\u1ebf: Nguy\\u1ec5n Qu\\u1ed1c V\\u0169\",\"date\":\"2025-11-16T18:24:00.000000Z\"}}', NULL, '2025-11-23 11:09:21', '2025-11-23 11:09:21'),
(402, 'default', 'created', 'App\\Models\\Transaction', 'created', 194, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":27,\"vehicle_id\":3,\"type\":\"chi\",\"amount\":\"500000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng l\\u00e1i xe: L\\u00ea Phong\",\"date\":\"2025-11-17T18:35:00.000000Z\"}}', NULL, '2025-11-23 11:10:34', '2025-11-23 11:10:34'),
(403, 'default', 'created', 'App\\Models\\Transaction', 'created', 195, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":27,\"vehicle_id\":3,\"type\":\"chi\",\"amount\":\"550000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng nh\\u00e2n vi\\u00ean y t\\u1ebf: Kh\\u00e1nh\",\"date\":\"2025-11-17T18:35:00.000000Z\"}}', NULL, '2025-11-23 11:10:34', '2025-11-23 11:10:34'),
(404, 'default', 'created', 'App\\Models\\Transaction', 'created', 196, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":27,\"vehicle_id\":3,\"type\":\"chi\",\"amount\":\"500000.00\",\"method\":\"cash\",\"note\":\"Hoa h\\u1ed3ng: Khoa Ph\\u1ed5i - BV \\u0110KL\\u0110\",\"date\":\"2025-11-17T18:35:00.000000Z\"}}', NULL, '2025-11-23 11:10:34', '2025-11-23 11:10:34'),
(405, 'default', 'created', 'App\\Models\\Transaction', 'created', 197, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":28,\"vehicle_id\":4,\"type\":\"chi\",\"amount\":\"500000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng l\\u00e1i xe: Nguy\\u1ec5n C\\u1eefu Ninh\",\"date\":\"2025-11-17T18:37:00.000000Z\"}}', NULL, '2025-11-23 11:12:11', '2025-11-23 11:12:11'),
(406, 'default', 'created', 'App\\Models\\Transaction', 'created', 198, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":28,\"vehicle_id\":4,\"type\":\"chi\",\"amount\":\"550000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng nh\\u00e2n vi\\u00ean y t\\u1ebf: Nguy\\u1ec5n Qu\\u1ed1c V\\u0169\",\"date\":\"2025-11-17T18:37:00.000000Z\"}}', NULL, '2025-11-23 11:12:11', '2025-11-23 11:12:11'),
(407, 'default', 'created', 'App\\Models\\Transaction', 'created', 199, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":29,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"350000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng l\\u00e1i xe: Cil \\u0110oan\",\"date\":\"2025-11-17T18:41:00.000000Z\"}}', NULL, '2025-11-23 11:13:12', '2025-11-23 11:13:12'),
(408, 'default', 'created', 'App\\Models\\Transaction', 'created', 200, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":29,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"350000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng nh\\u00e2n vi\\u00ean y t\\u1ebf: L\\u00e2m\",\"date\":\"2025-11-17T18:41:00.000000Z\"}}', NULL, '2025-11-23 11:13:12', '2025-11-23 11:13:12'),
(409, 'default', 'created', 'App\\Models\\Transaction', 'created', 201, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":29,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"150000.00\",\"method\":\"cash\",\"note\":\"Hoa h\\u1ed3ng: \\u0110\\u1ee9c Anh - khoa HSTC  - BV \\u0110KL\\u0110\",\"date\":\"2025-11-17T18:41:00.000000Z\"}}', NULL, '2025-11-23 11:13:12', '2025-11-23 11:13:12'),
(410, 'default', 'created', 'App\\Models\\Transaction', 'created', 202, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":30,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"450000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng l\\u00e1i xe: Cil \\u0110oan\",\"date\":\"2025-11-17T18:49:00.000000Z\"}}', NULL, '2025-11-23 11:14:37', '2025-11-23 11:14:37'),
(411, 'default', 'created', 'App\\Models\\Transaction', 'created', 203, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":30,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"450000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng nh\\u00e2n vi\\u00ean y t\\u1ebf: \\u0110\\u1ee9c Anh\",\"date\":\"2025-11-17T18:49:00.000000Z\"}}', NULL, '2025-11-23 11:14:37', '2025-11-23 11:14:37'),
(412, 'default', 'created', 'App\\Models\\Transaction', 'created', 204, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":30,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"150000.00\",\"method\":\"cash\",\"note\":\"Hoa h\\u1ed3ng: \\u0110\\u1ee9c Anh - khoa HSTC  - BV \\u0110KL\\u0110\",\"date\":\"2025-11-17T18:49:00.000000Z\"}}', NULL, '2025-11-23 11:14:37', '2025-11-23 11:14:37'),
(413, 'default', 'created', 'App\\Models\\Transaction', 'created', 205, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":31,\"vehicle_id\":3,\"type\":\"chi\",\"amount\":\"500000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng l\\u00e1i xe: L\\u00ea Phong\",\"date\":\"2025-11-18T18:50:00.000000Z\"}}', NULL, '2025-11-23 11:15:57', '2025-11-23 11:15:57'),
(414, 'default', 'created', 'App\\Models\\Transaction', 'created', 206, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":31,\"vehicle_id\":3,\"type\":\"chi\",\"amount\":\"550000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng nh\\u00e2n vi\\u00ean y t\\u1ebf: Nguy\\u1ec5n Qu\\u1ed1c V\\u0169\",\"date\":\"2025-11-18T18:50:00.000000Z\"}}', NULL, '2025-11-23 11:15:57', '2025-11-23 11:15:57'),
(415, 'default', 'created', 'App\\Models\\Transaction', 'created', 207, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":31,\"vehicle_id\":3,\"type\":\"chi\",\"amount\":\"500000.00\",\"method\":\"cash\",\"note\":\"Hoa h\\u1ed3ng: Khoa Ph\\u1ed5i - BV \\u0110KL\\u0110\",\"date\":\"2025-11-18T18:50:00.000000Z\"}}', NULL, '2025-11-23 11:15:57', '2025-11-23 11:15:57'),
(416, 'default', 'created', 'App\\Models\\Location', 'created', 29, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"B\\u1ed3ng lai\",\"type\":\"to\",\"address\":null,\"note\":null,\"is_active\":true}}', NULL, '2025-11-23 11:18:13', '2025-11-23 11:18:13'),
(417, 'default', 'created', 'App\\Models\\Patient', 'created', 27, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"Phan Ng\\u1ecdc Canh\",\"birth_year\":1929,\"phone\":\"0917469795\",\"gender\":\"male\",\"address\":null}}', NULL, '2025-11-23 11:18:13', '2025-11-23 11:18:13'),
(418, 'default', 'created', 'App\\Models\\Incident', 'created', 32, 'App\\Models\\User', 1, '{\"attributes\":{\"vehicle_id\":3,\"patient_id\":27,\"date\":\"2025-11-23T18:16:00.000000Z\",\"dispatch_by\":1,\"destination\":null,\"from_location_id\":10,\"to_location_id\":29,\"partner_id\":null,\"commission_amount\":null,\"summary\":null,\"tags\":null}}', NULL, '2025-11-23 11:18:13', '2025-11-23 11:18:13'),
(419, 'default', 'created', 'App\\Models\\Transaction', 'created', 208, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":32,\"vehicle_id\":3,\"type\":\"chi\",\"amount\":\"200000.00\",\"method\":\"cash\",\"note\":\"C\\u00f4ng l\\u00e1i xe: L\\u00ea Phong\",\"date\":\"2025-11-23T18:16:00.000000Z\"}}', NULL, '2025-11-23 11:18:13', '2025-11-23 11:18:13'),
(420, 'default', 'created', 'App\\Models\\Transaction', 'created', 209, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":32,\"vehicle_id\":3,\"type\":\"chi\",\"amount\":\"200000.00\",\"method\":\"cash\",\"note\":\"C\\u00f4ng NVYT: L\\u00e2m\",\"date\":\"2025-11-23T18:16:00.000000Z\"}}', NULL, '2025-11-23 11:18:13', '2025-11-23 11:18:13'),
(421, 'default', 'created', 'App\\Models\\Transaction', 'created', 210, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":32,\"vehicle_id\":3,\"type\":\"thu\",\"amount\":\"1500000.00\",\"method\":\"cash\",\"note\":\"Thu chuy\\u1ebfn \\u0111i\",\"date\":\"2025-11-23T18:16:00.000000Z\"}}', NULL, '2025-11-23 11:18:13', '2025-11-23 11:18:13');
INSERT INTO `activity_log` (`id`, `log_name`, `description`, `subject_type`, `event`, `subject_id`, `causer_type`, `causer_id`, `properties`, `batch_uuid`, `created_at`, `updated_at`) VALUES
(422, 'default', 'updated', 'App\\Models\\Incident', 'updated', 32, 'App\\Models\\User', 1, '{\"attributes\":{\"date\":\"2025-11-18T18:16:00.000000Z\"},\"old\":{\"date\":\"2025-11-23T18:16:00.000000Z\"}}', NULL, '2025-11-23 11:19:58', '2025-11-23 11:19:58'),
(423, 'default', 'created', 'App\\Models\\Transaction', 'created', 211, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":32,\"vehicle_id\":3,\"type\":\"chi\",\"amount\":\"200000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng l\\u00e1i xe: L\\u00ea Phong\",\"date\":\"2025-11-18T18:16:00.000000Z\"}}', NULL, '2025-11-23 11:19:58', '2025-11-23 11:19:58'),
(424, 'default', 'created', 'App\\Models\\Transaction', 'created', 212, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":32,\"vehicle_id\":3,\"type\":\"chi\",\"amount\":\"200000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng nh\\u00e2n vi\\u00ean y t\\u1ebf: L\\u00e2m\",\"date\":\"2025-11-18T18:16:00.000000Z\"}}', NULL, '2025-11-23 11:19:58', '2025-11-23 11:19:58'),
(425, 'default', 'created', 'App\\Models\\Location', 'created', 30, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"Nam Ban\",\"type\":\"to\",\"address\":null,\"note\":null,\"is_active\":true}}', NULL, '2025-11-23 11:21:29', '2025-11-23 11:21:29'),
(426, 'default', 'created', 'App\\Models\\Patient', 'created', 28, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"Nguy\\u1ec5n Hai \\u0110\\u01b0\\u1ee3c\",\"birth_year\":1968,\"phone\":\"0393906466\",\"gender\":\"male\",\"address\":null}}', NULL, '2025-11-23 11:21:29', '2025-11-23 11:21:29'),
(427, 'default', 'created', 'App\\Models\\Incident', 'created', 33, 'App\\Models\\User', 1, '{\"attributes\":{\"vehicle_id\":2,\"patient_id\":28,\"date\":\"2025-11-18T18:20:00.000000Z\",\"dispatch_by\":1,\"destination\":null,\"from_location_id\":10,\"to_location_id\":30,\"partner_id\":26,\"commission_amount\":\"150000.00\",\"summary\":null,\"tags\":null}}', NULL, '2025-11-23 11:21:29', '2025-11-23 11:21:29'),
(428, 'default', 'created', 'App\\Models\\Transaction', 'created', 213, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":33,\"vehicle_id\":2,\"type\":\"chi\",\"amount\":\"330000.00\",\"method\":\"cash\",\"note\":\"C\\u00f4ng l\\u00e1i xe: Nguy\\u1ec5n C\\u1eefu Ninh\",\"date\":\"2025-11-18T18:20:00.000000Z\"}}', NULL, '2025-11-23 11:21:29', '2025-11-23 11:21:29'),
(429, 'default', 'created', 'App\\Models\\Transaction', 'created', 214, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":33,\"vehicle_id\":2,\"type\":\"chi\",\"amount\":\"330000.00\",\"method\":\"cash\",\"note\":\"C\\u00f4ng NVYT: Nguy\\u1ec5n Qu\\u1ed1c V\\u0169\",\"date\":\"2025-11-18T18:20:00.000000Z\"}}', NULL, '2025-11-23 11:21:29', '2025-11-23 11:21:29'),
(430, 'default', 'created', 'App\\Models\\Transaction', 'created', 215, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":33,\"vehicle_id\":2,\"type\":\"thu\",\"amount\":\"2200000.00\",\"method\":\"cash\",\"note\":\"Thu chuy\\u1ebfn \\u0111i\",\"date\":\"2025-11-18T18:20:00.000000Z\"}}', NULL, '2025-11-23 11:21:29', '2025-11-23 11:21:29'),
(431, 'default', 'created', 'App\\Models\\Transaction', 'created', 216, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":33,\"vehicle_id\":2,\"type\":\"chi\",\"amount\":\"150000.00\",\"method\":\"cash\",\"note\":\"Hoa h\\u1ed3ng: \\u0110\\u1ee9c Anh - khoa HSTC  - BV \\u0110KL\\u0110\",\"date\":\"2025-11-18T18:20:00.000000Z\"}}', NULL, '2025-11-23 11:21:29', '2025-11-23 11:21:29'),
(432, 'default', 'created', 'App\\Models\\Location', 'created', 31, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"Bv Ph\\u1ee5c h\\u1ed3i ch\\u1ee9c n\\u0103ng HCM\",\"type\":\"from\",\"address\":null,\"note\":null,\"is_active\":true}}', NULL, '2025-11-23 11:23:11', '2025-11-23 11:23:11'),
(433, 'default', 'created', 'App\\Models\\Location', 'created', 32, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"V\\u1ea1n Th\\u00e0nh - \\u0110\\u00e0 L\\u1ea1t\",\"type\":\"to\",\"address\":null,\"note\":null,\"is_active\":true}}', NULL, '2025-11-23 11:23:11', '2025-11-23 11:23:11'),
(434, 'default', 'created', 'App\\Models\\Patient', 'created', 29, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"M\\u1eb9 C Trang\",\"birth_year\":null,\"phone\":\"0367564088\",\"gender\":\"female\",\"address\":null}}', NULL, '2025-11-23 11:23:11', '2025-11-23 11:23:11'),
(435, 'default', 'created', 'App\\Models\\Incident', 'created', 34, 'App\\Models\\User', 1, '{\"attributes\":{\"vehicle_id\":3,\"patient_id\":29,\"date\":\"2025-11-18T18:21:00.000000Z\",\"dispatch_by\":1,\"destination\":null,\"from_location_id\":31,\"to_location_id\":32,\"partner_id\":null,\"commission_amount\":null,\"summary\":null,\"tags\":null}}', NULL, '2025-11-23 11:23:11', '2025-11-23 11:23:11'),
(436, 'default', 'created', 'App\\Models\\Transaction', 'created', 217, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":34,\"vehicle_id\":3,\"type\":\"chi\",\"amount\":\"250000.00\",\"method\":\"cash\",\"note\":\"C\\u00f4ng NVYT: Nguy\\u1ec5n Qu\\u1ed1c V\\u0169\",\"date\":\"2025-11-18T18:21:00.000000Z\"}}', NULL, '2025-11-23 11:23:11', '2025-11-23 11:23:11'),
(437, 'default', 'created', 'App\\Models\\Transaction', 'created', 218, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":34,\"vehicle_id\":3,\"type\":\"thu\",\"amount\":\"2500000.00\",\"method\":\"cash\",\"note\":\"Thu chuy\\u1ebfn \\u0111i\",\"date\":\"2025-11-18T18:21:00.000000Z\"}}', NULL, '2025-11-23 11:23:11', '2025-11-23 11:23:11'),
(438, 'default', 'created', 'App\\Models\\Location', 'created', 33, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"Khoa Ngo\\u1ea1i TK - BV \\u0110a khoa L\\u0110\",\"type\":\"from\",\"address\":null,\"note\":null,\"is_active\":true}}', NULL, '2025-11-23 11:24:49', '2025-11-23 11:24:49'),
(439, 'default', 'created', 'App\\Models\\Location', 'created', 34, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"Ph\\u01b0\\u1eddng 10 - \\u0110\\u00e0 L\\u1ea1t\",\"type\":\"to\",\"address\":null,\"note\":null,\"is_active\":true}}', NULL, '2025-11-23 11:24:49', '2025-11-23 11:24:49'),
(440, 'default', 'created', 'App\\Models\\Patient', 'created', 30, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"Nguy\\u1ec5n Th\\u1ecb H\\u1ed3ng Anh\",\"birth_year\":1956,\"phone\":\"0978796479\",\"gender\":\"female\",\"address\":null}}', NULL, '2025-11-23 11:24:49', '2025-11-23 11:24:49'),
(441, 'default', 'created', 'App\\Models\\Incident', 'created', 35, 'App\\Models\\User', 1, '{\"attributes\":{\"vehicle_id\":2,\"patient_id\":30,\"date\":\"2025-11-18T18:23:00.000000Z\",\"dispatch_by\":1,\"destination\":null,\"from_location_id\":33,\"to_location_id\":34,\"partner_id\":null,\"commission_amount\":null,\"summary\":null,\"tags\":null}}', NULL, '2025-11-23 11:24:49', '2025-11-23 11:24:49'),
(442, 'default', 'created', 'App\\Models\\Transaction', 'created', 219, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":35,\"vehicle_id\":2,\"type\":\"chi\",\"amount\":\"75000.00\",\"method\":\"cash\",\"note\":\"C\\u00f4ng l\\u00e1i xe: Nguy\\u1ec5n C\\u1eefu Ninh\",\"date\":\"2025-11-18T18:23:00.000000Z\"}}', NULL, '2025-11-23 11:24:49', '2025-11-23 11:24:49'),
(443, 'default', 'created', 'App\\Models\\Transaction', 'created', 220, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":35,\"vehicle_id\":2,\"type\":\"chi\",\"amount\":\"75000.00\",\"method\":\"cash\",\"note\":\"C\\u00f4ng NVYT: L\\u00e2m\",\"date\":\"2025-11-18T18:23:00.000000Z\"}}', NULL, '2025-11-23 11:24:49', '2025-11-23 11:24:49'),
(444, 'default', 'created', 'App\\Models\\Transaction', 'created', 221, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":35,\"vehicle_id\":2,\"type\":\"thu\",\"amount\":\"500000.00\",\"method\":\"cash\",\"note\":\"Thu chuy\\u1ebfn \\u0111i\",\"date\":\"2025-11-18T18:23:00.000000Z\"}}', NULL, '2025-11-23 11:24:49', '2025-11-23 11:24:49'),
(445, 'default', 'created', 'App\\Models\\Patient', 'created', 31, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"D\\u01b0\\u01a1ng Thanh Gi\\u1ea3n\",\"birth_year\":1941,\"phone\":\"0868742856\",\"gender\":\"male\",\"address\":null}}', NULL, '2025-11-23 11:27:00', '2025-11-23 11:27:00'),
(446, 'default', 'created', 'App\\Models\\Incident', 'created', 36, 'App\\Models\\User', 1, '{\"attributes\":{\"vehicle_id\":3,\"patient_id\":31,\"date\":\"2025-11-19T18:25:00.000000Z\",\"dispatch_by\":1,\"destination\":null,\"from_location_id\":24,\"to_location_id\":3,\"partner_id\":23,\"commission_amount\":\"500000.00\",\"summary\":null,\"tags\":null}}', NULL, '2025-11-23 11:27:00', '2025-11-23 11:27:00'),
(447, 'default', 'created', 'App\\Models\\Transaction', 'created', 222, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":36,\"vehicle_id\":3,\"type\":\"chi\",\"amount\":\"500000.00\",\"method\":\"cash\",\"note\":\"C\\u00f4ng l\\u00e1i xe: Nguy\\u1ec5n C\\u1eefu Ninh\",\"date\":\"2025-11-19T18:25:00.000000Z\"}}', NULL, '2025-11-23 11:27:00', '2025-11-23 11:27:00'),
(448, 'default', 'created', 'App\\Models\\Transaction', 'created', 223, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":36,\"vehicle_id\":3,\"type\":\"chi\",\"amount\":\"550000.00\",\"method\":\"cash\",\"note\":\"C\\u00f4ng NVYT: Nguy\\u1ec5n Qu\\u1ed1c V\\u0169\",\"date\":\"2025-11-19T18:25:00.000000Z\"}}', NULL, '2025-11-23 11:27:01', '2025-11-23 11:27:01'),
(449, 'default', 'created', 'App\\Models\\Transaction', 'created', 224, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":36,\"vehicle_id\":3,\"type\":\"thu\",\"amount\":\"4500000.00\",\"method\":\"cash\",\"note\":\"Thu chuy\\u1ebfn \\u0111i\",\"date\":\"2025-11-19T18:25:00.000000Z\"}}', NULL, '2025-11-23 11:27:01', '2025-11-23 11:27:01'),
(450, 'default', 'created', 'App\\Models\\Transaction', 'created', 225, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":36,\"vehicle_id\":3,\"type\":\"chi\",\"amount\":\"1180000.00\",\"method\":\"cash\",\"note\":\"D\\u1ea7u\",\"date\":\"2025-11-19T18:25:00.000000Z\"}}', NULL, '2025-11-23 11:27:01', '2025-11-23 11:27:01'),
(451, 'default', 'created', 'App\\Models\\Transaction', 'created', 226, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":36,\"vehicle_id\":3,\"type\":\"chi\",\"amount\":\"70000.00\",\"method\":\"cash\",\"note\":\"Chi ph\\u00ed: Oxy\",\"date\":\"2025-11-19T18:25:00.000000Z\"}}', NULL, '2025-11-23 11:27:01', '2025-11-23 11:27:01'),
(452, 'default', 'created', 'App\\Models\\Transaction', 'created', 227, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":36,\"vehicle_id\":3,\"type\":\"chi\",\"amount\":\"500000.00\",\"method\":\"cash\",\"note\":\"Hoa h\\u1ed3ng: Khoa Ph\\u1ed5i - BV \\u0110KL\\u0110\",\"date\":\"2025-11-19T18:25:00.000000Z\"}}', NULL, '2025-11-23 11:27:01', '2025-11-23 11:27:01'),
(453, 'default', 'created', 'App\\Models\\Patient', 'created', 32, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"Tr\\u1ea7n V\\u0103n S\\u1ef9\",\"birth_year\":1966,\"phone\":\"0395849509\",\"gender\":\"male\",\"address\":null}}', NULL, '2025-11-23 11:28:57', '2025-11-23 11:28:57'),
(454, 'default', 'created', 'App\\Models\\Incident', 'created', 37, 'App\\Models\\User', 1, '{\"attributes\":{\"vehicle_id\":2,\"patient_id\":32,\"date\":\"2025-11-19T18:27:00.000000Z\",\"dispatch_by\":1,\"destination\":null,\"from_location_id\":10,\"to_location_id\":15,\"partner_id\":26,\"commission_amount\":\"150000.00\",\"summary\":null,\"tags\":null}}', NULL, '2025-11-23 11:28:57', '2025-11-23 11:28:57'),
(455, 'default', 'created', 'App\\Models\\Transaction', 'created', 228, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":37,\"vehicle_id\":2,\"type\":\"chi\",\"amount\":\"300000.00\",\"method\":\"cash\",\"note\":\"C\\u00f4ng l\\u00e1i xe: L\\u00ea Phong\",\"date\":\"2025-11-19T18:27:00.000000Z\"}}', NULL, '2025-11-23 11:28:57', '2025-11-23 11:28:57'),
(456, 'default', 'created', 'App\\Models\\Transaction', 'created', 229, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":37,\"vehicle_id\":2,\"type\":\"chi\",\"amount\":\"300000.00\",\"method\":\"cash\",\"note\":\"C\\u00f4ng NVYT: Nguy\\u1ec5n Qu\\u1ed1c V\\u0169\",\"date\":\"2025-11-19T18:27:00.000000Z\"}}', NULL, '2025-11-23 11:28:57', '2025-11-23 11:28:57'),
(457, 'default', 'created', 'App\\Models\\Transaction', 'created', 230, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":37,\"vehicle_id\":2,\"type\":\"thu\",\"amount\":\"1800000.00\",\"method\":\"cash\",\"note\":\"Thu chuy\\u1ebfn \\u0111i\",\"date\":\"2025-11-19T18:27:00.000000Z\"}}', NULL, '2025-11-23 11:28:57', '2025-11-23 11:28:57'),
(458, 'default', 'created', 'App\\Models\\Transaction', 'created', 231, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":37,\"vehicle_id\":2,\"type\":\"chi\",\"amount\":\"500000.00\",\"method\":\"cash\",\"note\":\"D\\u1ea7u\",\"date\":\"2025-11-19T18:27:00.000000Z\"}}', NULL, '2025-11-23 11:28:57', '2025-11-23 11:28:57'),
(459, 'default', 'created', 'App\\Models\\Transaction', 'created', 232, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":37,\"vehicle_id\":2,\"type\":\"chi\",\"amount\":\"150000.00\",\"method\":\"cash\",\"note\":\"Hoa h\\u1ed3ng: \\u0110\\u1ee9c Anh - khoa HSTC  - BV \\u0110KL\\u0110\",\"date\":\"2025-11-19T18:27:00.000000Z\"}}', NULL, '2025-11-23 11:28:57', '2025-11-23 11:28:57'),
(460, 'default', 'created', 'App\\Models\\Patient', 'created', 33, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"Ha So\",\"birth_year\":1963,\"phone\":\"0366254652\",\"gender\":\"male\",\"address\":null}}', NULL, '2025-11-23 11:30:58', '2025-11-23 11:30:58'),
(461, 'default', 'created', 'App\\Models\\Incident', 'created', 38, 'App\\Models\\User', 1, '{\"attributes\":{\"vehicle_id\":2,\"patient_id\":33,\"date\":\"2025-11-19T18:29:00.000000Z\",\"dispatch_by\":1,\"destination\":null,\"from_location_id\":10,\"to_location_id\":15,\"partner_id\":26,\"commission_amount\":\"150000.00\",\"summary\":null,\"tags\":null}}', NULL, '2025-11-23 11:30:58', '2025-11-23 11:30:58'),
(462, 'default', 'created', 'App\\Models\\Transaction', 'created', 233, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":38,\"vehicle_id\":2,\"type\":\"chi\",\"amount\":\"300000.00\",\"method\":\"cash\",\"note\":\"C\\u00f4ng l\\u00e1i xe: L\\u00ea Phong\",\"date\":\"2025-11-19T18:29:00.000000Z\"}}', NULL, '2025-11-23 11:30:58', '2025-11-23 11:30:58'),
(463, 'default', 'created', 'App\\Models\\Transaction', 'created', 234, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":38,\"vehicle_id\":2,\"type\":\"chi\",\"amount\":\"300000.00\",\"method\":\"cash\",\"note\":\"C\\u00f4ng NVYT: \\u0110\\u1ee9c Anh\",\"date\":\"2025-11-19T18:29:00.000000Z\"}}', NULL, '2025-11-23 11:30:58', '2025-11-23 11:30:58'),
(464, 'default', 'created', 'App\\Models\\Transaction', 'created', 235, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":38,\"vehicle_id\":2,\"type\":\"thu\",\"amount\":\"1800000.00\",\"method\":\"cash\",\"note\":\"Thu chuy\\u1ebfn \\u0111i\",\"date\":\"2025-11-19T18:29:00.000000Z\"}}', NULL, '2025-11-23 11:30:58', '2025-11-23 11:30:58'),
(465, 'default', 'created', 'App\\Models\\Transaction', 'created', 236, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":38,\"vehicle_id\":2,\"type\":\"chi\",\"amount\":\"150000.00\",\"method\":\"cash\",\"note\":\"Hoa h\\u1ed3ng: \\u0110\\u1ee9c Anh - khoa HSTC  - BV \\u0110KL\\u0110\",\"date\":\"2025-11-19T18:29:00.000000Z\"}}', NULL, '2025-11-23 11:30:58', '2025-11-23 11:30:58'),
(466, 'default', 'created', 'App\\Models\\Location', 'created', 35, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"Khoa n\\u1ed9i B - BV \\u0110a khoa L\\u0110\",\"type\":\"from\",\"address\":null,\"note\":null,\"is_active\":true}}', NULL, '2025-11-23 11:32:50', '2025-11-23 11:32:50'),
(467, 'default', 'created', 'App\\Models\\Patient', 'created', 34, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"Nguy\\u1ec5n Th\\u1ecb An\",\"birth_year\":1964,\"phone\":\"0987332969\",\"gender\":null,\"address\":null}}', NULL, '2025-11-23 11:32:50', '2025-11-23 11:32:50'),
(468, 'default', 'created', 'App\\Models\\Incident', 'created', 39, 'App\\Models\\User', 1, '{\"attributes\":{\"vehicle_id\":3,\"patient_id\":34,\"date\":\"2025-11-20T18:31:00.000000Z\",\"dispatch_by\":1,\"destination\":null,\"from_location_id\":35,\"to_location_id\":1,\"partner_id\":19,\"commission_amount\":\"500000.00\",\"summary\":null,\"tags\":null}}', NULL, '2025-11-23 11:32:50', '2025-11-23 11:32:50'),
(469, 'default', 'created', 'App\\Models\\Transaction', 'created', 237, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":39,\"vehicle_id\":3,\"type\":\"chi\",\"amount\":\"500000.00\",\"method\":\"cash\",\"note\":\"C\\u00f4ng l\\u00e1i xe: L\\u00ea Phong\",\"date\":\"2025-11-20T18:31:00.000000Z\"}}', NULL, '2025-11-23 11:32:50', '2025-11-23 11:32:50'),
(470, 'default', 'created', 'App\\Models\\Transaction', 'created', 238, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":39,\"vehicle_id\":3,\"type\":\"chi\",\"amount\":\"550000.00\",\"method\":\"cash\",\"note\":\"C\\u00f4ng NVYT: Nguy\\u1ec5n Qu\\u1ed1c V\\u0169\",\"date\":\"2025-11-20T18:31:00.000000Z\"}}', NULL, '2025-11-23 11:32:50', '2025-11-23 11:32:50'),
(471, 'default', 'created', 'App\\Models\\Transaction', 'created', 239, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":39,\"vehicle_id\":3,\"type\":\"thu\",\"amount\":\"4500000.00\",\"method\":\"cash\",\"note\":\"Thu chuy\\u1ebfn \\u0111i\",\"date\":\"2025-11-20T18:31:00.000000Z\"}}', NULL, '2025-11-23 11:32:50', '2025-11-23 11:32:50'),
(472, 'default', 'created', 'App\\Models\\Transaction', 'created', 240, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":39,\"vehicle_id\":3,\"type\":\"chi\",\"amount\":\"1270000.00\",\"method\":\"cash\",\"note\":\"D\\u1ea7u\",\"date\":\"2025-11-20T18:31:00.000000Z\"}}', NULL, '2025-11-23 11:32:50', '2025-11-23 11:32:50'),
(473, 'default', 'created', 'App\\Models\\Transaction', 'created', 241, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":39,\"vehicle_id\":3,\"type\":\"chi\",\"amount\":\"500000.00\",\"method\":\"cash\",\"note\":\"Hoa h\\u1ed3ng: Khoa n\\u1ed9i B - BV \\u0110KL\\u0110\",\"date\":\"2025-11-20T18:31:00.000000Z\"}}', NULL, '2025-11-23 11:32:50', '2025-11-23 11:32:50'),
(474, 'default', 'created', 'App\\Models\\Location', 'created', 36, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"Tam B\\u1ed1 - Di Linh\",\"type\":\"to\",\"address\":null,\"note\":null,\"is_active\":true}}', NULL, '2025-11-23 11:34:32', '2025-11-23 11:34:32'),
(475, 'default', 'created', 'App\\Models\\Patient', 'created', 35, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"L\\u00ea Th\\u1ecb Sung\",\"birth_year\":1937,\"phone\":\"0919440592\",\"gender\":\"female\",\"address\":null}}', NULL, '2025-11-23 11:34:32', '2025-11-23 11:34:32'),
(476, 'default', 'created', 'App\\Models\\Incident', 'created', 40, 'App\\Models\\User', 1, '{\"attributes\":{\"vehicle_id\":2,\"patient_id\":35,\"date\":\"2025-11-20T18:32:00.000000Z\",\"dispatch_by\":1,\"destination\":null,\"from_location_id\":10,\"to_location_id\":36,\"partner_id\":26,\"commission_amount\":\"150000.00\",\"summary\":null,\"tags\":null}}', NULL, '2025-11-23 11:34:32', '2025-11-23 11:34:32'),
(477, 'default', 'created', 'App\\Models\\Transaction', 'created', 242, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":40,\"vehicle_id\":2,\"type\":\"chi\",\"amount\":\"450000.00\",\"method\":\"cash\",\"note\":\"C\\u00f4ng l\\u00e1i xe: Nguy\\u1ec5n C\\u1eefu Ninh\",\"date\":\"2025-11-20T18:32:00.000000Z\"}}', NULL, '2025-11-23 11:34:32', '2025-11-23 11:34:32'),
(478, 'default', 'created', 'App\\Models\\Transaction', 'created', 243, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":40,\"vehicle_id\":2,\"type\":\"chi\",\"amount\":\"450000.00\",\"method\":\"cash\",\"note\":\"C\\u00f4ng NVYT: Ph\\u00fac\",\"date\":\"2025-11-20T18:32:00.000000Z\"}}', NULL, '2025-11-23 11:34:32', '2025-11-23 11:34:32'),
(479, 'default', 'created', 'App\\Models\\Transaction', 'created', 244, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":40,\"vehicle_id\":2,\"type\":\"thu\",\"amount\":\"3000000.00\",\"method\":\"cash\",\"note\":\"Thu chuy\\u1ebfn \\u0111i\",\"date\":\"2025-11-20T18:32:00.000000Z\"}}', NULL, '2025-11-23 11:34:32', '2025-11-23 11:34:32'),
(480, 'default', 'created', 'App\\Models\\Transaction', 'created', 245, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":40,\"vehicle_id\":2,\"type\":\"chi\",\"amount\":\"500000.00\",\"method\":\"cash\",\"note\":\"D\\u1ea7u\",\"date\":\"2025-11-20T18:32:00.000000Z\"}}', NULL, '2025-11-23 11:34:32', '2025-11-23 11:34:32'),
(481, 'default', 'created', 'App\\Models\\Transaction', 'created', 246, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":40,\"vehicle_id\":2,\"type\":\"chi\",\"amount\":\"150000.00\",\"method\":\"cash\",\"note\":\"Hoa h\\u1ed3ng: \\u0110\\u1ee9c Anh - khoa HSTC  - BV \\u0110KL\\u0110\",\"date\":\"2025-11-20T18:32:00.000000Z\"}}', NULL, '2025-11-23 11:34:32', '2025-11-23 11:34:32'),
(482, 'default', 'created', 'App\\Models\\Location', 'created', 37, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"Ph\\u00fac Th\\u1ecd - L\\u00e2m H\\u00e0\",\"type\":\"to\",\"address\":null,\"note\":null,\"is_active\":true}}', NULL, '2025-11-23 11:35:57', '2025-11-23 11:35:57'),
(483, 'default', 'created', 'App\\Models\\Patient', 'created', 36, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"Nguy\\u1ec5n V\\u0103n Thu\\u1eadn\",\"birth_year\":1982,\"phone\":\"0397709180\",\"gender\":\"male\",\"address\":null}}', NULL, '2025-11-23 11:35:57', '2025-11-23 11:35:57'),
(484, 'default', 'created', 'App\\Models\\Incident', 'created', 41, 'App\\Models\\User', 1, '{\"attributes\":{\"vehicle_id\":1,\"patient_id\":36,\"date\":\"2025-11-20T18:34:00.000000Z\",\"dispatch_by\":1,\"destination\":null,\"from_location_id\":10,\"to_location_id\":37,\"partner_id\":null,\"commission_amount\":null,\"summary\":null,\"tags\":null}}', NULL, '2025-11-23 11:35:57', '2025-11-23 11:35:57'),
(485, 'default', 'created', 'App\\Models\\Transaction', 'created', 247, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":41,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"230000.00\",\"method\":\"cash\",\"note\":\"C\\u00f4ng l\\u00e1i xe: Cil \\u0110oan\",\"date\":\"2025-11-20T18:34:00.000000Z\"}}', NULL, '2025-11-23 11:35:57', '2025-11-23 11:35:57'),
(486, 'default', 'created', 'App\\Models\\Transaction', 'created', 248, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":41,\"vehicle_id\":1,\"type\":\"thu\",\"amount\":\"1800000.00\",\"method\":\"cash\",\"note\":\"Thu chuy\\u1ebfn \\u0111i\",\"date\":\"2025-11-20T18:34:00.000000Z\"}}', NULL, '2025-11-23 11:35:57', '2025-11-23 11:35:57'),
(487, 'default', 'created', 'App\\Models\\Patient', 'created', 37, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"K\' Thoen\",\"birth_year\":2003,\"phone\":\"0984050265\",\"gender\":\"female\",\"address\":null}}', NULL, '2025-11-23 11:39:28', '2025-11-23 11:39:28'),
(488, 'default', 'created', 'App\\Models\\Incident', 'created', 42, 'App\\Models\\User', 1, '{\"attributes\":{\"vehicle_id\":4,\"patient_id\":37,\"date\":\"2025-11-21T18:36:00.000000Z\",\"dispatch_by\":1,\"destination\":null,\"from_location_id\":10,\"to_location_id\":1,\"partner_id\":18,\"commission_amount\":\"500000.00\",\"summary\":null,\"tags\":null}}', NULL, '2025-11-23 11:39:28', '2025-11-23 11:39:28'),
(489, 'default', 'created', 'App\\Models\\Transaction', 'created', 249, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":42,\"vehicle_id\":4,\"type\":\"chi\",\"amount\":\"500000.00\",\"method\":\"cash\",\"note\":\"C\\u00f4ng l\\u00e1i xe: Nguy\\u1ec5n C\\u1eefu Ninh\",\"date\":\"2025-11-21T18:36:00.000000Z\"}}', NULL, '2025-11-23 11:39:28', '2025-11-23 11:39:28'),
(490, 'default', 'created', 'App\\Models\\Transaction', 'created', 250, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":42,\"vehicle_id\":4,\"type\":\"chi\",\"amount\":\"550000.00\",\"method\":\"cash\",\"note\":\"C\\u00f4ng NVYT: Nguy\\u1ec5n Qu\\u1ed1c V\\u0169\",\"date\":\"2025-11-21T18:36:00.000000Z\"}}', NULL, '2025-11-23 11:39:28', '2025-11-23 11:39:28'),
(491, 'default', 'created', 'App\\Models\\Transaction', 'created', 251, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":42,\"vehicle_id\":4,\"type\":\"thu\",\"amount\":\"4500000.00\",\"method\":\"cash\",\"note\":\"Thu chuy\\u1ebfn \\u0111i\",\"date\":\"2025-11-21T18:36:00.000000Z\"}}', NULL, '2025-11-23 11:39:28', '2025-11-23 11:39:28'),
(492, 'default', 'created', 'App\\Models\\Transaction', 'created', 252, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":42,\"vehicle_id\":4,\"type\":\"chi\",\"amount\":\"1430000.00\",\"method\":\"cash\",\"note\":\"D\\u1ea7u\",\"date\":\"2025-11-21T18:36:00.000000Z\"}}', NULL, '2025-11-23 11:39:28', '2025-11-23 11:39:28'),
(493, 'default', 'created', 'App\\Models\\Transaction', 'created', 253, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":42,\"vehicle_id\":4,\"type\":\"chi\",\"amount\":\"60000.00\",\"method\":\"cash\",\"note\":\"Chi ph\\u00ed: D\\u00e1n l\\u1ea1i Epass\",\"date\":\"2025-11-21T18:36:00.000000Z\"}}', NULL, '2025-11-23 11:39:28', '2025-11-23 11:39:28'),
(494, 'default', 'created', 'App\\Models\\Transaction', 'created', 254, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":42,\"vehicle_id\":4,\"type\":\"chi\",\"amount\":\"500000.00\",\"method\":\"cash\",\"note\":\"Hoa h\\u1ed3ng: Khoa HSTC - BV \\u0110KL\\u0110\",\"date\":\"2025-11-21T18:36:00.000000Z\"}}', NULL, '2025-11-23 11:39:28', '2025-11-23 11:39:28'),
(495, 'default', 'created', 'App\\Models\\Transaction', 'created', 255, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":null,\"vehicle_id\":4,\"type\":\"chi\",\"amount\":\"1350000.00\",\"method\":\"bank\",\"note\":\"L\\u1ea5y gi\\u1ea5y t\\u1edd \\u0111i \\u0111\\u01b0\\u1eddng, ph\\u00ed c\\u00f4ng ch\\u1ee9ng  CP g\\u1eedi NH\",\"date\":\"2025-11-10T18:40:00.000000Z\"}}', NULL, '2025-11-23 11:41:16', '2025-11-23 11:41:16'),
(496, 'default', 'created', 'App\\Models\\Transaction', 'created', 256, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":null,\"vehicle_id\":3,\"type\":\"chi\",\"amount\":\"1350000.00\",\"method\":\"bank\",\"note\":\"L\\u1ea5y gi\\u1ea5y t\\u1edd \\u0111i \\u0111\\u01b0\\u1eddng, ph\\u00ed c\\u00f4ng ch\\u1ee9ng  CP g\\u1eedi NH\",\"date\":\"2025-11-10T18:41:00.000000Z\"}}', NULL, '2025-11-23 11:41:46', '2025-11-23 11:41:46'),
(497, 'default', 'created', 'App\\Models\\Transaction', 'created', 257, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":null,\"vehicle_id\":4,\"type\":\"chi\",\"amount\":\"1125000.00\",\"method\":\"bank\",\"note\":\"V\\u1eadt t\\u01b0 xe m\\u1edbi (\\u0110\\u1ee9c Anh mua)  Ck Cho DCYK Hai Dang\",\"date\":\"2025-11-23T18:41:00.000000Z\"}}', NULL, '2025-11-23 11:42:32', '2025-11-23 11:42:32'),
(498, 'default', 'updated', 'App\\Models\\Transaction', 'updated', 257, 'App\\Models\\User', 1, '{\"attributes\":{\"date\":\"2025-11-15T18:41:00.000000Z\"},\"old\":{\"date\":\"2025-11-23T18:41:00.000000Z\"}}', NULL, '2025-11-23 11:43:08', '2025-11-23 11:43:08'),
(499, 'default', 'created', 'App\\Models\\Transaction', 'created', 258, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":null,\"vehicle_id\":3,\"type\":\"chi\",\"amount\":\"1350000.00\",\"method\":\"bank\",\"note\":\"L\\u1ea5y gi\\u1ea5y t\\u1edd \\u0111i \\u0111\\u01b0\\u1eddng, ph\\u00ed c\\u00f4ng ch\\u1ee9ng  CP g\\u1eedi NH\",\"date\":\"2025-11-10T19:16:00.000000Z\"}}', NULL, '2025-11-23 12:17:04', '2025-11-23 12:17:04'),
(500, 'default', 'deleted', 'App\\Models\\Transaction', 'deleted', 258, 'App\\Models\\User', 1, '{\"old\":{\"incident_id\":null,\"vehicle_id\":3,\"type\":\"chi\",\"amount\":\"1350000.00\",\"method\":\"bank\",\"note\":\"L\\u1ea5y gi\\u1ea5y t\\u1edd \\u0111i \\u0111\\u01b0\\u1eddng, ph\\u00ed c\\u00f4ng ch\\u1ee9ng  CP g\\u1eedi NH\",\"date\":\"2025-11-10T19:16:00.000000Z\"}}', NULL, '2025-11-23 12:17:33', '2025-11-23 12:17:33'),
(501, 'default', 'created', 'App\\Models\\Transaction', 'created', 259, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":null,\"vehicle_id\":3,\"type\":\"chi\",\"amount\":\"1125000.00\",\"method\":\"cash\",\"note\":\"V\\u1eadt t\\u01b0 xe m\\u1edbi (\\u0110\\u1ee9c Anh mua)  Ck Cho DCYK Hai Dang\",\"date\":\"2025-11-15T19:18:00.000000Z\"}}', NULL, '2025-11-23 12:18:41', '2025-11-23 12:18:41'),
(502, 'default', 'created', 'App\\Models\\Transaction', 'created', 260, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":null,\"vehicle_id\":null,\"type\":\"chi\",\"amount\":\"400000.00\",\"method\":\"bank\",\"note\":\"Mua t\\u00fai \\u0111\\u1ef1ng oxy, d\\u00e2y, kim ti\\u00eam xe 506.14\",\"date\":\"2025-11-21T19:19:00.000000Z\"}}', NULL, '2025-11-23 12:19:47', '2025-11-23 12:19:47'),
(503, 'default', 'created', 'App\\Models\\VehicleMaintenance', 'created', 25, 'App\\Models\\User', 1, '{\"attributes\":{\"vehicle_id\":3,\"maintenance_service_id\":3,\"partner_id\":3,\"incident_id\":null,\"date\":\"2025-11-20T00:00:00.000000Z\",\"cost\":\"120000.00\",\"mileage\":null,\"description\":\"CT r\\u1eefa xe 313.84\",\"note\":null}}', NULL, '2025-11-23 12:20:45', '2025-11-23 12:20:45'),
(504, 'default', 'created', 'App\\Models\\Transaction', 'created', 261, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":null,\"vehicle_id\":3,\"type\":\"chi\",\"amount\":\"120000.00\",\"method\":\"cash\",\"note\":\"[B\\u1ea3o tr\\u00ec] R\\u1eeda xe - Gara ngo\\u00e0i - CT r\\u1eefa xe 313.84\",\"date\":\"2025-11-20T00:00:00.000000Z\"}}', NULL, '2025-11-23 12:20:45', '2025-11-23 12:20:45'),
(505, 'default', 'created', 'App\\Models\\Patient', 'created', 38, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"Tr\\u1ea7n Thanh B\\u00ecnh\",\"birth_year\":1980,\"phone\":\"0396320417\",\"gender\":null,\"address\":null}}', NULL, '2025-11-23 12:22:48', '2025-11-23 12:22:48'),
(506, 'default', 'created', 'App\\Models\\Incident', 'created', 43, 'App\\Models\\User', 1, '{\"attributes\":{\"vehicle_id\":3,\"patient_id\":38,\"date\":\"2025-11-21T19:21:00.000000Z\",\"dispatch_by\":1,\"destination\":null,\"from_location_id\":24,\"to_location_id\":1,\"partner_id\":23,\"commission_amount\":\"500000.00\",\"summary\":null,\"tags\":null}}', NULL, '2025-11-23 12:22:48', '2025-11-23 12:22:48'),
(507, 'default', 'created', 'App\\Models\\Transaction', 'created', 262, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":43,\"vehicle_id\":3,\"type\":\"chi\",\"amount\":\"500000.00\",\"method\":\"cash\",\"note\":\"C\\u00f4ng l\\u00e1i xe: L\\u00ea Phong\",\"date\":\"2025-11-21T19:21:00.000000Z\"}}', NULL, '2025-11-23 12:22:48', '2025-11-23 12:22:48'),
(508, 'default', 'created', 'App\\Models\\Transaction', 'created', 263, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":43,\"vehicle_id\":3,\"type\":\"chi\",\"amount\":\"550000.00\",\"method\":\"cash\",\"note\":\"C\\u00f4ng NVYT: L\\u00e2m\",\"date\":\"2025-11-21T19:21:00.000000Z\"}}', NULL, '2025-11-23 12:22:48', '2025-11-23 12:22:48'),
(509, 'default', 'created', 'App\\Models\\Transaction', 'created', 264, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":43,\"vehicle_id\":3,\"type\":\"thu\",\"amount\":\"4500000.00\",\"method\":\"cash\",\"note\":\"Thu chuy\\u1ebfn \\u0111i\",\"date\":\"2025-11-21T19:21:00.000000Z\"}}', NULL, '2025-11-23 12:22:48', '2025-11-23 12:22:48'),
(510, 'default', 'created', 'App\\Models\\Transaction', 'created', 265, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":43,\"vehicle_id\":3,\"type\":\"chi\",\"amount\":\"1190000.00\",\"method\":\"cash\",\"note\":\"D\\u1ea7u\",\"date\":\"2025-11-21T19:21:00.000000Z\"}}', NULL, '2025-11-23 12:22:48', '2025-11-23 12:22:48'),
(511, 'default', 'created', 'App\\Models\\Transaction', 'created', 266, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":43,\"vehicle_id\":3,\"type\":\"chi\",\"amount\":\"500000.00\",\"method\":\"cash\",\"note\":\"Hoa h\\u1ed3ng: Khoa Ph\\u1ed5i - BV \\u0110KL\\u0110\",\"date\":\"2025-11-21T19:21:00.000000Z\"}}', NULL, '2025-11-23 12:22:48', '2025-11-23 12:22:48'),
(512, 'default', 'created', 'App\\Models\\Transaction', 'created', 267, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":null,\"vehicle_id\":4,\"type\":\"chi\",\"amount\":\"1000000.00\",\"method\":\"bank\",\"note\":\"\\u0110\\u1eb7t c\\u1ecdc 1 b\\u00ecnh oxy\",\"date\":\"2025-11-23T19:29:00.000000Z\"}}', NULL, '2025-11-23 12:30:05', '2025-11-23 12:30:05'),
(513, 'default', 'created', 'App\\Models\\Transaction', 'created', 268, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":null,\"vehicle_id\":4,\"type\":\"thu\",\"amount\":\"447000.00\",\"method\":\"cash\",\"note\":\"M\\u00e1y h\\u00fat \\u0111\\u1eddm  Ck \\u0110.Anh mua\",\"date\":\"2025-11-21T19:30:00.000000Z\"}}', NULL, '2025-11-23 12:30:31', '2025-11-23 12:30:31'),
(514, 'default', 'created', 'App\\Models\\Transaction', 'created', 269, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":null,\"vehicle_id\":3,\"type\":\"thu\",\"amount\":\"447000.00\",\"method\":\"cash\",\"note\":\"M\\u00e1y h\\u00fat \\u0111\\u1eddm  Ck \\u0110.Anh mua\",\"date\":\"2025-11-21T19:30:00.000000Z\"}}', NULL, '2025-11-23 12:30:55', '2025-11-23 12:30:55'),
(515, 'default', 'updated', 'App\\Models\\Transaction', 'updated', 269, 'App\\Models\\User', 1, '{\"attributes\":{\"type\":\"chi\"},\"old\":{\"type\":\"thu\"}}', NULL, '2025-11-23 12:38:59', '2025-11-23 12:38:59'),
(516, 'default', 'updated', 'App\\Models\\Transaction', 'updated', 268, 'App\\Models\\User', 1, '{\"attributes\":{\"type\":\"chi\"},\"old\":{\"type\":\"thu\"}}', NULL, '2025-11-23 12:39:11', '2025-11-23 12:39:11'),
(517, 'default', 'updated', 'App\\Models\\Incident', 'updated', 13, 'App\\Models\\User', 1, '{\"attributes\":{\"from_location_id\":10,\"to_location_id\":15},\"old\":{\"from_location_id\":null,\"to_location_id\":null}}', NULL, '2025-11-24 06:04:59', '2025-11-24 06:04:59'),
(518, 'default', 'created', 'App\\Models\\Transaction', 'created', 270, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":13,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"300000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng l\\u00e1i xe: L\\u00ea Phong\",\"date\":\"2025-11-04T18:00:00.000000Z\"}}', NULL, '2025-11-24 06:04:59', '2025-11-24 06:04:59'),
(519, 'default', 'created', 'App\\Models\\Transaction', 'created', 271, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":13,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"300000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng nh\\u00e2n vi\\u00ean y t\\u1ebf: Nguy\\u1ec5n Qu\\u1ed1c V\\u0169\",\"date\":\"2025-11-04T18:00:00.000000Z\"}}', NULL, '2025-11-24 06:04:59', '2025-11-24 06:04:59'),
(520, 'default', 'created', 'App\\Models\\Transaction', 'created', 272, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":13,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"150000.00\",\"method\":\"cash\",\"note\":\"Hoa h\\u1ed3ng: \\u0110\\u1ee9c Anh - khoa HSTC  - BV \\u0110KL\\u0110\",\"date\":\"2025-11-04T18:00:00.000000Z\"}}', NULL, '2025-11-24 06:04:59', '2025-11-24 06:04:59'),
(521, 'default', 'created', 'App\\Models\\Patient', 'created', 39, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"N\\u00f4ng V\\u0103n Ph\\u1ea3u\",\"birth_year\":1946,\"phone\":\"0565265346\",\"gender\":\"male\",\"address\":null}}', NULL, '2025-11-24 06:09:23', '2025-11-24 06:09:23'),
(522, 'default', 'created', 'App\\Models\\Incident', 'created', 44, 'App\\Models\\User', 1, '{\"attributes\":{\"vehicle_id\":3,\"patient_id\":39,\"date\":\"2025-11-23T13:06:00.000000Z\",\"dispatch_by\":1,\"destination\":null,\"from_location_id\":null,\"to_location_id\":null,\"partner_id\":18,\"commission_amount\":\"500000.00\",\"summary\":null,\"tags\":null}}', NULL, '2025-11-24 06:09:23', '2025-11-24 06:09:23'),
(523, 'default', 'created', 'App\\Models\\Transaction', 'created', 273, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":44,\"vehicle_id\":3,\"type\":\"chi\",\"amount\":\"550000.00\",\"method\":\"bank\",\"note\":\"C\\u00f4ng l\\u00e1i xe: L\\u00ea Phong\",\"date\":\"2025-11-23T13:06:00.000000Z\"}}', NULL, '2025-11-24 06:09:23', '2025-11-24 06:09:23'),
(524, 'default', 'created', 'App\\Models\\Transaction', 'created', 274, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":44,\"vehicle_id\":3,\"type\":\"chi\",\"amount\":\"600000.00\",\"method\":\"bank\",\"note\":\"C\\u00f4ng NVYT: \\u0110\\u1ee9c Anh\",\"date\":\"2025-11-23T13:06:00.000000Z\"}}', NULL, '2025-11-24 06:09:23', '2025-11-24 06:09:23'),
(525, 'default', 'created', 'App\\Models\\Transaction', 'created', 275, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":44,\"vehicle_id\":3,\"type\":\"thu\",\"amount\":\"4700000.00\",\"method\":\"bank\",\"note\":\"Thu chuy\\u1ebfn \\u0111i\",\"date\":\"2025-11-23T13:06:00.000000Z\"}}', NULL, '2025-11-24 06:09:23', '2025-11-24 06:09:23'),
(526, 'default', 'created', 'App\\Models\\Transaction', 'created', 276, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":44,\"vehicle_id\":3,\"type\":\"chi\",\"amount\":\"1210000.00\",\"method\":\"bank\",\"note\":\"D\\u1ea7u\",\"date\":\"2025-11-23T13:06:00.000000Z\"}}', NULL, '2025-11-24 06:09:23', '2025-11-24 06:09:23'),
(527, 'default', 'created', 'App\\Models\\Transaction', 'created', 277, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":44,\"vehicle_id\":3,\"type\":\"chi\",\"amount\":\"60000.00\",\"method\":\"bank\",\"note\":\"Chi ph\\u00ed: D\\u00e1n l\\u1ea1i Epass\",\"date\":\"2025-11-23T13:06:00.000000Z\"}}', NULL, '2025-11-24 06:09:23', '2025-11-24 06:09:23'),
(528, 'default', 'created', 'App\\Models\\Transaction', 'created', 278, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":44,\"vehicle_id\":3,\"type\":\"chi\",\"amount\":\"50000.00\",\"method\":\"bank\",\"note\":\"Chi ph\\u00ed: V\\u00e1 v\\u1ecf\",\"date\":\"2025-11-23T13:06:00.000000Z\"}}', NULL, '2025-11-24 06:09:23', '2025-11-24 06:09:23'),
(529, 'default', 'created', 'App\\Models\\Transaction', 'created', 279, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":44,\"vehicle_id\":3,\"type\":\"chi\",\"amount\":\"140000.00\",\"method\":\"bank\",\"note\":\"Chi ph\\u00ed: Oxy - 2 b\\u00ecnh\",\"date\":\"2025-11-23T13:06:00.000000Z\"}}', NULL, '2025-11-24 06:09:23', '2025-11-24 06:09:23'),
(530, 'default', 'created', 'App\\Models\\Transaction', 'created', 280, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":44,\"vehicle_id\":3,\"type\":\"chi\",\"amount\":\"500000.00\",\"method\":\"bank\",\"note\":\"Hoa h\\u1ed3ng: Khoa HSTC - BV \\u0110KL\\u0110\",\"date\":\"2025-11-23T13:06:00.000000Z\"}}', NULL, '2025-11-24 06:09:23', '2025-11-24 06:09:23'),
(531, 'default', 'created', 'App\\Models\\Patient', 'created', 40, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"Ph\\u1ea1m S\\u1ef9\",\"birth_year\":1955,\"phone\":\"0707577767\",\"gender\":\"male\",\"address\":null}}', NULL, '2025-11-24 06:11:33', '2025-11-24 06:11:33'),
(532, 'default', 'created', 'App\\Models\\Incident', 'created', 45, 'App\\Models\\User', 1, '{\"attributes\":{\"vehicle_id\":4,\"patient_id\":40,\"date\":\"2025-11-24T13:09:00.000000Z\",\"dispatch_by\":1,\"destination\":null,\"from_location_id\":10,\"to_location_id\":1,\"partner_id\":18,\"commission_amount\":\"500000.00\",\"summary\":null,\"tags\":null}}', NULL, '2025-11-24 06:11:33', '2025-11-24 06:11:33'),
(533, 'default', 'created', 'App\\Models\\Transaction', 'created', 281, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":45,\"vehicle_id\":4,\"type\":\"chi\",\"amount\":\"550000.00\",\"method\":\"bank\",\"note\":\"C\\u00f4ng l\\u00e1i xe: Nguy\\u1ec5n C\\u1eefu Ninh\",\"date\":\"2025-11-24T13:09:00.000000Z\"}}', NULL, '2025-11-24 06:11:33', '2025-11-24 06:11:33'),
(534, 'default', 'created', 'App\\Models\\Transaction', 'created', 282, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":45,\"vehicle_id\":4,\"type\":\"chi\",\"amount\":\"600000.00\",\"method\":\"bank\",\"note\":\"C\\u00f4ng NVYT: Nguy\\u1ec5n Qu\\u1ed1c V\\u0169\",\"date\":\"2025-11-24T13:09:00.000000Z\"}}', NULL, '2025-11-24 06:11:33', '2025-11-24 06:11:33'),
(535, 'default', 'created', 'App\\Models\\Transaction', 'created', 283, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":45,\"vehicle_id\":4,\"type\":\"thu\",\"amount\":\"4500000.00\",\"method\":\"bank\",\"note\":\"Thu chuy\\u1ebfn \\u0111i\",\"date\":\"2025-11-24T13:09:00.000000Z\"}}', NULL, '2025-11-24 06:11:33', '2025-11-24 06:11:33'),
(536, 'default', 'created', 'App\\Models\\Transaction', 'created', 284, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":45,\"vehicle_id\":4,\"type\":\"chi\",\"amount\":\"1260000.00\",\"method\":\"bank\",\"note\":\"D\\u1ea7u\",\"date\":\"2025-11-24T13:09:00.000000Z\"}}', NULL, '2025-11-24 06:11:33', '2025-11-24 06:11:33'),
(537, 'default', 'created', 'App\\Models\\Transaction', 'created', 285, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":45,\"vehicle_id\":4,\"type\":\"chi\",\"amount\":\"500000.00\",\"method\":\"bank\",\"note\":\"Hoa h\\u1ed3ng: Khoa HSTC - BV \\u0110KL\\u0110\",\"date\":\"2025-11-24T13:09:00.000000Z\"}}', NULL, '2025-11-24 06:11:33', '2025-11-24 06:11:33'),
(538, 'default', 'created', 'App\\Models\\Location', 'created', 38, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"V\\u1ea1n ki\\u1ebfp - \\u0110\\u00e0 L\\u1ea1t\",\"type\":\"to\",\"address\":null,\"note\":null,\"is_active\":true}}', NULL, '2025-11-24 06:13:35', '2025-11-24 06:13:35'),
(539, 'default', 'created', 'App\\Models\\Patient', 'created', 41, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"Nguy\\u1ec5n V\\u0103n H\\u01b0ng\",\"birth_year\":1968,\"phone\":\"0769821906\",\"gender\":\"male\",\"address\":null}}', NULL, '2025-11-24 06:13:35', '2025-11-24 06:13:35'),
(540, 'default', 'created', 'App\\Models\\Incident', 'created', 46, 'App\\Models\\User', 1, '{\"attributes\":{\"vehicle_id\":4,\"patient_id\":41,\"date\":\"2025-11-23T13:12:00.000000Z\",\"dispatch_by\":1,\"destination\":null,\"from_location_id\":10,\"to_location_id\":38,\"partner_id\":null,\"commission_amount\":null,\"summary\":null,\"tags\":null}}', NULL, '2025-11-24 06:13:35', '2025-11-24 06:13:35'),
(541, 'default', 'created', 'App\\Models\\Transaction', 'created', 286, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":46,\"vehicle_id\":4,\"type\":\"chi\",\"amount\":\"50000.00\",\"method\":\"bank\",\"note\":\"C\\u00f4ng l\\u00e1i xe: Nguy\\u1ec5n C\\u1eefu Ninh\",\"date\":\"2025-11-23T13:12:00.000000Z\"}}', NULL, '2025-11-24 06:13:35', '2025-11-24 06:13:35'),
(542, 'default', 'created', 'App\\Models\\Transaction', 'created', 287, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":46,\"vehicle_id\":4,\"type\":\"chi\",\"amount\":\"100000.00\",\"method\":\"bank\",\"note\":\"C\\u00f4ng NVYT: Nguy\\u1ec5n Qu\\u1ed1c V\\u0169\",\"date\":\"2025-11-23T13:12:00.000000Z\"}}', NULL, '2025-11-24 06:13:35', '2025-11-24 06:13:35'),
(543, 'default', 'created', 'App\\Models\\Transaction', 'created', 288, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":46,\"vehicle_id\":4,\"type\":\"thu\",\"amount\":\"500000.00\",\"method\":\"bank\",\"note\":\"Thu chuy\\u1ebfn \\u0111i\",\"date\":\"2025-11-23T13:12:00.000000Z\"}}', NULL, '2025-11-24 06:13:35', '2025-11-24 06:13:35'),
(544, 'default', 'updated', 'App\\Models\\Incident', 'updated', 45, 'App\\Models\\User', 1, '{\"attributes\":{\"date\":\"2025-11-23T13:09:00.000000Z\"},\"old\":{\"date\":\"2025-11-24T13:09:00.000000Z\"}}', NULL, '2025-11-24 06:14:13', '2025-11-24 06:14:13'),
(545, 'default', 'created', 'App\\Models\\Transaction', 'created', 289, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":45,\"vehicle_id\":4,\"type\":\"chi\",\"amount\":\"550000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng l\\u00e1i xe: Nguy\\u1ec5n C\\u1eefu Ninh\",\"date\":\"2025-11-23T13:09:00.000000Z\"}}', NULL, '2025-11-24 06:14:13', '2025-11-24 06:14:13'),
(546, 'default', 'created', 'App\\Models\\Transaction', 'created', 290, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":45,\"vehicle_id\":4,\"type\":\"chi\",\"amount\":\"600000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng nh\\u00e2n vi\\u00ean y t\\u1ebf: Nguy\\u1ec5n Qu\\u1ed1c V\\u0169\",\"date\":\"2025-11-23T13:09:00.000000Z\"}}', NULL, '2025-11-24 06:14:13', '2025-11-24 06:14:13'),
(547, 'default', 'created', 'App\\Models\\Transaction', 'created', 291, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":45,\"vehicle_id\":4,\"type\":\"chi\",\"amount\":\"500000.00\",\"method\":\"cash\",\"note\":\"Hoa h\\u1ed3ng: Khoa HSTC - BV \\u0110KL\\u0110\",\"date\":\"2025-11-23T13:09:00.000000Z\"}}', NULL, '2025-11-24 06:14:13', '2025-11-24 06:14:13'),
(548, 'default', 'updated', 'App\\Models\\Incident', 'updated', 44, 'App\\Models\\User', 1, '{\"attributes\":{\"from_location_id\":10,\"to_location_id\":1},\"old\":{\"from_location_id\":null,\"to_location_id\":null}}', NULL, '2025-11-24 06:14:43', '2025-11-24 06:14:43'),
(549, 'default', 'created', 'App\\Models\\Transaction', 'created', 292, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":44,\"vehicle_id\":3,\"type\":\"chi\",\"amount\":\"550000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng l\\u00e1i xe: L\\u00ea Phong\",\"date\":\"2025-11-23T13:06:00.000000Z\"}}', NULL, '2025-11-24 06:14:43', '2025-11-24 06:14:43'),
(550, 'default', 'created', 'App\\Models\\Transaction', 'created', 293, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":44,\"vehicle_id\":3,\"type\":\"chi\",\"amount\":\"600000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng nh\\u00e2n vi\\u00ean y t\\u1ebf: \\u0110\\u1ee9c Anh\",\"date\":\"2025-11-23T13:06:00.000000Z\"}}', NULL, '2025-11-24 06:14:43', '2025-11-24 06:14:43'),
(551, 'default', 'created', 'App\\Models\\Transaction', 'created', 294, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":44,\"vehicle_id\":3,\"type\":\"chi\",\"amount\":\"500000.00\",\"method\":\"cash\",\"note\":\"Hoa h\\u1ed3ng: Khoa HSTC - BV \\u0110KL\\u0110\",\"date\":\"2025-11-23T13:06:00.000000Z\"}}', NULL, '2025-11-24 06:14:43', '2025-11-24 06:14:43'),
(552, 'default', 'created', 'App\\Models\\MaintenanceService', 'created', 20, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"Thay 2 van b\\u00ecnh oxy\",\"description\":null,\"is_active\":true}}', NULL, '2025-11-24 06:20:34', '2025-11-24 06:20:34'),
(553, 'default', 'created', 'App\\Models\\Partner', 'created', 28, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"DCYK Hai Dang\",\"type\":\"maintenance\",\"phone\":null,\"email\":null,\"address\":null,\"commission_rate\":null,\"note\":null,\"is_active\":true}}', NULL, '2025-11-24 06:20:34', '2025-11-24 06:20:34'),
(554, 'default', 'created', 'App\\Models\\VehicleMaintenance', 'created', 26, 'App\\Models\\User', 1, '{\"attributes\":{\"vehicle_id\":4,\"maintenance_service_id\":20,\"partner_id\":28,\"incident_id\":null,\"date\":\"2025-11-23T00:00:00.000000Z\",\"cost\":\"400000.00\",\"mileage\":null,\"description\":\"Thay van b\\u00ecnh oxy\",\"note\":\"Ck b\\u00ean DCYK Hai Dang\"}}', NULL, '2025-11-24 06:20:34', '2025-11-24 06:20:34'),
(555, 'default', 'created', 'App\\Models\\Transaction', 'created', 295, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":null,\"vehicle_id\":4,\"type\":\"chi\",\"amount\":\"400000.00\",\"method\":\"cash\",\"note\":\"[B\\u1ea3o tr\\u00ec] Thay 2 van b\\u00ecnh oxy - DCYK Hai Dang - Thay van b\\u00ecnh oxy\",\"date\":\"2025-11-23T00:00:00.000000Z\"}}', NULL, '2025-11-24 06:20:34', '2025-11-24 06:20:34'),
(556, 'default', 'created', 'App\\Models\\Transaction', 'created', 296, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":null,\"vehicle_id\":4,\"type\":\"chi\",\"amount\":\"447000.00\",\"method\":\"bank\",\"note\":\"M\\u00e1y \\u0111o huy\\u1ebft \\u00e1p,  Ck \\u0110.Anh mua\",\"date\":\"2025-11-21T13:20:00.000000Z\"}}', NULL, '2025-11-24 06:21:18', '2025-11-24 06:21:18'),
(557, 'default', 'created', 'App\\Models\\Transaction', 'created', 297, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":null,\"vehicle_id\":3,\"type\":\"chi\",\"amount\":\"447000.00\",\"method\":\"cash\",\"note\":\"M\\u00e1y \\u0111o huy\\u1ebft \\u00e1p,  Ck \\u0110.Anh mua\",\"date\":\"2025-11-21T13:21:00.000000Z\"}}', NULL, '2025-11-24 06:21:37', '2025-11-24 06:21:37'),
(558, 'default', 'created', 'App\\Models\\Location', 'created', 39, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"T\\u00e2n v\\u0103n - L\\u00e2m H\\u00e0\",\"type\":\"to\",\"address\":null,\"note\":null,\"is_active\":true}}', NULL, '2025-11-24 06:23:29', '2025-11-24 06:23:29'),
(559, 'default', 'created', 'App\\Models\\Patient', 'created', 42, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"Ho\\u00e0ng V\\u0103n Nh\\u00ec\",\"birth_year\":1952,\"phone\":\"0919800622\",\"gender\":\"male\",\"address\":null}}', NULL, '2025-11-24 06:23:29', '2025-11-24 06:23:29'),
(560, 'default', 'created', 'App\\Models\\Incident', 'created', 47, 'App\\Models\\User', 1, '{\"attributes\":{\"vehicle_id\":3,\"patient_id\":42,\"date\":\"2025-11-24T13:21:00.000000Z\",\"dispatch_by\":1,\"destination\":null,\"from_location_id\":10,\"to_location_id\":39,\"partner_id\":18,\"commission_amount\":\"150000.00\",\"summary\":null,\"tags\":null}}', NULL, '2025-11-24 06:23:29', '2025-11-24 06:23:29'),
(561, 'default', 'created', 'App\\Models\\Transaction', 'created', 298, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":47,\"vehicle_id\":3,\"type\":\"chi\",\"amount\":\"300000.00\",\"method\":\"bank\",\"note\":\"C\\u00f4ng l\\u00e1i xe: L\\u00ea Phong\",\"date\":\"2025-11-24T13:21:00.000000Z\"}}', NULL, '2025-11-24 06:23:29', '2025-11-24 06:23:29'),
(562, 'default', 'created', 'App\\Models\\Transaction', 'created', 299, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":47,\"vehicle_id\":3,\"type\":\"chi\",\"amount\":\"300000.00\",\"method\":\"bank\",\"note\":\"C\\u00f4ng NVYT: Nguy\\u1ec5n Qu\\u1ed1c V\\u0169\",\"date\":\"2025-11-24T13:21:00.000000Z\"}}', NULL, '2025-11-24 06:23:29', '2025-11-24 06:23:29');
INSERT INTO `activity_log` (`id`, `log_name`, `description`, `subject_type`, `event`, `subject_id`, `causer_type`, `causer_id`, `properties`, `batch_uuid`, `created_at`, `updated_at`) VALUES
(563, 'default', 'created', 'App\\Models\\Transaction', 'created', 300, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":47,\"vehicle_id\":3,\"type\":\"thu\",\"amount\":\"2000000.00\",\"method\":\"bank\",\"note\":\"Thu chuy\\u1ebfn \\u0111i\",\"date\":\"2025-11-24T13:21:00.000000Z\"}}', NULL, '2025-11-24 06:23:29', '2025-11-24 06:23:29'),
(564, 'default', 'created', 'App\\Models\\Transaction', 'created', 301, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":47,\"vehicle_id\":3,\"type\":\"chi\",\"amount\":\"150000.00\",\"method\":\"bank\",\"note\":\"Hoa h\\u1ed3ng: Khoa HSTC - BV \\u0110KL\\u0110\",\"date\":\"2025-11-24T13:21:00.000000Z\"}}', NULL, '2025-11-24 06:23:29', '2025-11-24 06:23:29'),
(565, 'default', 'created', 'App\\Models\\Location', 'created', 40, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"T\\u00e2n H\\u00e0 - L\\u00e2m H\\u00e0\",\"type\":\"to\",\"address\":null,\"note\":null,\"is_active\":true}}', NULL, '2025-11-24 06:24:55', '2025-11-24 06:24:55'),
(566, 'default', 'created', 'App\\Models\\Patient', 'created', 43, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"\\u0111o\\u00e0n v\\u0103n t\\u00e2n\",\"birth_year\":1959,\"phone\":\"0374659568\",\"gender\":\"male\",\"address\":null}}', NULL, '2025-11-24 06:24:55', '2025-11-24 06:24:55'),
(567, 'default', 'created', 'App\\Models\\Incident', 'created', 48, 'App\\Models\\User', 1, '{\"attributes\":{\"vehicle_id\":1,\"patient_id\":43,\"date\":\"2025-11-24T13:23:00.000000Z\",\"dispatch_by\":1,\"destination\":null,\"from_location_id\":10,\"to_location_id\":40,\"partner_id\":18,\"commission_amount\":\"150000.00\",\"summary\":null,\"tags\":null}}', NULL, '2025-11-24 06:24:55', '2025-11-24 06:24:55'),
(568, 'default', 'created', 'App\\Models\\Transaction', 'created', 302, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":48,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"300000.00\",\"method\":\"bank\",\"note\":\"C\\u00f4ng l\\u00e1i xe: Cil \\u0110oan\",\"date\":\"2025-11-24T13:23:00.000000Z\"}}', NULL, '2025-11-24 06:24:55', '2025-11-24 06:24:55'),
(569, 'default', 'created', 'App\\Models\\Transaction', 'created', 303, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":48,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"300000.00\",\"method\":\"bank\",\"note\":\"C\\u00f4ng NVYT: L\\u00e2n\",\"date\":\"2025-11-24T13:23:00.000000Z\"}}', NULL, '2025-11-24 06:24:55', '2025-11-24 06:24:55'),
(570, 'default', 'created', 'App\\Models\\Transaction', 'created', 304, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":48,\"vehicle_id\":1,\"type\":\"thu\",\"amount\":\"2000000.00\",\"method\":\"bank\",\"note\":\"Thu chuy\\u1ebfn \\u0111i\",\"date\":\"2025-11-24T13:23:00.000000Z\"}}', NULL, '2025-11-24 06:24:55', '2025-11-24 06:24:55'),
(571, 'default', 'created', 'App\\Models\\Transaction', 'created', 305, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":48,\"vehicle_id\":1,\"type\":\"chi\",\"amount\":\"150000.00\",\"method\":\"bank\",\"note\":\"Hoa h\\u1ed3ng: Khoa HSTC - BV \\u0110KL\\u0110\",\"date\":\"2025-11-24T13:23:00.000000Z\"}}', NULL, '2025-11-24 06:24:55', '2025-11-24 06:24:55'),
(572, 'default', 'created', 'App\\Models\\Transaction', 'created', 306, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":27,\"vehicle_id\":3,\"type\":\"chi\",\"amount\":\"550000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng l\\u00e1i xe: L\\u00ea Phong\",\"date\":\"2025-11-17T18:35:00.000000Z\"}}', NULL, '2025-11-24 06:25:48', '2025-11-24 06:25:48'),
(573, 'default', 'created', 'App\\Models\\Transaction', 'created', 307, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":27,\"vehicle_id\":3,\"type\":\"chi\",\"amount\":\"600000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng nh\\u00e2n vi\\u00ean y t\\u1ebf: Kh\\u00e1nh\",\"date\":\"2025-11-17T18:35:00.000000Z\"}}', NULL, '2025-11-24 06:25:48', '2025-11-24 06:25:48'),
(574, 'default', 'created', 'App\\Models\\Transaction', 'created', 308, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":27,\"vehicle_id\":3,\"type\":\"chi\",\"amount\":\"500000.00\",\"method\":\"cash\",\"note\":\"Hoa h\\u1ed3ng: Khoa Ph\\u1ed5i - BV \\u0110KL\\u0110\",\"date\":\"2025-11-17T18:35:00.000000Z\"}}', NULL, '2025-11-24 06:25:48', '2025-11-24 06:25:48'),
(575, 'default', 'created', 'App\\Models\\Transaction', 'created', 309, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":28,\"vehicle_id\":4,\"type\":\"chi\",\"amount\":\"550000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng l\\u00e1i xe: Nguy\\u1ec5n C\\u1eefu Ninh\",\"date\":\"2025-11-17T18:37:00.000000Z\"}}', NULL, '2025-11-24 06:26:03', '2025-11-24 06:26:03'),
(576, 'default', 'created', 'App\\Models\\Transaction', 'created', 310, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":28,\"vehicle_id\":4,\"type\":\"chi\",\"amount\":\"600000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng nh\\u00e2n vi\\u00ean y t\\u1ebf: Nguy\\u1ec5n Qu\\u1ed1c V\\u0169\",\"date\":\"2025-11-17T18:37:00.000000Z\"}}', NULL, '2025-11-24 06:26:03', '2025-11-24 06:26:03'),
(577, 'default', 'created', 'App\\Models\\Transaction', 'created', 311, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":42,\"vehicle_id\":4,\"type\":\"chi\",\"amount\":\"550000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng l\\u00e1i xe: Nguy\\u1ec5n C\\u1eefu Ninh\",\"date\":\"2025-11-21T18:36:00.000000Z\"}}', NULL, '2025-11-24 06:27:24', '2025-11-24 06:27:24'),
(578, 'default', 'created', 'App\\Models\\Transaction', 'created', 312, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":42,\"vehicle_id\":4,\"type\":\"chi\",\"amount\":\"600000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng nh\\u00e2n vi\\u00ean y t\\u1ebf: Nguy\\u1ec5n Qu\\u1ed1c V\\u0169\",\"date\":\"2025-11-21T18:36:00.000000Z\"}}', NULL, '2025-11-24 06:27:24', '2025-11-24 06:27:24'),
(579, 'default', 'created', 'App\\Models\\Transaction', 'created', 313, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":42,\"vehicle_id\":4,\"type\":\"chi\",\"amount\":\"500000.00\",\"method\":\"cash\",\"note\":\"Hoa h\\u1ed3ng: Khoa HSTC - BV \\u0110KL\\u0110\",\"date\":\"2025-11-21T18:36:00.000000Z\"}}', NULL, '2025-11-24 06:27:24', '2025-11-24 06:27:24'),
(580, 'default', 'created', 'App\\Models\\Transaction', 'created', 314, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":39,\"vehicle_id\":3,\"type\":\"chi\",\"amount\":\"550000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng l\\u00e1i xe: L\\u00ea Phong\",\"date\":\"2025-11-20T18:31:00.000000Z\"}}', NULL, '2025-11-24 06:27:46', '2025-11-24 06:27:46'),
(581, 'default', 'created', 'App\\Models\\Transaction', 'created', 315, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":39,\"vehicle_id\":3,\"type\":\"chi\",\"amount\":\"600000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng nh\\u00e2n vi\\u00ean y t\\u1ebf: Nguy\\u1ec5n Qu\\u1ed1c V\\u0169\",\"date\":\"2025-11-20T18:31:00.000000Z\"}}', NULL, '2025-11-24 06:27:46', '2025-11-24 06:27:46'),
(582, 'default', 'created', 'App\\Models\\Transaction', 'created', 316, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":39,\"vehicle_id\":3,\"type\":\"chi\",\"amount\":\"500000.00\",\"method\":\"cash\",\"note\":\"Hoa h\\u1ed3ng: Khoa n\\u1ed9i B - BV \\u0110KL\\u0110\",\"date\":\"2025-11-20T18:31:00.000000Z\"}}', NULL, '2025-11-24 06:27:46', '2025-11-24 06:27:46'),
(583, 'default', 'created', 'App\\Models\\Transaction', 'created', 317, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":36,\"vehicle_id\":3,\"type\":\"chi\",\"amount\":\"550000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng l\\u00e1i xe: Nguy\\u1ec5n C\\u1eefu Ninh\",\"date\":\"2025-11-19T18:25:00.000000Z\"}}', NULL, '2025-11-24 06:28:17', '2025-11-24 06:28:17'),
(584, 'default', 'created', 'App\\Models\\Transaction', 'created', 318, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":36,\"vehicle_id\":3,\"type\":\"chi\",\"amount\":\"600000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng nh\\u00e2n vi\\u00ean y t\\u1ebf: Nguy\\u1ec5n Qu\\u1ed1c V\\u0169\",\"date\":\"2025-11-19T18:25:00.000000Z\"}}', NULL, '2025-11-24 06:28:17', '2025-11-24 06:28:17'),
(585, 'default', 'created', 'App\\Models\\Transaction', 'created', 319, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":36,\"vehicle_id\":3,\"type\":\"chi\",\"amount\":\"500000.00\",\"method\":\"cash\",\"note\":\"Hoa h\\u1ed3ng: Khoa Ph\\u1ed5i - BV \\u0110KL\\u0110\",\"date\":\"2025-11-19T18:25:00.000000Z\"}}', NULL, '2025-11-24 06:28:17', '2025-11-24 06:28:17'),
(586, 'default', 'created', 'App\\Models\\Transaction', 'created', 320, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":31,\"vehicle_id\":3,\"type\":\"chi\",\"amount\":\"550000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng l\\u00e1i xe: L\\u00ea Phong\",\"date\":\"2025-11-18T18:50:00.000000Z\"}}', NULL, '2025-11-24 06:28:46', '2025-11-24 06:28:46'),
(587, 'default', 'created', 'App\\Models\\Transaction', 'created', 321, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":31,\"vehicle_id\":3,\"type\":\"chi\",\"amount\":\"600000.00\",\"method\":\"cash\",\"note\":\"Ti\\u1ec1n c\\u00f4ng nh\\u00e2n vi\\u00ean y t\\u1ebf: Nguy\\u1ec5n Qu\\u1ed1c V\\u0169\",\"date\":\"2025-11-18T18:50:00.000000Z\"}}', NULL, '2025-11-24 06:28:46', '2025-11-24 06:28:46'),
(588, 'default', 'created', 'App\\Models\\Transaction', 'created', 322, 'App\\Models\\User', 1, '{\"attributes\":{\"incident_id\":31,\"vehicle_id\":3,\"type\":\"chi\",\"amount\":\"500000.00\",\"method\":\"cash\",\"note\":\"Hoa h\\u1ed3ng: Khoa Ph\\u1ed5i - BV \\u0110KL\\u0110\",\"date\":\"2025-11-18T18:50:00.000000Z\"}}', NULL, '2025-11-24 06:28:46', '2025-11-24 06:28:46'),
(589, 'default', 'created', 'App\\Models\\AdditionalService', 'created', 5, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"Thu\\u00ea \\u0111i\\u1ec1u d\\u01b0\\u1ee1ng ch\\u0103m s\\u00f3c t\\u1ea1i nh\\u00e0\",\"description\":\"d\\u1ecbch v\\u1ee5 t\\u1ea1i nh\\u00e0 t\\u00ednh theo ng\\u00e0y\",\"default_price\":\"2000000.00\",\"is_active\":true}}', NULL, '2025-11-24 09:19:18', '2025-11-24 09:19:18'),
(590, 'default', 'created', 'App\\Models\\AdditionalService', 'created', 6, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"Tr\\u1ef1c s\\u1ef1 ki\\u1ec7n\",\"description\":\"K\\u00edp xe v\\u00e0 nh\\u00e2n vi\\u00ean y t\\u1ebf tr\\u1ef1c s\\u1ef1 ki\\u00ean, t\\u00ednh theo ng\\u00e0y\",\"default_price\":\"4500000.00\",\"is_active\":true}}', NULL, '2025-11-24 09:19:55', '2025-11-24 09:19:55');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `additional_services`
--

CREATE TABLE `additional_services` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `default_price` decimal(10,2) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `additional_services`
--

INSERT INTO `additional_services` (`id`, `name`, `description`, `default_price`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Máy thở', 'thuê máy thở theo ngày', 2500000.00, 1, '2025-11-21 12:05:22', '2025-11-21 12:05:22'),
(2, 'Vật tư y tế', 'găng tay, khẩu trang, cồn gạt', 200000.00, 1, '2025-11-21 12:05:49', '2025-11-21 12:05:49'),
(3, 'Bác sĩ', 'Thuê bác sĩ đi kèm (ngày)', 3500000.00, 1, '2025-11-21 12:06:11', '2025-11-21 12:06:24'),
(4, 'Chờ tái khám', 'mỗi 1 tiếng phát sinh sau 2 tiếng đầu', 150000.00, 1, '2025-11-21 12:06:59', '2025-11-21 12:06:59'),
(5, 'Thuê điều dưỡng chăm sóc tại nhà', 'dịch vụ tại nhà tính theo ngày', 2000000.00, 1, '2025-11-24 09:19:18', '2025-11-24 09:19:18'),
(6, 'Trực sự kiện', 'Kíp xe và nhân viên y tế trực sự kiên, tính theo ngày', 4500000.00, 1, '2025-11-24 09:19:55', '2025-11-24 09:19:55');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `departments`
--

CREATE TABLE `departments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `departments`
--

INSERT INTO `departments` (`id`, `name`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Hành chính', 1, '2025-11-21 23:15:36', '2025-11-21 23:15:36'),
(2, 'Nhân sự', 1, '2025-11-21 23:15:36', '2025-11-21 23:15:36'),
(3, 'Kế toán', 1, '2025-11-21 23:15:36', '2025-11-21 23:15:36'),
(4, 'Kinh doanh', 1, '2025-11-21 23:15:36', '2025-11-21 23:15:36'),
(5, 'Vận hành', 1, '2025-11-21 23:15:36', '2025-11-21 23:15:36'),
(6, 'Y tế', 1, '2025-11-21 23:15:36', '2025-11-21 23:15:36'),
(7, 'Kỹ thuật', 1, '2025-11-21 23:15:36', '2025-11-21 23:15:36'),
(8, 'Điều hành', 1, '2025-11-21 23:15:36', '2025-11-21 23:15:36'),
(9, 'GĐ', 1, '2025-11-22 01:21:14', '2025-11-22 01:21:14'),
(10, 'Cổ Đông', 1, '2025-11-22 04:02:39', '2025-11-22 04:02:39'),
(18, 'chủ xe', 1, '2025-11-22 05:33:04', '2025-11-22 05:33:04');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `incidents`
--

CREATE TABLE `incidents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vehicle_id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED DEFAULT NULL,
  `date` datetime NOT NULL,
  `dispatch_by` bigint(20) UNSIGNED NOT NULL,
  `destination` varchar(255) DEFAULT NULL,
  `from_location_id` bigint(20) UNSIGNED DEFAULT NULL,
  `to_location_id` bigint(20) UNSIGNED DEFAULT NULL,
  `partner_id` bigint(20) UNSIGNED DEFAULT NULL,
  `commission_amount` decimal(10,2) DEFAULT NULL,
  `summary` text DEFAULT NULL,
  `tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`tags`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `incidents`
--

INSERT INTO `incidents` (`id`, `vehicle_id`, `patient_id`, `date`, `dispatch_by`, `destination`, `from_location_id`, `to_location_id`, `partner_id`, `commission_amount`, `summary`, `tags`, `created_at`, `updated_at`) VALUES
(7, 2, 4, '2025-11-01 17:36:00', 1, NULL, 10, 11, 26, 150000.00, NULL, NULL, '2025-11-22 10:38:48', '2025-11-22 10:38:48'),
(9, 1, 5, '2025-11-02 17:43:00', 1, NULL, 11, 6, NULL, NULL, NULL, NULL, '2025-11-22 10:47:25', '2025-11-22 10:47:25'),
(10, 1, 6, '2025-11-03 17:47:00', 1, NULL, 10, 12, 26, 150000.00, NULL, NULL, '2025-11-22 10:50:14', '2025-11-22 10:52:47'),
(11, 2, 8, '2025-11-03 17:50:00', 1, NULL, 14, 6, NULL, NULL, NULL, NULL, '2025-11-22 10:58:48', '2025-11-22 10:58:48'),
(12, 2, 9, '2025-11-03 17:58:00', 1, NULL, 10, 15, 26, 150000.00, NULL, NULL, '2025-11-22 11:00:30', '2025-11-22 11:00:30'),
(13, 1, 10, '2025-11-04 18:00:00', 1, NULL, 10, 15, 26, 150000.00, NULL, NULL, '2025-11-22 11:01:42', '2025-11-24 06:04:59'),
(14, 1, 11, '2025-11-05 18:01:00', 1, NULL, 10, 16, 26, 150000.00, NULL, NULL, '2025-11-22 11:03:46', '2025-11-22 11:03:46'),
(15, 1, 12, '2025-11-08 18:03:00', 1, NULL, 10, 17, 26, 150000.00, NULL, NULL, '2025-11-22 11:05:55', '2025-11-22 11:05:55'),
(16, 1, 13, '2025-11-10 18:05:00', 1, NULL, 10, 18, 26, 150000.00, NULL, NULL, '2025-11-22 11:08:46', '2025-11-22 11:08:46'),
(17, 2, 14, '2025-11-12 18:08:00', 1, NULL, 10, 19, 26, 200000.00, NULL, NULL, '2025-11-22 11:11:05', '2025-11-22 11:11:05'),
(18, 1, 15, '2025-11-12 18:11:00', 1, NULL, 10, 20, NULL, NULL, NULL, NULL, '2025-11-22 11:13:13', '2025-11-22 11:13:13'),
(19, 1, 16, '2025-11-12 18:13:00', 1, NULL, 10, 19, 26, 200000.00, NULL, NULL, '2025-11-22 11:15:13', '2025-11-22 11:15:13'),
(20, 2, 17, '2025-11-13 18:15:00', 1, NULL, 10, 15, 26, 150000.00, NULL, NULL, '2025-11-22 11:16:55', '2025-11-22 11:16:55'),
(21, 1, 18, '2025-11-13 18:16:00', 1, NULL, 10, 17, NULL, NULL, NULL, NULL, '2025-11-22 11:18:15', '2025-11-22 11:18:15'),
(22, 2, 19, '2025-11-14 18:18:00', 1, NULL, 10, 17, NULL, NULL, NULL, NULL, '2025-11-22 11:21:17', '2025-11-22 11:21:17'),
(23, 1, 8, '2025-11-14 18:21:00', 1, NULL, 21, 6, NULL, NULL, NULL, NULL, '2025-11-22 11:22:56', '2025-11-22 11:22:56'),
(24, 2, 20, '2025-11-15 18:22:00', 1, NULL, 10, 22, 26, 150000.00, NULL, NULL, '2025-11-22 11:24:34', '2025-11-22 11:24:34'),
(25, 1, 8, '2025-11-16 18:24:00', 1, NULL, 10, 21, NULL, NULL, NULL, NULL, '2025-11-22 11:32:32', '2025-11-22 11:32:32'),
(26, 1, 21, '2025-11-17 18:32:00', 1, NULL, 10, 23, 26, 200000.00, NULL, NULL, '2025-11-22 11:35:18', '2025-11-22 11:35:18'),
(27, 3, 22, '2025-11-17 18:35:00', 1, NULL, 24, 1, 23, 500000.00, NULL, NULL, '2025-11-22 11:37:34', '2025-11-22 11:37:34'),
(28, 4, 23, '2025-11-17 18:37:00', 1, NULL, 25, 26, NULL, NULL, NULL, NULL, '2025-11-22 11:41:05', '2025-11-22 11:41:05'),
(29, 1, 24, '2025-11-17 18:41:00', 1, NULL, 25, 15, 26, 150000.00, NULL, NULL, '2025-11-22 11:45:07', '2025-11-22 11:45:07'),
(30, 1, 25, '2025-11-17 18:49:00', 1, NULL, 10, 27, 26, 150000.00, NULL, NULL, '2025-11-22 11:50:50', '2025-11-22 11:50:50'),
(31, 3, 26, '2025-11-18 18:50:00', 1, NULL, 24, 28, 23, 500000.00, NULL, NULL, '2025-11-22 11:57:35', '2025-11-22 11:57:35'),
(32, 3, 27, '2025-11-18 18:16:00', 1, NULL, 10, 29, NULL, NULL, NULL, NULL, '2025-11-23 11:18:13', '2025-11-23 11:19:58'),
(33, 2, 28, '2025-11-18 18:20:00', 1, NULL, 10, 30, 26, 150000.00, NULL, NULL, '2025-11-23 11:21:29', '2025-11-23 11:21:29'),
(34, 3, 29, '2025-11-18 18:21:00', 1, NULL, 31, 32, NULL, NULL, NULL, NULL, '2025-11-23 11:23:11', '2025-11-23 11:23:11'),
(35, 2, 30, '2025-11-18 18:23:00', 1, NULL, 33, 34, NULL, NULL, NULL, NULL, '2025-11-23 11:24:49', '2025-11-23 11:24:49'),
(36, 3, 31, '2025-11-19 18:25:00', 1, NULL, 24, 3, 23, 500000.00, NULL, NULL, '2025-11-23 11:27:00', '2025-11-23 11:27:00'),
(37, 2, 32, '2025-11-19 18:27:00', 1, NULL, 10, 15, 26, 150000.00, NULL, NULL, '2025-11-23 11:28:57', '2025-11-23 11:28:57'),
(38, 2, 33, '2025-11-19 18:29:00', 1, NULL, 10, 15, 26, 150000.00, NULL, NULL, '2025-11-23 11:30:58', '2025-11-23 11:30:58'),
(39, 3, 34, '2025-11-20 18:31:00', 1, NULL, 35, 1, 19, 500000.00, NULL, NULL, '2025-11-23 11:32:50', '2025-11-23 11:32:50'),
(40, 2, 35, '2025-11-20 18:32:00', 1, NULL, 10, 36, 26, 150000.00, NULL, NULL, '2025-11-23 11:34:32', '2025-11-23 11:34:32'),
(41, 1, 36, '2025-11-20 18:34:00', 1, NULL, 10, 37, NULL, NULL, NULL, NULL, '2025-11-23 11:35:57', '2025-11-23 11:35:57'),
(42, 4, 37, '2025-11-21 18:36:00', 1, NULL, 10, 1, 18, 500000.00, NULL, NULL, '2025-11-23 11:39:28', '2025-11-23 11:39:28'),
(43, 3, 38, '2025-11-21 19:21:00', 1, NULL, 24, 1, 23, 500000.00, NULL, NULL, '2025-11-23 12:22:48', '2025-11-23 12:22:48'),
(44, 3, 39, '2025-11-23 13:06:00', 1, NULL, 10, 1, 18, 500000.00, NULL, NULL, '2025-11-24 06:09:23', '2025-11-24 06:14:43'),
(45, 4, 40, '2025-11-23 13:09:00', 1, NULL, 10, 1, 18, 500000.00, NULL, NULL, '2025-11-24 06:11:33', '2025-11-24 06:14:13'),
(46, 4, 41, '2025-11-23 13:12:00', 1, NULL, 10, 38, NULL, NULL, NULL, NULL, '2025-11-24 06:13:35', '2025-11-24 06:13:35'),
(47, 3, 42, '2025-11-24 13:21:00', 1, NULL, 10, 39, 18, 150000.00, NULL, NULL, '2025-11-24 06:23:29', '2025-11-24 06:23:29'),
(48, 1, 43, '2025-11-24 13:23:00', 1, NULL, 10, 40, 18, 150000.00, NULL, NULL, '2025-11-24 06:24:55', '2025-11-24 06:24:55');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `incident_additional_services`
--

CREATE TABLE `incident_additional_services` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `incident_id` bigint(20) UNSIGNED NOT NULL,
  `additional_service_id` bigint(20) UNSIGNED DEFAULT NULL,
  `service_name` varchar(255) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `incident_staff`
--

CREATE TABLE `incident_staff` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `incident_id` bigint(20) UNSIGNED NOT NULL,
  `staff_id` bigint(20) UNSIGNED NOT NULL,
  `role` enum('driver','medical_staff') NOT NULL,
  `wage_amount` decimal(10,2) DEFAULT NULL COMMENT 'Tiền công nhân viên cho chuyến đi này',
  `wage_details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Chi tiết các loại tiền công (công, thưởng, hoa hồng, tip...)' CHECK (json_valid(`wage_details`)),
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `incident_staff`
--

INSERT INTO `incident_staff` (`id`, `incident_id`, `staff_id`, `role`, `wage_amount`, `wage_details`, `notes`, `created_at`, `updated_at`) VALUES
(12, 7, 4, 'driver', 250000.00, NULL, NULL, '2025-11-22 10:38:48', '2025-11-22 10:38:48'),
(13, 7, 6, 'medical_staff', 250000.00, NULL, NULL, '2025-11-22 10:38:48', '2025-11-22 10:38:48'),
(16, 9, 7, 'driver', 200000.00, NULL, NULL, '2025-11-22 10:47:25', '2025-11-22 10:47:25'),
(19, 10, 7, 'driver', 450000.00, NULL, NULL, '2025-11-22 10:52:47', '2025-11-22 10:52:47'),
(20, 10, 6, 'medical_staff', 450000.00, NULL, NULL, '2025-11-22 10:52:47', '2025-11-22 10:52:47'),
(23, 12, 4, 'driver', 300000.00, NULL, NULL, '2025-11-22 11:00:30', '2025-11-22 11:00:30'),
(24, 12, 16, 'medical_staff', 300000.00, NULL, NULL, '2025-11-22 11:00:30', '2025-11-22 11:00:30'),
(28, 15, 4, 'driver', 300000.00, NULL, NULL, '2025-11-22 11:05:55', '2025-11-22 11:05:55'),
(29, 15, 6, 'medical_staff', 300000.00, NULL, NULL, '2025-11-22 11:05:55', '2025-11-22 11:05:55'),
(32, 17, 4, 'driver', 500000.00, NULL, NULL, '2025-11-22 11:11:05', '2025-11-22 11:11:05'),
(33, 17, 6, 'medical_staff', 500000.00, NULL, NULL, '2025-11-22 11:11:05', '2025-11-22 11:11:05'),
(34, 18, 7, 'driver', 200000.00, NULL, NULL, '2025-11-22 11:13:13', '2025-11-22 11:13:13'),
(35, 18, 16, 'medical_staff', 200000.00, NULL, NULL, '2025-11-22 11:13:13', '2025-11-22 11:13:13'),
(38, 20, 4, 'driver', 300000.00, NULL, NULL, '2025-11-22 11:16:55', '2025-11-22 11:16:55'),
(39, 20, 17, 'medical_staff', 300000.00, NULL, NULL, '2025-11-22 11:16:55', '2025-11-22 11:16:55'),
(40, 21, 7, 'driver', 150000.00, NULL, NULL, '2025-11-22 11:18:15', '2025-11-22 11:18:15'),
(41, 21, 6, 'medical_staff', 150000.00, NULL, NULL, '2025-11-22 11:18:15', '2025-11-22 11:18:15'),
(42, 22, 4, 'driver', 150000.00, NULL, NULL, '2025-11-22 11:21:17', '2025-11-22 11:21:17'),
(43, 22, 6, 'medical_staff', 150000.00, NULL, NULL, '2025-11-22 11:21:17', '2025-11-22 11:21:17'),
(46, 24, 4, 'driver', 300000.00, NULL, NULL, '2025-11-22 11:24:34', '2025-11-22 11:24:34'),
(47, 24, 6, 'medical_staff', 300000.00, NULL, NULL, '2025-11-22 11:24:34', '2025-11-22 11:24:34'),
(49, 26, 5, 'driver', 500000.00, NULL, NULL, '2025-11-22 11:35:18', '2025-11-22 11:35:18'),
(50, 26, 6, 'medical_staff', 500000.00, NULL, NULL, '2025-11-22 11:35:18', '2025-11-22 11:35:18'),
(61, 11, 4, 'driver', 100000.00, NULL, NULL, '2025-11-23 10:35:26', '2025-11-23 10:35:26'),
(62, 11, 16, 'medical_staff', 100000.00, NULL, NULL, '2025-11-23 10:35:26', '2025-11-23 10:35:26'),
(65, 14, 4, 'driver', 300000.00, NULL, NULL, '2025-11-23 10:56:29', '2025-11-23 10:56:29'),
(66, 14, 6, 'medical_staff', 300000.00, NULL, NULL, '2025-11-23 10:56:29', '2025-11-23 10:56:29'),
(67, 16, 7, 'driver', 475000.00, NULL, NULL, '2025-11-23 10:59:20', '2025-11-23 10:59:20'),
(68, 16, 6, 'medical_staff', 475000.00, NULL, NULL, '2025-11-23 10:59:20', '2025-11-23 10:59:20'),
(69, 19, 7, 'driver', 525000.00, NULL, NULL, '2025-11-23 11:03:34', '2025-11-23 11:03:34'),
(70, 19, 16, 'medical_staff', 525000.00, NULL, NULL, '2025-11-23 11:03:34', '2025-11-23 11:03:34'),
(71, 23, 5, 'driver', 75000.00, NULL, NULL, '2025-11-23 11:07:53', '2025-11-23 11:07:53'),
(72, 23, 6, 'medical_staff', 75000.00, NULL, NULL, '2025-11-23 11:07:53', '2025-11-23 11:07:53'),
(73, 25, 5, 'driver', 75000.00, NULL, NULL, '2025-11-23 11:09:20', '2025-11-23 11:09:21'),
(74, 25, 6, 'medical_staff', 75000.00, NULL, NULL, '2025-11-23 11:09:21', '2025-11-23 11:09:21'),
(79, 29, 5, 'driver', 350000.00, NULL, NULL, '2025-11-23 11:13:12', '2025-11-23 11:13:12'),
(80, 29, 16, 'medical_staff', 350000.00, NULL, NULL, '2025-11-23 11:13:12', '2025-11-23 11:13:12'),
(81, 30, 5, 'driver', 450000.00, NULL, NULL, '2025-11-23 11:14:37', '2025-11-23 11:14:37'),
(82, 30, 13, 'medical_staff', 450000.00, NULL, NULL, '2025-11-23 11:14:37', '2025-11-23 11:14:37'),
(87, 32, 7, 'driver', 200000.00, NULL, NULL, '2025-11-23 11:19:58', '2025-11-23 11:19:58'),
(88, 32, 16, 'medical_staff', 200000.00, NULL, NULL, '2025-11-23 11:19:58', '2025-11-23 11:19:58'),
(89, 33, 4, 'driver', 330000.00, '[{\"type\":\"C\\u00f4ng\",\"amount\":\"330000\"}]', NULL, '2025-11-23 11:21:29', '2025-11-23 11:21:29'),
(90, 33, 6, 'medical_staff', 330000.00, '[{\"type\":\"C\\u00f4ng\",\"amount\":\"330000\"}]', NULL, '2025-11-23 11:21:29', '2025-11-23 11:21:29'),
(91, 34, 6, 'medical_staff', 250000.00, '[{\"type\":\"C\\u00f4ng\",\"amount\":\"250000\"}]', NULL, '2025-11-23 11:23:11', '2025-11-23 11:23:11'),
(92, 35, 4, 'driver', 75000.00, '[{\"type\":\"C\\u00f4ng\",\"amount\":\"75000\"}]', NULL, '2025-11-23 11:24:49', '2025-11-23 11:24:49'),
(93, 35, 16, 'medical_staff', 75000.00, '[{\"type\":\"C\\u00f4ng\",\"amount\":\"75000\"}]', NULL, '2025-11-23 11:24:49', '2025-11-23 11:24:49'),
(96, 37, 7, 'driver', 300000.00, '[{\"type\":\"C\\u00f4ng\",\"amount\":\"300000\"}]', NULL, '2025-11-23 11:28:57', '2025-11-23 11:28:57'),
(97, 37, 6, 'medical_staff', 300000.00, '[{\"type\":\"C\\u00f4ng\",\"amount\":\"300000\"}]', NULL, '2025-11-23 11:28:57', '2025-11-23 11:28:57'),
(98, 38, 7, 'driver', 300000.00, '[{\"type\":\"C\\u00f4ng\",\"amount\":\"300000\"}]', NULL, '2025-11-23 11:30:58', '2025-11-23 11:30:58'),
(99, 38, 13, 'medical_staff', 300000.00, '[{\"type\":\"C\\u00f4ng\",\"amount\":\"300000\"}]', NULL, '2025-11-23 11:30:58', '2025-11-23 11:30:58'),
(102, 40, 4, 'driver', 450000.00, '[{\"type\":\"C\\u00f4ng\",\"amount\":\"450000\"}]', NULL, '2025-11-23 11:34:32', '2025-11-23 11:34:32'),
(103, 40, 17, 'medical_staff', 450000.00, '[{\"type\":\"C\\u00f4ng\",\"amount\":\"450000\"}]', NULL, '2025-11-23 11:34:32', '2025-11-23 11:34:32'),
(104, 41, 5, 'driver', 230000.00, '[{\"type\":\"C\\u00f4ng\",\"amount\":\"230000\"}]', NULL, '2025-11-23 11:35:57', '2025-11-23 11:35:57'),
(107, 43, 7, 'driver', 500000.00, '[{\"type\":\"C\\u00f4ng\",\"amount\":\"500000\"}]', NULL, '2025-11-23 12:22:48', '2025-11-23 12:22:48'),
(108, 43, 16, 'medical_staff', 550000.00, '[{\"type\":\"C\\u00f4ng\",\"amount\":\"550000\"}]', NULL, '2025-11-23 12:22:48', '2025-11-23 12:22:48'),
(109, 13, 7, 'driver', 300000.00, NULL, NULL, '2025-11-24 06:04:59', '2025-11-24 06:04:59'),
(110, 13, 6, 'medical_staff', 300000.00, NULL, NULL, '2025-11-24 06:04:59', '2025-11-24 06:04:59'),
(115, 46, 4, 'driver', 50000.00, '[{\"type\":\"C\\u00f4ng\",\"amount\":\"50000\"}]', NULL, '2025-11-24 06:13:35', '2025-11-24 06:13:35'),
(116, 46, 6, 'medical_staff', 100000.00, '[{\"type\":\"C\\u00f4ng\",\"amount\":\"100000\"}]', NULL, '2025-11-24 06:13:35', '2025-11-24 06:13:35'),
(117, 45, 4, 'driver', 550000.00, NULL, NULL, '2025-11-24 06:14:13', '2025-11-24 06:14:13'),
(118, 45, 6, 'medical_staff', 600000.00, NULL, NULL, '2025-11-24 06:14:13', '2025-11-24 06:14:13'),
(119, 44, 7, 'driver', 550000.00, NULL, NULL, '2025-11-24 06:14:43', '2025-11-24 06:14:43'),
(120, 44, 13, 'medical_staff', 600000.00, NULL, NULL, '2025-11-24 06:14:43', '2025-11-24 06:14:43'),
(121, 47, 7, 'driver', 300000.00, '[{\"type\":\"C\\u00f4ng\",\"amount\":\"300000\"}]', NULL, '2025-11-24 06:23:29', '2025-11-24 06:23:29'),
(122, 47, 6, 'medical_staff', 300000.00, '[{\"type\":\"C\\u00f4ng\",\"amount\":\"300000\"}]', NULL, '2025-11-24 06:23:29', '2025-11-24 06:23:29'),
(123, 48, 5, 'driver', 300000.00, '[{\"type\":\"C\\u00f4ng\",\"amount\":\"300000\"}]', NULL, '2025-11-24 06:24:55', '2025-11-24 06:24:55'),
(124, 48, 18, 'medical_staff', 300000.00, '[{\"type\":\"C\\u00f4ng\",\"amount\":\"300000\"}]', NULL, '2025-11-24 06:24:55', '2025-11-24 06:24:55'),
(125, 27, 7, 'driver', 550000.00, NULL, NULL, '2025-11-24 06:25:48', '2025-11-24 06:25:48'),
(126, 27, 19, 'medical_staff', 600000.00, NULL, NULL, '2025-11-24 06:25:48', '2025-11-24 06:25:48'),
(127, 28, 4, 'driver', 550000.00, NULL, NULL, '2025-11-24 06:26:03', '2025-11-24 06:26:03'),
(128, 28, 6, 'medical_staff', 600000.00, NULL, NULL, '2025-11-24 06:26:03', '2025-11-24 06:26:03'),
(129, 42, 4, 'driver', 550000.00, NULL, NULL, '2025-11-24 06:27:24', '2025-11-24 06:27:24'),
(130, 42, 6, 'medical_staff', 600000.00, NULL, NULL, '2025-11-24 06:27:24', '2025-11-24 06:27:24'),
(131, 39, 7, 'driver', 550000.00, NULL, NULL, '2025-11-24 06:27:46', '2025-11-24 06:27:46'),
(132, 39, 6, 'medical_staff', 600000.00, NULL, NULL, '2025-11-24 06:27:46', '2025-11-24 06:27:46'),
(133, 36, 4, 'driver', 550000.00, NULL, NULL, '2025-11-24 06:28:17', '2025-11-24 06:28:17'),
(134, 36, 6, 'medical_staff', 600000.00, NULL, NULL, '2025-11-24 06:28:17', '2025-11-24 06:28:17'),
(135, 31, 7, 'driver', 550000.00, NULL, NULL, '2025-11-24 06:28:46', '2025-11-24 06:28:46'),
(136, 31, 6, 'medical_staff', 600000.00, NULL, NULL, '2025-11-24 06:28:46', '2025-11-24 06:28:46');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `locations`
--

CREATE TABLE `locations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'both',
  `address` varchar(255) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `locations`
--

INSERT INTO `locations` (`id`, `name`, `type`, `address`, `note`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'BV Chợ rẫy', 'both', 'HCM', NULL, 1, '2025-11-21 12:02:39', '2025-11-21 12:02:39'),
(2, 'BV nhi đồng', 'both', 'HCM', NULL, 1, '2025-11-21 12:02:51', '2025-11-21 12:02:51'),
(3, 'BV Phạm Ngọc Thạch', 'both', 'HCM', NULL, 1, '2025-11-21 12:03:02', '2025-11-21 12:03:22'),
(4, 'BV 115', 'both', 'HCM', NULL, 1, '2025-11-21 12:03:16', '2025-11-21 12:03:16'),
(5, 'BV Việt Đức HN', 'both', 'HN', NULL, 1, '2025-11-21 12:03:38', '2025-11-21 12:03:38'),
(6, 'BV Đa khoa Lâm Đồng', 'both', 'Lâm Đồng', NULL, 1, '2025-11-21 12:03:51', '2025-11-21 12:03:51'),
(7, 'BV Nam Sài Gòn', 'both', 'HCM', NULL, 1, '2025-11-21 12:04:07', '2025-11-21 12:04:07'),
(8, 'BV Sài Gòn ITO', 'both', 'Nguyễn Trọng Tuyển, Phú Nhuận, HCM', NULL, 1, '2025-11-21 12:04:33', '2025-11-21 12:04:33'),
(9, 'TTYT Đức Trọng', 'both', 'Đức Trọng', NULL, 1, '2025-11-21 12:04:54', '2025-11-21 12:04:54'),
(10, 'Khoa HSTC - BV ĐKLĐ', 'from', NULL, NULL, 1, '2025-11-22 10:38:48', '2025-11-22 10:38:48'),
(11, 'Đức Trọng', 'to', NULL, NULL, 1, '2025-11-22 10:38:48', '2025-11-22 10:38:48'),
(12, 'Đam Rông', 'to', NULL, NULL, 1, '2025-11-22 10:50:14', '2025-11-22 10:50:14'),
(14, 'Võ Trường Toản - Đà Lạt', 'from', NULL, NULL, 1, '2025-11-22 10:58:48', '2025-11-22 10:58:48'),
(15, 'Lâm Hà', 'to', NULL, NULL, 1, '2025-11-22 11:00:30', '2025-11-22 11:00:30'),
(16, 'Ka Đô - Đơn Dương', 'to', NULL, NULL, 1, '2025-11-22 11:03:46', '2025-11-22 11:03:46'),
(17, 'Đơn Dương', 'to', NULL, NULL, 1, '2025-11-22 11:05:54', '2025-11-22 11:05:54'),
(18, 'Liên Đầm - Di Linh', 'to', NULL, NULL, 1, '2025-11-22 11:08:46', '2025-11-22 11:08:46'),
(19, 'Bảo Lộc', 'to', NULL, NULL, 1, '2025-11-22 11:11:05', '2025-11-22 11:11:05'),
(20, 'Dốc ga Trại mát - Đà Lạt', 'to', NULL, NULL, 1, '2025-11-22 11:13:13', '2025-11-22 11:13:13'),
(21, 'Phan Đình Phùng - Đà Lạt', 'from', NULL, NULL, 1, '2025-11-22 11:22:56', '2025-11-22 11:22:56'),
(22, 'Phú Hội', 'to', NULL, NULL, 1, '2025-11-22 11:24:34', '2025-11-22 11:24:34'),
(23, 'Đạ Tẻh', 'to', NULL, NULL, 1, '2025-11-22 11:35:18', '2025-11-22 11:35:18'),
(24, 'Khoa Phổi - BVĐK', 'from', NULL, NULL, 1, '2025-11-22 11:37:34', '2025-11-22 11:37:34'),
(25, 'Khoa Ngoại TH - BV Đa khoa LĐ', 'from', NULL, NULL, 1, '2025-11-22 11:41:05', '2025-11-22 11:41:05'),
(26, 'BV Bình Dân', 'to', NULL, NULL, 1, '2025-11-22 11:41:05', '2025-11-22 11:41:05'),
(27, 'Di Linh', 'to', NULL, NULL, 1, '2025-11-22 11:50:50', '2025-11-22 11:50:50'),
(28, 'BV ĐH Y Dược HCM', 'to', NULL, NULL, 1, '2025-11-22 11:57:35', '2025-11-22 11:57:35'),
(29, 'Bồng lai', 'to', NULL, NULL, 1, '2025-11-23 11:18:13', '2025-11-23 11:18:13'),
(30, 'Nam Ban', 'to', NULL, NULL, 1, '2025-11-23 11:21:29', '2025-11-23 11:21:29'),
(31, 'Bv Phục hồi chức năng HCM', 'from', NULL, NULL, 1, '2025-11-23 11:23:11', '2025-11-23 11:23:11'),
(32, 'Vạn Thành - Đà Lạt', 'to', NULL, NULL, 1, '2025-11-23 11:23:11', '2025-11-23 11:23:11'),
(33, 'Khoa Ngoại TK - BV Đa khoa LĐ', 'from', NULL, NULL, 1, '2025-11-23 11:24:49', '2025-11-23 11:24:49'),
(34, 'Phường 10 - Đà Lạt', 'to', NULL, NULL, 1, '2025-11-23 11:24:49', '2025-11-23 11:24:49'),
(35, 'Khoa nội B - BV Đa khoa LĐ', 'from', NULL, NULL, 1, '2025-11-23 11:32:50', '2025-11-23 11:32:50'),
(36, 'Tam Bố - Di Linh', 'to', NULL, NULL, 1, '2025-11-23 11:34:32', '2025-11-23 11:34:32'),
(37, 'Phúc Thọ - Lâm Hà', 'to', NULL, NULL, 1, '2025-11-23 11:35:57', '2025-11-23 11:35:57'),
(38, 'Vạn kiếp - Đà Lạt', 'to', NULL, NULL, 1, '2025-11-24 06:13:35', '2025-11-24 06:13:35'),
(39, 'Tân văn - Lâm Hà', 'to', NULL, NULL, 1, '2025-11-24 06:23:29', '2025-11-24 06:23:29'),
(40, 'Tân Hà - Lâm Hà', 'to', NULL, NULL, 1, '2025-11-24 06:24:55', '2025-11-24 06:24:55');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `maintenance_services`
--

CREATE TABLE `maintenance_services` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `maintenance_services`
--

INSERT INTO `maintenance_services` (`id`, `name`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Thay nhớt', 'định kỳ mỗi 5000km', 1, '2025-11-21 12:11:06', '2025-11-21 12:11:06'),
(2, 'Thay vỏ', NULL, 1, '2025-11-21 12:11:15', '2025-11-21 12:11:15'),
(3, 'Rửa xe', NULL, 1, '2025-11-21 12:11:24', '2025-11-21 12:11:24'),
(4, 'vá lốp', NULL, 1, '2025-11-22 01:51:39', '2025-11-22 01:51:39'),
(5, 'Vé máy bay ĐL - HN', NULL, 1, '2025-11-22 09:11:02', '2025-11-22 09:11:02'),
(6, 'khách sạn', NULL, 1, '2025-11-22 09:18:40', '2025-11-22 09:18:40'),
(7, 'đổ dầu', NULL, 1, '2025-11-22 09:20:47', '2025-11-22 09:20:47'),
(8, 'Dán xe', NULL, 1, '2025-11-22 09:22:05', '2025-11-22 09:22:05'),
(9, 'Dán epass', NULL, 1, '2025-11-22 09:22:40', '2025-11-22 09:22:40'),
(10, 'Lắp đặt thiết bị', NULL, 1, '2025-11-22 09:23:09', '2025-11-22 09:23:09'),
(11, 'Ép biển số', NULL, 1, '2025-11-22 09:23:53', '2025-11-22 09:23:53'),
(13, 'Ăn uống', NULL, 1, '2025-11-22 09:25:53', '2025-11-22 09:25:53'),
(14, 'Công tài', NULL, 1, '2025-11-22 09:26:24', '2025-11-22 09:26:24'),
(15, 'Phí qua trạm', NULL, 1, '2025-11-22 09:27:02', '2025-11-22 09:27:02'),
(18, 'Bảo hiểm vật chất', NULL, 1, '2025-11-22 11:26:43', '2025-11-22 11:26:43'),
(19, 'Bảo hiểm Dân sự', NULL, 1, '2025-11-22 11:27:40', '2025-11-22 11:27:40'),
(20, 'Thay 2 van bình oxy', NULL, 1, '2025-11-24 06:20:34', '2025-11-24 06:20:34');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `media`
--

CREATE TABLE `media` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) DEFAULT NULL,
  `collection_name` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `mime_type` varchar(255) DEFAULT NULL,
  `disk` varchar(255) NOT NULL,
  `conversions_disk` varchar(255) DEFAULT NULL,
  `size` bigint(20) UNSIGNED NOT NULL,
  `manipulations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`manipulations`)),
  `custom_properties` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`custom_properties`)),
  `generated_conversions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`generated_conversions`)),
  `responsive_images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`responsive_images`)),
  `order_column` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2025_11_21_093250_create_permission_tables', 1),
(6, '2025_11_21_093300_create_activity_log_table', 1),
(7, '2025_11_21_093301_add_event_column_to_activity_log_table', 1),
(8, '2025_11_21_093302_add_batch_uuid_column_to_activity_log_table', 1),
(9, '2025_11_21_100001_create_vehicles_table', 1),
(10, '2025_11_21_100002_create_patients_table', 1),
(11, '2025_11_21_100003_create_incidents_table', 1),
(12, '2025_11_21_100004_create_transactions_table', 1),
(13, '2025_11_21_100005_create_notes_table', 1),
(14, '2025_11_21_183658_create_locations_table', 2),
(15, '2025_11_21_183706_create_additional_services_table', 2),
(16, '2025_11_21_183706_create_partners_table', 2),
(17, '2025_11_21_183707_add_location_and_service_fields_to_incidents_table', 3),
(18, '2025_11_21_183707_create_maintenance_services_table', 3),
(19, '2025_11_21_183707_create_vehicle_maintenances_table', 3),
(20, '2025_11_21_192510_create_incident_additional_services_table', 4),
(21, '2025_11_22_055109_create_staff_table', 5),
(22, '2025_11_22_061123_create_positions_and_departments_tables', 6),
(23, '2025_11_22_062627_add_driver_id_to_vehicles_table', 7),
(24, '2025_11_22_062629_create_incident_staff_table', 7),
(25, '2025_11_22_065110_add_wage_to_incident_staff_and_staff_id_to_transactions', 8),
(26, '2025_11_22_075231_add_wage_details_to_incident_staff_table', 9),
(27, '2025_11_22_080030_create_wage_types_table', 10),
(28, '2025_11_22_081641_add_base_salary_to_staff_table', 11),
(29, '2025_11_22_083242_create_staff_adjustments_table', 12),
(30, '2025_11_22_084547_add_incident_id_to_staff_adjustments_table', 13),
(31, '2025_11_22_091359_add_category_to_transactions_table', 14),
(32, '2025_11_22_092501_make_vehicle_id_nullable_in_transactions_table', 15),
(33, '2025_11_22_094433_create_salary_advances_table', 16),
(34, '2025_11_22_105806_add_equity_percentage_to_staff_table', 17),
(35, '2025_11_22_110905_add_du_kien_chi_to_transactions_type', 18),
(36, '2025_11_22_115014_alter_partners_commission_rate_to_price', 19),
(37, '2025_11_22_120943_add_vehicle_owner_type_and_vehicle_id_to_staff', 20),
(38, '2025_11_22_140000_add_vehicle_maintenance_id_to_transactions_table', 21),
(39, '2025_11_23_150000_add_transaction_category_and_audit_fields', 22),
(40, '2025_11_24_071913_create_system_settings_table', 23),
(41, '2025_11_24_075616_create_media_table', 23);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),
(2, 'App\\Models\\User', 2),
(3, 'App\\Models\\User', 3),
(4, 'App\\Models\\User', 4),
(4, 'App\\Models\\User', 7),
(4, 'App\\Models\\User', 8),
(4, 'App\\Models\\User', 10),
(4, 'App\\Models\\User', 14),
(5, 'App\\Models\\User', 5),
(5, 'App\\Models\\User', 6),
(6, 'App\\Models\\User', 9),
(6, 'App\\Models\\User', 23),
(6, 'App\\Models\\User', 24),
(6, 'App\\Models\\User', 26),
(6, 'App\\Models\\User', 27),
(6, 'App\\Models\\User', 28),
(6, 'App\\Models\\User', 29),
(6, 'App\\Models\\User', 30),
(7, 'App\\Models\\User', 11),
(7, 'App\\Models\\User', 12),
(7, 'App\\Models\\User', 13),
(8, 'App\\Models\\User', 22),
(8, 'App\\Models\\User', 25);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `notes`
--

CREATE TABLE `notes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `incident_id` bigint(20) UNSIGNED DEFAULT NULL,
  `vehicle_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `note` text NOT NULL,
  `severity` enum('info','warning','critical') NOT NULL DEFAULT 'info',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `partners`
--

CREATE TABLE `partners` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `commission_rate` decimal(10,2) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `partners`
--

INSERT INTO `partners` (`id`, `name`, `type`, `phone`, `email`, `address`, `commission_rate`, `note`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Khoa nội A - BV Đa khoa', 'collaborator', NULL, NULL, NULL, NULL, NULL, 1, '2025-11-21 12:10:15', '2025-11-21 12:10:15'),
(2, 'Gara ST-28', 'maintenance', NULL, NULL, NULL, NULL, NULL, 1, '2025-11-21 12:10:38', '2025-11-21 12:10:38'),
(3, 'Gara ngoài', 'maintenance', NULL, NULL, NULL, NULL, 'áp dụng cho trường hợp đột xuất trên đường', 1, '2025-11-22 00:56:02', '2025-11-22 00:56:02'),
(4, 'Môi giới HCM', 'collaborator', NULL, NULL, NULL, NULL, 'Dùng cho các trường hợp giới thiệu là cá nhân hoặc người bên ngoài', 1, '2025-11-22 00:56:56', '2025-11-22 00:56:56'),
(5, 'C - bone Hotel', 'maintenance', NULL, NULL, NULL, NULL, NULL, 1, '2025-11-22 09:15:16', '2025-11-22 09:15:16'),
(6, 'Vietjet air', 'maintenance', NULL, NULL, NULL, NULL, NULL, 1, '2025-11-22 09:18:34', '2025-11-22 09:18:34'),
(7, 'Petrolimex', 'maintenance', NULL, NULL, NULL, NULL, NULL, 1, '2025-11-22 09:20:47', '2025-11-22 09:20:47'),
(8, 'Cửa hàng ngoài', 'maintenance', NULL, NULL, NULL, NULL, NULL, 1, '2025-11-22 09:22:05', '2025-11-22 09:22:05'),
(9, 'Viettel', 'maintenance', NULL, NULL, NULL, NULL, NULL, 1, '2025-11-22 09:22:40', '2025-11-22 09:22:40'),
(10, 'Mai Anh GPS', 'maintenance', NULL, NULL, NULL, NULL, NULL, 1, '2025-11-22 09:23:09', '2025-11-22 09:23:09'),
(11, 'Thợ ngoài', 'maintenance', NULL, NULL, NULL, NULL, NULL, 1, '2025-11-22 09:23:53', '2025-11-22 09:23:53'),
(13, 'Quán cơm', 'maintenance', NULL, NULL, NULL, NULL, NULL, 1, '2025-11-22 09:25:53', '2025-11-22 09:25:53'),
(14, 'Lái xe', 'maintenance', NULL, NULL, NULL, NULL, NULL, 1, '2025-11-22 09:26:24', '2025-11-22 09:26:24'),
(15, 'BOT', 'maintenance', NULL, NULL, NULL, NULL, NULL, 1, '2025-11-22 09:27:02', '2025-11-22 09:27:02'),
(18, 'Khoa HSTC - BV ĐKLĐ', 'collaborator', NULL, NULL, NULL, NULL, NULL, 1, '2025-11-22 10:28:45', '2025-11-22 10:28:45'),
(19, 'Khoa nội B - BV ĐKLĐ', 'collaborator', NULL, NULL, NULL, NULL, NULL, 1, '2025-11-22 10:29:07', '2025-11-22 10:29:07'),
(20, 'Khoa Ngoại TK - BV ĐKLĐ', 'collaborator', NULL, NULL, NULL, NULL, NULL, 1, '2025-11-22 10:29:24', '2025-11-22 10:29:24'),
(21, 'Khoa Ngoại CT - BV ĐKLĐ', 'collaborator', NULL, NULL, NULL, NULL, NULL, 1, '2025-11-22 10:29:34', '2025-11-22 10:29:34'),
(22, 'kKhoa Ngoại TH - BV ĐKLĐ', 'collaborator', NULL, NULL, NULL, NULL, NULL, 1, '2025-11-22 10:29:56', '2025-11-22 10:29:56'),
(23, 'Khoa Phổi - BV ĐKLĐ', 'collaborator', NULL, NULL, NULL, NULL, NULL, 1, '2025-11-22 10:30:04', '2025-11-22 10:30:04'),
(24, 'Khoa Ung bướu - BV ĐKLĐ', 'collaborator', NULL, NULL, NULL, NULL, NULL, 1, '2025-11-22 10:30:14', '2025-11-22 10:30:14'),
(25, 'Khoa Cấp cứu - BV ĐKLĐ', 'collaborator', NULL, NULL, NULL, NULL, NULL, 1, '2025-11-22 10:30:23', '2025-11-22 10:30:23'),
(26, 'Đức Anh - khoa HSTC  - BV ĐKLĐ', 'collaborator', NULL, NULL, NULL, NULL, NULL, 1, '2025-11-22 10:30:51', '2025-11-22 10:30:51'),
(27, 'Cty bảo hiểm', 'maintenance', NULL, NULL, NULL, NULL, NULL, 1, '2025-11-22 11:26:43', '2025-11-22 11:26:43'),
(28, 'DCYK Hai Dang', 'maintenance', NULL, NULL, NULL, NULL, NULL, 1, '2025-11-24 06:20:34', '2025-11-24 06:20:34');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `patients`
--

CREATE TABLE `patients` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `birth_year` int(11) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `address` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `patients`
--

INSERT INTO `patients` (`id`, `name`, `birth_year`, `phone`, `gender`, `address`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'nguyen van a', 1999, '0987654321', 'male', NULL, NULL, '2025-11-21 11:14:39', '2025-11-21 11:14:39'),
(2, 'nguyen van b', 1995, '0909090909', 'male', NULL, NULL, '2025-11-21 12:41:12', '2025-11-21 12:41:12'),
(3, 'nguyen van b', 2000, NULL, 'male', NULL, NULL, '2025-11-22 05:05:06', '2025-11-22 05:05:06'),
(4, 'pham trong nghia', 1989, '0985108558', 'male', NULL, NULL, '2025-11-22 10:38:48', '2025-11-22 10:38:48'),
(5, 'Không có tên', NULL, '0386927627', 'male', NULL, NULL, '2025-11-22 10:43:13', '2025-11-22 10:43:13'),
(6, 'Liêng hot ha khiêm', 1972, '0325692873', 'male', NULL, NULL, '2025-11-22 10:50:14', '2025-11-22 10:50:14'),
(8, 'Không có tên', NULL, NULL, 'male', NULL, NULL, '2025-11-22 10:58:48', '2025-11-22 10:58:48'),
(9, 'ngo hai', 1968, '0365991068', NULL, NULL, NULL, '2025-11-22 11:00:30', '2025-11-22 11:00:30'),
(10, 'Lê Đước Cường', 1935, '0386122168', 'male', NULL, NULL, '2025-11-22 11:01:42', '2025-11-22 11:01:42'),
(11, 'Ya Nhât', 1970, '0862833804', 'male', NULL, NULL, '2025-11-22 11:03:46', '2025-11-22 11:03:46'),
(12, 'le thi thưa', 1935, '0985714494', 'female', NULL, NULL, '2025-11-22 11:05:55', '2025-11-22 11:05:55'),
(13, 'ka Breis', 1965, '0362720760', 'female', NULL, NULL, '2025-11-22 11:08:46', '2025-11-22 11:08:46'),
(14, 'ngo quoc thanh', 1983, '0352568520', 'male', NULL, NULL, '2025-11-22 11:11:05', '2025-11-22 11:11:05'),
(15, 'Đặng Thị Trang', 1954, '0347900160', 'female', NULL, NULL, '2025-11-22 11:13:13', '2025-11-22 11:13:13'),
(16, 'Đầu Thị Tứ', 1940, '0387852096', 'female', NULL, NULL, '2025-11-22 11:15:13', '2025-11-22 11:15:13'),
(17, 'pham van kiem', 1936, '0353930075', 'male', NULL, NULL, '2025-11-22 11:16:55', '2025-11-22 11:16:55'),
(18, 'Ya Đau', 1952, '0389819624', 'male', NULL, NULL, '2025-11-22 11:18:15', '2025-11-22 11:18:15'),
(19, 'bonhong', 1999, '0383057831', 'female', NULL, NULL, '2025-11-22 11:21:17', '2025-11-22 11:21:17'),
(20, 'vo thi tho', 1947, '0798666716', NULL, NULL, NULL, '2025-11-22 11:24:34', '2025-11-22 11:24:34'),
(21, 'Nguyễn văn ba', NULL, NULL, NULL, NULL, NULL, '2025-11-22 11:35:18', '2025-11-22 11:35:18'),
(22, 'ka Diuh', NULL, '0389253760', NULL, NULL, NULL, '2025-11-22 11:37:34', '2025-11-22 11:37:34'),
(23, 'Nguyễn Thị Hoa', 1969, '0333815585', 'female', NULL, NULL, '2025-11-22 11:41:05', '2025-11-22 11:41:05'),
(24, 'Cil Ka Nghiệp', 1984, '0339996201', 'male', NULL, NULL, '2025-11-22 11:45:07', '2025-11-22 11:45:07'),
(25, 'K Nghiệp', 1975, '0337700645', 'male', NULL, NULL, '2025-11-22 11:50:50', '2025-11-22 11:50:50'),
(26, 'Ngô Trần Thế Ngân', 1965, '0396916647', 'male', NULL, NULL, '2025-11-22 11:57:35', '2025-11-22 11:57:35'),
(27, 'Phan Ngọc Canh', 1929, '0917469795', 'male', NULL, NULL, '2025-11-23 11:18:13', '2025-11-23 11:18:13'),
(28, 'Nguyễn Hai Được', 1968, '0393906466', 'male', NULL, NULL, '2025-11-23 11:21:29', '2025-11-23 11:21:29'),
(29, 'Mẹ C Trang', NULL, '0367564088', 'female', NULL, NULL, '2025-11-23 11:23:11', '2025-11-23 11:23:11'),
(30, 'Nguyễn Thị Hồng Anh', 1956, '0978796479', 'female', NULL, NULL, '2025-11-23 11:24:49', '2025-11-23 11:24:49'),
(31, 'Dương Thanh Giản', 1941, '0868742856', 'male', NULL, NULL, '2025-11-23 11:27:00', '2025-11-23 11:27:00'),
(32, 'Trần Văn Sỹ', 1966, '0395849509', 'male', NULL, NULL, '2025-11-23 11:28:57', '2025-11-23 11:28:57'),
(33, 'Ha So', 1963, '0366254652', 'male', NULL, NULL, '2025-11-23 11:30:58', '2025-11-23 11:30:58'),
(34, 'Nguyễn Thị An', 1964, '0987332969', NULL, NULL, NULL, '2025-11-23 11:32:50', '2025-11-23 11:32:50'),
(35, 'Lê Thị Sung', 1937, '0919440592', 'female', NULL, NULL, '2025-11-23 11:34:32', '2025-11-23 11:34:32'),
(36, 'Nguyễn Văn Thuận', 1982, '0397709180', 'male', NULL, NULL, '2025-11-23 11:35:57', '2025-11-23 11:35:57'),
(37, 'K\' Thoen', 2003, '0984050265', 'female', NULL, NULL, '2025-11-23 11:39:28', '2025-11-23 11:39:28'),
(38, 'Trần Thanh Bình', 1980, '0396320417', NULL, NULL, NULL, '2025-11-23 12:22:48', '2025-11-23 12:22:48'),
(39, 'Nông Văn Phảu', 1946, '0565265346', 'male', NULL, NULL, '2025-11-24 06:09:23', '2025-11-24 06:09:23'),
(40, 'Phạm Sỹ', 1955, '0707577767', 'male', NULL, NULL, '2025-11-24 06:11:32', '2025-11-24 06:11:32'),
(41, 'Nguyễn Văn Hưng', 1968, '0769821906', 'male', NULL, NULL, '2025-11-24 06:13:35', '2025-11-24 06:13:35'),
(42, 'Hoàng Văn Nhì', 1952, '0919800622', 'male', NULL, NULL, '2025-11-24 06:23:29', '2025-11-24 06:23:29'),
(43, 'đoàn văn tân', 1959, '0374659568', 'male', NULL, NULL, '2025-11-24 06:24:55', '2025-11-24 06:24:55');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'view vehicles', 'web', '2025-11-21 11:07:23', '2025-11-21 11:07:23'),
(2, 'create vehicles', 'web', '2025-11-21 11:07:23', '2025-11-21 11:07:23'),
(3, 'edit vehicles', 'web', '2025-11-21 11:07:23', '2025-11-21 11:07:23'),
(4, 'delete vehicles', 'web', '2025-11-21 11:07:23', '2025-11-21 11:07:23'),
(5, 'view incidents', 'web', '2025-11-21 11:07:23', '2025-11-21 11:07:23'),
(6, 'create incidents', 'web', '2025-11-21 11:07:23', '2025-11-21 11:07:23'),
(7, 'edit incidents', 'web', '2025-11-21 11:07:23', '2025-11-21 11:07:23'),
(8, 'delete incidents', 'web', '2025-11-21 11:07:23', '2025-11-21 11:07:23'),
(9, 'view transactions', 'web', '2025-11-21 11:07:23', '2025-11-21 11:07:23'),
(10, 'create transactions', 'web', '2025-11-21 11:07:23', '2025-11-21 11:07:23'),
(11, 'edit transactions', 'web', '2025-11-21 11:07:23', '2025-11-21 11:07:23'),
(12, 'delete transactions', 'web', '2025-11-21 11:07:23', '2025-11-21 11:07:23'),
(13, 'view reports', 'web', '2025-11-21 11:07:23', '2025-11-21 11:07:23'),
(14, 'export reports', 'web', '2025-11-21 11:07:23', '2025-11-21 11:07:23'),
(15, 'view audits', 'web', '2025-11-21 11:07:23', '2025-11-21 11:07:23'),
(16, 'manage users', 'web', '2025-11-21 11:07:23', '2025-11-21 11:07:23'),
(17, 'manage vehicles', 'web', '2025-11-21 12:01:26', '2025-11-21 12:01:26'),
(18, 'view patients', 'web', '2025-11-21 12:01:26', '2025-11-21 12:01:26'),
(19, 'create patients', 'web', '2025-11-21 12:01:26', '2025-11-21 12:01:26'),
(20, 'edit patients', 'web', '2025-11-21 12:01:26', '2025-11-21 12:01:26'),
(21, 'delete patients', 'web', '2025-11-21 12:01:26', '2025-11-21 12:01:26'),
(22, 'manage settings', 'web', '2025-11-21 12:01:26', '2025-11-21 12:01:26'),
(23, 'view staff', 'web', '2025-11-21 22:55:14', '2025-11-21 22:55:14'),
(24, 'create staff', 'web', '2025-11-21 22:55:14', '2025-11-21 22:55:14'),
(25, 'edit staff', 'web', '2025-11-21 22:55:14', '2025-11-21 22:55:14'),
(26, 'delete staff', 'web', '2025-11-21 22:55:14', '2025-11-21 22:55:14'),
(27, 'view notes', 'web', '2025-11-21 22:55:14', '2025-11-21 22:55:14'),
(28, 'create notes', 'web', '2025-11-21 22:55:14', '2025-11-21 22:55:14'),
(29, 'edit notes', 'web', '2025-11-21 22:55:14', '2025-11-21 22:55:14'),
(30, 'delete notes', 'web', '2025-11-21 22:55:14', '2025-11-21 22:55:14');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `positions`
--

CREATE TABLE `positions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `positions`
--

INSERT INTO `positions` (`id`, `name`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Giám đốc', 1, '2025-11-21 23:15:36', '2025-11-21 23:15:36'),
(2, 'Phó giám đốc', 1, '2025-11-21 23:15:36', '2025-11-21 23:15:36'),
(3, 'Trưởng phòng', 1, '2025-11-21 23:15:36', '2025-11-21 23:15:36'),
(4, 'Phó phòng', 1, '2025-11-21 23:15:36', '2025-11-21 23:15:36'),
(5, 'Nhân viên', 1, '2025-11-21 23:15:36', '2025-11-21 23:15:36'),
(6, 'Trưởng nhóm', 1, '2025-11-21 23:15:36', '2025-11-21 23:15:36'),
(7, 'Chuyên viên', 1, '2025-11-21 23:15:36', '2025-11-21 23:15:36'),
(8, 'Lái xe', 1, '2025-11-21 23:15:36', '2025-11-21 23:15:36'),
(9, 'Điều dưỡng', 1, '2025-11-21 23:15:36', '2025-11-21 23:15:36'),
(10, 'Bác sĩ', 1, '2025-11-21 23:15:36', '2025-11-21 23:15:36'),
(11, 'Y tá', 1, '2025-11-21 23:15:36', '2025-11-21 23:15:36'),
(12, 'Kỹ thuật viên', 1, '2025-11-21 23:15:36', '2025-11-21 23:15:36'),
(13, 'Manager', 1, '2025-11-22 01:21:06', '2025-11-22 01:21:06'),
(14, 'Cổ đông', 1, '2025-11-22 04:02:39', '2025-11-22 04:02:39'),
(22, 'chủ xe', 1, '2025-11-22 05:33:04', '2025-11-22 05:33:04');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'web', '2025-11-21 11:07:23', '2025-11-21 11:07:23'),
(2, 'dispatcher', 'web', '2025-11-21 11:07:23', '2025-11-21 11:07:23'),
(3, 'accountant', 'web', '2025-11-21 11:07:23', '2025-11-21 11:07:23'),
(4, 'driver', 'web', '2025-11-21 11:07:23', '2025-11-21 11:07:23'),
(5, 'manager', 'web', '2025-11-21 22:55:14', '2025-11-21 22:55:14'),
(6, 'medical_staff', 'web', '2025-11-21 22:55:14', '2025-11-21 22:55:14'),
(7, 'investor', 'web', '2025-11-21 22:55:14', '2025-11-21 22:55:14'),
(8, 'vehicle_owner', 'web', '2025-11-22 05:31:56', '2025-11-22 05:31:56');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 5),
(1, 7),
(2, 1),
(2, 5),
(3, 1),
(3, 5),
(4, 1),
(4, 5),
(5, 1),
(5, 2),
(5, 3),
(5, 4),
(5, 5),
(5, 6),
(5, 7),
(6, 1),
(6, 2),
(6, 4),
(6, 5),
(6, 6),
(7, 1),
(7, 2),
(7, 5),
(8, 1),
(8, 5),
(9, 1),
(9, 2),
(9, 3),
(9, 5),
(9, 7),
(10, 1),
(10, 2),
(10, 3),
(10, 5),
(11, 1),
(11, 3),
(11, 5),
(12, 1),
(12, 5),
(13, 2),
(13, 3),
(14, 3),
(17, 1),
(17, 5),
(18, 1),
(18, 2),
(18, 3),
(18, 5),
(18, 7),
(19, 1),
(19, 2),
(19, 5),
(20, 1),
(20, 2),
(20, 5),
(21, 1),
(21, 5),
(22, 1),
(22, 2),
(22, 3),
(22, 5),
(23, 1),
(23, 5),
(23, 7),
(24, 1),
(24, 5),
(25, 1),
(25, 5),
(26, 1),
(26, 5),
(27, 1),
(27, 5),
(27, 7),
(28, 1),
(28, 5),
(29, 1),
(29, 5),
(30, 1),
(30, 5);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `salary_advances`
--

CREATE TABLE `salary_advances` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `staff_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `from_earnings` decimal(15,2) NOT NULL DEFAULT 0.00,
  `from_company` decimal(15,2) NOT NULL DEFAULT 0.00,
  `debt_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `status` enum('pending','approved','paid_off') NOT NULL DEFAULT 'approved',
  `note` text DEFAULT NULL,
  `transaction_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`transaction_ids`)),
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `date` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `staff`
--

CREATE TABLE `staff` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `employee_code` varchar(255) DEFAULT NULL,
  `staff_type` enum('medical_staff','driver','manager','investor','admin','vehicle_owner') NOT NULL DEFAULT 'medical_staff',
  `equity_percentage` decimal(5,2) DEFAULT NULL COMMENT 'Tỷ lệ vốn góp (%) - chỉ áp dụng cho cổ đông',
  `vehicle_id` bigint(20) UNSIGNED DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `id_card` varchar(255) DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `address` text DEFAULT NULL,
  `hire_date` date DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `position` varchar(255) DEFAULT NULL,
  `base_salary` decimal(10,2) DEFAULT NULL COMMENT 'Lương cơ bản hàng tháng',
  `notes` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `staff`
--

INSERT INTO `staff` (`id`, `user_id`, `full_name`, `employee_code`, `staff_type`, `equity_percentage`, `vehicle_id`, `phone`, `email`, `id_card`, `birth_date`, `gender`, `address`, `hire_date`, `department`, `position`, `base_salary`, `notes`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'Admin', 'NV001', 'admin', NULL, NULL, NULL, 'admin@binhan.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-11-22 05:57:38', '2025-11-21 23:07:33'),
(2, 5, 'Vỹ Phạm', 'NV002', 'manager', NULL, NULL, '0986152906', 'vypham@115lamdong.com', NULL, '1989-06-20', 'female', 'Đà Lạt', '2020-01-01', 'GĐ', 'Manager', 3000000.00, NULL, 1, '2025-11-21 23:02:30', '2025-11-22 01:21:14'),
(3, 6, 'Ngô Quyền', 'NV003', 'manager', NULL, NULL, '0337677379', 'ngoquyen@115lamdong.com', NULL, NULL, 'male', 'Đà Lạt', '2025-11-01', NULL, 'Manager', 2000000.00, NULL, 1, '2025-11-21 23:03:48', '2025-11-22 08:09:15'),
(4, 7, 'Nguyễn Cữu Ninh', 'NV004', 'driver', NULL, NULL, '0888135115', 'ninh@115lamdong.com', '680154010355', '1995-02-19', 'male', 'Đà Lạt', '2024-01-01', 'Kỹ thuật', 'Lái xe', 3000000.00, NULL, 1, '2025-11-21 23:20:28', '2025-11-22 01:20:57'),
(5, 8, 'Cil Đoan', 'NV005', 'driver', NULL, NULL, '0336301619', 'doan@115lamdong.com', NULL, NULL, 'male', 'Lạc Dương', '2025-11-22', 'Kỹ thuật', 'Lái xe', 3000000.00, NULL, 1, '2025-11-21 23:22:31', '2025-11-22 01:20:36'),
(6, 9, 'Nguyễn Quốc Vũ', 'NV006', 'medical_staff', NULL, NULL, '0942548434', 'vu@115lamdong.com', NULL, NULL, 'male', 'Đà Lạt', '2025-11-22', 'Y tế', 'Điều dưỡng', 3000000.00, NULL, 1, '2025-11-21 23:23:40', '2025-11-22 01:20:08'),
(7, 10, 'Lê Phong', 'NV007', 'driver', NULL, NULL, '0775353979', 'phong@115lamdong.com', NULL, NULL, 'male', NULL, '2025-01-01', 'Kỹ thuật', 'Lái xe', 3000000.00, NULL, 1, '2025-11-22 01:22:10', '2025-11-22 08:58:20'),
(8, 11, 'Trần Đức Anh', 'NV008', 'investor', 18.42, NULL, NULL, 'ducanh@115lamdong.com', NULL, NULL, 'male', 'đà lạt', '2025-11-22', 'Cổ Đông', 'Cổ đông', NULL, NULL, 1, '2025-11-22 04:02:39', '2025-11-22 04:02:39'),
(9, 12, 'A Thành', 'NV009', 'investor', 18.42, NULL, NULL, 'thanh@115lamdong.com', NULL, NULL, 'male', NULL, '2025-11-22', 'Cổ Đông', 'Cổ đông', NULL, NULL, 1, '2025-11-22 04:03:36', '2025-11-22 04:03:36'),
(10, 13, 'A Trung', 'NV010', 'investor', 63.16, NULL, NULL, 'trung@115lamdong.com', NULL, NULL, 'male', 'đà lạt', '2022-01-01', 'Cổ Đông', 'Cổ đông', NULL, NULL, 1, '2025-11-22 04:43:36', '2025-11-22 04:43:36'),
(11, 14, 'Nova', 'NV011', 'driver', NULL, NULL, NULL, 'nova@115lamdong.com', NULL, NULL, 'male', 'Đà Lạt', '2025-11-22', 'Kỹ thuật', 'Lái xe', NULL, NULL, 1, '2025-11-22 04:55:39', '2025-11-22 04:55:39'),
(12, 22, 'Minh Trung 2', 'NV012', 'vehicle_owner', NULL, 4, '0987890909', 'minhtrung2@115lamdong.com', NULL, NULL, 'male', '36 trần phú', '2025-11-22', 'chủ xe', 'chủ xe', NULL, NULL, 1, '2025-11-22 05:33:04', '2025-11-22 10:21:20'),
(13, 23, 'Đức Anh', 'NV013', 'medical_staff', NULL, NULL, NULL, 'tranducanh@115lamdong.com', NULL, NULL, 'male', 'đà lạt', '2023-01-01', 'Y tế', 'Điều dưỡng', NULL, NULL, 1, '2025-11-22 08:03:22', '2025-11-22 08:03:22'),
(14, 24, 'Bs Hoa', 'NV014', 'medical_staff', NULL, NULL, NULL, 'bshoa@115lamdong.com', NULL, NULL, 'female', 'ĐÀ LẠT', '2022-01-01', 'Y tế', 'Bác sĩ', 4000000.00, NULL, 1, '2025-11-22 08:36:30', '2025-11-22 08:36:30'),
(15, 25, 'Cty Minh Trung 1', 'NV015', 'vehicle_owner', NULL, 3, NULL, 'minhtrung1@115lamdong.com', NULL, NULL, 'male', NULL, '2025-11-22', 'chủ xe', 'chủ xe', NULL, NULL, 1, '2025-11-22 10:19:37', '2025-11-22 10:19:37'),
(16, 26, 'Lâm', 'NV016', 'medical_staff', NULL, NULL, NULL, 'lam@115lamdong.com', NULL, NULL, 'male', NULL, '2025-11-01', 'Y tế', 'Điều dưỡng', NULL, NULL, 1, '2025-11-22 10:31:57', '2025-11-22 10:35:24'),
(17, 27, 'Phúc', 'NV017', 'medical_staff', NULL, NULL, NULL, 'phuc@115lamdong.com', NULL, NULL, 'male', NULL, '2025-11-01', 'Y tế', 'Điều dưỡng', NULL, NULL, 1, '2025-11-22 10:32:39', '2025-11-22 10:35:10'),
(18, 28, 'Lân', 'NV018', 'medical_staff', NULL, NULL, NULL, 'lan@115lamdong.com', NULL, NULL, 'male', NULL, '2025-11-01', 'Y tế', 'Điều dưỡng', NULL, NULL, 1, '2025-11-22 10:33:13', '2025-11-22 10:34:26'),
(19, 29, 'Khánh', 'NV019', 'medical_staff', NULL, NULL, NULL, 'khanh@115lamdong.com', NULL, NULL, 'male', NULL, '2025-11-01', 'Y tế', 'Điều dưỡng', NULL, NULL, 1, '2025-11-22 10:34:08', '2025-11-22 10:34:08'),
(20, 30, 'Danh', 'NV020', 'medical_staff', NULL, NULL, NULL, 'danh@115lamdong.com', NULL, NULL, 'male', NULL, '2025-11-01', 'Y tế', 'Điều dưỡng', NULL, NULL, 1, '2025-11-22 11:49:31', '2025-11-22 11:49:31');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `staff_adjustments`
--

CREATE TABLE `staff_adjustments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `staff_id` bigint(20) UNSIGNED NOT NULL,
  `incident_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `type` enum('addition','deduction') NOT NULL COMMENT 'addition = cộng, deduction = trừ',
  `amount` decimal(10,2) NOT NULL,
  `month` date NOT NULL COMMENT 'Tháng áp dụng điều chỉnh (YYYY-MM-01)',
  `category` varchar(255) NOT NULL COMMENT 'Loại: thưởng, phạt, tạm ứng, etc',
  `reason` text NOT NULL COMMENT 'Lý do điều chỉnh',
  `status` enum('pending','applied','debt') NOT NULL DEFAULT 'pending' COMMENT 'pending = chờ, applied = đã áp dụng, debt = nợ',
  `debt_amount` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Số tiền nợ nếu không đủ trừ',
  `applied_at` timestamp NULL DEFAULT NULL COMMENT 'Thời điểm áp dụng',
  `transaction_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Danh sách ID transactions được tạo' CHECK (json_valid(`transaction_ids`)),
  `from_incident_amount` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Số tiền lấy từ chuyến đi',
  `from_company_amount` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Số tiền lấy từ công ty',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `staff_adjustments`
--

INSERT INTO `staff_adjustments` (`id`, `staff_id`, `incident_id`, `created_by`, `type`, `amount`, `month`, `category`, `reason`, `status`, `debt_amount`, `applied_at`, `transaction_ids`, `from_incident_amount`, `from_company_amount`, `created_at`, `updated_at`) VALUES
(10, 5, NULL, 1, 'addition', 450000.00, '2025-11-01', 'Phụ cấp trực', '3 ngày trực 15-16-17 /11', 'applied', 0.00, '2025-11-22 11:47:15', '[161]', 0.00, 450000.00, '2025-11-22 11:47:15', '2025-11-22 11:47:15');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `system_settings`
--

CREATE TABLE `system_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `group` varchar(100) NOT NULL,
  `key` varchar(100) NOT NULL,
  `value` text DEFAULT NULL,
  `type` varchar(50) NOT NULL DEFAULT 'text',
  `options` text DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `order` int(11) NOT NULL DEFAULT 0,
  `is_public` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `system_settings`
--

INSERT INTO `system_settings` (`id`, `group`, `key`, `value`, `type`, `options`, `description`, `order`, `is_public`, `created_at`, `updated_at`) VALUES
(1, 'company', 'company_name', 'Binhan Ambulance', 'text', NULL, 'Tên công ty', 1, 1, '2025-11-24 05:48:17', '2025-11-24 05:48:17'),
(2, 'company', 'company_short_name', 'Binhan', 'text', NULL, 'Tên viết tắt', 2, 1, '2025-11-24 05:48:17', '2025-11-24 05:48:17'),
(3, 'company', 'company_slogan', 'Chăm sóc sức khỏe - Tận tâm phục vụ', 'text', NULL, 'Slogan công ty', 3, 1, '2025-11-24 05:48:17', '2025-11-24 05:48:17'),
(4, 'company', 'company_description', 'Hệ thống quản lý xe cấp cứu chuyên nghiệp', 'textarea', NULL, 'Mô tả công ty', 4, 1, '2025-11-24 05:48:17', '2025-11-24 05:48:17'),
(5, 'company', 'company_email', 'contact@binhan.com', 'email', NULL, 'Email liên hệ', 5, 1, '2025-11-24 05:48:17', '2025-11-24 05:48:17'),
(6, 'company', 'company_phone', '1900 xxxx', 'text', NULL, 'Số điện thoại', 6, 1, '2025-11-24 05:48:17', '2025-11-24 05:48:17'),
(7, 'company', 'company_hotline', '0901 234 567', 'text', NULL, 'Hotline', 7, 1, '2025-11-24 05:48:17', '2025-11-24 05:48:17'),
(8, 'company', 'company_address', 'Lâm Đồng, Việt Nam', 'textarea', NULL, 'Địa chỉ công ty', 8, 1, '2025-11-24 05:48:17', '2025-11-24 08:51:46'),
(9, 'company', 'company_tax_code', NULL, 'text', NULL, 'Mã số thuế', 9, 0, '2025-11-24 05:48:17', '2025-11-24 06:02:54'),
(10, 'company', 'company_website', 'https://binhan.com', 'url', NULL, 'Website', 10, 1, '2025-11-24 05:48:17', '2025-11-24 05:48:17'),
(11, 'appearance', 'site_name', 'Binhan Ambulance', 'text', NULL, 'Tên website (hiển thị trên tab trình duyệt)', 1, 1, '2025-11-24 05:48:17', '2025-11-24 05:48:17'),
(12, 'appearance', 'site_logo', NULL, 'image', NULL, 'Logo chính (PNG, tối đa 2MB)', 2, 1, '2025-11-24 05:48:17', '2025-11-24 06:02:54'),
(13, 'appearance', 'site_favicon', 'settings/z2YqX1X1mmxJVNg2OVl595hMiMWH1pFwrAMJOEAe.png', 'image', NULL, 'Favicon (ICO/PNG 32x32)', 3, 1, '2025-11-24 05:48:17', '2025-11-24 06:02:50'),
(14, 'appearance', 'primary_color', '#667eea', 'color', NULL, 'Màu chủ đạo', 4, 1, '2025-11-24 05:48:17', '2025-11-24 05:48:17'),
(15, 'appearance', 'secondary_color', '#764ba2', 'color', NULL, 'Màu phụ', 5, 1, '2025-11-24 05:48:17', '2025-11-24 05:48:17'),
(16, 'appearance', 'login_background', NULL, 'image', NULL, 'Hình nền trang login', 6, 1, '2025-11-24 05:48:17', '2025-11-24 06:02:54'),
(17, 'appearance', 'font_family', 'Inter', 'select', '\"[\\\"Inter\\\",\\\"Roboto\\\",\\\"Open Sans\\\",\\\"Montserrat\\\",\\\"Poppins\\\"]\"', 'Font chữ hệ thống', 7, 1, '2025-11-24 05:48:17', '2025-11-24 05:48:17'),
(18, 'appearance', 'records_per_page', '15', 'select', '\"[\\\"10\\\",\\\"15\\\",\\\"25\\\",\\\"50\\\",\\\"100\\\"]\"', 'Số bản ghi mỗi trang', 8, 0, '2025-11-24 05:48:17', '2025-11-24 05:48:17'),
(19, 'language', 'default_language', 'vi', 'select', '\"{\\\"vi\\\":\\\"Ti\\\\u1ebfng Vi\\\\u1ec7t\\\",\\\"en\\\":\\\"English\\\"}\"', 'Ngôn ngữ mặc định', 1, 1, '2025-11-24 05:48:17', '2025-11-24 05:48:17'),
(20, 'language', 'timezone', 'Asia/Ho_Chi_Minh', 'select', '\"{\\\"Asia\\\\\\/Ho_Chi_Minh\\\":\\\"Vi\\\\u1ec7t Nam (GMT+7)\\\",\\\"Asia\\\\\\/Bangkok\\\":\\\"Bangkok (GMT+7)\\\",\\\"Asia\\\\\\/Singapore\\\":\\\"Singapore (GMT+8)\\\"}\"', 'Múi giờ', 2, 0, '2025-11-24 05:48:17', '2025-11-24 05:48:17'),
(21, 'language', 'date_format', 'd/m/Y', 'select', '\"{\\\"d\\\\\\/m\\\\\\/Y\\\":\\\"DD\\\\\\/MM\\\\\\/YYYY\\\",\\\"m\\\\\\/d\\\\\\/Y\\\":\\\"MM\\\\\\/DD\\\\\\/YYYY\\\",\\\"Y-m-d\\\":\\\"YYYY-MM-DD\\\"}\"', 'Định dạng ngày', 3, 1, '2025-11-24 05:48:17', '2025-11-24 05:48:17'),
(22, 'language', 'time_format', 'H:i', 'select', '\"{\\\"H:i\\\":\\\"24 gi\\\\u1edd (13:00)\\\",\\\"h:i A\\\":\\\"12 gi\\\\u1edd (01:00 PM)\\\"}\"', 'Định dạng giờ', 4, 1, '2025-11-24 05:48:17', '2025-11-24 05:48:17'),
(23, 'language', 'currency', 'VND', 'text', NULL, 'Đơn vị tiền tệ', 5, 1, '2025-11-24 05:48:17', '2025-11-24 05:48:17'),
(24, 'language', 'currency_symbol', '₫', 'text', NULL, 'Ký hiệu tiền tệ', 6, 1, '2025-11-24 05:48:17', '2025-11-24 05:48:17'),
(25, 'language', 'currency_position', 'after', 'select', '\"{\\\"before\\\":\\\"Tr\\\\u01b0\\\\u1edbc s\\\\u1ed1 (\\\\u20ab100)\\\",\\\"after\\\":\\\"Sau s\\\\u1ed1 (100\\\\u20ab)\\\"}\"', 'Vị trí ký hiệu tiền', 7, 1, '2025-11-24 05:48:17', '2025-11-24 05:48:17'),
(26, 'language', 'decimal_places', '0', 'select', '\"{\\\"0\\\":\\\"0\\\",\\\"2\\\":\\\"2\\\"}\"', 'Số chữ số thập phân', 8, 1, '2025-11-24 05:48:17', '2025-11-24 05:48:17'),
(27, 'language', 'thousand_separator', ',', 'select', '\"{\\\",\\\":\\\"D\\\\u1ea5u ph\\\\u1ea9y (,)\\\",\\\".\\\":\\\"D\\\\u1ea5u ch\\\\u1ea5m (.)\\\",\\\" \\\":\\\"Kho\\\\u1ea3ng tr\\\\u1eafng\\\"}\"', 'Phân cách hàng nghìn', 9, 1, '2025-11-24 05:48:17', '2025-11-24 05:48:17'),
(28, 'business', 'free_kilometers', '10', 'number', NULL, 'Số km miễn phí', 1, 0, '2025-11-24 05:48:17', '2025-11-24 05:48:17'),
(29, 'business', 'price_per_km', '15000', 'number', NULL, 'Giá mỗi km thêm (VND)', 2, 0, '2025-11-24 05:48:17', '2025-11-24 05:48:17'),
(30, 'business', 'vat_rate', '10', 'number', NULL, 'Thuế VAT (%)', 3, 0, '2025-11-24 05:48:17', '2025-11-24 05:48:17'),
(31, 'business', 'service_fee', '0', 'number', NULL, 'Phí dịch vụ (%)', 4, 0, '2025-11-24 05:48:17', '2025-11-24 05:48:17'),
(32, 'business', 'waiting_fee_per_hour', '50000', 'number', NULL, 'Giá tiền chờ mỗi giờ (VND)', 5, 0, '2025-11-24 05:48:17', '2025-11-24 05:48:17'),
(33, 'business', 'free_waiting_time', '30', 'number', NULL, 'Thời gian chờ miễn phí (phút)', 6, 0, '2025-11-24 05:48:17', '2025-11-24 05:48:17'),
(34, 'business', 'auto_calculate_fee', '0', 'checkbox', NULL, 'Tự động tính tiền', 7, 0, '2025-11-24 05:48:17', '2025-11-24 08:50:39'),
(35, 'business', 'require_approval', '0', 'checkbox', NULL, 'Yêu cầu phê duyệt chuyến đi', 8, 0, '2025-11-24 05:48:17', '2025-11-24 05:48:17'),
(36, 'business', 'allow_edit_after_save', '1', 'checkbox', NULL, 'Cho phép chỉnh sửa sau khi lưu', 9, 0, '2025-11-24 05:48:17', '2025-11-24 05:48:17'),
(37, 'business', 'shift_start_time', '07:00', 'time', NULL, 'Giờ bắt đầu ca', 10, 0, '2025-11-24 05:48:17', '2025-11-24 05:48:17'),
(38, 'business', 'shift_end_time', '19:00', 'time', NULL, 'Giờ kết thúc ca', 11, 0, '2025-11-24 05:48:17', '2025-11-24 05:48:17'),
(39, 'security', 'session_timeout', '120', 'number', NULL, 'Timeout session (phút)', 1, 0, '2025-11-24 05:48:17', '2025-11-24 05:48:17'),
(40, 'security', 'max_login_attempts', '5', 'number', NULL, 'Số lần đăng nhập sai tối đa', 2, 0, '2025-11-24 05:48:17', '2025-11-24 05:48:17'),
(41, 'security', 'lockout_duration', '15', 'number', NULL, 'Thời gian khóa tài khoản (phút)', 3, 0, '2025-11-24 05:48:17', '2025-11-24 05:48:17'),
(42, 'security', 'min_password_length', '8', 'number', NULL, 'Độ dài mật khẩu tối thiểu', 4, 0, '2025-11-24 05:48:17', '2025-11-24 05:48:17'),
(43, 'security', 'require_special_char', '0', 'checkbox', NULL, 'Yêu cầu ký tự đặc biệt trong mật khẩu', 5, 0, '2025-11-24 05:48:17', '2025-11-24 05:48:17'),
(44, 'security', 'require_number', '1', 'checkbox', NULL, 'Yêu cầu số trong mật khẩu', 6, 0, '2025-11-24 05:48:17', '2025-11-24 05:48:17'),
(45, 'security', 'require_uppercase', '0', 'checkbox', NULL, 'Yêu cầu chữ hoa trong mật khẩu', 7, 0, '2025-11-24 05:48:17', '2025-11-24 05:48:17'),
(46, 'security', 'allow_remember_me', '1', 'checkbox', NULL, 'Cho phép ghi nhớ đăng nhập', 8, 0, '2025-11-24 05:48:17', '2025-11-24 05:48:17'),
(47, 'maintenance', 'auto_backup', '0', 'checkbox', NULL, 'Tự động backup', 1, 0, '2025-11-24 05:48:17', '2025-11-24 05:48:17'),
(48, 'maintenance', 'backup_frequency', 'daily', 'select', '\"{\\\"daily\\\":\\\"H\\\\u00e0ng ng\\\\u00e0y\\\",\\\"weekly\\\":\\\"H\\\\u00e0ng tu\\\\u1ea7n\\\",\\\"monthly\\\":\\\"H\\\\u00e0ng th\\\\u00e1ng\\\"}\"', 'Tần suất backup', 2, 0, '2025-11-24 05:48:17', '2025-11-24 05:48:17'),
(49, 'maintenance', 'retain_backups_days', '30', 'number', NULL, 'Giữ backup trong bao nhiêu ngày', 3, 0, '2025-11-24 05:48:17', '2025-11-24 05:48:17'),
(50, 'maintenance', 'maintenance_mode', '0', 'checkbox', NULL, 'Chế độ bảo trì', 4, 0, '2025-11-24 05:48:17', '2025-11-24 05:48:17'),
(51, 'maintenance', 'maintenance_message', 'Hệ thống đang bảo trì, vui lòng quay lại sau.', 'textarea', NULL, 'Thông báo bảo trì', 5, 1, '2025-11-24 05:48:17', '2025-11-24 05:48:17'),
(52, 'system', 'debug_mode', '0', 'checkbox', NULL, 'Chế độ debug (chỉ dùng khi development)', 1, 0, '2025-11-24 05:48:17', '2025-11-24 05:48:17'),
(53, 'system', 'log_level', 'info', 'select', '\"{\\\"debug\\\":\\\"Debug\\\",\\\"info\\\":\\\"Info\\\",\\\"warning\\\":\\\"Warning\\\",\\\"error\\\":\\\"Error\\\"}\"', 'Mức độ log', 2, 0, '2025-11-24 05:48:17', '2025-11-24 05:48:17'),
(54, 'system', 'max_upload_size', '10', 'number', NULL, 'Kích thước file upload tối đa (MB)', 3, 0, '2025-11-24 05:48:17', '2025-11-24 05:48:17'),
(55, 'system', 'allowed_file_types', 'jpg,jpeg,png,pdf,doc,docx,xls,xlsx', 'text', NULL, 'Loại file cho phép upload (phân cách bằng dấu phẩy)', 4, 0, '2025-11-24 05:48:17', '2025-11-24 05:48:17'),
(56, 'system', 'enable_cache', '1', 'checkbox', NULL, 'Bật cache hệ thống', 5, 0, '2025-11-24 05:48:17', '2025-11-24 05:48:17'),
(57, 'system', 'cache_duration', '60', 'number', NULL, 'Thời gian cache (phút)', 6, 0, '2025-11-24 05:48:17', '2025-11-24 05:48:17'),
(58, 'system', 'log_retention_days', '90', 'number', NULL, 'Số ngày giữ log', 7, 0, '2025-11-24 05:48:17', '2025-11-24 05:48:17');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `transactions`
--

CREATE TABLE `transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `incident_id` bigint(20) UNSIGNED DEFAULT NULL,
  `staff_id` bigint(20) UNSIGNED DEFAULT NULL,
  `vehicle_id` bigint(20) UNSIGNED DEFAULT NULL,
  `vehicle_maintenance_id` bigint(20) UNSIGNED DEFAULT NULL,
  `type` enum('thu','chi','du_kien_chi') NOT NULL,
  `category` varchar(255) DEFAULT NULL,
  `transaction_category` varchar(50) DEFAULT NULL COMMENT 'thu_chinh, chi_chinh, tien_cong_lai_xe, tien_cong_nvyt, hoa_hong, bao_tri, dich_vu_bo_sung, chi_phi_bo_sung',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `replaced_by` bigint(20) UNSIGNED DEFAULT NULL,
  `edited_at` timestamp NULL DEFAULT NULL,
  `edited_by` bigint(20) UNSIGNED DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL,
  `method` enum('cash','bank','other') NOT NULL DEFAULT 'cash',
  `payment_method` varchar(255) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `recorded_by` bigint(20) UNSIGNED NOT NULL,
  `date` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `transactions`
--

INSERT INTO `transactions` (`id`, `incident_id`, `staff_id`, `vehicle_id`, `vehicle_maintenance_id`, `type`, `category`, `transaction_category`, `is_active`, `replaced_by`, `edited_at`, `edited_by`, `amount`, `method`, `payment_method`, `note`, `recorded_by`, `date`, `created_at`, `updated_at`) VALUES
(35, NULL, NULL, NULL, NULL, 'thu', NULL, NULL, 1, NULL, NULL, NULL, 147241500.00, 'bank', NULL, 'kết chuyển số dư T10', 1, '2025-11-22 11:12:00', '2025-11-22 04:12:34', '2025-11-22 04:12:34'),
(36, NULL, NULL, NULL, NULL, 'du_kien_chi', NULL, NULL, 1, NULL, NULL, NULL, 48000000.00, 'bank', NULL, 'Giữ lại trước khi trích lợi nhuận , sau khi xử lý vi phạm, thiếu trích thêm, dư trả lại theo tỷ lệ chia lợi nhuận', 1, '2025-11-22 11:12:00', '2025-11-22 04:12:53', '2025-11-22 04:12:53'),
(37, NULL, 8, NULL, NULL, 'chi', 'cổ_tức', NULL, 1, NULL, NULL, NULL, 18280284.30, 'bank', 'chuyển khoản', 'Chia cổ tức 100% - Trần Đức Anh (Vốn góp: 18.42%) - chia hết lợi nhuận tháng 10', 1, '2025-11-22 11:46:13', '2025-11-22 04:46:13', '2025-11-22 04:46:13'),
(38, NULL, 9, NULL, NULL, 'chi', 'cổ_tức', NULL, 1, NULL, NULL, NULL, 18280284.30, 'bank', 'chuyển khoản', 'Chia cổ tức 100% - A Thành (Vốn góp: 18.42%) - chia hết lợi nhuận tháng 10', 1, '2025-11-22 11:46:13', '2025-11-22 04:46:13', '2025-11-22 04:46:13'),
(39, NULL, 10, NULL, NULL, 'chi', 'cổ_tức', NULL, 1, NULL, NULL, NULL, 62680931.40, 'bank', 'chuyển khoản', 'Chia cổ tức 100% - A Trung (Vốn góp: 63.16%) - chia hết lợi nhuận tháng 10', 1, '2025-11-22 11:46:13', '2025-11-22 04:46:13', '2025-11-22 04:46:13'),
(49, NULL, NULL, 4, 5, 'chi', 'bảo_trì_xe_chủ_riêng', NULL, 1, NULL, NULL, NULL, 1666000.00, 'cash', NULL, '[Bảo trì] Vé máy bay ĐL - HN - Vietjet air - vé mb cho nhân sự đi ra lấy xe', 1, '2025-11-14 00:00:00', '2025-11-22 10:10:31', '2025-11-22 10:10:31'),
(50, NULL, NULL, 4, 6, 'chi', 'bảo_trì_xe_chủ_riêng', NULL, 1, NULL, NULL, NULL, 518298.00, 'cash', NULL, '[Bảo trì] khách sạn - C - bone Hotel - ks 1 đêm cho lái xe', 1, '2025-11-07 00:00:00', '2025-11-22 10:10:31', '2025-11-22 10:10:31'),
(51, NULL, NULL, 4, 7, 'chi', 'bảo_trì_xe_chủ_riêng', NULL, 1, NULL, NULL, NULL, 500000.00, 'cash', NULL, '[Bảo trì] đổ dầu - Petrolimex - Đổ dầu HN về TQ', 1, '2025-11-13 00:00:00', '2025-11-22 10:10:31', '2025-11-22 10:10:31'),
(52, NULL, NULL, 4, 8, 'chi', 'bảo_trì_xe_chủ_riêng', NULL, 1, NULL, NULL, NULL, 850000.00, 'cash', NULL, '[Bảo trì] Thay nhớt - Gara ngoài - Thay nhớt lần đầu (1600km)', 1, '2025-11-14 00:00:00', '2025-11-22 10:10:31', '2025-11-22 10:10:31'),
(53, NULL, NULL, 4, 9, 'chi', 'bảo_trì_xe_chủ_riêng', NULL, 1, NULL, NULL, NULL, 450000.00, 'cash', NULL, '[Bảo trì] Dán xe - Cửa hàng ngoài - Dán logo cty', 1, '2025-11-14 00:00:00', '2025-11-22 10:10:31', '2025-11-22 10:10:31'),
(54, NULL, NULL, 4, 10, 'chi', 'bảo_trì_xe_chủ_riêng', NULL, 1, NULL, NULL, NULL, 100000.00, 'cash', NULL, '[Bảo trì] Dán epass - Viettel - Dán thẻ Epass', 1, '2025-11-14 00:00:00', '2025-11-22 10:10:31', '2025-11-22 10:10:31'),
(55, NULL, NULL, 4, 11, 'chi', 'bảo_trì_xe_chủ_riêng', NULL, 1, NULL, NULL, NULL, 3800000.00, 'cash', NULL, '[Bảo trì] Lắp đặt thiết bị - Mai Anh GPS - Lắp cam hành trình, định vị', 1, '2025-11-14 00:00:00', '2025-11-22 10:10:31', '2025-11-22 10:10:31'),
(56, NULL, NULL, 4, 12, 'chi', 'bảo_trì_xe_chủ_riêng', NULL, 1, NULL, NULL, NULL, 350000.00, 'cash', NULL, '[Bảo trì] Ép biển số - Thợ ngoài - Ép biển số', 1, '2025-11-14 00:00:00', '2025-11-22 10:10:31', '2025-11-22 10:10:31'),
(57, NULL, NULL, 4, 13, 'chi', 'bảo_trì_xe_chủ_riêng', NULL, 1, NULL, NULL, NULL, 1020000.00, 'cash', NULL, '[Bảo trì] đổ dầu - Petrolimex - Đổ dầu TQ - Túy Loan', 1, '2025-11-14 00:00:00', '2025-11-22 10:10:31', '2025-11-22 10:10:31'),
(58, NULL, NULL, 4, 14, 'chi', 'bảo_trì_xe_chủ_riêng', NULL, 1, NULL, NULL, NULL, 1300000.00, 'cash', NULL, '[Bảo trì] đổ dầu - Petrolimex - Đổ dầu Túy loan - Lâm Đồng', 1, '2025-11-15 00:00:00', '2025-11-22 10:10:31', '2025-11-22 10:10:31'),
(59, NULL, NULL, 4, 15, 'chi', 'bảo_trì_xe_chủ_riêng', NULL, 1, NULL, NULL, NULL, 500000.00, 'cash', NULL, '[Bảo trì] Ăn uống - Quán cơm - Ăn uống đi đường ship xe vào', 1, '2025-11-15 00:00:00', '2025-11-22 10:10:31', '2025-11-22 10:10:31'),
(60, NULL, NULL, 4, 16, 'chi', 'bảo_trì_xe_chủ_riêng', NULL, 1, NULL, NULL, NULL, 2000000.00, 'cash', NULL, '[Bảo trì] Công tài - Lái xe - Công lái xe', 1, '2025-11-15 00:00:00', '2025-11-22 10:10:31', '2025-11-22 10:10:31'),
(61, NULL, NULL, 4, 17, 'chi', 'bảo_trì_xe_chủ_riêng', NULL, 1, NULL, NULL, NULL, 792315.00, 'cash', NULL, '[Bảo trì] Phí qua trạm - BOT - Phí qua trạm BOT', 1, '2025-11-15 00:00:00', '2025-11-22 10:10:31', '2025-11-22 10:10:31'),
(62, NULL, NULL, 4, 18, 'chi', 'bảo_trì_xe_chủ_riêng', NULL, 1, NULL, NULL, NULL, 362252.00, 'cash', NULL, '[Bảo trì] Phí qua trạm - BOT - Phí qua trạm chuyển viện ĐL - SG 17/11', 1, '2025-11-17 00:00:00', '2025-11-22 10:10:31', '2025-11-22 10:10:31'),
(63, NULL, NULL, 4, 19, 'chi', 'bảo_trì_xe_chủ_riêng', NULL, 1, NULL, NULL, NULL, 870000.00, 'cash', NULL, '[Bảo trì] Phí qua trạm - BOT - Nạp vé tháng trạm định an', 1, '2025-11-19 00:00:00', '2025-11-22 10:10:31', '2025-11-22 10:10:31'),
(64, NULL, NULL, 4, 20, 'chi', 'bảo_trì_xe_chủ_riêng', NULL, 1, NULL, NULL, NULL, 1208000.00, 'cash', NULL, '[Bảo trì] Phí qua trạm - BOT - mua vé tháng qua trạm liên đầm', 1, '2025-11-19 00:00:00', '2025-11-22 10:10:31', '2025-11-22 10:10:31'),
(65, 7, 4, 2, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 250000.00, 'cash', NULL, 'Tiền công lái xe: Nguyễn Cữu Ninh', 1, '2025-11-01 17:36:00', '2025-11-22 10:38:48', '2025-11-22 10:38:48'),
(66, 7, 6, 2, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 250000.00, 'cash', NULL, 'Tiền công nhân viên y tế: Nguyễn Quốc Vũ', 1, '2025-11-01 17:36:00', '2025-11-22 10:38:48', '2025-11-22 10:38:48'),
(67, 7, NULL, 2, NULL, 'thu', NULL, NULL, 1, NULL, NULL, NULL, 1500000.00, 'cash', NULL, 'Thu chuyến đi', 1, '2025-11-01 17:36:00', '2025-11-22 10:38:48', '2025-11-22 10:38:48'),
(68, 7, NULL, 2, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 150000.00, 'cash', NULL, 'Hoa hồng: Đức Anh - khoa HSTC  - BV ĐKLĐ', 1, '2025-11-01 17:36:00', '2025-11-22 10:38:48', '2025-11-22 10:38:48'),
(71, NULL, NULL, NULL, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 100000.00, 'cash', NULL, 'Làm chìa khoá vào cổng BV đa khoa', 1, '2025-11-02 17:44:00', '2025-11-22 10:44:41', '2025-11-22 10:44:41'),
(73, 9, 7, 1, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 200000.00, 'cash', NULL, 'Tiền công lái xe: Lê Phong', 1, '2025-11-02 17:43:00', '2025-11-22 10:47:25', '2025-11-22 10:47:25'),
(74, 9, NULL, 1, NULL, 'thu', NULL, NULL, 1, NULL, NULL, NULL, 1500000.00, 'cash', NULL, 'Thu chuyến đi', 1, '2025-11-02 17:43:00', '2025-11-22 10:47:25', '2025-11-22 10:47:25'),
(75, 9, NULL, 1, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 500000.00, 'cash', NULL, 'Đổ dầu', 1, '2025-11-02 17:43:00', '2025-11-22 10:47:25', '2025-11-22 10:47:25'),
(76, 10, 7, 1, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 450000.00, 'cash', NULL, 'Tiền công lái xe: Lê Phong', 1, '2025-11-02 17:47:00', '2025-11-22 10:50:14', '2025-11-22 10:50:14'),
(77, 10, 6, 1, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 450000.00, 'cash', NULL, 'Tiền công nhân viên y tế: Nguyễn Quốc Vũ', 1, '2025-11-02 17:47:00', '2025-11-22 10:50:14', '2025-11-22 10:50:14'),
(78, 10, NULL, 1, NULL, 'thu', NULL, NULL, 1, NULL, NULL, NULL, 3000000.00, 'cash', NULL, 'Thu chuyến đi', 1, '2025-11-02 17:47:00', '2025-11-22 10:50:14', '2025-11-22 10:50:14'),
(79, 10, NULL, 1, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 700000.00, 'cash', NULL, 'Dầu', 1, '2025-11-02 17:47:00', '2025-11-22 10:50:14', '2025-11-22 10:50:14'),
(83, 10, NULL, 1, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 150000.00, 'cash', NULL, 'Hoa hồng: Đức Anh - khoa HSTC  - BV ĐKLĐ', 1, '2025-11-03 17:47:00', '2025-11-22 10:52:47', '2025-11-22 10:52:47'),
(85, 11, NULL, 2, NULL, 'thu', NULL, NULL, 1, NULL, NULL, NULL, 600000.00, 'cash', NULL, 'Thu chuyến đi', 1, '2025-11-03 17:50:00', '2025-11-22 10:58:48', '2025-11-22 10:58:48'),
(86, 12, 4, 2, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 300000.00, 'cash', NULL, 'Tiền công lái xe: Nguyễn Cữu Ninh', 1, '2025-11-03 17:58:00', '2025-11-22 11:00:30', '2025-11-22 11:00:30'),
(87, 12, 16, 2, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 300000.00, 'cash', NULL, 'Tiền công nhân viên y tế: Lâm', 1, '2025-11-03 17:58:00', '2025-11-22 11:00:30', '2025-11-22 11:00:30'),
(88, 12, NULL, 2, NULL, 'thu', NULL, NULL, 1, NULL, NULL, NULL, 1700000.00, 'cash', NULL, 'Thu chuyến đi', 1, '2025-11-03 17:58:00', '2025-11-22 11:00:30', '2025-11-22 11:00:30'),
(89, 12, NULL, 2, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 500000.00, 'cash', NULL, 'Dầu', 1, '2025-11-03 17:58:00', '2025-11-22 11:00:30', '2025-11-22 11:00:30'),
(90, 12, NULL, 2, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 150000.00, 'cash', NULL, 'Hoa hồng: Đức Anh - khoa HSTC  - BV ĐKLĐ', 1, '2025-11-03 17:58:00', '2025-11-22 11:00:30', '2025-11-22 11:00:30'),
(92, 13, NULL, 1, NULL, 'thu', NULL, NULL, 1, NULL, NULL, NULL, 1800000.00, 'cash', NULL, 'Thu chuyến đi', 1, '2025-11-04 18:00:00', '2025-11-22 11:01:42', '2025-11-22 11:01:42'),
(96, 14, NULL, 1, NULL, 'thu', NULL, NULL, 1, NULL, NULL, NULL, 2000000.00, 'cash', NULL, 'Thu chuyến đi', 1, '2025-11-05 18:01:00', '2025-11-22 11:03:46', '2025-11-22 11:03:46'),
(98, 15, 4, 1, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 300000.00, 'cash', NULL, 'Tiền công lái xe: Nguyễn Cữu Ninh', 1, '2025-11-08 18:03:00', '2025-11-22 11:05:55', '2025-11-22 11:05:55'),
(99, 15, 6, 1, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 300000.00, 'cash', NULL, 'Tiền công nhân viên y tế: Nguyễn Quốc Vũ', 1, '2025-11-08 18:03:00', '2025-11-22 11:05:55', '2025-11-22 11:05:55'),
(100, 15, NULL, 1, NULL, 'thu', NULL, NULL, 1, NULL, NULL, NULL, 1800000.00, 'cash', NULL, 'Thu chuyến đi', 1, '2025-11-08 18:03:00', '2025-11-22 11:05:55', '2025-11-22 11:05:55'),
(101, 15, NULL, 1, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 150000.00, 'cash', NULL, 'Hoa hồng: Đức Anh - khoa HSTC  - BV ĐKLĐ', 1, '2025-11-08 18:03:00', '2025-11-22 11:05:55', '2025-11-22 11:05:55'),
(104, 16, NULL, 1, NULL, 'thu', NULL, NULL, 1, NULL, NULL, NULL, 3200000.00, 'cash', NULL, 'Thu chuyến đi', 1, '2025-11-10 18:05:00', '2025-11-22 11:08:46', '2025-11-22 11:08:46'),
(105, 16, NULL, 1, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 120000.00, 'cash', NULL, 'Rửa xe', 1, '2025-11-10 18:05:00', '2025-11-22 11:08:46', '2025-11-22 11:08:46'),
(107, 17, 4, 2, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 500000.00, 'cash', NULL, 'Tiền công lái xe: Nguyễn Cữu Ninh', 1, '2025-11-12 18:08:00', '2025-11-22 11:11:05', '2025-11-22 11:11:05'),
(108, 17, 6, 2, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 500000.00, 'cash', NULL, 'Tiền công nhân viên y tế: Nguyễn Quốc Vũ', 1, '2025-11-12 18:08:00', '2025-11-22 11:11:05', '2025-11-22 11:11:05'),
(109, 17, NULL, 2, NULL, 'thu', NULL, NULL, 1, NULL, NULL, NULL, 3200000.00, 'cash', NULL, 'Thu chuyến đi', 1, '2025-11-12 18:08:00', '2025-11-22 11:11:05', '2025-11-22 11:11:05'),
(110, 17, NULL, 2, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 100000.00, 'cash', NULL, 'Oxy', 1, '2025-11-12 18:08:00', '2025-11-22 11:11:05', '2025-11-22 11:11:05'),
(111, 17, NULL, 2, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 200000.00, 'cash', NULL, 'Hoa hồng: Đức Anh - khoa HSTC  - BV ĐKLĐ', 1, '2025-11-12 18:08:00', '2025-11-22 11:11:05', '2025-11-22 11:11:05'),
(112, 18, 7, 1, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 200000.00, 'cash', NULL, 'Tiền công lái xe: Lê Phong', 1, '2025-11-12 18:11:00', '2025-11-22 11:13:13', '2025-11-22 11:13:13'),
(113, 18, 16, 1, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 200000.00, 'cash', NULL, 'Tiền công nhân viên y tế: Lâm', 1, '2025-11-12 18:11:00', '2025-11-22 11:13:13', '2025-11-22 11:13:13'),
(114, 18, NULL, 1, NULL, 'thu', NULL, NULL, 1, NULL, NULL, NULL, 1200000.00, 'cash', NULL, 'Thu chuyến đi', 1, '2025-11-12 18:11:00', '2025-11-22 11:13:13', '2025-11-22 11:13:13'),
(117, 19, NULL, 1, NULL, 'thu', NULL, NULL, 1, NULL, NULL, NULL, 3500000.00, 'cash', NULL, 'Thu chuyến đi', 1, '2025-11-12 18:13:00', '2025-11-22 11:15:13', '2025-11-22 11:15:13'),
(119, 20, 4, 2, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 300000.00, 'cash', NULL, 'Tiền công lái xe: Nguyễn Cữu Ninh', 1, '2025-11-13 18:15:00', '2025-11-22 11:16:55', '2025-11-22 11:16:55'),
(120, 20, 17, 2, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 300000.00, 'cash', NULL, 'Tiền công nhân viên y tế: Phúc', 1, '2025-11-13 18:15:00', '2025-11-22 11:16:55', '2025-11-22 11:16:55'),
(121, 20, NULL, 2, NULL, 'thu', NULL, NULL, 1, NULL, NULL, NULL, 2000000.00, 'cash', NULL, 'Thu chuyến đi', 1, '2025-11-13 18:15:00', '2025-11-22 11:16:55', '2025-11-22 11:16:55'),
(122, 20, NULL, 2, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 500000.00, 'cash', NULL, 'Dầu', 1, '2025-11-13 18:15:00', '2025-11-22 11:16:55', '2025-11-22 11:16:55'),
(123, 20, NULL, 2, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 150000.00, 'cash', NULL, 'Hoa hồng: Đức Anh - khoa HSTC  - BV ĐKLĐ', 1, '2025-11-13 18:15:00', '2025-11-22 11:16:55', '2025-11-22 11:16:55'),
(124, 21, 7, 1, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 150000.00, 'cash', NULL, 'Tiền công lái xe: Lê Phong', 1, '2025-11-13 18:16:00', '2025-11-22 11:18:15', '2025-11-22 11:18:15'),
(125, 21, 6, 1, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 150000.00, 'cash', NULL, 'Tiền công nhân viên y tế: Nguyễn Quốc Vũ', 1, '2025-11-13 18:16:00', '2025-11-22 11:18:15', '2025-11-22 11:18:15'),
(126, 22, 4, 2, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 150000.00, 'cash', NULL, 'Tiền công lái xe: Nguyễn Cữu Ninh', 1, '2025-11-14 18:18:00', '2025-11-22 11:21:17', '2025-11-22 11:21:17'),
(127, 22, 6, 2, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 150000.00, 'cash', NULL, 'Tiền công nhân viên y tế: Nguyễn Quốc Vũ', 1, '2025-11-14 18:18:00', '2025-11-22 11:21:17', '2025-11-22 11:21:17'),
(128, 22, NULL, 2, NULL, 'thu', NULL, NULL, 1, NULL, NULL, NULL, 1000000.00, 'cash', NULL, 'Thu chuyến đi', 1, '2025-11-14 18:18:00', '2025-11-22 11:21:17', '2025-11-22 11:21:17'),
(130, 23, NULL, 1, NULL, 'thu', NULL, NULL, 1, NULL, NULL, NULL, 500000.00, 'cash', NULL, 'Thu chuyến đi', 1, '2025-11-14 18:21:00', '2025-11-22 11:22:56', '2025-11-22 11:22:56'),
(131, 23, NULL, 1, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 150000.00, 'cash', NULL, 'Oxy', 1, '2025-11-14 18:21:00', '2025-11-22 11:22:56', '2025-11-22 11:22:56'),
(132, 24, 4, 2, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 300000.00, 'cash', NULL, 'Tiền công lái xe: Nguyễn Cữu Ninh', 1, '2025-11-15 18:22:00', '2025-11-22 11:24:34', '2025-11-22 11:24:34'),
(133, 24, 6, 2, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 300000.00, 'cash', NULL, 'Tiền công nhân viên y tế: Nguyễn Quốc Vũ', 1, '2025-11-15 18:22:00', '2025-11-22 11:24:34', '2025-11-22 11:24:34'),
(134, 24, NULL, 2, NULL, 'thu', NULL, NULL, 1, NULL, NULL, NULL, 2000000.00, 'cash', NULL, 'Thu chuyến đi', 1, '2025-11-15 18:22:00', '2025-11-22 11:24:34', '2025-11-22 11:24:34'),
(135, 24, NULL, 2, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 150000.00, 'cash', NULL, 'Hoa hồng: Đức Anh - khoa HSTC  - BV ĐKLĐ', 1, '2025-11-15 18:22:00', '2025-11-22 11:24:34', '2025-11-22 11:24:34'),
(136, NULL, NULL, 4, 21, 'chi', 'bảo_trì_xe_chủ_riêng', NULL, 1, NULL, NULL, NULL, 13563000.00, 'cash', NULL, '[Bảo trì] Bảo hiểm vật chất - Cty bảo hiểm - BH thân vỏ xe để giải ngân', 1, '2025-11-07 00:00:00', '2025-11-22 11:26:43', '2025-11-22 11:26:43'),
(137, NULL, NULL, 3, 22, 'chi', 'bảo_trì_xe_chủ_riêng', NULL, 1, NULL, NULL, NULL, 13563000.00, 'cash', NULL, '[Bảo trì] Bảo hiểm vật chất - Cty bảo hiểm', 1, '2025-11-07 00:00:00', '2025-11-22 11:27:11', '2025-11-22 11:30:33'),
(138, NULL, NULL, 3, 23, 'chi', 'bảo_trì_xe_chủ_riêng', NULL, 1, NULL, NULL, NULL, 1311560.00, 'cash', NULL, '[Bảo trì] Bảo hiểm Dân sự - Cty bảo hiểm', 1, '2025-11-17 00:00:00', '2025-11-22 11:27:41', '2025-11-22 11:30:48'),
(139, NULL, NULL, 4, 24, 'chi', 'bảo_trì_xe_chủ_riêng', NULL, 1, NULL, NULL, NULL, 1311560.00, 'cash', NULL, '[Bảo trì] Bảo hiểm Dân sự - Cty bảo hiểm', 1, '2025-11-17 00:00:00', '2025-11-22 11:28:01', '2025-11-22 11:30:57'),
(141, 25, NULL, 1, NULL, 'thu', NULL, NULL, 1, NULL, NULL, NULL, 500000.00, 'cash', NULL, 'Thu chuyến đi', 1, '2025-11-16 18:24:00', '2025-11-22 11:32:32', '2025-11-22 11:32:32'),
(142, 26, 5, 1, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 500000.00, 'cash', NULL, 'Tiền công lái xe: Cil Đoan', 1, '2025-11-17 18:32:00', '2025-11-22 11:35:18', '2025-11-22 11:35:18'),
(143, 26, 6, 1, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 500000.00, 'cash', NULL, 'Tiền công nhân viên y tế: Nguyễn Quốc Vũ', 1, '2025-11-17 18:32:00', '2025-11-22 11:35:18', '2025-11-22 11:35:18'),
(144, 26, NULL, 1, NULL, 'thu', NULL, NULL, 1, NULL, NULL, NULL, 3500000.00, 'cash', NULL, 'Thu chuyến đi', 1, '2025-11-17 18:32:00', '2025-11-22 11:35:18', '2025-11-22 11:35:18'),
(145, 26, NULL, 1, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 700000.00, 'cash', NULL, 'Dầu', 1, '2025-11-17 18:32:00', '2025-11-22 11:35:18', '2025-11-22 11:35:18'),
(146, 26, NULL, 1, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 200000.00, 'cash', NULL, 'Hoa hồng: Đức Anh - khoa HSTC  - BV ĐKLĐ', 1, '2025-11-17 18:32:00', '2025-11-22 11:35:18', '2025-11-22 11:35:18'),
(149, 27, NULL, 3, NULL, 'thu', NULL, NULL, 1, NULL, NULL, NULL, 4500000.00, 'cash', NULL, 'Thu chuyến đi', 1, '2025-11-17 18:35:00', '2025-11-22 11:37:34', '2025-11-22 11:37:34'),
(150, 27, NULL, 3, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 1970000.00, 'cash', NULL, 'Dầu', 1, '2025-11-17 18:35:00', '2025-11-22 11:37:34', '2025-11-22 11:37:34'),
(154, 28, NULL, 4, NULL, 'thu', NULL, NULL, 1, NULL, NULL, NULL, 4500000.00, 'cash', NULL, 'Thu chuyến đi', 1, '2025-11-17 18:37:00', '2025-11-22 11:41:05', '2025-11-22 11:41:05'),
(155, 28, NULL, 4, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 1610000.00, 'cash', NULL, 'Dầu', 1, '2025-11-17 18:37:00', '2025-11-22 11:41:05', '2025-11-22 11:41:05'),
(156, 28, NULL, 4, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 500000.00, 'cash', NULL, 'Chi phí: môi giới ngoài', 1, '2025-11-17 18:37:00', '2025-11-22 11:41:05', '2025-11-22 11:41:05'),
(159, 29, NULL, 1, NULL, 'thu', NULL, NULL, 1, NULL, NULL, NULL, 2500000.00, 'cash', NULL, 'Thu chuyến đi', 1, '2025-11-17 18:41:00', '2025-11-22 11:45:07', '2025-11-22 11:45:07'),
(161, NULL, 5, NULL, NULL, 'chi', 'điều_chỉnh_lương', NULL, 1, NULL, NULL, NULL, 450000.00, 'cash', 'chuyển khoản', 'Điều chỉnh: Phụ cấp trực - Cil Đoan (từ quỹ công ty)', 1, '2025-11-22 18:47:15', '2025-11-22 11:47:15', '2025-11-22 11:47:15'),
(164, 30, NULL, 1, NULL, 'thu', NULL, NULL, 1, NULL, NULL, NULL, 3000000.00, 'cash', NULL, 'Thu chuyến đi', 1, '2025-11-17 18:49:00', '2025-11-22 11:50:50', '2025-11-22 11:50:50'),
(168, 31, NULL, 3, NULL, 'thu', NULL, NULL, 1, NULL, NULL, NULL, 4500000.00, 'cash', NULL, 'Thu chuyến đi', 1, '2025-11-18 18:50:00', '2025-11-22 11:57:35', '2025-11-22 11:57:35'),
(169, 31, NULL, 3, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 1390000.00, 'cash', NULL, 'Dầu', 1, '2025-11-18 18:50:00', '2025-11-22 11:57:35', '2025-11-22 11:57:35'),
(170, 31, NULL, 3, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 70000.00, 'cash', NULL, 'Chi phí: Oxy', 1, '2025-11-18 18:50:00', '2025-11-22 11:57:35', '2025-11-22 11:57:35'),
(172, 30, NULL, 1, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 500000.00, 'cash', NULL, 'Dầu', 1, '2025-11-22 18:58:00', '2025-11-22 11:58:40', '2025-11-22 11:58:40'),
(173, 11, 4, 2, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 100000.00, 'cash', NULL, 'Tiền công lái xe: Nguyễn Cữu Ninh', 1, '2025-11-03 17:50:00', '2025-11-23 10:35:26', '2025-11-23 10:35:26'),
(174, 11, 16, 2, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 100000.00, 'cash', NULL, 'Tiền công nhân viên y tế: Lâm', 1, '2025-11-03 17:50:00', '2025-11-23 10:35:26', '2025-11-23 10:35:26'),
(179, 14, 4, 1, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 300000.00, 'cash', NULL, 'Tiền công lái xe: Nguyễn Cữu Ninh', 1, '2025-11-05 18:01:00', '2025-11-23 10:56:29', '2025-11-23 10:56:29'),
(180, 14, 6, 1, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 300000.00, 'cash', NULL, 'Tiền công nhân viên y tế: Nguyễn Quốc Vũ', 1, '2025-11-05 18:01:00', '2025-11-23 10:56:29', '2025-11-23 10:56:29'),
(181, 14, NULL, 1, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 150000.00, 'cash', NULL, 'Hoa hồng: Đức Anh - khoa HSTC  - BV ĐKLĐ', 1, '2025-11-05 18:01:00', '2025-11-23 10:56:29', '2025-11-23 10:56:29'),
(182, 16, 7, 1, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 475000.00, 'cash', NULL, 'Tiền công lái xe: Lê Phong', 1, '2025-11-10 18:05:00', '2025-11-23 10:59:20', '2025-11-23 10:59:20'),
(183, 16, 6, 1, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 475000.00, 'cash', NULL, 'Tiền công nhân viên y tế: Nguyễn Quốc Vũ', 1, '2025-11-10 18:05:00', '2025-11-23 10:59:20', '2025-11-23 10:59:20'),
(184, 16, NULL, 1, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 150000.00, 'cash', NULL, 'Hoa hồng: Đức Anh - khoa HSTC  - BV ĐKLĐ', 1, '2025-11-10 18:05:00', '2025-11-23 10:59:20', '2025-11-23 10:59:20'),
(185, 17, NULL, 2, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 500000.00, 'cash', NULL, 'Dầu', 1, '2025-11-23 18:00:00', '2025-11-23 11:01:21', '2025-11-23 11:01:21'),
(186, 19, 7, 1, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 525000.00, 'cash', NULL, 'Tiền công lái xe: Lê Phong', 1, '2025-11-12 18:13:00', '2025-11-23 11:03:34', '2025-11-23 11:03:34'),
(187, 19, 16, 1, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 525000.00, 'cash', NULL, 'Tiền công nhân viên y tế: Lâm', 1, '2025-11-12 18:13:00', '2025-11-23 11:03:34', '2025-11-23 11:03:34'),
(188, 19, NULL, 1, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 200000.00, 'cash', NULL, 'Hoa hồng: Đức Anh - khoa HSTC  - BV ĐKLĐ', 1, '2025-11-12 18:13:00', '2025-11-23 11:03:34', '2025-11-23 11:03:34'),
(189, 21, NULL, 1, NULL, 'thu', NULL, NULL, 1, NULL, NULL, NULL, 1000000.00, 'cash', NULL, 'Thu chuyến đi', 1, '2025-11-23 18:05:00', '2025-11-23 11:05:50', '2025-11-23 11:05:50'),
(190, 23, 5, 1, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 75000.00, 'cash', NULL, 'Tiền công lái xe: Cil Đoan', 1, '2025-11-14 18:21:00', '2025-11-23 11:07:53', '2025-11-23 11:07:53'),
(191, 23, 6, 1, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 75000.00, 'cash', NULL, 'Tiền công nhân viên y tế: Nguyễn Quốc Vũ', 1, '2025-11-14 18:21:00', '2025-11-23 11:07:53', '2025-11-23 11:07:53'),
(192, 25, 5, 1, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 75000.00, 'cash', NULL, 'Tiền công lái xe: Cil Đoan', 1, '2025-11-16 18:24:00', '2025-11-23 11:09:20', '2025-11-23 11:09:20'),
(193, 25, 6, 1, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 75000.00, 'cash', NULL, 'Tiền công nhân viên y tế: Nguyễn Quốc Vũ', 1, '2025-11-16 18:24:00', '2025-11-23 11:09:21', '2025-11-23 11:09:21'),
(199, 29, 5, 1, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 350000.00, 'cash', NULL, 'Tiền công lái xe: Cil Đoan', 1, '2025-11-17 18:41:00', '2025-11-23 11:13:12', '2025-11-23 11:13:12'),
(200, 29, 16, 1, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 350000.00, 'cash', NULL, 'Tiền công nhân viên y tế: Lâm', 1, '2025-11-17 18:41:00', '2025-11-23 11:13:12', '2025-11-23 11:13:12'),
(201, 29, NULL, 1, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 150000.00, 'cash', NULL, 'Hoa hồng: Đức Anh - khoa HSTC  - BV ĐKLĐ', 1, '2025-11-17 18:41:00', '2025-11-23 11:13:12', '2025-11-23 11:13:12'),
(202, 30, 5, 1, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 450000.00, 'cash', NULL, 'Tiền công lái xe: Cil Đoan', 1, '2025-11-17 18:49:00', '2025-11-23 11:14:37', '2025-11-23 11:14:37'),
(203, 30, 13, 1, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 450000.00, 'cash', NULL, 'Tiền công nhân viên y tế: Đức Anh', 1, '2025-11-17 18:49:00', '2025-11-23 11:14:37', '2025-11-23 11:14:37'),
(204, 30, NULL, 1, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 150000.00, 'cash', NULL, 'Hoa hồng: Đức Anh - khoa HSTC  - BV ĐKLĐ', 1, '2025-11-17 18:49:00', '2025-11-23 11:14:37', '2025-11-23 11:14:37'),
(210, 32, NULL, 3, NULL, 'thu', NULL, NULL, 1, NULL, NULL, NULL, 1500000.00, 'cash', NULL, 'Thu chuyến đi', 1, '2025-11-23 18:16:00', '2025-11-23 11:18:13', '2025-11-23 11:18:13'),
(211, 32, 7, 3, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 200000.00, 'cash', NULL, 'Tiền công lái xe: Lê Phong', 1, '2025-11-18 18:16:00', '2025-11-23 11:19:58', '2025-11-23 11:19:58'),
(212, 32, 16, 3, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 200000.00, 'cash', NULL, 'Tiền công nhân viên y tế: Lâm', 1, '2025-11-18 18:16:00', '2025-11-23 11:19:58', '2025-11-23 11:19:58'),
(213, 33, 4, 2, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 330000.00, 'cash', NULL, 'Công lái xe: Nguyễn Cữu Ninh', 1, '2025-11-18 18:20:00', '2025-11-23 11:21:29', '2025-11-23 11:21:29'),
(214, 33, 6, 2, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 330000.00, 'cash', NULL, 'Công NVYT: Nguyễn Quốc Vũ', 1, '2025-11-18 18:20:00', '2025-11-23 11:21:29', '2025-11-23 11:21:29'),
(215, 33, NULL, 2, NULL, 'thu', NULL, NULL, 1, NULL, NULL, NULL, 2200000.00, 'cash', NULL, 'Thu chuyến đi', 1, '2025-11-18 18:20:00', '2025-11-23 11:21:29', '2025-11-23 11:21:29'),
(216, 33, NULL, 2, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 150000.00, 'cash', NULL, 'Hoa hồng: Đức Anh - khoa HSTC  - BV ĐKLĐ', 1, '2025-11-18 18:20:00', '2025-11-23 11:21:29', '2025-11-23 11:21:29'),
(217, 34, 6, 3, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 250000.00, 'cash', NULL, 'Công NVYT: Nguyễn Quốc Vũ', 1, '2025-11-18 18:21:00', '2025-11-23 11:23:11', '2025-11-23 11:23:11'),
(218, 34, NULL, 3, NULL, 'thu', NULL, NULL, 1, NULL, NULL, NULL, 2500000.00, 'cash', NULL, 'Thu chuyến đi', 1, '2025-11-18 18:21:00', '2025-11-23 11:23:11', '2025-11-23 11:23:11'),
(219, 35, 4, 2, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 75000.00, 'cash', NULL, 'Công lái xe: Nguyễn Cữu Ninh', 1, '2025-11-18 18:23:00', '2025-11-23 11:24:49', '2025-11-23 11:24:49'),
(220, 35, 16, 2, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 75000.00, 'cash', NULL, 'Công NVYT: Lâm', 1, '2025-11-18 18:23:00', '2025-11-23 11:24:49', '2025-11-23 11:24:49'),
(221, 35, NULL, 2, NULL, 'thu', NULL, NULL, 1, NULL, NULL, NULL, 500000.00, 'cash', NULL, 'Thu chuyến đi', 1, '2025-11-18 18:23:00', '2025-11-23 11:24:49', '2025-11-23 11:24:49'),
(224, 36, NULL, 3, NULL, 'thu', NULL, NULL, 1, NULL, NULL, NULL, 4500000.00, 'cash', NULL, 'Thu chuyến đi', 1, '2025-11-19 18:25:00', '2025-11-23 11:27:01', '2025-11-23 11:27:01'),
(225, 36, NULL, 3, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 1180000.00, 'cash', NULL, 'Dầu', 1, '2025-11-19 18:25:00', '2025-11-23 11:27:01', '2025-11-23 11:27:01'),
(226, 36, NULL, 3, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 70000.00, 'cash', NULL, 'Chi phí: Oxy', 1, '2025-11-19 18:25:00', '2025-11-23 11:27:01', '2025-11-23 11:27:01'),
(228, 37, 7, 2, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 300000.00, 'cash', NULL, 'Công lái xe: Lê Phong', 1, '2025-11-19 18:27:00', '2025-11-23 11:28:57', '2025-11-23 11:28:57'),
(229, 37, 6, 2, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 300000.00, 'cash', NULL, 'Công NVYT: Nguyễn Quốc Vũ', 1, '2025-11-19 18:27:00', '2025-11-23 11:28:57', '2025-11-23 11:28:57'),
(230, 37, NULL, 2, NULL, 'thu', NULL, NULL, 1, NULL, NULL, NULL, 1800000.00, 'cash', NULL, 'Thu chuyến đi', 1, '2025-11-19 18:27:00', '2025-11-23 11:28:57', '2025-11-23 11:28:57'),
(231, 37, NULL, 2, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 500000.00, 'cash', NULL, 'Dầu', 1, '2025-11-19 18:27:00', '2025-11-23 11:28:57', '2025-11-23 11:28:57'),
(232, 37, NULL, 2, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 150000.00, 'cash', NULL, 'Hoa hồng: Đức Anh - khoa HSTC  - BV ĐKLĐ', 1, '2025-11-19 18:27:00', '2025-11-23 11:28:57', '2025-11-23 11:28:57'),
(233, 38, 7, 2, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 300000.00, 'cash', NULL, 'Công lái xe: Lê Phong', 1, '2025-11-19 18:29:00', '2025-11-23 11:30:58', '2025-11-23 11:30:58'),
(234, 38, 13, 2, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 300000.00, 'cash', NULL, 'Công NVYT: Đức Anh', 1, '2025-11-19 18:29:00', '2025-11-23 11:30:58', '2025-11-23 11:30:58'),
(235, 38, NULL, 2, NULL, 'thu', NULL, NULL, 1, NULL, NULL, NULL, 1800000.00, 'cash', NULL, 'Thu chuyến đi', 1, '2025-11-19 18:29:00', '2025-11-23 11:30:58', '2025-11-23 11:30:58'),
(236, 38, NULL, 2, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 150000.00, 'cash', NULL, 'Hoa hồng: Đức Anh - khoa HSTC  - BV ĐKLĐ', 1, '2025-11-19 18:29:00', '2025-11-23 11:30:58', '2025-11-23 11:30:58'),
(239, 39, NULL, 3, NULL, 'thu', NULL, NULL, 1, NULL, NULL, NULL, 4500000.00, 'cash', NULL, 'Thu chuyến đi', 1, '2025-11-20 18:31:00', '2025-11-23 11:32:50', '2025-11-23 11:32:50'),
(240, 39, NULL, 3, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 1270000.00, 'cash', NULL, 'Dầu', 1, '2025-11-20 18:31:00', '2025-11-23 11:32:50', '2025-11-23 11:32:50'),
(242, 40, 4, 2, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 450000.00, 'cash', NULL, 'Công lái xe: Nguyễn Cữu Ninh', 1, '2025-11-20 18:32:00', '2025-11-23 11:34:32', '2025-11-23 11:34:32'),
(243, 40, 17, 2, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 450000.00, 'cash', NULL, 'Công NVYT: Phúc', 1, '2025-11-20 18:32:00', '2025-11-23 11:34:32', '2025-11-23 11:34:32'),
(244, 40, NULL, 2, NULL, 'thu', NULL, NULL, 1, NULL, NULL, NULL, 3000000.00, 'cash', NULL, 'Thu chuyến đi', 1, '2025-11-20 18:32:00', '2025-11-23 11:34:32', '2025-11-23 11:34:32'),
(245, 40, NULL, 2, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 500000.00, 'cash', NULL, 'Dầu', 1, '2025-11-20 18:32:00', '2025-11-23 11:34:32', '2025-11-23 11:34:32'),
(246, 40, NULL, 2, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 150000.00, 'cash', NULL, 'Hoa hồng: Đức Anh - khoa HSTC  - BV ĐKLĐ', 1, '2025-11-20 18:32:00', '2025-11-23 11:34:32', '2025-11-23 11:34:32'),
(247, 41, 5, 1, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 230000.00, 'cash', NULL, 'Công lái xe: Cil Đoan', 1, '2025-11-20 18:34:00', '2025-11-23 11:35:57', '2025-11-23 11:35:57'),
(248, 41, NULL, 1, NULL, 'thu', NULL, NULL, 1, NULL, NULL, NULL, 1800000.00, 'cash', NULL, 'Thu chuyến đi', 1, '2025-11-20 18:34:00', '2025-11-23 11:35:57', '2025-11-23 11:35:57'),
(251, 42, NULL, 4, NULL, 'thu', NULL, NULL, 1, NULL, NULL, NULL, 4500000.00, 'cash', NULL, 'Thu chuyến đi', 1, '2025-11-21 18:36:00', '2025-11-23 11:39:28', '2025-11-23 11:39:28'),
(252, 42, NULL, 4, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 1430000.00, 'cash', NULL, 'Dầu', 1, '2025-11-21 18:36:00', '2025-11-23 11:39:28', '2025-11-23 11:39:28'),
(253, 42, NULL, 4, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 60000.00, 'cash', NULL, 'Chi phí: Dán lại Epass', 1, '2025-11-21 18:36:00', '2025-11-23 11:39:28', '2025-11-23 11:39:28'),
(255, NULL, NULL, 4, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 1350000.00, 'bank', NULL, 'Lấy giấy tờ đi đường, phí công chứng  CP gửi NH', 1, '2025-11-10 18:40:00', '2025-11-23 11:41:16', '2025-11-23 11:41:16'),
(256, NULL, NULL, 3, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 1350000.00, 'bank', NULL, 'Lấy giấy tờ đi đường, phí công chứng  CP gửi NH', 1, '2025-11-10 18:41:00', '2025-11-23 11:41:46', '2025-11-23 11:41:46'),
(257, NULL, NULL, 4, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 1125000.00, 'bank', NULL, 'Vật tư xe mới (Đức Anh mua)  Ck Cho DCYK Hai Dang', 1, '2025-11-15 18:41:00', '2025-11-23 11:42:32', '2025-11-23 11:43:08'),
(259, NULL, NULL, 3, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 1125000.00, 'cash', NULL, 'Vật tư xe mới (Đức Anh mua)  Ck Cho DCYK Hai Dang', 1, '2025-11-15 19:18:00', '2025-11-23 12:18:41', '2025-11-23 12:18:41'),
(260, NULL, NULL, NULL, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 400000.00, 'bank', NULL, 'Mua túi đựng oxy, dây, kim tiêm xe 506.14', 1, '2025-11-21 19:19:00', '2025-11-23 12:19:47', '2025-11-23 12:19:47'),
(261, NULL, NULL, 3, 25, 'chi', 'bảo_trì_xe_chủ_riêng', NULL, 1, NULL, NULL, NULL, 120000.00, 'cash', NULL, '[Bảo trì] Rửa xe - Gara ngoài - CT rữa xe 313.84', 1, '2025-11-20 00:00:00', '2025-11-23 12:20:45', '2025-11-23 12:20:45'),
(262, 43, 7, 3, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 500000.00, 'cash', NULL, 'Công lái xe: Lê Phong', 1, '2025-11-21 19:21:00', '2025-11-23 12:22:48', '2025-11-23 12:22:48'),
(263, 43, 16, 3, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 550000.00, 'cash', NULL, 'Công NVYT: Lâm', 1, '2025-11-21 19:21:00', '2025-11-23 12:22:48', '2025-11-23 12:22:48'),
(264, 43, NULL, 3, NULL, 'thu', NULL, NULL, 1, NULL, NULL, NULL, 4500000.00, 'cash', NULL, 'Thu chuyến đi', 1, '2025-11-21 19:21:00', '2025-11-23 12:22:48', '2025-11-23 12:22:48'),
(265, 43, NULL, 3, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 1190000.00, 'cash', NULL, 'Dầu', 1, '2025-11-21 19:21:00', '2025-11-23 12:22:48', '2025-11-23 12:22:48'),
(266, 43, NULL, 3, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 500000.00, 'cash', NULL, 'Hoa hồng: Khoa Phổi - BV ĐKLĐ', 1, '2025-11-21 19:21:00', '2025-11-23 12:22:48', '2025-11-23 12:22:48'),
(267, NULL, NULL, 4, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 1000000.00, 'bank', NULL, 'Đặt cọc 1 bình oxy', 1, '2025-11-23 19:29:00', '2025-11-23 12:30:05', '2025-11-23 12:30:05'),
(268, NULL, NULL, 4, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 447000.00, 'cash', NULL, 'Máy hút đờm  Ck Đ.Anh mua', 1, '2025-11-21 19:30:00', '2025-11-23 12:30:31', '2025-11-23 12:39:11'),
(269, NULL, NULL, 3, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 447000.00, 'cash', NULL, 'Máy hút đờm  Ck Đ.Anh mua', 1, '2025-11-21 19:30:00', '2025-11-23 12:30:55', '2025-11-23 12:38:59'),
(270, 13, 7, 1, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 300000.00, 'cash', NULL, 'Tiền công lái xe: Lê Phong', 1, '2025-11-04 18:00:00', '2025-11-24 06:04:59', '2025-11-24 06:04:59'),
(271, 13, 6, 1, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 300000.00, 'cash', NULL, 'Tiền công nhân viên y tế: Nguyễn Quốc Vũ', 1, '2025-11-04 18:00:00', '2025-11-24 06:04:59', '2025-11-24 06:04:59'),
(272, 13, NULL, 1, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 150000.00, 'cash', NULL, 'Hoa hồng: Đức Anh - khoa HSTC  - BV ĐKLĐ', 1, '2025-11-04 18:00:00', '2025-11-24 06:04:59', '2025-11-24 06:04:59'),
(275, 44, NULL, 3, NULL, 'thu', NULL, NULL, 1, NULL, NULL, NULL, 4700000.00, 'bank', NULL, 'Thu chuyến đi', 1, '2025-11-23 13:06:00', '2025-11-24 06:09:23', '2025-11-24 06:09:23'),
(276, 44, NULL, 3, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 1210000.00, 'bank', NULL, 'Dầu', 1, '2025-11-23 13:06:00', '2025-11-24 06:09:23', '2025-11-24 06:09:23'),
(277, 44, NULL, 3, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 60000.00, 'bank', NULL, 'Chi phí: Dán lại Epass', 1, '2025-11-23 13:06:00', '2025-11-24 06:09:23', '2025-11-24 06:09:23'),
(278, 44, NULL, 3, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 50000.00, 'bank', NULL, 'Chi phí: Vá vỏ', 1, '2025-11-23 13:06:00', '2025-11-24 06:09:23', '2025-11-24 06:09:23'),
(279, 44, NULL, 3, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 140000.00, 'bank', NULL, 'Chi phí: Oxy - 2 bình', 1, '2025-11-23 13:06:00', '2025-11-24 06:09:23', '2025-11-24 06:09:23'),
(283, 45, NULL, 4, NULL, 'thu', NULL, NULL, 1, NULL, NULL, NULL, 4500000.00, 'bank', NULL, 'Thu chuyến đi', 1, '2025-11-24 13:09:00', '2025-11-24 06:11:33', '2025-11-24 06:11:33'),
(284, 45, NULL, 4, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 1260000.00, 'bank', NULL, 'Dầu', 1, '2025-11-24 13:09:00', '2025-11-24 06:11:33', '2025-11-24 06:11:33'),
(286, 46, 4, 4, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 50000.00, 'bank', NULL, 'Công lái xe: Nguyễn Cữu Ninh', 1, '2025-11-23 13:12:00', '2025-11-24 06:13:35', '2025-11-24 06:13:35'),
(287, 46, 6, 4, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 100000.00, 'bank', NULL, 'Công NVYT: Nguyễn Quốc Vũ', 1, '2025-11-23 13:12:00', '2025-11-24 06:13:35', '2025-11-24 06:13:35'),
(288, 46, NULL, 4, NULL, 'thu', NULL, NULL, 1, NULL, NULL, NULL, 500000.00, 'bank', NULL, 'Thu chuyến đi', 1, '2025-11-23 13:12:00', '2025-11-24 06:13:35', '2025-11-24 06:13:35'),
(289, 45, 4, 4, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 550000.00, 'cash', NULL, 'Tiền công lái xe: Nguyễn Cữu Ninh', 1, '2025-11-23 13:09:00', '2025-11-24 06:14:13', '2025-11-24 06:14:13'),
(290, 45, 6, 4, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 600000.00, 'cash', NULL, 'Tiền công nhân viên y tế: Nguyễn Quốc Vũ', 1, '2025-11-23 13:09:00', '2025-11-24 06:14:13', '2025-11-24 06:14:13'),
(291, 45, NULL, 4, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 500000.00, 'cash', NULL, 'Hoa hồng: Khoa HSTC - BV ĐKLĐ', 1, '2025-11-23 13:09:00', '2025-11-24 06:14:13', '2025-11-24 06:14:13'),
(292, 44, 7, 3, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 550000.00, 'cash', NULL, 'Tiền công lái xe: Lê Phong', 1, '2025-11-23 13:06:00', '2025-11-24 06:14:43', '2025-11-24 06:14:43'),
(293, 44, 13, 3, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 600000.00, 'cash', NULL, 'Tiền công nhân viên y tế: Đức Anh', 1, '2025-11-23 13:06:00', '2025-11-24 06:14:43', '2025-11-24 06:14:43'),
(294, 44, NULL, 3, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 500000.00, 'cash', NULL, 'Hoa hồng: Khoa HSTC - BV ĐKLĐ', 1, '2025-11-23 13:06:00', '2025-11-24 06:14:43', '2025-11-24 06:14:43'),
(295, NULL, NULL, 4, 26, 'chi', 'bảo_trì_xe_chủ_riêng', NULL, 1, NULL, NULL, NULL, 400000.00, 'cash', NULL, '[Bảo trì] Thay 2 van bình oxy - DCYK Hai Dang - Thay van bình oxy', 1, '2025-11-23 00:00:00', '2025-11-24 06:20:34', '2025-11-24 06:20:34'),
(296, NULL, NULL, 4, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 447000.00, 'bank', NULL, 'Máy đo huyết áp,  Ck Đ.Anh mua', 1, '2025-11-21 13:20:00', '2025-11-24 06:21:18', '2025-11-24 06:21:18'),
(297, NULL, NULL, 3, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 447000.00, 'cash', NULL, 'Máy đo huyết áp,  Ck Đ.Anh mua', 1, '2025-11-21 13:21:00', '2025-11-24 06:21:37', '2025-11-24 06:21:37'),
(298, 47, 7, 3, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 300000.00, 'bank', NULL, 'Công lái xe: Lê Phong', 1, '2025-11-24 13:21:00', '2025-11-24 06:23:29', '2025-11-24 06:23:29'),
(299, 47, 6, 3, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 300000.00, 'bank', NULL, 'Công NVYT: Nguyễn Quốc Vũ', 1, '2025-11-24 13:21:00', '2025-11-24 06:23:29', '2025-11-24 06:23:29'),
(300, 47, NULL, 3, NULL, 'thu', NULL, NULL, 1, NULL, NULL, NULL, 2000000.00, 'bank', NULL, 'Thu chuyến đi', 1, '2025-11-24 13:21:00', '2025-11-24 06:23:29', '2025-11-24 06:23:29'),
(301, 47, NULL, 3, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 150000.00, 'bank', NULL, 'Hoa hồng: Khoa HSTC - BV ĐKLĐ', 1, '2025-11-24 13:21:00', '2025-11-24 06:23:29', '2025-11-24 06:23:29'),
(302, 48, 5, 1, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 300000.00, 'bank', NULL, 'Công lái xe: Cil Đoan', 1, '2025-11-24 13:23:00', '2025-11-24 06:24:55', '2025-11-24 06:24:55'),
(303, 48, 18, 1, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 300000.00, 'bank', NULL, 'Công NVYT: Lân', 1, '2025-11-24 13:23:00', '2025-11-24 06:24:55', '2025-11-24 06:24:55'),
(304, 48, NULL, 1, NULL, 'thu', NULL, NULL, 1, NULL, NULL, NULL, 2000000.00, 'bank', NULL, 'Thu chuyến đi', 1, '2025-11-24 13:23:00', '2025-11-24 06:24:55', '2025-11-24 06:24:55'),
(305, 48, NULL, 1, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 150000.00, 'bank', NULL, 'Hoa hồng: Khoa HSTC - BV ĐKLĐ', 1, '2025-11-24 13:23:00', '2025-11-24 06:24:55', '2025-11-24 06:24:55'),
(306, 27, 7, 3, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 550000.00, 'cash', NULL, 'Tiền công lái xe: Lê Phong', 1, '2025-11-17 18:35:00', '2025-11-24 06:25:48', '2025-11-24 06:25:48'),
(307, 27, 19, 3, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 600000.00, 'cash', NULL, 'Tiền công nhân viên y tế: Khánh', 1, '2025-11-17 18:35:00', '2025-11-24 06:25:48', '2025-11-24 06:25:48'),
(308, 27, NULL, 3, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 500000.00, 'cash', NULL, 'Hoa hồng: Khoa Phổi - BV ĐKLĐ', 1, '2025-11-17 18:35:00', '2025-11-24 06:25:48', '2025-11-24 06:25:48'),
(309, 28, 4, 4, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 550000.00, 'cash', NULL, 'Tiền công lái xe: Nguyễn Cữu Ninh', 1, '2025-11-17 18:37:00', '2025-11-24 06:26:03', '2025-11-24 06:26:03'),
(310, 28, 6, 4, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 600000.00, 'cash', NULL, 'Tiền công nhân viên y tế: Nguyễn Quốc Vũ', 1, '2025-11-17 18:37:00', '2025-11-24 06:26:03', '2025-11-24 06:26:03'),
(311, 42, 4, 4, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 550000.00, 'cash', NULL, 'Tiền công lái xe: Nguyễn Cữu Ninh', 1, '2025-11-21 18:36:00', '2025-11-24 06:27:24', '2025-11-24 06:27:24'),
(312, 42, 6, 4, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 600000.00, 'cash', NULL, 'Tiền công nhân viên y tế: Nguyễn Quốc Vũ', 1, '2025-11-21 18:36:00', '2025-11-24 06:27:24', '2025-11-24 06:27:24'),
(313, 42, NULL, 4, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 500000.00, 'cash', NULL, 'Hoa hồng: Khoa HSTC - BV ĐKLĐ', 1, '2025-11-21 18:36:00', '2025-11-24 06:27:24', '2025-11-24 06:27:24'),
(314, 39, 7, 3, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 550000.00, 'cash', NULL, 'Tiền công lái xe: Lê Phong', 1, '2025-11-20 18:31:00', '2025-11-24 06:27:46', '2025-11-24 06:27:46'),
(315, 39, 6, 3, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 600000.00, 'cash', NULL, 'Tiền công nhân viên y tế: Nguyễn Quốc Vũ', 1, '2025-11-20 18:31:00', '2025-11-24 06:27:46', '2025-11-24 06:27:46'),
(316, 39, NULL, 3, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 500000.00, 'cash', NULL, 'Hoa hồng: Khoa nội B - BV ĐKLĐ', 1, '2025-11-20 18:31:00', '2025-11-24 06:27:46', '2025-11-24 06:27:46'),
(317, 36, 4, 3, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 550000.00, 'cash', NULL, 'Tiền công lái xe: Nguyễn Cữu Ninh', 1, '2025-11-19 18:25:00', '2025-11-24 06:28:17', '2025-11-24 06:28:17'),
(318, 36, 6, 3, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 600000.00, 'cash', NULL, 'Tiền công nhân viên y tế: Nguyễn Quốc Vũ', 1, '2025-11-19 18:25:00', '2025-11-24 06:28:17', '2025-11-24 06:28:17'),
(319, 36, NULL, 3, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 500000.00, 'cash', NULL, 'Hoa hồng: Khoa Phổi - BV ĐKLĐ', 1, '2025-11-19 18:25:00', '2025-11-24 06:28:17', '2025-11-24 06:28:17'),
(320, 31, 7, 3, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 550000.00, 'cash', NULL, 'Tiền công lái xe: Lê Phong', 1, '2025-11-18 18:50:00', '2025-11-24 06:28:46', '2025-11-24 06:28:46'),
(321, 31, 6, 3, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 600000.00, 'cash', NULL, 'Tiền công nhân viên y tế: Nguyễn Quốc Vũ', 1, '2025-11-18 18:50:00', '2025-11-24 06:28:46', '2025-11-24 06:28:46'),
(322, 31, NULL, 3, NULL, 'chi', NULL, NULL, 1, NULL, NULL, NULL, 500000.00, 'cash', NULL, 'Hoa hồng: Khoa Phổi - BV ĐKLĐ', 1, '2025-11-18 18:50:00', '2025-11-24 06:28:46', '2025-11-24 06:28:46');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin@binhan.com', NULL, '$2y$12$XYgS5wyO7cIi6lyLJ3l5K.9fS7AHmEXARJUvLQWWP33I17Mx8cX8q', NULL, '2025-11-21 11:07:30', '2025-11-21 11:07:30'),
(2, 'Điều phối viên', 'dispatcher@binhan.com', NULL, '$2y$12$66.Eob/jjBiKJcpt3O3CE.YTw1x.zJFZZHc6vGFfCHFz/97oJvvla', NULL, '2025-11-21 11:07:30', '2025-11-21 11:07:30'),
(3, 'Kế toán', 'accountant@binhan.com', NULL, '$2y$12$mQX8.RBnZdMwkDuNjdEH2enSIp2vuLd9.Zg3WsayjGULfwKKByryW', NULL, '2025-11-21 11:07:30', '2025-11-21 11:07:30'),
(4, 'Tài xế', 'driver@binhan.com', NULL, '$2y$12$SQ6EBB.0inYqAlcmLbi5OObUiIQxuN6MohZ/zYibv3suSyfzSy5wK', NULL, '2025-11-21 11:07:30', '2025-11-21 11:07:30'),
(5, 'Vỹ Phạm', 'vypham@115lamdong.com', NULL, '$2y$12$HuavUOxAAwiI2kuojg129u4Davmx5Ex36Xn1r02cr88jwQ1sawbjC', NULL, '2025-11-21 23:02:30', '2025-11-21 23:02:30'),
(6, 'Ngô Quyền', 'ngoquyen@115lamdong.com', NULL, '$2y$12$5Vts1opWhd3Ew0C3BUNqDOC9XDKAYpzOiTtIfTy4S1d1m2nt3h8tW', NULL, '2025-11-21 23:03:48', '2025-11-21 23:03:48'),
(7, 'Nguyễn Cữu Ninh', 'ninh@115lamdong.com', NULL, '$2y$12$znMSXKDbdVDIAWABB4W.CO3oS434PkRGP3sOGYtnCDrz06VAhmzXK', NULL, '2025-11-21 23:20:28', '2025-11-21 23:20:28'),
(8, 'Cil Đoan', 'doan@115lamdong.com', NULL, '$2y$12$s3iR6hfWrPUqnRLj0JVUPejxKOyp5.luGqrxxc9wujeeNg7MEOVKW', NULL, '2025-11-21 23:22:31', '2025-11-21 23:22:31'),
(9, 'Nguyễn Quốc Vũ', 'vu@115lamdong.com', NULL, '$2y$12$UlMNAZ2FsZNiG5VfRD5bdentf2/nsnlCQv.yTf314N9Hyroa/A16C', NULL, '2025-11-21 23:23:40', '2025-11-21 23:23:40'),
(10, 'Lê Phong', 'phong@115lamdong.com', NULL, '$2y$12$ywk/TVuDQ0oewhK2TwnHse6fMsVTfq14wYmk33QZ5louFtTLukEFi', NULL, '2025-11-22 01:22:10', '2025-11-22 01:22:10'),
(11, 'Trần Đức Anh', 'ducanh@115lamdong.com', NULL, '$2y$12$0iQeZxbmuOZGvMCWFg2sIu9dkqB1ZO9X5TWzyY.LCCYNgG.3GDS4C', NULL, '2025-11-22 04:02:39', '2025-11-22 04:02:39'),
(12, 'A Thành', 'thanh@115lamdong.com', NULL, '$2y$12$QfV4M7kOlNV2HTc4exsP8eYGi/EE1ezWqs8TsHGGcy9UrqCbxvjCO', NULL, '2025-11-22 04:03:36', '2025-11-22 04:03:36'),
(13, 'A Trung', 'trung@115lamdong.com', NULL, '$2y$12$fZMul3PLrwWlPkfR3GdoSufVzNzctJU7VdmlpPR6lRHfnsLO2qumO', NULL, '2025-11-22 04:43:36', '2025-11-22 04:43:36'),
(14, 'Nova', 'nova@115lamdong.com', NULL, '$2y$12$phxo3djttavV2K1.iTh18e4dBMO7.y5vvYrEsr0uCeUn06Lw8wgAS', NULL, '2025-11-22 04:55:39', '2025-11-22 04:55:39'),
(22, 'Minh Trung 2', 'minhtrung2@115lamdong.com', NULL, '$2y$12$4188JtoyzwS7u0N6FAd8meq3IK52.zn6HphBN/.T9ydE.eG/27CKW', NULL, '2025-11-22 05:33:04', '2025-11-22 10:21:20'),
(23, 'Đức Anh', 'tranducanh@115lamdong.com', NULL, '$2y$12$c2.kt/v1XOnGhBwJ9PLuYuYi0wOk0jEI9tx5sTgFFZ0TF8/osmyAm', NULL, '2025-11-22 08:03:22', '2025-11-22 08:03:22'),
(24, 'Bs Hoa', 'bshoa@115lamdong.com', NULL, '$2y$12$i634mGhT4JHEcvKcNrQtdOJkGVc/2RDhmpWbFbghNx.d10FaDJIk2', NULL, '2025-11-22 08:36:30', '2025-11-22 08:36:30'),
(25, 'Cty Minh Trung 1', 'minhtrung1@115lamdong.com', NULL, '$2y$12$3.lJVzNKZdf/zYN/dXhIJ.2/1kCcNxR/3qrc/bLcdQ2e5cuGFYAWC', NULL, '2025-11-22 10:19:37', '2025-11-22 10:19:37'),
(26, 'Lâm', 'lam@115lamdong.com', NULL, '$2y$12$Ol0tH4Cmg5VsZze/76KCOurs8YPfnorQ68G4CKh7SD9PfzQMoVtya', NULL, '2025-11-22 10:31:57', '2025-11-22 10:31:57'),
(27, 'Phúc', 'phuc@115lamdong.com', NULL, '$2y$12$Mg4TG1JLeMP1wdcpXOlJ8.vFgr5fJF9QakKJFchYveRQfV.v91XkW', NULL, '2025-11-22 10:32:39', '2025-11-22 10:32:39'),
(28, 'Lân', 'lan@115lamdong.com', NULL, '$2y$12$GyJbmBFSUKSga1AAHXNgqulQfTA7Vi261H/g9hnnKHiKUZYbbv2qC', NULL, '2025-11-22 10:33:13', '2025-11-22 10:33:13'),
(29, 'Khánh', 'khanh@115lamdong.com', NULL, '$2y$12$ZKNSAy//rkmHSfk0yq6n2.jKQDK3YAviy4OSuT4BpenJPXN50hn5i', NULL, '2025-11-22 10:34:08', '2025-11-22 10:34:08'),
(30, 'Danh', 'danh@115lamdong.com', NULL, '$2y$12$JJbOHfKjXoCj7TPrZbGDqeOcFKkkQ1YwBL3lz1LjNmWBVKHd/2gwa', NULL, '2025-11-22 11:49:31', '2025-11-22 11:49:31');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `vehicles`
--

CREATE TABLE `vehicles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `license_plate` varchar(20) NOT NULL,
  `model` varchar(100) DEFAULT NULL,
  `driver_name` varchar(100) DEFAULT NULL,
  `driver_id` bigint(20) UNSIGNED DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `status` enum('active','inactive','maintenance') NOT NULL DEFAULT 'active',
  `note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `vehicles`
--

INSERT INTO `vehicles` (`id`, `license_plate`, `model`, `driver_name`, `driver_id`, `phone`, `status`, `note`, `created_at`, `updated_at`) VALUES
(1, '51B50614', 'Toyota', 'Đoan', 5, NULL, 'active', NULL, '2025-11-21 11:11:29', '2025-11-22 01:12:43'),
(2, '51B51291', 'Toyota', 'Ninh', 4, NULL, 'active', NULL, '2025-11-21 11:11:45', '2025-11-22 01:12:55'),
(3, '86A31384', 'Ford Transit', 'Lê Phong', 7, NULL, 'active', NULL, '2025-11-22 04:54:03', '2025-11-22 04:54:03'),
(4, '49B08879', 'Ford Transit', 'Nova', 11, NULL, 'active', NULL, '2025-11-22 04:54:24', '2025-11-22 04:56:00');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `vehicle_maintenances`
--

CREATE TABLE `vehicle_maintenances` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vehicle_id` bigint(20) UNSIGNED NOT NULL,
  `maintenance_service_id` bigint(20) UNSIGNED DEFAULT NULL,
  `partner_id` bigint(20) UNSIGNED DEFAULT NULL,
  `incident_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `cost` decimal(10,2) NOT NULL,
  `mileage` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `vehicle_maintenances`
--

INSERT INTO `vehicle_maintenances` (`id`, `vehicle_id`, `maintenance_service_id`, `partner_id`, `incident_id`, `user_id`, `date`, `cost`, `mileage`, `description`, `note`, `created_at`, `updated_at`) VALUES
(5, 4, 5, 6, NULL, 1, '2025-11-14', 1666000.00, NULL, 'vé mb cho nhân sự đi ra lấy xe', NULL, '2025-11-22 09:11:02', '2025-11-22 09:18:34'),
(6, 4, 6, 5, NULL, 1, '2025-11-07', 518298.00, NULL, 'ks 1 đêm cho lái xe', NULL, '2025-11-22 09:14:18', '2025-11-22 09:18:40'),
(7, 4, 7, 7, NULL, 1, '2025-11-13', 500000.00, NULL, 'Đổ dầu HN về TQ', NULL, '2025-11-22 09:20:47', '2025-11-22 09:20:47'),
(8, 4, 1, 3, NULL, 1, '2025-11-14', 850000.00, 1600, 'Thay nhớt lần đầu (1600km)', NULL, '2025-11-22 09:21:26', '2025-11-22 09:21:26'),
(9, 4, 8, 8, NULL, 1, '2025-11-14', 450000.00, NULL, 'Dán logo cty', NULL, '2025-11-22 09:22:05', '2025-11-22 09:22:05'),
(10, 4, 9, 9, NULL, 1, '2025-11-14', 100000.00, NULL, 'Dán thẻ Epass', NULL, '2025-11-22 09:22:40', '2025-11-22 09:22:40'),
(11, 4, 10, 10, NULL, 1, '2025-11-14', 3800000.00, NULL, 'Lắp cam hành trình, định vị', NULL, '2025-11-22 09:23:09', '2025-11-22 09:23:09'),
(12, 4, 11, 11, NULL, 1, '2025-11-14', 350000.00, NULL, 'Ép biển số', NULL, '2025-11-22 09:23:53', '2025-11-22 09:23:53'),
(13, 4, 7, 7, NULL, 1, '2025-11-14', 1020000.00, NULL, 'Đổ dầu TQ - Túy Loan', NULL, '2025-11-22 09:24:30', '2025-11-22 09:52:44'),
(14, 4, 7, 7, NULL, 1, '2025-11-15', 1300000.00, NULL, 'Đổ dầu Túy loan - Lâm Đồng', NULL, '2025-11-22 09:25:14', '2025-11-22 09:52:44'),
(15, 4, 13, 13, NULL, 1, '2025-11-15', 500000.00, NULL, 'Ăn uống đi đường ship xe vào', NULL, '2025-11-22 09:25:53', '2025-11-22 09:25:53'),
(16, 4, 14, 14, NULL, 1, '2025-11-15', 2000000.00, NULL, 'Công lái xe', NULL, '2025-11-22 09:26:24', '2025-11-22 09:26:24'),
(17, 4, 15, 15, NULL, 1, '2025-11-15', 792315.00, NULL, 'Phí qua trạm BOT', NULL, '2025-11-22 09:27:02', '2025-11-22 09:27:02'),
(18, 4, 15, 15, NULL, 1, '2025-11-17', 362252.00, NULL, 'Phí qua trạm chuyển viện ĐL - SG 17/11', NULL, '2025-11-22 09:27:49', '2025-11-22 09:27:49'),
(19, 4, 15, 15, NULL, 1, '2025-11-19', 870000.00, NULL, 'Nạp vé tháng trạm định an', NULL, '2025-11-22 09:28:27', '2025-11-22 09:52:44'),
(20, 4, 15, 15, NULL, 1, '2025-11-19', 1208000.00, NULL, 'mua vé tháng qua trạm liên đầm', NULL, '2025-11-22 09:29:49', '2025-11-22 09:52:44'),
(21, 4, 18, 27, NULL, 1, '2025-11-07', 13563000.00, NULL, 'BH thân vỏ xe để giải ngân', NULL, '2025-11-22 11:26:43', '2025-11-22 11:26:43'),
(22, 3, 18, 27, NULL, 1, '2025-11-07', 13563000.00, NULL, NULL, NULL, '2025-11-22 11:27:11', '2025-11-22 11:30:33'),
(23, 3, 19, 27, NULL, 1, '2025-11-17', 1311560.00, NULL, NULL, NULL, '2025-11-22 11:27:41', '2025-11-22 11:30:48'),
(24, 4, 19, 27, NULL, 1, '2025-11-17', 1311560.00, NULL, NULL, NULL, '2025-11-22 11:28:01', '2025-11-22 11:30:57'),
(25, 3, 3, 3, NULL, 1, '2025-11-20', 120000.00, NULL, 'CT rữa xe 313.84', NULL, '2025-11-23 12:20:45', '2025-11-23 12:20:45'),
(26, 4, 20, 28, NULL, 1, '2025-11-23', 400000.00, NULL, 'Thay van bình oxy', 'Ck bên DCYK Hai Dang', '2025-11-24 06:20:34', '2025-11-24 06:20:34');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `wage_types`
--

CREATE TABLE `wage_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL COMMENT 'Tên loại tiền công',
  `sort_order` int(11) NOT NULL DEFAULT 0 COMMENT 'Thứ tự sắp xếp',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Trạng thái hoạt động',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `wage_types`
--

INSERT INTO `wage_types` (`id`, `name`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Công', 1, 1, '2025-11-22 01:01:03', '2025-11-22 01:01:03'),
(2, 'Thưởng', 2, 1, '2025-11-22 01:01:03', '2025-11-22 01:01:03'),
(3, 'Hoa hồng', 3, 1, '2025-11-22 01:01:03', '2025-11-22 01:01:03'),
(4, 'Tip', 4, 1, '2025-11-22 01:01:03', '2025-11-22 01:01:03'),
(5, 'Khác', 99, 1, '2025-11-22 01:01:03', '2025-11-22 01:01:03'),
(6, 'Ngủ đêm', 5, 1, '2025-11-22 01:11:33', '2025-11-22 01:11:33'),
(7, 'Máy thở', 1, 1, '2025-11-22 01:11:52', '2025-11-22 01:11:52');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subject` (`subject_type`,`subject_id`),
  ADD KEY `causer` (`causer_type`,`causer_id`),
  ADD KEY `activity_log_log_name_index` (`log_name`);

--
-- Chỉ mục cho bảng `additional_services`
--
ALTER TABLE `additional_services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `additional_services_name_index` (`name`);

--
-- Chỉ mục cho bảng `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `departments_name_unique` (`name`);

--
-- Chỉ mục cho bảng `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Chỉ mục cho bảng `incidents`
--
ALTER TABLE `incidents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `incidents_dispatch_by_foreign` (`dispatch_by`),
  ADD KEY `incidents_vehicle_id_index` (`vehicle_id`),
  ADD KEY `incidents_patient_id_index` (`patient_id`),
  ADD KEY `incidents_date_index` (`date`),
  ADD KEY `incidents_vehicle_id_date_index` (`vehicle_id`,`date`),
  ADD KEY `incidents_from_location_id_foreign` (`from_location_id`),
  ADD KEY `incidents_to_location_id_foreign` (`to_location_id`),
  ADD KEY `incidents_partner_id_foreign` (`partner_id`);

--
-- Chỉ mục cho bảng `incident_additional_services`
--
ALTER TABLE `incident_additional_services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `incident_additional_services_incident_id_foreign` (`incident_id`),
  ADD KEY `incident_additional_services_additional_service_id_foreign` (`additional_service_id`);

--
-- Chỉ mục cho bảng `incident_staff`
--
ALTER TABLE `incident_staff`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `incident_staff_incident_id_staff_id_role_unique` (`incident_id`,`staff_id`,`role`),
  ADD KEY `incident_staff_incident_id_index` (`incident_id`),
  ADD KEY `incident_staff_staff_id_index` (`staff_id`);

--
-- Chỉ mục cho bảng `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `locations_name_index` (`name`),
  ADD KEY `locations_type_index` (`type`);

--
-- Chỉ mục cho bảng `maintenance_services`
--
ALTER TABLE `maintenance_services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `maintenance_services_name_index` (`name`);

--
-- Chỉ mục cho bảng `media`
--
ALTER TABLE `media`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `media_uuid_unique` (`uuid`),
  ADD KEY `media_model_type_model_id_index` (`model_type`,`model_id`),
  ADD KEY `media_order_column_index` (`order_column`);

--
-- Chỉ mục cho bảng `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Chỉ mục cho bảng `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Chỉ mục cho bảng `notes`
--
ALTER TABLE `notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notes_user_id_foreign` (`user_id`),
  ADD KEY `notes_incident_id_index` (`incident_id`),
  ADD KEY `notes_vehicle_id_index` (`vehicle_id`);

--
-- Chỉ mục cho bảng `partners`
--
ALTER TABLE `partners`
  ADD PRIMARY KEY (`id`),
  ADD KEY `partners_name_index` (`name`),
  ADD KEY `partners_type_index` (`type`);

--
-- Chỉ mục cho bảng `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Chỉ mục cho bảng `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patients_name_index` (`name`),
  ADD KEY `patients_phone_index` (`phone`);

--
-- Chỉ mục cho bảng `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Chỉ mục cho bảng `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Chỉ mục cho bảng `positions`
--
ALTER TABLE `positions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `positions_name_unique` (`name`);

--
-- Chỉ mục cho bảng `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Chỉ mục cho bảng `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Chỉ mục cho bảng `salary_advances`
--
ALTER TABLE `salary_advances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `salary_advances_approved_by_foreign` (`approved_by`),
  ADD KEY `salary_advances_staff_id_index` (`staff_id`),
  ADD KEY `salary_advances_status_index` (`status`),
  ADD KEY `salary_advances_date_index` (`date`);

--
-- Chỉ mục cho bảng `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `staff_employee_code_unique` (`employee_code`),
  ADD KEY `staff_user_id_foreign` (`user_id`),
  ADD KEY `staff_vehicle_id_foreign` (`vehicle_id`);

--
-- Chỉ mục cho bảng `staff_adjustments`
--
ALTER TABLE `staff_adjustments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `staff_adjustments_created_by_foreign` (`created_by`),
  ADD KEY `staff_adjustments_staff_id_month_index` (`staff_id`,`month`),
  ADD KEY `staff_adjustments_status_index` (`status`),
  ADD KEY `staff_adjustments_incident_id_foreign` (`incident_id`);

--
-- Chỉ mục cho bảng `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `system_settings_key_unique` (`key`),
  ADD KEY `system_settings_group_index` (`group`);

--
-- Chỉ mục cho bảng `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transactions_recorded_by_foreign` (`recorded_by`),
  ADD KEY `transactions_incident_id_index` (`incident_id`),
  ADD KEY `transactions_vehicle_id_index` (`vehicle_id`),
  ADD KEY `transactions_type_index` (`type`),
  ADD KEY `transactions_date_index` (`date`),
  ADD KEY `transactions_vehicle_id_type_date_index` (`vehicle_id`,`type`,`date`),
  ADD KEY `transactions_staff_id_index` (`staff_id`),
  ADD KEY `transactions_category_index` (`category`),
  ADD KEY `transactions_vehicle_maintenance_id_foreign` (`vehicle_maintenance_id`),
  ADD KEY `transactions_replaced_by_foreign` (`replaced_by`),
  ADD KEY `transactions_edited_by_foreign` (`edited_by`),
  ADD KEY `transactions_transaction_category_index` (`transaction_category`),
  ADD KEY `transactions_is_active_index` (`is_active`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Chỉ mục cho bảng `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vehicles_license_plate_unique` (`license_plate`),
  ADD KEY `vehicles_license_plate_index` (`license_plate`),
  ADD KEY `vehicles_driver_id_foreign` (`driver_id`);

--
-- Chỉ mục cho bảng `vehicle_maintenances`
--
ALTER TABLE `vehicle_maintenances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vehicle_maintenances_maintenance_service_id_foreign` (`maintenance_service_id`),
  ADD KEY `vehicle_maintenances_partner_id_foreign` (`partner_id`),
  ADD KEY `vehicle_maintenances_incident_id_foreign` (`incident_id`),
  ADD KEY `vehicle_maintenances_user_id_foreign` (`user_id`),
  ADD KEY `vehicle_maintenances_vehicle_id_index` (`vehicle_id`),
  ADD KEY `vehicle_maintenances_date_index` (`date`);

--
-- Chỉ mục cho bảng `wage_types`
--
ALTER TABLE `wage_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `wage_types_name_unique` (`name`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=591;

--
-- AUTO_INCREMENT cho bảng `additional_services`
--
ALTER TABLE `additional_services`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `departments`
--
ALTER TABLE `departments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT cho bảng `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `incidents`
--
ALTER TABLE `incidents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT cho bảng `incident_additional_services`
--
ALTER TABLE `incident_additional_services`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `incident_staff`
--
ALTER TABLE `incident_staff`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=137;

--
-- AUTO_INCREMENT cho bảng `locations`
--
ALTER TABLE `locations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT cho bảng `maintenance_services`
--
ALTER TABLE `maintenance_services`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT cho bảng `media`
--
ALTER TABLE `media`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT cho bảng `notes`
--
ALTER TABLE `notes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `partners`
--
ALTER TABLE `partners`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT cho bảng `patients`
--
ALTER TABLE `patients`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT cho bảng `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT cho bảng `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `positions`
--
ALTER TABLE `positions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT cho bảng `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `salary_advances`
--
ALTER TABLE `salary_advances`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `staff`
--
ALTER TABLE `staff`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT cho bảng `staff_adjustments`
--
ALTER TABLE `staff_adjustments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT cho bảng `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=323;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT cho bảng `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `vehicle_maintenances`
--
ALTER TABLE `vehicle_maintenances`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT cho bảng `wage_types`
--
ALTER TABLE `wage_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `incidents`
--
ALTER TABLE `incidents`
  ADD CONSTRAINT `incidents_dispatch_by_foreign` FOREIGN KEY (`dispatch_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `incidents_from_location_id_foreign` FOREIGN KEY (`from_location_id`) REFERENCES `locations` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `incidents_partner_id_foreign` FOREIGN KEY (`partner_id`) REFERENCES `partners` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `incidents_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `incidents_to_location_id_foreign` FOREIGN KEY (`to_location_id`) REFERENCES `locations` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `incidents_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `incident_additional_services`
--
ALTER TABLE `incident_additional_services`
  ADD CONSTRAINT `incident_additional_services_additional_service_id_foreign` FOREIGN KEY (`additional_service_id`) REFERENCES `additional_services` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `incident_additional_services_incident_id_foreign` FOREIGN KEY (`incident_id`) REFERENCES `incidents` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `incident_staff`
--
ALTER TABLE `incident_staff`
  ADD CONSTRAINT `incident_staff_incident_id_foreign` FOREIGN KEY (`incident_id`) REFERENCES `incidents` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `incident_staff_staff_id_foreign` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `notes`
--
ALTER TABLE `notes`
  ADD CONSTRAINT `notes_incident_id_foreign` FOREIGN KEY (`incident_id`) REFERENCES `incidents` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `notes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notes_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `salary_advances`
--
ALTER TABLE `salary_advances`
  ADD CONSTRAINT `salary_advances_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `salary_advances_staff_id_foreign` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `staff`
--
ALTER TABLE `staff`
  ADD CONSTRAINT `staff_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `staff_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `staff_adjustments`
--
ALTER TABLE `staff_adjustments`
  ADD CONSTRAINT `staff_adjustments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `staff_adjustments_incident_id_foreign` FOREIGN KEY (`incident_id`) REFERENCES `incidents` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `staff_adjustments_staff_id_foreign` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_edited_by_foreign` FOREIGN KEY (`edited_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `transactions_incident_id_foreign` FOREIGN KEY (`incident_id`) REFERENCES `incidents` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `transactions_recorded_by_foreign` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `transactions_replaced_by_foreign` FOREIGN KEY (`replaced_by`) REFERENCES `transactions` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `transactions_staff_id_foreign` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `transactions_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_vehicle_maintenance_id_foreign` FOREIGN KEY (`vehicle_maintenance_id`) REFERENCES `vehicle_maintenances` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `vehicles`
--
ALTER TABLE `vehicles`
  ADD CONSTRAINT `vehicles_driver_id_foreign` FOREIGN KEY (`driver_id`) REFERENCES `staff` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `vehicle_maintenances`
--
ALTER TABLE `vehicle_maintenances`
  ADD CONSTRAINT `vehicle_maintenances_incident_id_foreign` FOREIGN KEY (`incident_id`) REFERENCES `incidents` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `vehicle_maintenances_maintenance_service_id_foreign` FOREIGN KEY (`maintenance_service_id`) REFERENCES `maintenance_services` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `vehicle_maintenances_partner_id_foreign` FOREIGN KEY (`partner_id`) REFERENCES `partners` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `vehicle_maintenances_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `vehicle_maintenances_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
