-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 16, 2026 at 10:57 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `facturation_local`
--

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `fiscalId` varchar(100) DEFAULT NULL,
  `cin` varchar(100) DEFAULT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`id`, `type`, `name`, `email`, `phone`, `address`, `fiscalId`, `cin`, `user_id`) VALUES
(37, 'person', 'Ahmed Ben Ali', 'ahmed@gmail.com', '22123456', 'Tunis', NULL, '12345678', 1),
(38, 'person', 'Sami Trabelsi', 'sami@gmail.com', '22111111', 'Sfax', NULL, '87654321', 1),
(39, 'company', 'Tech Solutions SARL', 'contact@techsolutions.tn', '70123456', 'Lac 2 Tunis', 'TN123456', NULL, 1),
(40, 'company', 'Digital Factory', 'info@digitalfactory.tn', '71123456', 'Ariana', 'TN987654', NULL, 1),
(41, 'person', 'Mouna Khelifi', 'mouna@gmail.com', '22198765', 'Sousse', NULL, '11223344', 1),
(42, 'person', 'Karim Mansour', 'karim@gmail.com', '22112233', 'Monastir', NULL, '99887766', 1),
(124, 'individual', 'Client 11111111', '', '', '', '', '11111111', 1);

-- --------------------------------------------------------

--
-- Table structure for table `erp_invoices`
--

CREATE TABLE `erp_invoices` (
  `id` int(11) NOT NULL,
  `invoice` varchar(191) NOT NULL,
  `custom_email` text DEFAULT NULL,
  `custom_code` varchar(255) DEFAULT NULL,
  `invoice_date` date NOT NULL,
  `invoice_due_date` date NOT NULL,
  `subtotal` decimal(20,3) NOT NULL DEFAULT 0.000,
  `montant_tva` decimal(20,3) DEFAULT NULL,
  `subtotal_ttc` decimal(20,3) NOT NULL DEFAULT 0.000,
  `shipping` decimal(20,3) NOT NULL DEFAULT 0.000,
  `discount` decimal(20,3) NOT NULL DEFAULT 0.000,
  `vat` decimal(20,3) NOT NULL DEFAULT 0.000,
  `total` decimal(20,3) NOT NULL DEFAULT 0.000,
  `notes` text NOT NULL,
  `invoice_type` varchar(255) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'open',
  `type_doc` varchar(5) DEFAULT NULL,
  `timbre` decimal(20,3) DEFAULT NULL,
  `date_ajout` datetime NOT NULL DEFAULT current_timestamp(),
  `tx_retenue` decimal(20,3) DEFAULT NULL,
  `retenue` decimal(20,3) DEFAULT NULL,
  `net_retenue` decimal(20,3) DEFAULT NULL,
  `id_extract` int(5) DEFAULT NULL,
  `id_lettrage` int(5) DEFAULT NULL,
  `contrat_no` varchar(50) DEFAULT NULL,
  `json_finsys` longtext DEFAULT NULL,
  `json_return` longtext DEFAULT NULL,
  `json_return2` longtext DEFAULT NULL,
  `stat_api` int(5) DEFAULT NULL,
  `mnt_lettre` varchar(1000) DEFAULT NULL,
  `stat_ttn` varchar(20) DEFAULT NULL,
  `qr_code` longtext DEFAULT NULL,
  `uuid` varchar(50) DEFAULT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `erp_invoices`
--

INSERT INTO `erp_invoices` (`id`, `invoice`, `custom_email`, `custom_code`, `invoice_date`, `invoice_due_date`, `subtotal`, `montant_tva`, `subtotal_ttc`, `shipping`, `discount`, `vat`, `total`, `notes`, `invoice_type`, `status`, `type_doc`, `timbre`, `date_ajout`, `tx_retenue`, `retenue`, `net_retenue`, `id_extract`, `id_lettrage`, `contrat_no`, `json_finsys`, `json_return`, `json_return2`, `stat_api`, `mnt_lettre`, `stat_ttn`, `qr_code`, `uuid`, `user_id`) VALUES
(19622, 'INV-20260310-130723', NULL, '16', '2026-03-10', '2026-03-17', 5000.000, 950.000, 5950.000, 0.000, 0.000, 0.000, 5950.000, '', 'FACTURE', 'open', 'F', NULL, '2026-03-10 13:07:23', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1),
(19626, 'INV-20260314-003146', NULL, '16', '2026-03-14', '2026-03-21', 3800.000, 722.000, 4522.000, 0.000, 0.000, 0.000, 4522.000, '', 'FACTURE', 'open', 'F', NULL, '2026-03-14 00:31:46', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1),
(19629, 'INV-20260316-030735', NULL, '16', '2026-03-16', '2026-03-23', 1200.000, 228.000, 1428.000, 0.000, 0.000, 0.000, 1428.000, '', 'FACTURE', 'open', 'F', NULL, '2026-03-16 03:07:35', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1),
(19635, 'INV-20260320-0001', NULL, NULL, '2026-03-20', '2026-03-27', 5000.000, 950.000, 5950.000, 0.000, 0.000, 0.000, 5950.000, 'Laptop purchase', 'FACTURE', 'open', 'F', NULL, '2026-03-16 03:41:31', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1),
(19636, 'INV-20260320-0002', NULL, NULL, '2026-03-20', '2026-03-27', 650.000, 123.500, 773.500, 0.000, 0.000, 0.000, 773.500, 'Monitor sale', 'FACTURE', 'paid', 'F', NULL, '2026-03-16 03:41:31', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1),
(19637, 'INV-20260320-0003', NULL, NULL, '2026-03-21', '2026-03-28', 900.000, 171.000, 1071.000, 0.000, 0.000, 0.000, 1071.000, 'Printer invoice', 'FACTURE', 'open', 'F', NULL, '2026-03-16 03:41:31', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1),
(19638, 'INV-20260321-0004', NULL, NULL, '2026-03-21', '2026-03-28', 450.000, 85.500, 535.500, 0.000, 0.000, 0.000, 535.500, 'SSD purchase', 'FACTURE', 'open', 'F', NULL, '2026-03-16 03:41:31', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1),
(19639, 'INV-20260316-034250', NULL, '42', '2026-03-16', '2026-03-23', 1770.600, 336.414, 2107.014, 0.000, 0.000, 0.000, 2107.014, '', 'FACTURE', 'open', 'F', NULL, '2026-03-16 03:42:50', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1),
(19640, 'INV-20260316-034559', NULL, '42', '2026-03-16', '2026-03-23', 1350.000, 256.500, 1606.500, 0.000, 0.000, 0.000, 1606.500, '', 'FACTURE', 'open', 'F', NULL, '2026-03-16 03:45:59', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1),
(19641, 'INV-20260316-034927', NULL, '42', '2026-03-16', '2026-03-23', 5450.000, 1035.500, 6485.500, 0.000, 0.000, 0.000, 6485.500, '', 'FACTURE', 'open', 'F', NULL, '2026-03-16 03:49:27', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1),
(19643, 'INV-20260316-042211', NULL, '42', '2026-03-16', '2026-03-23', 6750.000, 1282.500, 8032.500, 0.000, 0.000, 0.000, 8032.500, '', 'FACTURE', 'open', 'F', NULL, '2026-03-16 04:22:11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1),
(19647, 'INV-20260316-054623', NULL, '120', '2026-03-16', '2026-03-23', 900.000, 171.000, 1071.000, 0.000, 0.000, 0.000, 1071.000, '', 'FACTURE', 'open', 'F', NULL, '2026-03-16 05:46:23', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1),
(19648, 'INV-20260316-055508', NULL, '121', '2026-03-16', '2026-03-23', 450.000, 85.500, 535.500, 0.000, 0.000, 0.000, 535.500, '', 'FACTURE', 'open', 'F', NULL, '2026-03-16 05:55:08', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1),
(19649, 'INV-20260316-055856', NULL, '42', '2026-03-16', '2026-03-23', 450.000, 85.500, 535.500, 0.000, 0.000, 0.000, 535.500, '', 'FACTURE', 'open', 'F', NULL, '2026-03-16 05:58:56', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1),
(19650, 'INV-20260316-055917', NULL, '40', '2026-03-16', '2026-03-23', 450.000, 85.500, 535.500, 0.000, 0.000, 0.000, 535.500, '', 'FACTURE', 'open', 'F', NULL, '2026-03-16 05:59:17', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1),
(19651, 'INV-20260316-064851', NULL, '121', '2026-03-16', '2026-03-23', 450.000, 85.500, 535.500, 0.000, 0.000, 0.000, 535.500, '', 'FACTURE', 'open', 'F', NULL, '2026-03-16 06:48:51', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1),
(19652, 'INV-20260316-094554', NULL, '42', '2026-03-16', '2026-03-23', 80.000, 15.200, 95.200, 0.000, 0.000, 0.000, 95.200, '', 'FACTURE', 'open', 'F', NULL, '2026-03-16 09:45:54', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1),
(19653, 'INV-20260316-101951', NULL, '124', '2026-03-16', '2026-03-23', 160.000, 30.400, 190.400, 0.000, 0.000, 0.000, 190.400, '', 'FACTURE', 'open', 'F', NULL, '2026-03-16 10:19:51', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `erp_invoice_items`
--

CREATE TABLE `erp_invoice_items` (
  `id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `invoice` varchar(191) NOT NULL,
  `product_code` varchar(255) DEFAULT NULL,
  `product` text NOT NULL,
  `qty` decimal(20,3) NOT NULL DEFAULT 1.000,
  `tva_rate` decimal(10,3) DEFAULT NULL,
  `montant_tva` decimal(20,3) NOT NULL DEFAULT 0.000,
  `tva_src` decimal(20,3) DEFAULT NULL,
  `ttc_src` decimal(20,3) DEFAULT NULL,
  `diff_tva` decimal(20,3) DEFAULT NULL,
  `diff_ttc` decimal(20,3) DEFAULT NULL,
  `price` decimal(20,3) NOT NULL DEFAULT 0.000,
  `discount` decimal(20,3) NOT NULL DEFAULT 0.000,
  `subtotal` decimal(20,3) NOT NULL DEFAULT 0.000,
  `subtotalTTC` decimal(20,3) NOT NULL DEFAULT 0.000,
  `invoice_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `erp_invoice_items`
--

INSERT INTO `erp_invoice_items` (`id`, `invoice_id`, `invoice`, `product_code`, `product`, `qty`, `tva_rate`, `montant_tva`, `tva_src`, `ttc_src`, `diff_tva`, `diff_ttc`, `price`, `discount`, `subtotal`, `subtotalTTC`, `invoice_date`) VALUES
(25964, 19622, 'INV-20260310-130723', 'PRD001', 'Laptop Dell Inspiron', 2.000, 19.000, 950.000, NULL, NULL, NULL, NULL, 2500.000, 0.000, 5000.000, 5950.000, '2026-03-10'),
(25967, 19626, 'INV-20260314-003146', 'prc123', 'produit', 2.000, 19.000, 722.000, NULL, NULL, NULL, NULL, 2000.000, 5.000, 3800.000, 4522.000, '2026-03-14'),
(25969, 19629, 'INV-20260316-030735', 'prc123', 'produit', 1.000, 19.000, 228.000, NULL, NULL, NULL, NULL, 1200.000, 0.000, 1200.000, 1428.000, '2026-03-16'),
(25978, 19639, 'INV-20260316-034250', 'PRD006', 'External SSD 1TB', 5.000, 19.000, 230.394, NULL, NULL, NULL, NULL, 258.000, 6.000, 1212.600, 1442.994, '2026-03-16'),
(25979, 19639, 'INV-20260316-034250', 'PRD006', 'External SSD 1TB', 1.000, 19.000, 106.020, NULL, NULL, NULL, NULL, 558.000, 0.000, 558.000, 664.020, '2026-03-16'),
(25980, 19640, 'INV-20260316-034559', 'PRD006', 'External SSD 1TB', 1.000, 19.000, 85.500, NULL, NULL, NULL, NULL, 450.000, 0.000, 450.000, 535.500, '2026-03-16'),
(25981, 19640, 'INV-20260316-034559', 'PRD006', 'External SSD 1TB', 1.000, 19.000, 85.500, NULL, NULL, NULL, NULL, 450.000, 0.000, 450.000, 535.500, '2026-03-16'),
(25982, 19640, 'INV-20260316-034559', 'PRD006', 'External SSD 1TB', 1.000, 19.000, 85.500, NULL, NULL, NULL, NULL, 450.000, 0.000, 450.000, 535.500, '2026-03-16'),
(25983, 19641, 'INV-20260316-034927', 'PRD006', 'External SSD 1TB', 1.000, 19.000, 85.500, NULL, NULL, NULL, NULL, 450.000, 0.000, 450.000, 535.500, '2026-03-16'),
(25984, 19641, 'INV-20260316-034927', 'PRD001', 'Laptop Dell Inspiron', 1.000, 19.000, 475.000, NULL, NULL, NULL, NULL, 2500.000, 0.000, 2500.000, 2975.000, '2026-03-16'),
(25985, 19641, 'INV-20260316-034927', 'PRD001', 'Laptop Dell Inspiron', 1.000, 19.000, 475.000, NULL, NULL, NULL, NULL, 2500.000, 0.000, 2500.000, 2975.000, '2026-03-16'),
(25986, 19643, 'INV-20260316-042211', 'PRD006', 'External SSD 1TB', 1.000, 19.000, 85.500, NULL, NULL, NULL, NULL, 450.000, 0.000, 450.000, 535.500, '2026-03-16'),
(25987, 19643, 'INV-20260316-042211', 'PRD006', 'External SSD 1TB', 1.000, 19.000, 85.500, NULL, NULL, NULL, NULL, 450.000, 0.000, 450.000, 535.500, '2026-03-16'),
(25988, 19643, 'INV-20260316-042211', 'PRD006', 'External SSD 1TB', 1.000, 19.000, 85.500, NULL, NULL, NULL, NULL, 450.000, 0.000, 450.000, 535.500, '2026-03-16'),
(25989, 19643, 'INV-20260316-042211', 'PRD006', 'External SSD 1TB', 1.000, 19.000, 85.500, NULL, NULL, NULL, NULL, 450.000, 0.000, 450.000, 535.500, '2026-03-16'),
(25990, 19643, 'INV-20260316-042211', 'PRD006', 'External SSD 1TB', 1.000, 19.000, 85.500, NULL, NULL, NULL, NULL, 450.000, 0.000, 450.000, 535.500, '2026-03-16'),
(25991, 19643, 'INV-20260316-042211', 'PRD006', 'External SSD 1TB', 1.000, 19.000, 85.500, NULL, NULL, NULL, NULL, 450.000, 0.000, 450.000, 535.500, '2026-03-16'),
(25992, 19643, 'INV-20260316-042211', 'PRD006', 'External SSD 1TB', 1.000, 19.000, 85.500, NULL, NULL, NULL, NULL, 450.000, 0.000, 450.000, 535.500, '2026-03-16'),
(25993, 19643, 'INV-20260316-042211', 'PRD006', 'External SSD 1TB', 1.000, 19.000, 85.500, NULL, NULL, NULL, NULL, 450.000, 0.000, 450.000, 535.500, '2026-03-16'),
(25994, 19643, 'INV-20260316-042211', 'PRD006', 'External SSD 1TB', 1.000, 19.000, 85.500, NULL, NULL, NULL, NULL, 450.000, 0.000, 450.000, 535.500, '2026-03-16'),
(25995, 19643, 'INV-20260316-042211', 'PRD006', 'External SSD 1TB', 1.000, 19.000, 85.500, NULL, NULL, NULL, NULL, 450.000, 0.000, 450.000, 535.500, '2026-03-16'),
(25996, 19643, 'INV-20260316-042211', 'PRD006', 'External SSD 1TB', 1.000, 19.000, 85.500, NULL, NULL, NULL, NULL, 450.000, 0.000, 450.000, 535.500, '2026-03-16'),
(25997, 19643, 'INV-20260316-042211', 'PRD006', 'External SSD 1TB', 1.000, 19.000, 85.500, NULL, NULL, NULL, NULL, 450.000, 0.000, 450.000, 535.500, '2026-03-16'),
(25998, 19643, 'INV-20260316-042211', 'PRD006', 'External SSD 1TB', 1.000, 19.000, 85.500, NULL, NULL, NULL, NULL, 450.000, 0.000, 450.000, 535.500, '2026-03-16'),
(25999, 19643, 'INV-20260316-042211', 'PRD006', 'External SSD 1TB', 1.000, 19.000, 85.500, NULL, NULL, NULL, NULL, 450.000, 0.000, 450.000, 535.500, '2026-03-16'),
(26000, 19643, 'INV-20260316-042211', 'PRD006', 'External SSD 1TB', 1.000, 19.000, 85.500, NULL, NULL, NULL, NULL, 450.000, 0.000, 450.000, 535.500, '2026-03-16'),
(26001, 19647, 'INV-20260316-054623', 'PRD006', 'External SSD 1TB', 1.000, 19.000, 85.500, NULL, NULL, NULL, NULL, 450.000, 0.000, 450.000, 535.500, '2026-03-16'),
(26002, 19647, 'INV-20260316-054623', 'PRD006', 'External SSD 1TB', 1.000, 19.000, 85.500, NULL, NULL, NULL, NULL, 450.000, 0.000, 450.000, 535.500, '2026-03-16'),
(26003, 19648, 'INV-20260316-055508', 'PRD006', 'External SSD 1TB', 1.000, 19.000, 85.500, NULL, NULL, NULL, NULL, 450.000, 0.000, 450.000, 535.500, '2026-03-16'),
(26004, 19649, 'INV-20260316-055856', 'PRD006', 'External SSD 1TB', 1.000, 19.000, 85.500, NULL, NULL, NULL, NULL, 450.000, 0.000, 450.000, 535.500, '2026-03-16'),
(26007, 19650, 'INV-20260316-055917', 'PRD006', 'External SSD 1TB', 1.000, 19.000, 85.500, NULL, NULL, NULL, NULL, 450.000, 0.000, 450.000, 535.500, '2026-03-16'),
(26008, 19651, 'INV-20260316-064851', 'PRD006', 'External SSD 1TB', 1.000, 19.000, 85.500, NULL, NULL, NULL, NULL, 450.000, 0.000, 450.000, 535.500, '2026-03-16'),
(26009, 19652, 'INV-20260316-094554', 'PRD003', 'Keyboard Logitech', 1.000, 19.000, 15.200, NULL, NULL, NULL, NULL, 80.000, 0.000, 80.000, 95.200, '2026-03-16'),
(26010, 19653, 'INV-20260316-101951', 'PRD003', 'Keyboard Logitech', 1.000, 19.000, 15.200, NULL, NULL, NULL, NULL, 80.000, 0.000, 80.000, 95.200, '2026-03-16'),
(26011, 19653, 'INV-20260316-101951', 'PRD003', 'Keyboard Logitech', 1.000, 19.000, 15.200, NULL, NULL, NULL, NULL, 80.000, 0.000, 80.000, 95.200, '2026-03-16');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `code` varchar(100) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(20,3) NOT NULL DEFAULT 0.000,
  `tva_rate` decimal(10,3) NOT NULL DEFAULT 0.000,
  `unit` varchar(100) DEFAULT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `code`, `name`, `price`, `tva_rate`, `unit`, `user_id`) VALUES
(1, 'PRD006', 'External SSD 1TB', 450.000, 19.000, 'piece', 4),
(29, 'prc123', 'produit', 1200.000, 19.000, '', 1),
(37, 'PRD001', 'Laptop Dell Inspiron', 2500.000, 19.000, 'piece', 1),
(38, 'PRD002', 'Monitor Samsung 24\"', 650.000, 19.000, 'piece', 1),
(39, 'PRD003', 'Keyboard Logitech', 80.000, 19.000, 'piece', 1),
(40, 'PRD004', 'Office Chair', 350.000, 19.000, 'piece', 1),
(41, 'PRD005', 'Printer HP LaserJet', 900.000, 19.000, 'piece', 1),
(43, 'prp', 'hi', 199.000, 19.000, '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `organization_name` varchar(191) DEFAULT NULL,
  `fiscal_id` varchar(20) NOT NULL,
  `email` varchar(191) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `fax` varchar(50) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `organization_name`, `fiscal_id`, `email`, `phone`, `password_hash`, `created_at`, `updated_at`, `fax`, `address`, `website`) VALUES
(1, 'admin', '1111111AAA111', 'admin@gmail.com', '+216 55555555', '$2y$10$OI.9eTslBqnbuLSXI1nbdeb/UvkiYT/eYkCyP4prCUcjbXpNp1Obe', '2026-03-16 07:53:51', '2026-03-16 08:44:17', '01 01 01 01 01', '7 rue de mars', 'www.admin.com'),
(4, 'testing', '1234567ABC123', 'test@gmail.com', '+21651170669', '$2y$10$9ozOCRYsvwU7yEo.Z5j5pOZ4PePYXby/FoFO4lsKC5AR4nohI3erK', '2026-03-10 08:49:51', '2026-03-16 07:56:01', NULL, NULL, NULL),
(13, 'Alpha Consulting SARL', '1111111ABC111', 'alpha@company.tn', '+216 70 000 001', '$2y$10$examplehash1', '2026-03-16 02:35:28', '2026-03-16 02:35:28', NULL, NULL, NULL),
(14, 'MedTech Solutions', '2222222ABC222', 'contact@medtech.tn', '+216 70 000 002', '$2y$10$examplehash2', '2026-03-16 02:35:28', '2026-03-16 02:35:28', NULL, NULL, NULL),
(15, 'Digital Tunisia', '3333333ABC333', 'info@digital.tn', '+216 70 000 003', '$2y$10$examplehash3', '2026-03-16 02:35:28', '2026-03-16 02:35:28', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_tokens`
--

CREATE TABLE `user_tokens` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token_hash` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_clients_user` (`user_id`);

--
-- Indexes for table `erp_invoices`
--
ALTER TABLE `erp_invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_erp_invoices_invoice` (`invoice`),
  ADD KEY `idx_erp_invoices_date` (`invoice_date`),
  ADD KEY `idx_erp_invoices_status` (`status`),
  ADD KEY `idx_erp_invoices_stat_ttn` (`stat_ttn`),
  ADD KEY `idx_erp_invoices_stat_api` (`stat_api`),
  ADD KEY `idx_erp_invoices_date_stat_ttn` (`invoice_date`,`stat_ttn`),
  ADD KEY `idx_erp_invoices_date_stat_api` (`invoice_date`,`stat_api`),
  ADD KEY `fk_invoice_user` (`user_id`);

--
-- Indexes for table `erp_invoice_items`
--
ALTER TABLE `erp_invoice_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_items_invoice_id` (`invoice_id`),
  ADD KEY `idx_items_invoice` (`invoice`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_products_user` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_tokens`
--
ALTER TABLE `user_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_tokens_user` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=125;

--
-- AUTO_INCREMENT for table `erp_invoices`
--
ALTER TABLE `erp_invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19654;

--
-- AUTO_INCREMENT for table `erp_invoice_items`
--
ALTER TABLE `erp_invoice_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26012;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `user_tokens`
--
ALTER TABLE `user_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `clients`
--
ALTER TABLE `clients`
  ADD CONSTRAINT `fk_clients_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `erp_invoices`
--
ALTER TABLE `erp_invoices`
  ADD CONSTRAINT `fk_invoice_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `erp_invoice_items`
--
ALTER TABLE `erp_invoice_items`
  ADD CONSTRAINT `fk_items_invoice` FOREIGN KEY (`invoice_id`) REFERENCES `erp_invoices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_products_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_tokens`
--
ALTER TABLE `user_tokens`
  ADD CONSTRAINT `fk_user_tokens_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
