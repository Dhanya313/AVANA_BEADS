-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 03, 2026 at 03:39 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `avana_beads`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `billing`
--

CREATE TABLE `billing` (
  `bill_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `bill_date` date DEFAULT curdate(),
  `total_price` decimal(10,2) DEFAULT NULL,
  `payment_status` varchar(50) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `billing`
--

INSERT INTO `billing` (`bill_id`, `order_id`, `bill_date`, `total_price`, `payment_status`, `payment_method`) VALUES
(1, 26, '2026-02-27', 150.00, 'Pending', 'Cash on Delivery'),
(2, 27, '2026-02-27', 300.00, 'Pending', 'Cash on Delivery'),
(3, 28, '2026-02-28', 150.00, 'Paid', 'Cash on Delivery'),
(4, 29, '2026-02-28', 150.00, 'Paid', 'Cash on Delivery'),
(5, 30, '2026-02-28', 100.00, 'Pending', 'Cash on Delivery'),
(6, 31, '2026-02-28', 100.00, 'Pending', 'Cash on Delivery'),
(7, 32, '2026-02-28', 350.00, 'Pending', 'Cash on Delivery'),
(8, 33, '2026-02-28', 50.00, 'Pending', 'Cash on Delivery'),
(9, 34, '2026-02-28', 50.00, 'Pending', 'Cash on Delivery'),
(10, 35, '2026-02-28', 150.00, 'Pending', 'Cash on Delivery'),
(11, 36, '2026-02-28', 300.00, 'Paid', 'Cash on Delivery');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `cust_id` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`cart_id`, `cust_id`, `created_date`) VALUES
(1, 1, '2026-02-21 22:36:20'),
(2, 2, '2026-02-22 20:06:03'),
(3, 4, '2026-02-23 13:00:33'),
(4, 5, '2026-02-23 20:31:27'),
(5, 6, '2026-02-27 15:15:50');

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `cart_item_id` int(11) NOT NULL,
  `cart_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `customization_text` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart_items`
--

INSERT INTO `cart_items` (`cart_item_id`, `cart_id`, `product_id`, `quantity`, `customization_text`) VALUES
(21, 1, 1, 2, '');

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `category_id` int(11) NOT NULL,
  `category_code` varchar(20) NOT NULL,
  `category_name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`category_id`, `category_code`, `category_name`) VALUES
(14, 'BC', 'Bag Chain'),
(15, 'CB', 'Chain Bracelets'),
(16, 'CR', 'Crystal Bracelets'),
(17, 'ER', 'Earrings'),
(18, 'FB', 'Flat Beads'),
(19, 'KC', 'Keychains'),
(20, 'NC', 'Neck Chains'),
(21, 'PB', 'Pearl Bracelets'),
(22, 'PC', 'Phone Charms'),
(23, 'SB', 'Small Beaded Bracelets'),
(25, 'TB001', 'Thread Bracelets');

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `cust_id` int(11) NOT NULL,
  `cust_name` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `contact` varchar(15) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `role` enum('admin','customer') DEFAULT 'customer'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`cust_id`, `cust_name`, `email`, `password`, `contact`, `address`, `role`) VALUES
(1, 'Dhanya C', 'dhanya123@gmail.com', 'dhanya2005', '7483872573', 'sri laxmi,post office road,kavoor', 'customer'),
(2, 'anu', 'anu123@gmail.com', 'anu2005', '7019441675', 'bejai', 'customer'),
(3, 'Shanvi H', 'shanvi2026@gmail.com', 'SHAANVI@2026', '8197019837', 'CBD Cluster, International City, Dubai', 'admin'),
(4, 'Jyo', 'jyostnasalian@gmail.com', 'jyogonemad', '9663637098', 'Mangalore', 'customer'),
(5, 'TINA  MARIA', 'tina@gmail.com', 'tina@123', '8523697416', 'PVS', 'customer'),
(6, 'JAI', 'jai@gmail.com', 'jai@123', '7410325698', 'Madyar', 'customer');

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `inventory_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `stock_quantity` int(11) DEFAULT NULL,
  `last_updated` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`inventory_id`, `product_id`, `stock_quantity`, `last_updated`) VALUES
(1, 1, 44, '2026-02-28 12:42:42'),
(2, 2, 2, '2026-02-28 12:42:54'),
(3, 3, 47, '2026-02-28 12:43:04'),
(4, 9, 5, '2026-02-28 12:51:58');

-- --------------------------------------------------------

--
-- Table structure for table `offers`
--

CREATE TABLE `offers` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `discount_percent` int(11) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `offers`
--

INSERT INTO `offers` (`id`, `title`, `description`, `discount_percent`, `start_date`, `end_date`, `status`, `created_at`) VALUES
(1, 'Midnight Sale', 'Flat 25% on all premium beads', 25, '2026-02-15', '2026-02-16', 'active', '2026-02-15 09:23:27');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `cust_id` int(11) DEFAULT NULL,
  `order_date` date DEFAULT curdate(),
  `order_status` varchar(50) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_status` varchar(50) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `cust_id`, `order_date`, `order_status`, `payment_method`, `payment_status`, `total_amount`) VALUES
(11, 1, '2026-02-19', 'Processing', 'Cash on Delivery', 'Paid', 100.00),
(12, 1, '2026-02-21', 'Pending', 'Cash on Delivery', 'Pending', 150.00),
(13, 1, '2026-02-21', 'Pending', 'Cash on Delivery', 'Pending', 100.00),
(14, 1, '2026-02-21', 'Completed', 'Cash on Delivery', 'Paid', 400.00),
(15, 1, '2026-02-21', 'Pending', 'Cash on Delivery', 'Pending', 350.00),
(16, 2, '2026-02-22', 'Pending', 'Cash on Delivery', 'Pending', 50.00),
(17, 2, '2026-02-22', 'Pending', 'Cash on Delivery', 'Pending', 50.00),
(18, 4, '2026-02-23', 'Pending', 'Cash on Delivery', 'Pending', 150.00),
(19, 4, '2026-02-23', 'Pending', 'Cash on Delivery', 'Pending', 200.00),
(20, 5, '2026-02-23', 'Shipped', 'Cash on Delivery', 'Pending', 150.00),
(21, 5, '2026-02-24', 'Completed', 'Cash on Delivery', 'Pending', 200.00),
(22, 5, '2026-02-24', 'Pending', 'Cash on Delivery', 'Pending', 300.00),
(23, 5, '2026-02-25', 'Completed', 'Cash on Delivery', 'Paid', 300.00),
(24, 1, '2026-02-26', 'Pending', 'Cash on Delivery', 'Pending', 300.00),
(26, 6, '2026-02-27', 'Completed', 'Cash on Delivery', 'Paid', 150.00),
(27, 6, '2026-02-27', 'Completed', 'Cash on Delivery', 'Paid', 300.00),
(28, 6, '2026-02-28', 'Completed', 'Cash on Delivery', 'Paid', 150.00),
(29, 5, '2026-02-28', 'Completed', 'Cash on Delivery', 'Paid', 150.00),
(30, 5, '2026-02-28', 'Pending', 'Cash on Delivery', 'Pending', 100.00),
(31, 5, '2026-02-28', 'Pending', 'Cash on Delivery', 'Pending', 100.00),
(32, 2, '2026-02-28', 'Pending', 'Cash on Delivery', 'Pending', 350.00),
(33, 2, '2026-02-28', 'Pending', 'Cash on Delivery', 'Pending', 50.00),
(34, 2, '2026-02-28', 'Pending', 'Cash on Delivery', 'Pending', 50.00),
(35, 2, '2026-02-28', 'Pending', 'Cash on Delivery', 'Pending', 150.00),
(36, 2, '2026-02-28', 'Processing', 'Cash on Delivery', 'Paid', 300.00);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `customization_text` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `product_id`, `quantity`, `price`, `customization_text`) VALUES
(11, 11, 1, 1, 100.00, ''),
(12, 12, 1, 1, 100.00, 'name'),
(13, 12, 2, 1, 50.00, ''),
(14, 13, 1, 1, 100.00, ''),
(15, 14, 1, 4, 100.00, ''),
(16, 15, 1, 3, 100.00, ''),
(17, 15, 3, 1, 50.00, ''),
(18, 16, 2, 1, 50.00, ''),
(19, 17, 2, 1, 50.00, 'aaaaaaaaaaaa'),
(20, 18, 2, 2, 50.00, ''),
(21, 18, 4, 1, 50.00, ''),
(22, 19, 1, 1, 200.00, ''),
(23, 20, 5, 1, 150.00, 'tina'),
(24, 21, 1, 1, 200.00, ''),
(25, 22, 1, 2, 150.00, ''),
(26, 23, 1, 2, 150.00, ''),
(27, 24, 1, 2, 150.00, 'dfgg'),
(28, 26, 1, 1, 150.00, 'jai'),
(29, 27, 1, 2, 150.00, 'sd'),
(30, 28, 1, 1, 150.00, ''),
(31, 29, 1, 1, 150.00, ''),
(32, 30, 9, 1, 100.00, ''),
(33, 31, 9, 1, 100.00, ''),
(34, 32, 1, 2, 150.00, ''),
(35, 32, 3, 1, 50.00, ''),
(36, 33, 3, 1, 50.00, ''),
(37, 34, 3, 1, 50.00, ''),
(38, 35, 1, 1, 150.00, ''),
(39, 36, 1, 2, 150.00, '');

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `product_id` int(11) NOT NULL,
  `product_code` varchar(30) NOT NULL,
  `product_name` varchar(100) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `is_new` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`product_id`, `product_code`, `product_name`, `category_id`, `price`, `description`, `image`, `is_new`) VALUES
(1, 'PB001', 'Classic White ', 21, 150.00, 'Elegant white pearl bracelet for daily wear with customized name', 'pearl_b1.jpeg', 0),
(2, 'PB002', 'Pink Pearl Charm Bracelet', 21, 50.00, 'Soft pink pearls with charm detail', 'pearl_b2.jpeg', 0),
(3, 'PB003', 'Golden Pearl Designer Bracelet', 21, 50.00, 'Premium golden pearl handcrafted bracelet', 'pearl_b3.jpeg', 0),
(4, 'PB004', 'Mini Pearl Beaded Bracelet', 21, 50.00, 'Minimal small pearl bracelet for casual style', 'pearl_b4.jpeg', 0),
(5, 'PB005', 'Locket Pearl Bracelet', 21, 150.00, 'Minimal small pearl bracelet for casual style', 'pearl_b5.jpeg', 0),
(6, 'PB006', 'Multi Charm Pearl Bracelet', 21, 200.00, 'Minimal small pearl bracelet for casual style', 'pearl_b6.jpeg', 0),
(9, 'BC001', 'Chain for bag', 14, 100.00, 'asdfgh', 'bag_chain1.jpeg', 0);

-- --------------------------------------------------------

--
-- Table structure for table `product_feedback`
--

CREATE TABLE `product_feedback` (
  `feedback_id` int(11) NOT NULL,
  `cust_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` between 1 and 5),
  `feedback_text` text DEFAULT NULL,
  `feedback_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `sales_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `sales_date` date DEFAULT curdate(),
  `quantity_sold` int(11) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`sales_id`, `order_id`, `sales_date`, `quantity_sold`, `total_price`) VALUES
(1, 11, '2026-02-27', 1, 100.00),
(2, 26, '2026-02-27', 1, 150.00),
(3, 14, '2026-02-28', 4, 400.00),
(4, 28, '2026-02-28', 1, 150.00),
(5, 23, '2026-02-28', 2, 300.00),
(6, 29, '2026-02-28', 1, 150.00),
(7, 36, '2026-02-28', 2, 300.00);

-- --------------------------------------------------------

--
-- Table structure for table `tracking`
--

CREATE TABLE `tracking` (
  `tracking_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `delivery_status` varchar(50) DEFAULT NULL,
  `expected_delivery_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tracking`
--

INSERT INTO `tracking` (`tracking_id`, `order_id`, `delivery_status`, `expected_delivery_date`) VALUES
(1, 26, 'Order Placed', '2026-03-04'),
(2, 27, 'Order Placed', '2026-03-04'),
(3, 28, 'Order Placed', '2026-03-05'),
(4, 29, 'Order Placed', '2026-03-05'),
(5, 30, 'Order Placed', '2026-03-05'),
(6, 31, 'Order Placed', '2026-03-05'),
(7, 32, 'Order Placed', '2026-03-05'),
(8, 33, 'Order Placed', '2026-03-05'),
(9, 34, 'Order Placed', '2026-03-05'),
(10, 35, 'Order Placed', '2026-03-05'),
(11, 36, 'Order Placed', '2026-03-05');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `billing`
--
ALTER TABLE `billing`
  ADD PRIMARY KEY (`bill_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `cust_id` (`cust_id`);

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`cart_item_id`),
  ADD KEY `cart_id` (`cart_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `category_code` (`category_code`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`cust_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`inventory_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `offers`
--
ALTER TABLE `offers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `cust_id` (`cust_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`product_id`),
  ADD UNIQUE KEY `product_code` (`product_code`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `product_feedback`
--
ALTER TABLE `product_feedback`
  ADD PRIMARY KEY (`feedback_id`),
  ADD KEY `cust_id` (`cust_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`sales_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `tracking`
--
ALTER TABLE `tracking`
  ADD PRIMARY KEY (`tracking_id`),
  ADD KEY `order_id` (`order_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `billing`
--
ALTER TABLE `billing`
  MODIFY `bill_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `cart_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `cust_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `inventory_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `offers`
--
ALTER TABLE `offers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `product_feedback`
--
ALTER TABLE `product_feedback`
  MODIFY `feedback_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `sales_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tracking`
--
ALTER TABLE `tracking`
  MODIFY `tracking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `billing`
--
ALTER TABLE `billing`
  ADD CONSTRAINT `billing_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE;

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`cust_id`) REFERENCES `customer` (`cust_id`) ON DELETE CASCADE;

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`cart_id`) REFERENCES `cart` (`cart_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `inventory`
--
ALTER TABLE `inventory`
  ADD CONSTRAINT `inventory_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`cust_id`) REFERENCES `customer` (`cust_id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `product_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`) ON DELETE CASCADE;

--
-- Constraints for table `product_feedback`
--
ALTER TABLE `product_feedback`
  ADD CONSTRAINT `product_feedback_ibfk_1` FOREIGN KEY (`cust_id`) REFERENCES `customer` (`cust_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_feedback_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `sales_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE;

--
-- Constraints for table `tracking`
--
ALTER TABLE `tracking`
  ADD CONSTRAINT `tracking_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
