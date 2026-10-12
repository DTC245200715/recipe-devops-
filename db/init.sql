SET NAMES utf8mb4;
CREATE USER IF NOT EXISTS 'exporter'@'%' IDENTIFIED BY 'ExporterStrongPass!2026';
GRANT PROCESS, REPLICATION CLIENT, SELECT ON *.* TO 'exporter'@'%';
FLUSH PRIVILEGES;

-- MySQL dump 10.13  Distrib 8.0.46, for Linux (x86_64)
--
-- Host: localhost    Database: recipe_db
-- ------------------------------------------------------
-- Server version	8.0.46

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'M├│n ch├¡nh','mon-chinh','C├íc m├│n ─ân ch├¡nh trong bß╗»a c╞ím'),(2,'M├│n canh','mon-canh','C├íc m├│n canh thanh m├ít'),(3,'M├│n chay','mon-chay','C├íc m├│n ─ân chay thanh ─æß║ím'),(4,'Tr├íng miß╗çng','trang-mieng','─Éß╗ô ngß╗ìt sau bß╗»a ─ân'),(5,'─Éß╗ô uß╗æng','do-uong','N╞░ß╗¢c giß║úi kh├ít c├íc loß║íi');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ingredients`
--

DROP TABLE IF EXISTS `ingredients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ingredients` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ingredients`
--

LOCK TABLES `ingredients` WRITE;
/*!40000 ALTER TABLE `ingredients` DISABLE KEYS */;
INSERT INTO `ingredients` VALUES (2,'B├ính phß╗ƒ'),(20,'B├ính tr├íng'),(39,'B├¡ ─æß╗Å'),(43,'Bß╗Öt b├ính x├¿o'),(33,'Bß╗Öt n─âng'),(6,'B├║n t╞░╞íi'),(15,'C├á chua'),(7,'C├í l├│c'),(48,'─É├í b├áo'),(36,'Da heo'),(42,'Dß║ºu ─ân'),(16,'─Éß║¡u bß║»p'),(29,'Dß║ºu h├áo'),(8,'─Éß║¡u h┼⌐ non'),(13,'─Éu ─æß╗º xanh'),(14,'Dß╗⌐a'),(34,'─É╞░ß╗¥ng ─æen'),(25,'─É╞░ß╗¥ng ph├¿n'),(49,'Gan heo'),(10,'Gß║ío tß║Ñm'),(24,'Gelatin'),(45,'Gi├í ─æß╗ù'),(26,'Hß║ính nh├ón l├ít'),(40,'H├ánh t├¡m'),(38,'Mß║¡t ong'),(17,'Me chua'),(41,'Ng├▓ gai'),(46,'N╞░ß╗¢c cß╗æt dß╗½a'),(4,'N╞░ß╗¢c mß║»m'),(28,'Sa tß║┐'),(32,'Sß╗»a ─æß║╖c'),(22,'Sß╗»a t╞░╞íi kh├┤ng ─æ╞░ß╗¥ng'),(35,'S╞░ß╗¥n cß╗æt lß║┐t'),(9,'S╞░ß╗¥n non'),(37,'Th├¡nh gß║ío'),(11,'Thß╗ït ba chß╗ë'),(3,'Thß╗ït b├▓'),(5,'Thß╗ït heo'),(27,'Thß╗ït heo b─âm'),(18,'Thß╗ït lß╗ún luß╗Öc'),(12,'Thß╗ït nß║íc vai b─âm'),(50,'Ti├¬u ─æen'),(30,'Tß╗Åi b─âm'),(44,'T├┤m ─æß║Ñt'),(19,'T├┤m t╞░╞íi'),(31,'Tr├á ─æen'),(23,'Whipping cream'),(21,'X├á l├ích'),(47,'Xo├ái ch├¡n'),(1,'X╞░╞íng b├▓');
/*!40000 ALTER TABLE `ingredients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `recipe_ingredients`
--

DROP TABLE IF EXISTS `recipe_ingredients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `recipe_ingredients` (
  `recipe_id` int NOT NULL,
  `ingredient_id` int NOT NULL,
  `quantity` varchar(50) DEFAULT NULL,
  `unit` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`recipe_id`,`ingredient_id`),
  KEY `ingredient_id` (`ingredient_id`),
  CONSTRAINT `recipe_ingredients_ibfk_1` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `recipe_ingredients_ibfk_2` FOREIGN KEY (`ingredient_id`) REFERENCES `ingredients` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recipe_ingredients`
--

LOCK TABLES `recipe_ingredients` WRITE;
/*!40000 ALTER TABLE `recipe_ingredients` DISABLE KEYS */;
INSERT INTO `recipe_ingredients` VALUES (1,1,'1','kg'),(1,2,'500','g'),(1,3,'300','g'),(2,4,'5','muß╗ùng'),(2,6,'1','kg'),(2,11,'500','g'),(2,12,'300','g'),(2,13,'1/2','quß║ú'),(3,7,'500','g'),(3,14,'1/2','quß║ú'),(3,15,'2','quß║ú'),(3,16,'100','g'),(3,17,'50','g'),(4,18,'300','g'),(4,19,'300','g'),(4,20,'1','xß║Ñp'),(4,21,'200','g'),(5,22,'500','ml'),(5,23,'500','ml'),(5,24,'25','g'),(5,25,'150','g'),(5,26,'50','g'),(6,8,'2','hß╗Öp'),(6,27,'150','g'),(6,28,'2','muß╗ùng'),(6,29,'1','muß╗ùng'),(6,30,'1','cß╗º'),(7,31,'50','g'),(7,32,'100','ml'),(7,33,'150','g'),(7,34,'100','g'),(8,10,'500','g'),(8,35,'500','g'),(8,36,'200','g'),(8,37,'50','g'),(8,38,'2','muß╗ùng'),(9,39,'500','g'),(9,40,'2','cß╗º'),(9,41,'1','mß╗¢'),(9,42,'1','muß╗ùng'),(10,43,'400','g'),(10,44,'300','g'),(10,45,'200','g'),(10,46,'200','ml'),(11,47,'2','quß║ú'),(11,48,'1','ly'),(13,49,'300','g'),(13,50,'1','muß╗ùng');
/*!40000 ALTER TABLE `recipe_ingredients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `recipes`
--

DROP TABLE IF EXISTS `recipes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `recipes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text,
  `image` varchar(255) DEFAULT NULL,
  `cook_time` int DEFAULT NULL,
  `servings` int DEFAULT NULL,
  `difficulty` varchar(50) DEFAULT NULL,
  `category_id` int DEFAULT NULL,
  `instructions` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `recipes_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recipes`
--

LOCK TABLES `recipes` WRITE;
/*!40000 ALTER TABLE `recipes` DISABLE KEYS */;
INSERT INTO `recipes` VALUES (1,'Phß╗ƒ B├▓','pho-bo','M├│n phß╗ƒ truyß╗ün thß╗æng Viß╗çt Nam','https://upload.wikimedia.org/wikipedia/commons/5/53/Pho-Beef-Noodles-2008.jpg',120,4,'Kh├│',1,'B╞░ß╗¢c 1: Rß╗¡a sß║ích x╞░╞íng b├▓, luß╗Öc s╞í qua n╞░ß╗¢c s├┤i ─æß╗â khß╗¡ m├╣i h├┤i, sau ─æ├│ hß║ºm vß╗¢i gß╗½ng, h├ánh t├¡m n╞░ß╗¢ng v├á c├íc gia vß╗ï phß╗ƒ trong 2-3 tiß║┐ng.\nB╞░ß╗¢c 2: Th├íi mß╗Ång thß╗ït b├▓ t├íi. Rß╗¡a sß║ích c├íc loß║íi rau th╞ím, ng├▓ gai, h├║ng quß║┐ v├á th├íi nhß╗Å h├ánh l├í.\nB╞░ß╗¢c 3: Trß║ºn b├ính phß╗ƒ qua n╞░ß╗¢c s├┤i cho mß╗üm rß╗ôi cho v├áo b├ít. Xß║┐p thß╗ït b├▓ th├íi mß╗Ång l├¬n tr├¬n c├╣ng vß╗¢i h├ánh ng├▓.\nB╞░ß╗¢c 4: Chan n╞░ß╗¢c d├╣ng ─æang s├┤i s├╣ng sß╗Ñc v├áo b├ít ─æß╗â l├ám ch├¡n thß╗ït b├▓ t├íi, ─ân k├¿m vß╗¢i quß║⌐y v├á chanh ß╗¢t.','2026-10-10 08:19:01','2026-10-10 11:35:23'),(2,'B├║n Chß║ú','bun-cha','B├║n chß║ú H├á Nß╗Öi th╞ím ngon','https://upload.wikimedia.org/wikipedia/commons/4/4d/B%C3%BAn_ch%E1%BA%A3_Vietnamese_food.jpg',60,4,'Trung b├¼nh',1,'B╞░ß╗¢c 1: Th├íi mß╗Ång thß╗ït ba chß╗ë v├á nß║╖n thß╗ït nß║íc b─âm th├ánh tß╗½ng vi├¬n nhß╗Å. ╞»ß╗¢p thß╗ït vß╗¢i n╞░ß╗¢c mß║»m, ─æ╞░ß╗¥ng, ti├¬u, h├ánh tß╗Åi trong 30 ph├║t.\nB╞░ß╗¢c 2: N╞░ß╗¢ng thß╗ït ba chß╗ë v├á thß╗ït vi├¬n tr├¬n bß║┐p than hoa cho ─æß║┐n khi ch├¡n v├áng, x├⌐m cß║ính v├á dß║¡y m├╣i th╞ím.\nB╞░ß╗¢c 3: Pha n╞░ß╗¢c chß║Ñm chua ngß╗ìt vß╗¢i tß╗╖ lß╗ç mß║»m, ─æ╞░ß╗¥ng, chanh, n╞░ß╗¢c lß╗ìc vß╗½a ─ân, th├¬m tß╗Åi ß╗¢t b─âm v├á ─æu ─æß╗º xanh th├íi mß╗Ång.\nB╞░ß╗¢c 4: B├áy b├║n t╞░╞íi, rau sß╗æng v├á chß║ú n╞░ß╗¢ng ra ─æ─⌐a, th╞░ß╗ƒng thß╗⌐c bß║▒ng c├ích chß║Ñm b├║n v├á chß║ú v├áo b├ít n╞░ß╗¢c chß║Ñm chua ngß╗ìt.','2026-10-10 08:19:01','2026-10-10 11:35:23'),(3,'Canh Chua C├í L├│c','canh-chua-ca-loc','Canh chua chuß║⌐n miß╗ün T├óy','https://upload.wikimedia.org/wikipedia/commons/a/a0/Canhchua2.jpg',30,4,'Dß╗à',2,'B╞░ß╗¢c 1: L├ám sß║ích c├í l├│c, cß║»t kh├║c vß╗½a ─ân. Nhß║╖t v├á rß╗¡a sß║ích gi├í, dß╗ìc m├╣ng, ─æß║¡u bß║»p, dß╗⌐a v├á c├á chua.\nB╞░ß╗¢c 2: Phi th╞ím tß╗Åi, x├áo s╞í c├á chua v├á dß╗⌐a ─æß╗â tß║ío m├áu, ─æß╗ò n╞░ß╗¢c v├áo ─æun s├┤i v├á dß║ºm cß╗æt me chua.\nB╞░ß╗¢c 3: Cho c├í l├│c v├áo ─æun ch├¡n, tiß║┐p tß╗Ñc th├¬m ─æß║¡u bß║»p, dß╗ìc m├╣ng, gi├í ─æß╗ù v├áo nß║Ñu vß╗½a ch├¡n tß╗¢i ─æß╗â rau vß║½n giß╗» ─æß╗Ö gi├▓n.\nB╞░ß╗¢c 4: N├¬m nß║┐m lß║íi gia vß╗ï cho c├ón bß║▒ng chua ngß╗ìt, tß║»t bß║┐p, rß║»c rau ng├▓ ├┤m, ng├▓ gai th├íi nhß╗Å v├ái l├ít ß╗¢t l├¬n tr├¬n.','2026-10-10 08:19:01','2026-10-10 11:35:23'),(4,'Gß╗Åi Cuß╗æn','goi-cuon','Gß╗Åi cuß╗æn t├┤m thß╗ït l├á m├│n ─ân vß║╖t hoß║╖c khai vß╗ï cß╗▒c kß╗│ thanh m├ít v├á dß╗à ─ân. B├¬n trong lß╗¢p b├ính tr├íng mß╗Ång dai l├á t├┤m t╞░╞íi ─æß╗Å au, thß╗ït luß╗Öc th├íi mß╗Ång, b├║n t╞░╞íi v├á ngß║¡p tr├án c├íc loß║íi rau th╞ím, chß║Ñm k├¿m t╞░╞íng ─æen ─æß║¡u phß╗Öng ─æß║¡m ─æ├á.','https://upload.wikimedia.org/wikipedia/commons/9/90/G%E1%BB%8Fi_cu%E1%BB%91n.jpg',20,4,'Dß╗à',1,'B╞░ß╗¢c 1: Thß╗ït ba chß╗ë luß╗Öc ch├¡n, th├íi l├ít mß╗Ång. T├┤m luß╗Öc ch├¡n, b├│c vß╗Å v├á chß║╗ ─æ├┤i.\nB╞░ß╗¢c 2: Rß╗¡a sß║ích x├á l├ích, hß║╣ v├á c├íc loß║íi rau th╞ím. B├║n t╞░╞íi gß╗í t╞íi ─æß╗â sß║╡n.\nB╞░ß╗¢c 3: L├ám ╞░ß╗¢t b├ính tr├íng, xß║┐p x├á l├ích, rau th╞ím, b├║n, thß╗ït lß╗ún v├á t├┤m l├¬n tr├¬n, cuß╗Ön thß║¡t chß║╖t tay.\nB╞░ß╗¢c 4: Pha n╞░ß╗¢c chß║Ñm t╞░╞íng ─æen vß╗¢i b╞í ─æß║¡u phß╗Öng, rß║»c th├¬m ch├║t h├ánh phi v├á ─æß║¡u phß╗Öng rang gi├ú nhß╗Å ─æß╗â chß║Ñm gß╗Åi.','2026-10-10 08:19:01','2026-10-10 11:41:09'),(5,'Ch├¿ Kh├║c Bß║ích','che-khuc-bach','Ch├¿ kh├║c bß║ích g├óy th╞░╞íng nhß╗¢ bß╗ƒi sß╗▒ thanh tao v├á ─æß║╣p mß║»t. Nhß╗»ng khß╗æi thß║ích n├║ng n├¡nh, mß╗üm tan b├⌐o ngß║¡y vß╗ï sß╗»a t╞░╞íi, ─ân c├╣ng n╞░ß╗¢c ─æ╞░ß╗¥ng ph├¿n l├í dß╗⌐a dß╗ïu m├ít, ─æiß╗âm xuyß║┐t th├¬m nh├ún lß╗ông hay vß║úi thiß╗üu v├á hß║ính nh├ón rang gi├▓n rß╗Ñm.','https://upload.wikimedia.org/wikipedia/commons/c/c3/Ch%C3%A8_b%C3%A0_ba.jpg',40,6,'Trung b├¼nh',4,'B╞░ß╗¢c 1: Ng├óm gelatin cho nß╗ƒ. ─Éun n├│ng sß╗»a t╞░╞íi kh├┤ng ─æ╞░ß╗¥ng v├á whipping cream, h├▓a tan ─æ╞░ß╗¥ng v├á gelatin ─æ├ú ng├óm v├áo.\nB╞░ß╗¢c 2: ─Éß╗ò hß╗ùn hß╗úp sß╗»a ra khu├┤n, cß║Ñt v├áo tß╗º lß║ính khoß║úng 3-4 tiß║┐ng ─æß╗â thß║ích ─æ├┤ng lß║íi, sau ─æ├│ cß║»t th├ánh khß╗æi vu├┤ng nhß╗Å.\nB╞░ß╗¢c 3: ─Éun s├┤i n╞░ß╗¢c vß╗¢i ─æ╞░ß╗¥ng ph├¿n v├á l├í dß╗⌐a l├ám n╞░ß╗¢c chan ch├¿, ─æß╗â nguß╗Öi. Ng├óm hß║ính nh├ón l├ít qua n╞░ß╗¢c v├á rang v├áng rß╗Ñm.\nB╞░ß╗¢c 4: M├║c thß║ích kh├║c bß║ích ra b├ít, th├¬m vß║úi thiß╗üu, chan n╞░ß╗¢c ─æ╞░ß╗¥ng l├í dß╗⌐a ╞░ß╗¢p lß║ính v├á rß║»c hß║ính nh├ón rang l├¬n tr├¬n c├╣ng.','2026-10-10 08:19:01','2026-10-10 11:41:09'),(6,'─Éß║¡u H┼⌐ Tß╗⌐ Xuy├¬n','dau-hu-tu-xuyen','─Éß║¡u h┼⌐ non sß╗æt cay','https://upload.wikimedia.org/wikipedia/commons/8/88/Chen_Mapo_Tofu.jpg',20,2,'Dß╗à',3,'B╞░ß╗¢c 1: ─Éß║¡u h┼⌐ non cß║»t khß╗æi vu├┤ng nhß╗Å, trß║ºn s╞í qua n╞░ß╗¢c s├┤i c├│ pha ch├║t muß╗æi ─æß╗â ─æß║¡u kh├┤ng bß╗ï n├ít.\nB╞░ß╗¢c 2: B─âm nhß╗Å thß╗ït heo. Tß╗Åi, gß╗½ng b─âm nhuyß╗àn. Pha sß║╡n b├ít n╞░ß╗¢c xß╗æt gß╗ôm n╞░ß╗¢c t╞░╞íng, dß║ºu h├áo, ─æ╞░ß╗¥ng v├á sa tß║┐.\nB╞░ß╗¢c 3: X├áo tß╗Åi gß╗½ng cho th╞ím, ─æß╗ò thß╗ït b─âm v├áo x├áo s─ân lß║íi, sau ─æ├│ th├¬m n╞░ß╗¢c xß╗æt cay ─æ├ú pha v├á mß╗Öt ch├║t n╞░ß╗¢c ─æun s├┤i.\nB╞░ß╗¢c 4: Thß║ú ─æß║¡u h┼⌐ non v├áo chß║úo, lß║»c nhß║╣ chß║úo ─æß╗â ─æß║¡u ngß║Ñm xß╗æt, ─æun liu riu th├¬m 3 ph├║t rß╗ôi rß║»c h├ánh l├í th├íi nhß╗Å tß║»t bß║┐p.','2026-10-10 08:19:01','2026-10-10 11:35:23'),(7,'Tr├á Sß╗»a Tr├ón Ch├óu','tra-sua-tran-chau','Tr├á sß╗»a tr├ón ch├óu kh├┤ng chß╗ë l├á mß╗Öt thß╗⌐c uß╗æng m├á c├▓n l├á ─æam m├¬ cß╗ºa giß╗¢i trß║╗. Sß╗▒ h├▓a quyß╗çn ho├án hß║úo giß╗»a vß╗ï ─æß║»ng nhß║╣ cß╗ºa hß╗ông tr├á, vß╗ï b├⌐o cß╗ºa sß╗»a v├á nhß╗»ng hß║ít tr├ón ch├óu dai dß║╗o, ngß╗ìt ng├áo tß║ío n├¬n mß╗Öt h╞░╞íng vß╗ï kh├│ c╞░ß╗íng.','https://upload.wikimedia.org/wikipedia/commons/a/a2/Bubble_Tea.png',30,2,'Dß╗à',5,'B╞░ß╗¢c 1: Nh├áo bß╗Öt n─âng vß╗¢i cacao v├á n╞░ß╗¢c s├┤i ─æß╗â tß║ío th├ánh khß╗æi bß╗Öt mß╗ïn, sau ─æ├│ nß║╖n th├ánh tß╗½ng vi├¬n tr├ón ch├óu nhß╗Å.\nB╞░ß╗¢c 2: ─Éun s├┤i n╞░ß╗¢c, thß║ú tr├ón ch├óu v├áo luß╗Öc ─æß║┐n khi nß╗òi l├¬n, vß╗¢t ra ng├óm ngay v├áo b├ít n╞░ß╗¢c ─æ├í, rß╗ôi trß╗Ön vß╗¢i mß║¡t ong.\nB╞░ß╗¢c 3: H├úm tr├á ─æen vß╗¢i n╞░ß╗¢c s├┤i trong 10 ph├║t, lß╗ìc bß╗Å b├ú tr├á ─æß╗â lß║Ñy n╞░ß╗¢c cß╗æt.\nB╞░ß╗¢c 4: Lß║»c ─æß╗üu n╞░ß╗¢c cß╗æt tr├á, sß╗»a ─æß║╖c, sß╗»a t╞░╞íi v├á ─æ├í vi├¬n trong b├¼nh shaker, ─æß╗ò ra ly ─æ├ú c├│ sß║╡n tr├ón ch├óu ß╗ƒ ─æ├íy.','2026-10-10 08:19:01','2026-10-10 11:41:09'),(8,'C╞ím Tß║Ñm S╞░ß╗¥n B├¼','com-tam-suon-bi','C╞ím tß║Ñm S├ái G├▓n','https://upload.wikimedia.org/wikipedia/commons/b/b0/C%C6%A1m_T%E1%BA%A5m%2C_Da_Nang%2C_Vietnam.jpg',60,4,'Trung b├¼nh',1,'B╞░ß╗¢c 1: ╞»ß╗¢p s╞░ß╗¥n cß╗æt lß║┐t vß╗¢i mß║¡t ong, n╞░ß╗¢c mß║»m, tß╗Åi b─âm, h├ánh t├¡m b─âm, dß║ºu h├áo v├á ch├║t n╞░ß╗¢c cß╗æt dß╗½a trong 2 tiß║┐ng.\nB╞░ß╗¢c 2: N╞░ß╗¢ng s╞░ß╗¥n tr├¬n bß║┐p than hß╗ông ß╗ƒ lß╗¡a vß╗½a, lß║¡t ─æß╗üu tay ─æß╗â s╞░ß╗¥n ch├¡n v├áng ╞░╞ím v├á kh├┤ng bß╗ï ch├íy x├⌐m.\nB╞░ß╗¢c 3: L├ám b├¼ heo: Trß╗Ön thß╗ït nß║íc heo ram th├íi sß╗úi vß╗¢i da heo luß╗Öc th├íi mß╗Ång v├á th├¡nh gß║ío rang cho thß║¡t ─æß╗üu.\nB╞░ß╗¢c 4: Tr├¼nh b├áy c╞ím ra ─æ─⌐a, ─æß║╖t s╞░ß╗¥n n╞░ß╗¢ng, b├¼ heo, chß║ú trß╗⌐ng hß║Ñp l├¬n tr├¬n, ─ân k├¿m vß╗¢i mß╗í h├ánh, ─æß╗ô chua v├á n╞░ß╗¢c mß║»m mß║╖n ngß╗ìt.','2026-10-10 08:19:01','2026-10-10 11:35:23'),(9,'Canh B├¡ ─Éß╗Å Thß╗ït B─âm','canh-bi-do-thit-bam','Canh bß╗ò d╞░ß╗íng','https://upload.wikimedia.org/wikipedia/commons/c/cd/K%C3%BCrbissuppe_mit_Kern%C3%B6l.JPG',25,4,'Dß╗à',2,'B╞░ß╗¢c 1: B├¡ ─æß╗Å gß╗ìt vß╗Å, bß╗Å ruß╗Öt, rß╗¡a sß║ích v├á th├íi miß║┐ng vu├┤ng. Thß╗ït b─âm ╞░ß╗¢p vß╗¢i ch├║t muß╗æi, ti├¬u v├á h├ánh t├¡m.\nB╞░ß╗¢c 2: Phi th╞ím h├ánh t├¡m trong nß╗ôi vß╗¢i mß╗Öt ├¡t dß║ºu ─ân, cho thß╗ït b─âm v├áo x├áo s─ân lß║íi ─æß╗â dß║¡y m├╣i th╞ím.\nB╞░ß╗¢c 3: ─Éß╗ò l╞░ß╗úng n╞░ß╗¢c vß╗½a ─æß╗º d├╣ng v├áo nß╗ôi ─æun s├┤i, hß╗¢t bß╗Å bß╗ìt d╞í, sau ─æ├│ cho b├¡ ─æß╗Å v├áo hß║ºm lß╗¡a nhß╗Å khoß║úng 15 ph├║t.\nB╞░ß╗¢c 4: Khi b├¡ ─æß╗Å ─æ├ú ch├¡n mß╗üm bß╗ƒ, n├¬m nß║┐m lß║íi gia vß╗ï vß╗½a miß╗çng, rß║»c ng├▓ gai th├íi nhß╗Å v├áo rß╗ôi tß║»t bß║┐p m├║c ra b├ít.','2026-10-10 08:19:01','2026-10-10 11:35:23'),(10,'B├ính X├¿o','banh-xeo','B├ính x├¿o miß╗ün T├óy gi├▓n rß╗Ñm','https://upload.wikimedia.org/wikipedia/commons/5/59/Banh_Xeo_with_fish_sauce_and_vegetables.jpg',45,4,'Trung b├¼nh',1,'B╞░ß╗¢c 1: Pha bß╗Öt b├ính x├¿o vß╗¢i n╞░ß╗¢c cß╗æt dß╗½a, bß╗Öt nghß╗ç, h├ánh l├í th├íi nhß╗Å v├á mß╗Öt x├¡u muß╗æi, ─æß╗â bß╗Öt nghß╗ë khoß║úng 30 ph├║t.\nB╞░ß╗¢c 2: X├áo x╞í t├┤m, thß╗ït ba chß╗ë th├íi mß╗Ång v├á ─æß║¡u xanh x├ít vß╗Å ─æ├ú hß║Ñp ch├¡n vß╗¢i mß╗Öt ch├║t gia vß╗ï ─æß╗â l├ám nh├ón.\nB╞░ß╗¢c 3: Qu├⌐t dß║ºu ─ân l├¬n chß║úo n├│ng, m├║c mß╗Öt v├í bß╗Öt ─æß╗ò v├áo chß║úo xoay ─æß╗üu, thß║ú t├┤m, thß╗ït, gi├í ─æß╗ù v├áo ─æß║¡y nß║»p 2 ph├║t.\nB╞░ß╗¢c 4: Khi viß╗ün b├ính tr├│c v├á gi├▓n, gß║¡p ─æ├┤i b├ính lß║íi v├á vß╗¢t ra ─æ─⌐a, d├╣ng k├¿m vß╗¢i rau sß╗æng c├íc loß║íi v├á n╞░ß╗¢c mß║»m chua ngß╗ìt.','2026-10-10 08:19:01','2026-10-10 11:35:23'),(11,'Sinh Tß╗æ Xo├ái','sinh-to-xoai','Sinh tß╗æ xo├ái l├á m├│n ─æß╗ô uß╗æng giß║úi kh├ít tuyß╗çt vß╗¥i cho nhß╗»ng ng├áy h├¿ oi bß╗⌐c. Vß╗ï ngß╗ìt lß╗ïm, th╞ím lß╗½ng cß╗ºa xo├ái ch├¡n kß║┐t hß╗úp c├╣ng ch├║t b├⌐o ngß║¡y cß╗ºa sß╗»a mang ─æß║┐n mß╗Öt ly sinh tß╗æ m├ít lß║ính, bß╗ò sung nhiß╗üu vitamin C v├á n─âng l╞░ß╗úng.','https://upload.wikimedia.org/wikipedia/commons/0/09/Fresh-mango-smoothie_01.jpg',10,2,'Dß╗à',5,'B╞░ß╗¢c 1: Chß╗ìn xo├ái ch├¡n ngß╗ìt, gß╗ìt vß╗Å v├á cß║»t lß║Ñy phß║ºn thß╗ït xo├ái th├ánh c├íc hß║ít lß╗▒u lß╗¢n.\nB╞░ß╗¢c 2: Cho thß╗ït xo├ái v├áo cß╗æi xay sinh tß╗æ c├╣ng vß╗¢i sß╗»a t╞░╞íi, sß╗»a ─æß║╖c v├á mß╗Öt ch├║t n╞░ß╗¢c cß╗æt chanh ─æß╗â l├ám t─âng h╞░╞íng vß╗ï.\nB╞░ß╗¢c 3: Th├¬m ─æ├í b├áo v├áo cß╗æi, xay ß╗ƒ tß╗æc ─æß╗Ö cao cho ─æß║┐n khi hß╗ùn hß╗úp nhuyß╗àn mß╗ïn v├á b├┤ng xß╗æp.\nB╞░ß╗¢c 4: R├│t sinh tß╗æ ra ly, trang tr├¡ bß║▒ng v├ái l├ít xo├ái t╞░╞íi cß║»t khß╗æi hoß║╖c l├í bß║íc h├á tr├¬n miß╗çng ly rß╗ôi th╞░ß╗ƒng thß╗⌐c ngay.','2026-10-10 08:19:01','2026-10-10 11:41:09'),(13,'Gan ch├íy tß╗Åi ','gan-ch-y-t-i-','Gan ch├íy tß╗Åi l├á m├│n ─ân Viß╗çt Nam l├ám tß╗½ gan (th╞░ß╗¥ng l├á gan heo hoß║╖c g├á), x├áo hoß║╖c chi├¬n vß╗¢i nhiß╗üu tß╗Åi ─æß║┐n khi tß╗Åi th╞ím, v├áng v├á h╞íi ch├íy, tß║ío vß╗ï ─æß║¡m ─æ├á, th╞ím nß╗⌐c.','https://images.unsplash.com/photo-1604908176997-125f25cc6f3d?q=80&w=800&auto=format&fit=crop',45,1,'Kh├│',1,'1. Rß╗¡a sß║ích gan, th├íi mß╗Ång, ╞░ß╗¢p vß╗¢i muß╗æi, ti├¬u, n╞░ß╗¢c mß║»m khoß║úng 10ΓÇô15 ph├║t.\r\n2. Phi th╞ím nhiß╗üu tß╗Åi b─âm vß╗¢i dß║ºu.\r\n3. Cho gan v├áo x├áo nhanh tr├¬n lß╗¡a lß╗¢n ─æß║┐n khi ch├¡n, tß╗Åi v├áng ch├íy th╞ím.\r\n4. N├¬m nß║┐m lß║íi cho vß╗½a miß╗çng, tß║»t bß║┐p.','2026-10-10 10:48:44','2026-10-10 11:12:01');
/*!40000 ALTER TABLE `recipes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` varchar(50) DEFAULT 'user',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'thang','$2y$10$4hPm2VirZdOeZPxz8XT6.ePUSIn3AqGGG59qfg556tCEi3Pi9bIOm','admin','2026-10-10 08:21:28');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-10-10 11:41:55
