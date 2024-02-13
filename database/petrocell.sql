/*
Navicat MySQL Data Transfer

Source Server         : KNinson
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : cylinder

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2021-05-03 11:43:55
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `failed_jobs`
-- ----------------------------
DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of failed_jobs
-- ----------------------------

-- ----------------------------
-- Table structure for `migrations`
-- ----------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of migrations
-- ----------------------------
INSERT INTO `migrations` VALUES ('1', '2014_10_12_000000_create_users_table', '1');
INSERT INTO `migrations` VALUES ('2', '2014_10_12_100000_create_password_resets_table', '1');
INSERT INTO `migrations` VALUES ('3', '2019_08_19_000000_create_failed_jobs_table', '1');
INSERT INTO `migrations` VALUES ('4', '2019_12_14_000001_create_personal_access_tokens_table', '1');

-- ----------------------------
-- Table structure for `password_resets`
-- ----------------------------
DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of password_resets
-- ----------------------------

-- ----------------------------
-- Table structure for `personal_access_tokens`
-- ----------------------------
DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of personal_access_tokens
-- ----------------------------

-- ----------------------------
-- Table structure for `tblcustomer`
-- ----------------------------
DROP TABLE IF EXISTS `tblcustomer`;
CREATE TABLE `tblcustomer` (
  `transid` varchar(50) NOT NULL,
  `custno` varchar(50) NOT NULL,
  `title` varchar(10) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `mname` varchar(255) DEFAULT NULL,
  `lname` varchar(255) NOT NULL,
  `gender` char(1) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `email` varchar(50) NOT NULL,
  `pob` varchar(50) DEFAULT NULL COMMENT 'Place of Birth',
  `marital_status` varchar(10) DEFAULT '',
  `occupation` varchar(255) DEFAULT NULL,
  `home_address` varchar(255) DEFAULT NULL,
  `landmark` varchar(255) DEFAULT NULL,
  `town` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `gpsaddress` varchar(50) DEFAULT NULL,
  `streetname` varchar(255) DEFAULT NULL,
  `region` varchar(50) NOT NULL,
  `id_type` varchar(50) DEFAULT NULL,
  `id_no` varchar(50) DEFAULT NULL,
  `id_link` varchar(25) DEFAULT NULL,
  `picture` varchar(50) DEFAULT NULL,
  `longitude` varchar(50) DEFAULT NULL,
  `latitude` varchar(50) DEFAULT NULL,
  `createuser` varchar(50) DEFAULT NULL,
  `createdate` datetime DEFAULT NULL,
  `modifyuser` varchar(50) DEFAULT NULL,
  `modifydate` datetime DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`transid`,`custno`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of tblcustomer
-- ----------------------------

-- ----------------------------
-- Table structure for `tblcustomer_cylinder`
-- ----------------------------
DROP TABLE IF EXISTS `tblcustomer_cylinder`;
CREATE TABLE `tblcustomer_cylinder` (
  `transid` varchar(50) NOT NULL,
  `custno` varchar(50) NOT NULL,
  `barcode` varchar(50) DEFAULT NULL,
  `cylcode` varchar(50) NOT NULL,
  `date_acquired` date NOT NULL,
  `vendor_no` varchar(50) NOT NULL,
  `status` int(11) NOT NULL COMMENT '0 implies returned, 1 implies not returned',
  `createuser` varchar(50) NOT NULL,
  `createdate` datetime NOT NULL,
  `modifyuser` varchar(50) DEFAULT NULL,
  `modifydate` datetime DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`transid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of tblcustomer_cylinder
-- ----------------------------

-- ----------------------------
-- Table structure for `tblcylinder`
-- ----------------------------
DROP TABLE IF EXISTS `tblcylinder`;
CREATE TABLE `tblcylinder` (
  `transid` varchar(50) NOT NULL,
  `owner` varchar(50) DEFAULT NULL,
  `barcode` varchar(50) NOT NULL,
  `cylcode` varchar(50) NOT NULL,
  `size` varchar(50) DEFAULT '',
  `weight` varchar(20) DEFAULT NULL,
  `initial_amount` varchar(50) DEFAULT NULL,
  `images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`images`)),
  `createuser` varchar(50) NOT NULL,
  `createdate` datetime NOT NULL,
  `modifyuser` varchar(50) DEFAULT NULL,
  `modifydate` datetime DEFAULT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`transid`,`cylcode`,`barcode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of tblcylinder
-- ----------------------------

-- ----------------------------
-- Table structure for `tblcylinder_condition`
-- ----------------------------
DROP TABLE IF EXISTS `tblcylinder_condition`;
CREATE TABLE `tblcylinder_condition` (
  `transid` varchar(50) NOT NULL,
  `description` varchar(255) NOT NULL,
  `createuser` varchar(50) NOT NULL,
  `createdate` datetime NOT NULL,
  `modifyuser` varchar(50) DEFAULT NULL,
  `modifydate` datetime DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of tblcylinder_condition
-- ----------------------------

-- ----------------------------
-- Table structure for `tblcylinder_part`
-- ----------------------------
DROP TABLE IF EXISTS `tblcylinder_part`;
CREATE TABLE `tblcylinder_part` (
  `transid` varchar(50) NOT NULL,
  `description` varchar(100) NOT NULL,
  `createdate` datetime NOT NULL,
  `createuser` varchar(50) NOT NULL,
  `modifydate` datetime DEFAULT NULL,
  `modifyuser` varchar(50) DEFAULT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`transid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of tblcylinder_part
-- ----------------------------

-- ----------------------------
-- Table structure for `tblcylinder_size`
-- ----------------------------
DROP TABLE IF EXISTS `tblcylinder_size`;
CREATE TABLE `tblcylinder_size` (
  `transid` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `createdate` datetime NOT NULL,
  `createuser` varchar(50) NOT NULL,
  `modifyuser` varchar(50) NOT NULL,
  `modifydate` datetime DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`transid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of tblcylinder_size
-- ----------------------------

-- ----------------------------
-- Table structure for `tbldiscount`
-- ----------------------------
DROP TABLE IF EXISTS `tbldiscount`;
CREATE TABLE `tbldiscount` (
  `transid` varchar(50) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `amount` float NOT NULL,
  `createuser` varchar(50) NOT NULL,
  `createdate` datetime NOT NULL,
  `modifyuser` varchar(50) DEFAULT NULL,
  `modifydate` datetime DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`transid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of tbldiscount
-- ----------------------------

-- ----------------------------
-- Table structure for `tbldispatch`
-- ----------------------------
DROP TABLE IF EXISTS `tbldispatch`;
CREATE TABLE `tbldispatch` (
  `transid` varchar(50) NOT NULL,
  `cylcode` varchar(50) NOT NULL,
  `vendor_no` varchar(50) NOT NULL,
  `createuser` varchar(50) NOT NULL,
  `createdate` datetime NOT NULL,
  `modifyuser` varchar(50) DEFAULT NULL,
  `modifydate` datetime DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`transid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of tbldispatch
-- ----------------------------

-- ----------------------------
-- Table structure for `tblexchange`
-- ----------------------------
DROP TABLE IF EXISTS `tblexchange`;
CREATE TABLE `tblexchange` (
  `transid` varchar(50) NOT NULL,
  `custno` varchar(50) NOT NULL,
  `vendor_no` varchar(50) NOT NULL,
  `barcode` varchar(50) DEFAULT NULL,
  `cylcode_old` varchar(50) NOT NULL,
  `cylcode_new` varchar(50) DEFAULT NULL,
  `litres` varchar(50) DEFAULT NULL,
  `cylcode_condition` varchar(50) DEFAULT NULL COMMENT 'FK on tblcylinder_condition',
  `createuser` varchar(50) NOT NULL,
  `createdate` datetime NOT NULL,
  `modifyuser` varchar(50) DEFAULT NULL,
  `modifydate` datetime DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`transid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of tblexchange
-- ----------------------------

-- ----------------------------
-- Table structure for `tblinvoice_header`
-- ----------------------------
DROP TABLE IF EXISTS `tblinvoice_header`;
CREATE TABLE `tblinvoice_header` (
  `transid` varchar(50) NOT NULL,
  `cylcode` varchar(50) NOT NULL,
  `custno` varchar(50) NOT NULL,
  `subtotal` decimal(16,2) NOT NULL,
  `tax` float DEFAULT NULL,
  `tax_amount` decimal(16,2) DEFAULT NULL,
  `vat` float DEFAULT NULL,
  `vat_amount` decimal(16,2) DEFAULT NULL,
  `discount` float DEFAULT NULL,
  `discount_amount` decimal(16,2) DEFAULT NULL,
  `grandtotal_amt` decimal(16,2) NOT NULL,
  `createuser` varchar(16) DEFAULT NULL,
  `createdate` datetime NOT NULL,
  `modifyuser` varchar(50) NOT NULL,
  `modifydate` datetime DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`transid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of tblinvoice_header
-- ----------------------------

-- ----------------------------
-- Table structure for `tbllogs`
-- ----------------------------
DROP TABLE IF EXISTS `tbllogs`;
CREATE TABLE `tbllogs` (
  `transid` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `module` varchar(100) NOT NULL,
  `action` varchar(255) NOT NULL,
  `activity` varchar(255) NOT NULL,
  `ipaddress` varchar(20) NOT NULL,
  `longitude` varchar(20) NOT NULL,
  `latitude` varchar(20) NOT NULL,
  `createuser` varchar(50) NOT NULL,
  `createdate` datetime NOT NULL,
  `modifyuser` varchar(50) DEFAULT NULL,
  `modifydate` datetime DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`transid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of tbllogs
-- ----------------------------
INSERT INTO `tbllogs` VALUES ('30BDD307', 'sam', 'Vendor', 'Login', 'sam logged in', '127.0.0.1', '127.0.0.1', '127.0.0.1', 'sam', '2021-05-03 17:22:21', null, null, '0');
INSERT INTO `tbllogs` VALUES ('49776B33', 'sam', 'Customer', 'Update', 'Customer profile updated from Mobile with id CUS-3C633EE7 successfully', '127.0.0.1', '127.0.0.1', '127.0.0.1', 'sam', '2021-05-03 17:56:36', null, null, '0');
INSERT INTO `tbllogs` VALUES ('696BFEFA', 'admin', 'Vendor', 'Add', 'Vendor registered from Mobile with id VND-C959BD5C successfully', '127.0.0.1', '127.0.0.1', '127.0.0.1', 'admin', '2021-05-03 17:15:49', null, null, '0');
INSERT INTO `tbllogs` VALUES ('9303063C', 'sam', 'Customer', 'Restore Customer', 'Restored Customer with id CUS-3C633EE7', '127.0.0.1', '127.0.0.1', '127.0.0.1', 'sam', '2021-05-03 18:17:26', null, null, '0');
INSERT INTO `tbllogs` VALUES ('AF467DA5', 'sam', 'Cylinder', 'Register', 'Cylinder registered from Mobile with id 324344 successfully', '127.0.0.1', '127.0.0.1', '127.0.0.1', 'sam', '2021-05-03 18:30:27', null, null, '0');
INSERT INTO `tbllogs` VALUES ('B564DA09', 'sam', 'Customer', 'Add', 'Customer registered from Mobile with id CUS-3C633EE7 successfully', '127.0.0.1', '127.0.0.1', '127.0.0.1', 'sam', '2021-05-03 17:47:29', null, null, '0');
INSERT INTO `tbllogs` VALUES ('DF39247E', 'sam', 'Customer', 'Delete Customer', 'Deleted Customer with id CUS-3C633EE7', '127.0.0.1', '127.0.0.1', '127.0.0.1', 'sam', '2021-05-03 18:15:19', null, null, '0');

-- ----------------------------
-- Table structure for `tblmodule`
-- ----------------------------
DROP TABLE IF EXISTS `tblmodule`;
CREATE TABLE `tblmodule` (
  `transid` varchar(50) NOT NULL,
  `modID` varchar(50) NOT NULL,
  `modName` varchar(100) NOT NULL,
  `modLabel` varchar(100) NOT NULL,
  `modURL` varchar(255) NOT NULL,
  `hasChild` tinyint(4) DEFAULT NULL,
  `isChild` tinyint(4) DEFAULT NULL,
  `pmodID` varchar(50) DEFAULT NULL,
  `arrange` int(11) DEFAULT NULL,
  `modIcon` varchar(50) DEFAULT NULL,
  `modStatus` tinyint(4) NOT NULL DEFAULT 0,
  `createuser` varchar(50) NOT NULL,
  `createdate` datetime NOT NULL,
  `modifyuser` varchar(50) DEFAULT NULL,
  `modifydate` datetime DEFAULT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`transid`,`modID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of tblmodule
-- ----------------------------

-- ----------------------------
-- Table structure for `tblmodule_priv`
-- ----------------------------
DROP TABLE IF EXISTS `tblmodule_priv`;
CREATE TABLE `tblmodule_priv` (
  `transid` varchar(50) NOT NULL,
  `userid` varchar(50) NOT NULL,
  `modID` varchar(50) NOT NULL,
  `modRead` tinyint(4) NOT NULL DEFAULT 0,
  `createuser` varchar(50) NOT NULL,
  `createdate` datetime NOT NULL,
  `modifyuser` varchar(50) DEFAULT NULL,
  `modifydate` datetime DEFAULT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`transid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of tblmodule_priv
-- ----------------------------

-- ----------------------------
-- Table structure for `tblowner`
-- ----------------------------
DROP TABLE IF EXISTS `tblowner`;
CREATE TABLE `tblowner` (
  `transid` varchar(50) NOT NULL,
  `description` varchar(50) NOT NULL,
  `createuser` varchar(50) NOT NULL,
  `createdate` datetime NOT NULL,
  `modifyuser` varchar(50) DEFAULT NULL,
  `modifydate` datetime DEFAULT NULL,
  `deleted` tinyint(4) NOT NULL,
  PRIMARY KEY (`transid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of tblowner
-- ----------------------------

-- ----------------------------
-- Table structure for `tblpayment`
-- ----------------------------
DROP TABLE IF EXISTS `tblpayment`;
CREATE TABLE `tblpayment` (
  `transid` varchar(50) NOT NULL,
  `custno` varchar(50) NOT NULL,
  `payment_type` varchar(50) NOT NULL,
  `payment_mode` varchar(50) NOT NULL,
  `cylcode` varchar(50) NOT NULL,
  `barcode` varchar(50) DEFAULT NULL,
  `amount_due` decimal(16,2) NOT NULL,
  `amount_paid` decimal(16,2) NOT NULL,
  `balance` decimal(16,2) NOT NULL,
  `createuser` varchar(50) NOT NULL,
  `createdate` datetime NOT NULL,
  `modifyuser` varchar(50) DEFAULT NULL,
  `modifydate` datetime DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`transid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of tblpayment
-- ----------------------------

-- ----------------------------
-- Table structure for `tblpayment_mode`
-- ----------------------------
DROP TABLE IF EXISTS `tblpayment_mode`;
CREATE TABLE `tblpayment_mode` (
  `transid` varchar(50) NOT NULL,
  `description` varchar(255) NOT NULL,
  `createuser` varchar(50) NOT NULL,
  `createdate` datetime NOT NULL,
  `modifyuser` varchar(50) DEFAULT NULL,
  `modifydate` datetime DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`transid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of tblpayment_mode
-- ----------------------------

-- ----------------------------
-- Table structure for `tblpayment_type`
-- ----------------------------
DROP TABLE IF EXISTS `tblpayment_type`;
CREATE TABLE `tblpayment_type` (
  `transid` varchar(50) NOT NULL,
  `description` varchar(255) NOT NULL,
  `createuser` varchar(50) NOT NULL,
  `createdate` datetime NOT NULL,
  `modifyuser` varchar(50) DEFAULT NULL,
  `modifydate` datetime DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`transid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of tblpayment_type
-- ----------------------------

-- ----------------------------
-- Table structure for `tblpricelist`
-- ----------------------------
DROP TABLE IF EXISTS `tblpricelist`;
CREATE TABLE `tblpricelist` (
  `transid` varchar(50) NOT NULL,
  `size_id` varchar(50) NOT NULL,
  `price` decimal(16,2) NOT NULL,
  `createuser` varchar(50) NOT NULL,
  `createdate` datetime NOT NULL,
  `modifyuser` varchar(50) DEFAULT NULL,
  `modifydate` datetime DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`transid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of tblpricelist
-- ----------------------------

-- ----------------------------
-- Table structure for `tblproduction`
-- ----------------------------
DROP TABLE IF EXISTS `tblproduction`;
CREATE TABLE `tblproduction` (
  `transid` varchar(50) NOT NULL,
  `cylcode_new` varchar(50) NOT NULL,
  `cylcode_old` varchar(50) NOT NULL,
  `weight_empty` varchar(50) DEFAULT NULL,
  `weight_filled` varchar(50) DEFAULT NULL,
  `total_weight` varchar(50) DEFAULT NULL,
  `filled_by` varchar(100) DEFAULT NULL,
  `createdate` datetime DEFAULT NULL,
  `createuser` varchar(50) NOT NULL,
  `modifydate` datetime DEFAULT NULL,
  `modifyuser` varchar(50) DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`transid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of tblproduction
-- ----------------------------

-- ----------------------------
-- Table structure for `tblregion`
-- ----------------------------
DROP TABLE IF EXISTS `tblregion`;
CREATE TABLE `tblregion` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `region_code` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `region_description` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `coverage` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  `createuser` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `createdate` datetime NOT NULL,
  `modifyuser` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `modifydate` datetime NOT NULL,
  PRIMARY KEY (`id`,`region_code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of tblregion
-- ----------------------------

-- ----------------------------
-- Table structure for `tblregion_state`
-- ----------------------------
DROP TABLE IF EXISTS `tblregion_state`;
CREATE TABLE `tblregion_state` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `region_code` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `state_code` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `deleted` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `createuser` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `createdate` date NOT NULL,
  `modifyuser` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `modifydate` date NOT NULL,
  PRIMARY KEY (`id`,`region_code`,`state_code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of tblregion_state
-- ----------------------------

-- ----------------------------
-- Table structure for `tblrepair`
-- ----------------------------
DROP TABLE IF EXISTS `tblrepair`;
CREATE TABLE `tblrepair` (
  `transid` varchar(50) NOT NULL,
  `custno` varchar(50) NOT NULL,
  `cylcode` varchar(50) NOT NULL,
  `repair_part` varchar(50) NOT NULL,
  `pricecode` varchar(50) NOT NULL,
  `createuser` varchar(50) NOT NULL,
  `createdate` datetime NOT NULL,
  `modifyuser` varchar(50) DEFAULT NULL,
  `modifydate` datetime DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`transid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of tblrepair
-- ----------------------------

-- ----------------------------
-- Table structure for `tblrepair_price`
-- ----------------------------
DROP TABLE IF EXISTS `tblrepair_price`;
CREATE TABLE `tblrepair_price` (
  `transid` varchar(50) NOT NULL,
  `pricecode` varchar(50) NOT NULL,
  `cylinder_part_id` varchar(50) NOT NULL COMMENT 'FK to tblcylinder_part',
  `price` decimal(16,2) NOT NULL,
  `createuser` varchar(50) NOT NULL,
  `createdate` datetime NOT NULL,
  `modifyuser` varchar(50) DEFAULT NULL,
  `modifydate` datetime DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`transid`,`pricecode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of tblrepair_price
-- ----------------------------

-- ----------------------------
-- Table structure for `tblreturn`
-- ----------------------------
DROP TABLE IF EXISTS `tblreturn`;
CREATE TABLE `tblreturn` (
  `transid` varchar(50) NOT NULL,
  `cylcode` varchar(50) NOT NULL,
  `vendor_no` varchar(50) NOT NULL,
  `empty_full` varchar(20) NOT NULL,
  `return_to` varchar(50) DEFAULT NULL COMMENT 'warehouse returned to-warehouse code',
  `createuser` varchar(50) DEFAULT NULL,
  `createdate` datetime DEFAULT NULL,
  `modifyuser` varchar(50) DEFAULT NULL,
  `modifydate` datetime DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`transid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of tblreturn
-- ----------------------------

-- ----------------------------
-- Table structure for `tblrole`
-- ----------------------------
DROP TABLE IF EXISTS `tblrole`;
CREATE TABLE `tblrole` (
  `transid` varchar(50) NOT NULL,
  `description` varchar(255) NOT NULL,
  `createuser` varchar(50) NOT NULL,
  `createdate` datetime NOT NULL,
  `modifyuser` varchar(50) DEFAULT NULL,
  `modifydate` datetime DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL,
  PRIMARY KEY (`transid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of tblrole
-- ----------------------------

-- ----------------------------
-- Table structure for `tblroute`
-- ----------------------------
DROP TABLE IF EXISTS `tblroute`;
CREATE TABLE `tblroute` (
  `transid` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `route_code` char(5) COLLATE utf8_unicode_ci NOT NULL,
  `route_description` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  `createuser` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `createdate` datetime NOT NULL,
  `modifyuser` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `modifydate` datetime NOT NULL,
  PRIMARY KEY (`route_code`,`transid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of tblroute
-- ----------------------------

-- ----------------------------
-- Table structure for `tblstaff`
-- ----------------------------
DROP TABLE IF EXISTS `tblstaff`;
CREATE TABLE `tblstaff` (
  `transid` varchar(50) NOT NULL,
  `staffid` varchar(50) NOT NULL,
  `fname` varchar(100) NOT NULL,
  `lname` varchar(100) NOT NULL,
  `mname` varchar(100) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(50) NOT NULL,
  `region` varchar(50) DEFAULT NULL,
  `town` varchar(100) DEFAULT NULL,
  `streetname` varchar(255) DEFAULT NULL,
  `gpsaddress` varchar(255) DEFAULT NULL,
  `landmark` varchar(255) DEFAULT NULL,
  `roleid` varchar(50) DEFAULT NULL,
  `empdate` date DEFAULT NULL,
  `createuser` varchar(50) DEFAULT NULL,
  `createdate` datetime DEFAULT NULL,
  `modifyuser` varchar(50) DEFAULT NULL,
  `modifydate` datetime DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`transid`,`staffid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of tblstaff
-- ----------------------------

-- ----------------------------
-- Table structure for `tblterritory`
-- ----------------------------
DROP TABLE IF EXISTS `tblterritory`;
CREATE TABLE `tblterritory` (
  `transid` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `route_code` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `route_description` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `state_code` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `region_code` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `deleted` tinyint(4) NOT NULL,
  `createuser` char(10) COLLATE utf8_unicode_ci NOT NULL,
  `createdate` datetime NOT NULL,
  `modifyuser` char(10) COLLATE utf8_unicode_ci NOT NULL,
  `modifydate` datetime NOT NULL,
  PRIMARY KEY (`route_code`,`state_code`,`region_code`,`transid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of tblterritory
-- ----------------------------

-- ----------------------------
-- Table structure for `tblterritory_users`
-- ----------------------------
DROP TABLE IF EXISTS `tblterritory_users`;
CREATE TABLE `tblterritory_users` (
  `id` int(50) NOT NULL AUTO_INCREMENT,
  `vendor_code` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `route_code` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `state_code` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `region_code` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `createuser` char(11) COLLATE utf8_unicode_ci NOT NULL,
  `createdate` datetime NOT NULL,
  `modifyuser` char(1) COLLATE utf8_unicode_ci NOT NULL,
  `modifydate` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of tblterritory_users
-- ----------------------------

-- ----------------------------
-- Table structure for `tbluser`
-- ----------------------------
DROP TABLE IF EXISTS `tbluser`;
CREATE TABLE `tbluser` (
  `transid` varchar(50) NOT NULL,
  `userid` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `fname` varchar(100) NOT NULL,
  `lname` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `usertype` varchar(50) NOT NULL,
  `picture` varchar(255) DEFAULT NULL,
  `createuser` varchar(50) NOT NULL,
  `createdate` datetime NOT NULL,
  `modifyuser` varchar(50) DEFAULT NULL,
  `modifydate` datetime DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`transid`,`username`,`phone`,`email`,`userid`),
  KEY `usertype` (`usertype`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of tbluser
-- ----------------------------

-- ----------------------------
-- Table structure for `tblusertype`
-- ----------------------------
DROP TABLE IF EXISTS `tblusertype`;
CREATE TABLE `tblusertype` (
  `transid` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `createdate` datetime NOT NULL,
  `createuser` varchar(50) NOT NULL,
  `modifydate` datetime DEFAULT NULL,
  `modifyuser` varchar(50) DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`transid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of tblusertype
-- ----------------------------

-- ----------------------------
-- Table structure for `tblvendor`
-- ----------------------------
DROP TABLE IF EXISTS `tblvendor`;
CREATE TABLE `tblvendor` (
  `transid` varchar(50) NOT NULL,
  `vendor_no` varchar(50) NOT NULL,
  `fname` varchar(100) NOT NULL,
  `lname` varchar(100) NOT NULL,
  `mname` varchar(100) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `gender` char(1) NOT NULL,
  `dob` date DEFAULT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(50) NOT NULL,
  `id_type` varchar(50) DEFAULT NULL,
  `id_no` varchar(50) DEFAULT NULL,
  `id_file_link` varchar(255) DEFAULT NULL,
  `region` varchar(50) DEFAULT NULL,
  `town` varchar(50) DEFAULT NULL,
  `streetname` varchar(255) DEFAULT NULL,
  `landmark` varchar(255) DEFAULT NULL,
  `gpsaddress` varchar(50) DEFAULT NULL,
  `latitude` varchar(50) DEFAULT NULL,
  `longitude` varchar(50) DEFAULT NULL,
  `picture` varchar(255) DEFAULT NULL,
  `createuser` varchar(50) NOT NULL,
  `createdate` datetime NOT NULL,
  `modifyuser` varchar(50) DEFAULT NULL,
  `modifydate` datetime DEFAULT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT 0,
  `approved` tinyint(4) NOT NULL DEFAULT 1,
  PRIMARY KEY (`transid`,`vendor_no`,`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of tblvendor
-- ----------------------------

-- ----------------------------
-- Table structure for `tblwarehouse`
-- ----------------------------
DROP TABLE IF EXISTS `tblwarehouse`;
CREATE TABLE `tblwarehouse` (
  `transid` varchar(50) NOT NULL,
  `wcode` varchar(50) NOT NULL,
  `wname` varchar(255) NOT NULL,
  `region` varchar(50) NOT NULL,
  `town` varchar(100) NOT NULL,
  `streetname` varchar(255) DEFAULT NULL,
  `landmark` varchar(255) DEFAULT NULL,
  `gpsaddress` varchar(20) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(50) NOT NULL,
  `createuser` varchar(50) NOT NULL,
  `createdate` datetime NOT NULL,
  `modifyuser` varchar(50) DEFAULT NULL,
  `modifydate` datetime DEFAULT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`transid`,`wcode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of tblwarehouse
-- ----------------------------

-- ----------------------------
-- Table structure for `users`
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of users
-- ----------------------------
