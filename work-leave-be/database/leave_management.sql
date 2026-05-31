-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th5 13, 2026 lúc 05:28 AM
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
-- Cơ sở dữ liệu: `leave_management`
--

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
-- Cấu trúc bảng cho bảng `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `leave_applications`
--

CREATE TABLE `leave_applications` (
  `id` char(10) NOT NULL,
  `user_id` char(10) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `total_days` int(11) NOT NULL DEFAULT 0,
  `reason` text NOT NULL,
  `type` enum('annual','sick','unpaid') NOT NULL DEFAULT 'annual',
  `status` enum('new','pending','approved','rejected','cancelled') NOT NULL DEFAULT 'new',
  `created_by` char(10) DEFAULT NULL,
  `updated_by` char(10) DEFAULT NULL,
  `deleted_by` char(10) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `leave_applications`
--

INSERT INTO `leave_applications` (`id`, `user_id`, `start_date`, `end_date`, `total_days`, `reason`, `type`, `status`, `created_by`, `updated_by`, `deleted_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
('1d9qdQ8wbv', 'OifUBLi3Jf', '2026-05-12', '2026-05-13', 2, 'Đi chơi gia đình', 'annual', 'new', NULL, NULL, NULL, '2026-05-11 05:45:10', '2026-05-11 05:45:10', NULL),
('1UImIfQoRW', '7CKdtIUKL7', '2026-05-19', '2026-05-20', 2, 'Attending a wedding ceremony.', 'annual', 'approved', '7CKdtIUKL7', '7CKdtIUKL7', NULL, '2026-05-09 01:13:54', '2026-05-11 04:52:20', NULL),
('auDHoNsJe2', 'OifUBLi3Jf', '2026-04-19', '2026-04-21', 3, 'Personal matters.', 'annual', 'approved', 'OifUBLi3Jf', 'IwvoVkmgd7', NULL, '2026-05-09 01:13:54', '2026-05-09 01:13:54', NULL),
('eO9bV90mJd', '7CKdtIUKL7', '2026-04-29', '2026-04-30', 2, 'Sick - fever and cold.', 'sick', 'pending', '7CKdtIUKL7', 'Ob8QRuFDzq', NULL, '2026-05-09 01:13:54', '2026-05-09 01:13:54', NULL),
('ev0PV6tRbP', '7CKdtIUKL7', '2026-06-08', '2026-06-09', 2, 'Moving to new house - unpaid.', 'unpaid', 'pending', '7CKdtIUKL7', '7CKdtIUKL7', NULL, '2026-05-09 01:13:54', '2026-05-09 01:13:54', NULL),
('GeJXmnfmcU', 'OifUBLi3Jf', '2026-05-24', '2026-05-25', 1, 'Doctor appointment for annual checkup.', 'sick', 'new', 'OifUBLi3Jf', 'OifUBLi3Jf', NULL, '2026-05-09 01:13:54', '2026-05-12 11:13:09', NULL),
('MVvmmzGN1l', 'OifUBLi3Jf', '2026-05-14', '2026-05-15', 2, 'Family vacation trip to Da Nang.', 'annual', 'approved', 'OifUBLi3Jf', 'OifUBLi3Jf', NULL, '2026-05-09 01:13:54', '2026-05-11 04:20:15', NULL),
('Wp6X4ZAfhn', 'OifUBLi3Jf', '2026-05-12', '2026-05-13', 2, 'Nghỉ việc gia đình', 'annual', 'new', NULL, NULL, NULL, '2026-05-09 03:49:39', '2026-05-09 03:49:39', NULL);

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
(1, '0001_01_01_000002_create_jobs_table', 1),
(2, '2024_01_01_000001_create_users_table', 1),
(3, '2024_01_01_000002_create_leave_applications_table', 1);

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
-- Cấu trúc bảng cho bảng `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` char(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `type` tinyint(4) NOT NULL DEFAULT 2 COMMENT '0=admin,1=manager,2=employee',
  `remaining_days` int(11) NOT NULL DEFAULT 12,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_by` char(10) DEFAULT NULL,
  `updated_by` char(10) DEFAULT NULL,
  `deleted_by` char(10) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `type`, `remaining_days`, `remember_token`, `created_by`, `updated_by`, `deleted_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
('7CKdtIUKL7', 'Tran Thi Bich', 'bich.tran@company.com', NULL, '$2y$12$EUzrCfL9caZi0rbxhqdB4uGyd.u82OYfNTZkQMBQYfvubUAHWR4zy', 2, 6, NULL, NULL, NULL, NULL, '2026-05-09 01:13:54', '2026-05-11 04:52:20', NULL),
('IwvoVkmgd7', 'Manager User', 'manager@company.com', NULL, '$2y$12$ze6FQWLq/TNO0vVTl8.JHuozogUg8PgYWJaoKhslXakURYQYxV1RS', 1, 12, NULL, NULL, NULL, NULL, '2026-05-09 01:13:54', '2026-05-09 01:13:54', NULL),
('Ob8QRuFDzq', 'Admin User', 'admin@company.com', NULL, '$2y$12$XRddB2PrSQ94XbiDgaL6AuaZqVJ1q/SbCPOIBGBeZahhzpdZC6Zma', 0, 12, NULL, NULL, NULL, NULL, '2026-05-09 01:13:54', '2026-05-09 01:13:54', NULL),
('OifUBLi3Jf', 'Nguyen Van An', 'an.nguyen@company.com', NULL, '$2y$12$JeX1FJ/rH6gznFB95hkaKOp/usnTwEUhXhfVOmGgsXKyxnhPb/PGm', 2, 8, NULL, NULL, NULL, NULL, '2026-05-09 01:13:54', '2026-05-11 04:20:16', NULL);

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Chỉ mục cho bảng `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Chỉ mục cho bảng `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `leave_applications`
--
ALTER TABLE `leave_applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `leave_applications_user_id_status_index` (`user_id`,`status`),
  ADD KEY `leave_applications_start_date_end_date_index` (`start_date`,`end_date`);

--
-- Chỉ mục cho bảng `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Chỉ mục cho bảng `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `leave_applications`
--
ALTER TABLE `leave_applications`
  ADD CONSTRAINT `leave_applications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
