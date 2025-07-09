-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 09, 2025 at 05:22 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `deliverysystem`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `admin_username` varchar(50) DEFAULT NULL,
  `admin_password` varchar(255) DEFAULT NULL,
  `admin_email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `admin_username`, `admin_password`, `admin_email`) VALUES
(4, 'admin', '$2y$10$v2E4Ow4u5GOlO.jkTsNNWOMuqYCiC49lQyvzcoQfbehssIdV3Qi6y', 'admin@rimbunancafe.com'),
(5, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@rimbunancafe.com');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(50) NOT NULL,
  `category_icon` varchar(10) DEFAULT '?️',
  `category_status` enum('Active','Inactive') DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`, `category_icon`, `category_status`, `created_at`) VALUES
(1, 'CONTINENTAL', '🍽️', 'Active', '2025-07-09 14:29:38'),
(2, 'SOUP', '🍲', 'Active', '2025-07-09 14:29:38'),
(3, 'ASIAN', '🥢', 'Active', '2025-07-09 14:29:38'),
(4, 'SNACK', '🍿', 'Active', '2025-07-09 14:29:38'),
(5, 'HOT DRINKS', '☕', 'Active', '2025-07-09 14:29:38'),
(6, 'ICE DRINKS', '🧊', 'Active', '2025-07-09 14:29:38'),
(7, 'SPARKLING DRINKS', '🥤', 'Active', '2025-07-09 14:29:38'),
(8, 'MILK SHAKE', '🥛', 'Active', '2025-07-09 14:29:38'),
(9, 'ICE TEA SERIES', '🧋', 'Active', '2025-07-09 14:29:38'),
(10, 'FRAPPE', '🥤', 'Active', '2025-07-09 14:29:38');

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `cust_id` int(11) NOT NULL,
  `cust_username` varchar(50) DEFAULT NULL,
  `cust_password` varchar(255) DEFAULT NULL,
  `cust_phonenumber` varchar(20) DEFAULT NULL,
  `cust_email` varchar(100) DEFAULT NULL,
  `cust_address` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`cust_id`, `cust_username`, `cust_password`, `cust_phonenumber`, `cust_email`, `cust_address`) VALUES
(1, 'azhar', '$2y$10$pNmRVuEIgvkzKLtwnaGYvOUqHPyybthyGxOOui8qnFv4i58Sownpi', '01164979233', 'ali@gmail.com', 'No. 88, Jalan Cempaka 3,\r\nTaman Pekan Baru,\r\n08000 Sungai Petani,\r\nKedah Darul Aman.'),
(2, 'pidi', '$2y$10$APWRy9b.qvr98yYx.neJ6eFPCx4azu5HFe23dIV0e3e95xn98TvvK', '0173926485', 'pidiusam@gmail.com', 'No. 56, Lorong Permatang 2,\r\nTaman Ria Jaya,\r\n08000 Sungai Petani,\r\nKedah Darul Aman.');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `cust_id` int(11) DEFAULT NULL,
  `rider_id` int(11) DEFAULT NULL,
  `order_date` datetime DEFAULT NULL,
  `order_status` varchar(50) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `delivery_status` int(11) DEFAULT NULL,
  `payment_method` varchar(10) DEFAULT 'cod',
  `delivery_address` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `admin_id`, `cust_id`, `rider_id`, `order_date`, `order_status`, `total_price`, `delivery_status`, `payment_method`, `delivery_address`) VALUES
(4, NULL, 1, NULL, '2025-07-09 05:21:26', 'Delivered', 7.00, 2, 'cod', NULL),
(5, NULL, 1, 2, '2025-07-09 09:05:11', 'Delivered', 7.00, 2, 'cod', NULL),
(6, NULL, 1, 2, '2025-07-09 09:05:13', 'Delivered', 7.00, 2, 'cod', NULL),
(7, NULL, 1, 2, '2025-07-09 09:19:11', 'Delivered', 27.00, 2, 'cod', NULL),
(8, NULL, 1, 2, '2025-07-09 09:22:42', 'Delivered', 7.00, 2, 'cod', NULL),
(11, NULL, 1, 2, '2025-07-09 10:50:40', 'Preparing', 20.00, 2, 'cod', NULL),
(12, NULL, 1, NULL, '2025-07-09 21:10:57', 'Preparing', 59.00, 0, 'cod', NULL),
(13, NULL, 2, 2, '2025-07-09 21:14:31', 'Preparing', 54.00, 0, 'cod', NULL),
(14, NULL, 1, 2, '2025-07-09 21:16:40', 'Preparing', 28.00, 0, 'cod', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE `order_details` (
  `details_id` int(11) NOT NULL,
  `orders_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_details`
--

INSERT INTO `order_details` (`details_id`, `orders_id`, `product_id`, `qty`) VALUES
(5, 5, 1, 1),
(6, 6, 1, 1),
(7, 7, 2, 2),
(8, 7, 1, 1),
(9, 8, 1, 1),
(17, 13, 7, 1),
(18, 13, 9, 2),
(19, 13, 10, 1),
(20, 14, 5, 1);

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(100) DEFAULT NULL,
  `product_info` text DEFAULT NULL,
  `product_price` decimal(10,2) DEFAULT NULL,
  `product_status` varchar(50) DEFAULT NULL,
  `product_image` varchar(255) DEFAULT NULL,
  `product_category` varchar(20) DEFAULT 'food'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`product_id`, `product_name`, `product_info`, `product_price`, `product_status`, `product_image`, `product_category`) VALUES
(1, 'burger', 'burger', 7.00, 'Unavailable', 'uploads/products/686dc27cc2a05_1752023676.png', 'CONTINENTAL'),
(2, 'rimbunan', 'real', 10.00, 'Unavailable', 'uploads/products/686dc23c50b5e_1752023612.png', 'CONTINENTAL'),
(4, 'Grilled Chicken  ', 'Roasted Chicken Breast, asparagus, roasted pumpkin,  \r\nmix vegetables and Black Pepper Sauce ', 21.00, 'Available', 'uploads/products/686e64bf6229d_1752065215.jpg', 'CONTINENTAL'),
(5, 'Chicken Chop ', 'Deep fried chicken chop, roasted pumpkin,  \r\nmix vegetables and Black Pepper Sauce', 18.00, 'Available', 'uploads/products/686e64f4c9134_1752065268.jpg', 'CONTINENTAL'),
(6, 'Fish & Chips', 'Deep fried Talapia, mix vegetables,  \r\nhomemade fries & Tartar Sauce  ', 20.00, 'Available', 'uploads/products/686e6529cf9f8_1752065321.jpg', 'CONTINENTAL'),
(7, 'Creamy Mushroom Soup ', 'Served with toasted garlic bread  ', 12.00, 'Available', 'uploads/products/686e65e3741a6_1752065507.jpg', 'CONTINENTAL'),
(8, 'Grilled Lamb Chop  ', 'Grilled Lamb Shoulder, roasted pumpkin,  \r\nmix vegetables and Black Pepper Sauce ', 38.00, 'Available', 'uploads/products/686e660918c47_1752065545.jpg', 'CONTINENTAL'),
(9, 'Thai Fried Rice', 'Fried rice, prawns, chicken, squid, lettuce, cracker, \r\nspring onion and fried shallot.  \r\n*Spicy Level 2  ', 11.00, 'Available', 'uploads/products/686e669f80c68_1752065695.jpg', 'CONTINENTAL'),
(10, 'Chicken Popcorn ', 'Deep fry batter chicken with sauce ', 10.00, 'Available', 'uploads/products/686e67100e24b_1752065808.jpg', 'CONTINENTAL'),
(11, 'Deep fry butter chicken with sauce ', 'Steamed rice, buttermilk chicken, lettuce,  \r\ncracker, spring onion and fried shallot  ', 15.00, 'Available', 'uploads/products/686e675551c4a_1752065877.jpg', 'CONTINENTAL'),
(12, 'Fries Homemade', 'Bowl of homemade fries and sauce ', 8.00, 'Available', 'uploads/products/686e678a2fce2_1752065930.jpg', 'CONTINENTAL'),
(13, 'Cappuccino', 'Hot Cappuccino', 7.00, 'Available', 'uploads/products/686e67db9e31f_1752066011.jpg', 'CONTINENTAL'),
(14, 'Ice Americano', 'Ice Americano', 6.90, 'Available', 'uploads/products/686e6811291ff_1752066065.jpg', 'CONTINENTAL'),
(15, 'Caramel Machiato', 'iced Caramel Machiato', 9.90, 'Available', 'uploads/products/686e68714e79c_1752066161.jpg', 'CONTINENTAL'),
(16, 'Premium Chocolate', 'iced Premium Chocolate', 7.90, 'Available', 'uploads/products/686e689ddaa14_1752066205.jpg', 'CONTINENTAL'),
(17, 'Ice Green Tea Matcha ', 'Ice Green Tea Matcha ', 8.00, 'Available', 'uploads/products/686e68c25ae43_1752066242.jpg', 'CONTINENTAL'),
(18, 'Ice Green Tea Matcha ', 'Ice Green Tea Matcha ', 8.00, 'Available', 'uploads/products/686e68e569f94_1752066277.jpg', 'CONTINENTAL'),
(19, 'Classic Burger', 'Juicy beef patty with lettuce, tomato, and special sauce', 15.90, 'Available', NULL, 'CONTINENTAL'),
(20, 'Chicken Burger', 'Grilled chicken breast with fresh vegetables', 14.90, 'Available', NULL, 'CONTINENTAL'),
(21, 'Fish & Chips', 'Crispy battered fish with golden fries', 18.90, 'Available', NULL, 'CONTINENTAL'),
(22, 'Pasta Carbonara', 'Creamy pasta with bacon and parmesan cheese', 16.90, 'Available', NULL, 'CONTINENTAL'),
(23, 'Iced Coffee', 'Refreshing cold brew coffee with ice', 8.90, 'Available', NULL, 'ICE DRINKS'),
(24, 'Fresh Orange Juice', 'Freshly squeezed orange juice', 7.90, 'Available', NULL, 'ICE DRINKS'),
(25, 'Chocolate Milkshake', 'Rich chocolate milkshake with whipped cream', 9.90, 'Available', NULL, 'ICE DRINKS'),
(26, 'Green Tea', 'Premium green tea served hot or cold', 6.90, 'Available', NULL, 'ICE DRINKS');

-- --------------------------------------------------------

--
-- Table structure for table `rider`
--

CREATE TABLE `rider` (
  `rider_id` int(11) NOT NULL,
  `rider_username` varchar(50) DEFAULT NULL,
  `rider_password` varchar(255) DEFAULT NULL,
  `rider_status` int(11) DEFAULT NULL,
  `rider_phonenumber` varchar(20) DEFAULT NULL,
  `rider_vehicleinfo` varchar(100) DEFAULT NULL,
  `rider_email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rider`
--

INSERT INTO `rider` (`rider_id`, `rider_username`, `rider_password`, `rider_status`, `rider_phonenumber`, `rider_vehicleinfo`, `rider_email`) VALUES
(2, 'kim', '$2y$10$7gO5Jw4MBNI0xXdNDPH91.ze8f1jgvs7ZuKJwcR1huSLarNtrkTaW', 1, '0111545879', 'wave500', 'kim@gmail.com'),
(3, 'usam', '$2y$10$OkhzxYIFRFlBGBmkWnJ4GeybuUQHnBMugW6FMUNWQY94pQkNXbxxe', 1, '021213145', 'honda', 'sam@gmail.com'),
(4, 'Mike Rider', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, '0123456789', 'Honda Wave 125, ABC1234', 'rider@rimbunancafe.com');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `staff_id` int(11) NOT NULL,
  `staff_name` varchar(100) DEFAULT NULL,
  `staff_email` varchar(100) DEFAULT NULL,
  `staff_password` varchar(255) DEFAULT NULL,
  `staff_phonenumber` varchar(20) DEFAULT NULL,
  `orders_id` int(11) DEFAULT NULL,
  `status_updated` varchar(50) DEFAULT NULL,
  `assigned_rider_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`staff_id`, `staff_name`, `staff_email`, `staff_password`, `staff_phonenumber`, `orders_id`, `status_updated`, `assigned_rider_id`) VALUES
(1, 'kevin', 'kevindebruine@gmail.com', '$2y$10$1i6rTrC8ktIzHfmwytmofurHHZOfhdA9tjR.2xtwnAO16hsHdjtc.', '0123456789', 14, 'Preparing', 2),
(2, 'John Staff', 'staff@rimbunancafe.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0123456789', NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `category_name` (`category_name`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`cust_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `admin_id` (`admin_id`),
  ADD KEY `cust_id` (`cust_id`),
  ADD KEY `rider_id` (`rider_id`);

--
-- Indexes for table `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`details_id`),
  ADD KEY `orders_id` (`orders_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `rider`
--
ALTER TABLE `rider`
  ADD PRIMARY KEY (`rider_id`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`staff_id`),
  ADD KEY `orders_id` (`orders_id`),
  ADD KEY `assigned_rider_id` (`assigned_rider_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `cust_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `order_details`
--
ALTER TABLE `order_details`
  MODIFY `details_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `rider`
--
ALTER TABLE `rider`
  MODIFY `rider_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `staff_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`admin_id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`cust_id`) REFERENCES `customer` (`cust_id`),
  ADD CONSTRAINT `orders_ibfk_3` FOREIGN KEY (`rider_id`) REFERENCES `rider` (`rider_id`);

--
-- Constraints for table `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`orders_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `order_details_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`);

--
-- Constraints for table `staff`
--
ALTER TABLE `staff`
  ADD CONSTRAINT `staff_ibfk_1` FOREIGN KEY (`orders_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `staff_ibfk_2` FOREIGN KEY (`assigned_rider_id`) REFERENCES `rider` (`rider_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
