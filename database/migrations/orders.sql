CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reference` varchar(50) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `address` varchar(255) NOT NULL,
  `mobile_number` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `date_time` varchar(255) NOT NULL,
  `gross_amount` varchar(255) NOT NULL,
  `net_amount` varchar(255) NOT NULL,
  `discount_amount` varchar(255) NOT NULL,
  `is_paid` int(11) NOT NULL,
  `remarks` text DEFAULT NULL,
  `order_status` enum('completed','cancelled') DEFAULT 'completed',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8