-- MySQL dump 10.13  Distrib 5.6.23, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: xiaoego
-- ------------------------------------------------------
-- Server version	5.6.21

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `t_address`
--

DROP TABLE IF EXISTS `t_address`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t_address` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `consignee` varchar(60) NOT NULL COMMENT '收货人',
  `cellphone` varchar(20) NOT NULL DEFAULT '' COMMENT '手机',
  `gender` enum('male','woman') NOT NULL DEFAULT 'male',
  `school_id` int(10) unsigned NOT NULL COMMENT '学校id',
  `building_id` int(10) unsigned NOT NULL COMMENT '建筑id',
  `room` varchar(60) NOT NULL COMMENT '门牌号',
  `user_id` int(10) unsigned NOT NULL COMMENT '用户id',
  `is_default` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否默认',
  `created_at` int(10) unsigned NOT NULL,
  `updated_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `school_id` (`school_id`),
  KEY `building_id` (`building_id`),
  KEY `user_id` (`user_id`),
  KEY `created_at` (`created_at`,`updated_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='收货地址';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t_address`
--

LOCK TABLES `t_address` WRITE;
/*!40000 ALTER TABLE `t_address` DISABLE KEYS */;
/*!40000 ALTER TABLE `t_address` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t_admin`
--

DROP TABLE IF EXISTS `t_admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t_admin` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL COMMENT '用户名',
  `real_name` varchar(20) NOT NULL COMMENT '真实姓名',
  `auth_key` varchar(32) NOT NULL,
  `password_hash` varchar(255) NOT NULL COMMENT '密码',
  `access_token` varchar(255) DEFAULT NULL,
  `gender` enum('male','woman','other') NOT NULL DEFAULT 'male' COMMENT '性别',
  `email` varchar(255) NOT NULL DEFAULT '' COMMENT '邮箱',
  `mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '手机',
  `status` enum('active','blocked') NOT NULL DEFAULT 'active' COMMENT '状态',
  `created_at` int(10) unsigned NOT NULL COMMENT '创建时间',
  `updated_at` int(10) unsigned NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='管理员';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t_admin`
--

LOCK TABLES `t_admin` WRITE;
/*!40000 ALTER TABLE `t_admin` DISABLE KEYS */;
INSERT INTO `t_admin` VALUES (1,'admin','管理员','djzEmNQp6b82ZjedCfQZyF2NZNRKcfgz','$2y$13$t8ei8oClEZiZwxdEvcaNKu.DeOJcX1xsYekv1rHcWoocVCfUhkZm2',NULL,'male','admin@xiaoego.com','18851510363','active',1433420037,1433652786);
/*!40000 ALTER TABLE `t_admin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t_apply`
--

DROP TABLE IF EXISTS `t_apply`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t_apply` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `apply_sn` char(12) NOT NULL COMMENT '申请单号',
  `store_id` int(10) unsigned NOT NULL COMMENT '店铺id',
  `fee` decimal(8,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '总价',
  `status` enum('pending','rejected','passed','cancelled','completed') NOT NULL COMMENT '状态',
  `remark` varchar(255) DEFAULT NULL,
  `created_at` int(10) unsigned NOT NULL,
  `updated_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `apply_sn_UNIQUE` (`apply_sn`),
  KEY `store_id` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='采购申请表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t_apply`
--

LOCK TABLES `t_apply` WRITE;
/*!40000 ALTER TABLE `t_apply` DISABLE KEYS */;
/*!40000 ALTER TABLE `t_apply` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t_apply_goods`
--

DROP TABLE IF EXISTS `t_apply_goods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t_apply_goods` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `apply_id` int(10) unsigned NOT NULL COMMENT '采购申请id',
  `goods_id` int(10) unsigned NOT NULL COMMENT '商品id',
  `name` varchar(60) NOT NULL COMMENT '商品名称',
  `category` varchar(60) NOT NULL COMMENT '商品分类',
  `count` int(10) unsigned NOT NULL COMMENT '数量',
  `price` decimal(8,2) unsigned NOT NULL COMMENT '单价',
  `cover` varchar(20) NOT NULL COMMENT '封面',
  `unit` varchar(20) NOT NULL DEFAULT '' COMMENT '单位',
  PRIMARY KEY (`id`),
  KEY `apply_id` (`apply_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='店铺采购商品表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t_apply_goods`
--

LOCK TABLES `t_apply_goods` WRITE;
/*!40000 ALTER TABLE `t_apply_goods` DISABLE KEYS */;
/*!40000 ALTER TABLE `t_apply_goods` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t_apply_log`
--

DROP TABLE IF EXISTS `t_apply_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t_apply_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `apply_id` int(10) unsigned NOT NULL COMMENT '采购申请id',
  `remark` varchar(255) NOT NULL COMMENT '备注',
  `created_at` int(10) unsigned NOT NULL,
  `updated_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `apply_id` (`apply_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='店铺采购申请记录';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t_apply_log`
--

LOCK TABLES `t_apply_log` WRITE;
/*!40000 ALTER TABLE `t_apply_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `t_apply_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t_building`
--

DROP TABLE IF EXISTS `t_building`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t_building` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL,
  `school_id` int(10) unsigned NOT NULL,
  `status` enum('enabled','disabled') NOT NULL DEFAULT 'enabled',
  `created_at` int(10) unsigned NOT NULL,
  `updated_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `school_id` (`school_id`),
  KEY `status` (`status`),
  KEY `created_at` (`created_at`,`updated_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='学校建筑';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t_building`
--

LOCK TABLES `t_building` WRITE;
/*!40000 ALTER TABLE `t_building` DISABLE KEYS */;
/*!40000 ALTER TABLE `t_building` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t_cart_goods`
--

DROP TABLE IF EXISTS `t_cart_goods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t_cart_goods` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL COMMENT '用户id',
  `goods_id` int(10) unsigned NOT NULL COMMENT '商品id',
  `store_id` int(10) unsigned NOT NULL COMMENT '营业点id',
  `price` decimal(8,2) unsigned NOT NULL COMMENT '加入购物车时的商品价格',
  `count` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '商品数量',
  `created_at` int(10) unsigned NOT NULL,
  `updated_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`goods_id`),
  KEY `created_at` (`created_at`,`updated_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='购物车商品';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t_cart_goods`
--

LOCK TABLES `t_cart_goods` WRITE;
/*!40000 ALTER TABLE `t_cart_goods` DISABLE KEYS */;
/*!40000 ALTER TABLE `t_cart_goods` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t_category`
--

DROP TABLE IF EXISTS `t_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL COMMENT '名称',
  `slug` varchar(60) NOT NULL COMMENT '唯一字符串',
  `sort` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品分类';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t_category`
--

LOCK TABLES `t_category` WRITE;
/*!40000 ALTER TABLE `t_category` DISABLE KEYS */;
INSERT INTO `t_category` VALUES (1,'充饥','hunger',1),(2,'解馋','glutton',2),(3,'水饮','thirst',3),(4,'用品','articles',4);
/*!40000 ALTER TABLE `t_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t_feedback`
--

DROP TABLE IF EXISTS `t_feedback`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t_feedback` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `content` varchar(255) NOT NULL COMMENT '内容',
  `mobile` char(11) NOT NULL COMMENT '手机号',
  `created_at` int(10) unsigned NOT NULL,
  `updated_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户反馈';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t_feedback`
--

LOCK TABLES `t_feedback` WRITE;
/*!40000 ALTER TABLE `t_feedback` DISABLE KEYS */;
/*!40000 ALTER TABLE `t_feedback` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t_goods`
--

DROP TABLE IF EXISTS `t_goods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t_goods` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL COMMENT '商品名称',
  `store_id` int(10) unsigned NOT NULL COMMENT '营业点id',
  `category_id` int(10) unsigned NOT NULL COMMENT '分类id',
  `cover` varchar(20) NOT NULL COMMENT '封面图片',
  `price` decimal(8,2) unsigned NOT NULL COMMENT '现价',
  `price_original` decimal(8,2) unsigned DEFAULT NULL COMMENT '原价',
  `cost` decimal(10,4) unsigned NOT NULL DEFAULT '0.0000' COMMENT '成本价',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '描述',
  `status` enum('normal','off_shelves','deleted') NOT NULL DEFAULT 'normal' COMMENT '商品状态',
  `surplus` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '库存',
  `sales` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '月销量',
  `unit` varchar(10) NOT NULL DEFAULT '' COMMENT '数量单位',
  `is_new` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否最新商品',
  `is_hot` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否热门商品',
  `is_promotion` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否促销品',
  `created_at` int(10) unsigned NOT NULL,
  `updated_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `store_id` (`store_id`),
  KEY `status` (`status`),
  KEY `created_at` (`created_at`,`updated_at`),
  KEY `is_new` (`is_new`,`is_hot`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t_goods`
--

LOCK TABLES `t_goods` WRITE;
/*!40000 ALTER TABLE `t_goods` DISABLE KEYS */;
/*!40000 ALTER TABLE `t_goods` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t_goods_img`
--

DROP TABLE IF EXISTS `t_goods_img`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t_goods_img` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL COMMENT '图片名',
  `goods_id` int(10) unsigned NOT NULL COMMENT '商品id',
  PRIMARY KEY (`id`),
  KEY `name` (`name`,`goods_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品图片';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t_goods_img`
--

LOCK TABLES `t_goods_img` WRITE;
/*!40000 ALTER TABLE `t_goods_img` DISABLE KEYS */;
/*!40000 ALTER TABLE `t_goods_img` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t_goods_surplus`
--

DROP TABLE IF EXISTS `t_goods_surplus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t_goods_surplus` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `goods_id` int(10) unsigned NOT NULL COMMENT '商品id',
  `surplus_before` int(10) unsigned NOT NULL COMMENT '之前库存',
  `amount` int(10) NOT NULL COMMENT '变化库存',
  `surplus_after` int(10) unsigned NOT NULL COMMENT '之后库存',
  `remark` varchar(255) NOT NULL COMMENT '备注',
  `created_at` int(10) unsigned NOT NULL,
  `updated_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `goods_id` (`goods_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品库存变化记录';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t_goods_surplus`
--

LOCK TABLES `t_goods_surplus` WRITE;
/*!40000 ALTER TABLE `t_goods_surplus` DISABLE KEYS */;
/*!40000 ALTER TABLE `t_goods_surplus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t_member`
--

DROP TABLE IF EXISTS `t_member`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t_member` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `store_id` int(10) unsigned NOT NULL COMMENT '营业点id',
  `username` varchar(20) NOT NULL COMMENT '用户名',
  `real_name` varchar(20) NOT NULL COMMENT '真实姓名',
  `auth_key` varchar(32) NOT NULL,
  `password_hash` varchar(255) NOT NULL COMMENT '密码',
  `access_token` varchar(255) DEFAULT NULL,
  `gender` enum('male','woman','other') NOT NULL DEFAULT 'male' COMMENT '性别',
  `email` varchar(255) NOT NULL DEFAULT '' COMMENT '邮箱',
  `mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '手机',
  `status` enum('active','blocked') NOT NULL DEFAULT 'active' COMMENT '状态',
  `created_at` int(10) unsigned NOT NULL COMMENT '创建时间',
  `updated_at` int(10) unsigned NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username_UNIQUE` (`username`),
  KEY `status` (`status`),
  KEY `store_id` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='店铺人员';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t_member`
--

LOCK TABLES `t_member` WRITE;
/*!40000 ALTER TABLE `t_member` DISABLE KEYS */;
/*!40000 ALTER TABLE `t_member` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t_migration`
--

DROP TABLE IF EXISTS `t_migration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t_migration` (
  `version` varchar(180) NOT NULL,
  `apply_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t_migration`
--

LOCK TABLES `t_migration` WRITE;
/*!40000 ALTER TABLE `t_migration` DISABLE KEYS */;
/*!40000 ALTER TABLE `t_migration` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t_order`
--

DROP TABLE IF EXISTS `t_order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_sn` char(14) NOT NULL COMMENT '订单编号',
  `user_id` int(10) unsigned NOT NULL COMMENT '用户id',
  `store_id` int(10) unsigned NOT NULL COMMENT '营业点id',
  `school_id` int(10) unsigned NOT NULL COMMENT '学校id',
  `status` enum('unshipped','shipped','unpaid','completed','cancelled','deleted') NOT NULL COMMENT '订单状态',
  `payment` enum('online','offline') NOT NULL COMMENT '支付方式',
  `fee` decimal(8,2) unsigned NOT NULL COMMENT '总价',
  `real_fee` decimal(8,2) unsigned NOT NULL COMMENT '实付款',
  `preferential` enum('down','gift','none') NOT NULL COMMENT '优惠类别',
  `down_val` decimal(4,2) unsigned DEFAULT NULL COMMENT '满减优惠金额',
  `gift_val` varchar(60) DEFAULT NULL COMMENT '满送优惠礼品',
  `new_down_val` decimal(4,2) unsigned DEFAULT NULL COMMENT '新用户立减优惠金额',
  `book_time` int(10) unsigned DEFAULT NULL COMMENT '预定时间',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `cancelled_msg` varchar(60) DEFAULT NULL COMMENT '取消说明',
  `created_at` int(10) unsigned NOT NULL,
  `updated_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_sn` (`order_sn`),
  KEY `user_id` (`user_id`,`store_id`,`status`,`created_at`,`updated_at`),
  KEY `payment` (`payment`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='订单表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t_order`
--

LOCK TABLES `t_order` WRITE;
/*!40000 ALTER TABLE `t_order` DISABLE KEYS */;
/*!40000 ALTER TABLE `t_order` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t_order_address`
--

DROP TABLE IF EXISTS `t_order_address`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t_order_address` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) unsigned NOT NULL COMMENT '订单id',
  `consignee` varchar(60) NOT NULL COMMENT '收货人',
  `cellphone` varchar(20) NOT NULL DEFAULT '' COMMENT '手机',
  `gender` enum('male','woman') NOT NULL,
  `school` varchar(60) NOT NULL COMMENT '学校',
  `building` varchar(60) NOT NULL COMMENT '建筑',
  `room` varchar(60) NOT NULL COMMENT '门牌号',
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='订单收货地址表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t_order_address`
--

LOCK TABLES `t_order_address` WRITE;
/*!40000 ALTER TABLE `t_order_address` DISABLE KEYS */;
/*!40000 ALTER TABLE `t_order_address` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t_order_goods`
--

DROP TABLE IF EXISTS `t_order_goods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t_order_goods` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) unsigned NOT NULL COMMENT '订单id',
  `goods_id` int(10) unsigned NOT NULL COMMENT '商品id',
  `name` varchar(60) NOT NULL COMMENT '商品名称',
  `category` varchar(60) NOT NULL COMMENT '分类',
  `price` decimal(8,2) unsigned NOT NULL COMMENT '单价',
  `cost` decimal(10,4) unsigned NOT NULL COMMENT '成本价',
  `count` int(10) unsigned NOT NULL COMMENT '数量',
  `cover` varchar(20) NOT NULL COMMENT '封面图片',
  `unit` varchar(10) NOT NULL DEFAULT '' COMMENT '数量单位',
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`,`goods_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='订单商品表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t_order_goods`
--

LOCK TABLES `t_order_goods` WRITE;
/*!40000 ALTER TABLE `t_order_goods` DISABLE KEYS */;
/*!40000 ALTER TABLE `t_order_goods` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t_order_volume`
--

DROP TABLE IF EXISTS `t_order_volume`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t_order_volume` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `volume` decimal(8,2) unsigned NOT NULL COMMENT '交易额',
  `cost` decimal(10,4) unsigned NOT NULL COMMENT '成本',
  `profit` decimal(10,4) NOT NULL COMMENT '利润',
  `order_id` int(10) unsigned NOT NULL COMMENT '订单id',
  `payment` enum('online','offline') NOT NULL COMMENT '支付方式',
  `user_id` int(10) unsigned NOT NULL COMMENT '买家id',
  `store_id` int(10) unsigned NOT NULL COMMENT '店铺id',
  `created_at` int(10) unsigned NOT NULL,
  `updated_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `payment` (`payment`),
  KEY `created_at` (`created_at`),
  KEY `updated_at` (`updated_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='交易记录';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t_order_volume`
--

LOCK TABLES `t_order_volume` WRITE;
/*!40000 ALTER TABLE `t_order_volume` DISABLE KEYS */;
/*!40000 ALTER TABLE `t_order_volume` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t_purchase`
--

DROP TABLE IF EXISTS `t_purchase`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t_purchase` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `goods_id` int(10) unsigned NOT NULL COMMENT '商品id',
  `store_id` int(10) unsigned NOT NULL COMMENT '店铺id',
  `count` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '采购数量',
  `created_at` int(10) unsigned NOT NULL,
  `updated_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `store_id` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='店铺采购表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t_purchase`
--

LOCK TABLES `t_purchase` WRITE;
/*!40000 ALTER TABLE `t_purchase` DISABLE KEYS */;
/*!40000 ALTER TABLE `t_purchase` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t_school`
--

DROP TABLE IF EXISTS `t_school`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t_school` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL COMMENT '学校名称',
  `created_at` int(10) unsigned NOT NULL,
  `updated_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='学校表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t_school`
--

LOCK TABLES `t_school` WRITE;
/*!40000 ALTER TABLE `t_school` DISABLE KEYS */;
/*!40000 ALTER TABLE `t_school` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t_store`
--

DROP TABLE IF EXISTS `t_store`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t_store` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL COMMENT '商店名称',
  `school_id` int(10) unsigned NOT NULL COMMENT '学校id',
  `address` varchar(255) NOT NULL DEFAULT '' COMMENT '地址',
  `cellphone` varchar(20) NOT NULL DEFAULT '' COMMENT '手机',
  `telephone` varchar(20) NOT NULL DEFAULT '' COMMENT '电话',
  `notice` varchar(255) NOT NULL DEFAULT '' COMMENT '公告',
  `status` enum('active','rest','disabled') NOT NULL DEFAULT 'active' COMMENT '状态',
  `hours` varchar(60) NOT NULL DEFAULT '' COMMENT '营业时间',
  `has_book` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否支持预定',
  `has_down` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否支持满减优惠',
  `has_gift` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否支持满送优惠',
  `has_least` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否有最低购买价',
  `down_upper` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '满减优惠价',
  `down_val` decimal(4,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '满减优惠金额',
  `gift_upper` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '满送优惠价',
  `gift_val` varchar(60) NOT NULL DEFAULT '' COMMENT '满送优惠礼品',
  `least_val` decimal(4,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '最低购买金额',
  `layout` enum('merger','open') NOT NULL DEFAULT 'merger' COMMENT '布局',
  `enable_sms` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否启用短信提醒',
  `auto_toggle` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否自动切换休息营业状态',
  `toggle_type` enum('active','rest','both') DEFAULT 'active' COMMENT '切换类型',
  `created_at` int(10) unsigned NOT NULL COMMENT '创建时间',
  `updated_at` int(10) unsigned NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `auto_toggle` (`auto_toggle`),
  KEY `toggle_type` (`toggle_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='营业点';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t_store`
--

LOCK TABLES `t_store` WRITE;
/*!40000 ALTER TABLE `t_store` DISABLE KEYS */;
/*!40000 ALTER TABLE `t_store` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t_tag`
--

DROP TABLE IF EXISTS `t_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t_tag` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `store_id` int(10) unsigned NOT NULL COMMENT '营业点id',
  `name` varchar(8) NOT NULL COMMENT 'tag名称',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `created_at` int(10) unsigned NOT NULL COMMENT '创建时间',
  `updated_at` int(10) unsigned NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='营业点tag';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t_tag`
--

LOCK TABLES `t_tag` WRITE;
/*!40000 ALTER TABLE `t_tag` DISABLE KEYS */;
/*!40000 ALTER TABLE `t_tag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t_user`
--

DROP TABLE IF EXISTS `t_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mobile` char(11) NOT NULL COMMENT '手机号',
  `nickname` varchar(60) DEFAULT NULL COMMENT '昵称',
  `auth_key` varchar(32) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `password_reset_token` varchar(255) DEFAULT NULL,
  `access_token` varchar(255) DEFAULT NULL,
  `gender` enum('male','woman','other') NOT NULL DEFAULT 'male' COMMENT '性别',
  `email` varchar(60) DEFAULT NULL COMMENT '邮箱',
  `status` enum('active','blocked') NOT NULL DEFAULT 'active' COMMENT '状态',
  `has_new_down` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否还有新用户立减优惠资格',
  `created_at` int(10) unsigned NOT NULL COMMENT '创建时间',
  `updated_at` int(10) unsigned NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`mobile`),
  UNIQUE KEY `email` (`email`),
  KEY `created_at` (`created_at`,`updated_at`),
  KEY `gender` (`gender`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t_user`
--

LOCK TABLES `t_user` WRITE;
/*!40000 ALTER TABLE `t_user` DISABLE KEYS */;
/*!40000 ALTER TABLE `t_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t_user_account`
--

DROP TABLE IF EXISTS `t_user_account`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t_user_account` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `balance` decimal(8,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '余额',
  `password_hash` varchar(255) DEFAULT NULL COMMENT '支付密码',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户账户';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t_user_account`
--

LOCK TABLES `t_user_account` WRITE;
/*!40000 ALTER TABLE `t_user_account` DISABLE KEYS */;
/*!40000 ALTER TABLE `t_user_account` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-01-02 22:50:01
