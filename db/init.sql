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
INSERT INTO `categories` VALUES (1,'Món chính','mon-chinh','Các món ăn chính trong bữa cơm'),(2,'Món canh','mon-canh','Các món canh thanh mát'),(3,'Món chay','mon-chay','Các món ăn chay thanh đạm'),(4,'Tráng miệng','trang-mieng','Đồ ngọt sau bữa ăn'),(5,'Đồ uống','do-uong','Nước giải khát các loại');
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
INSERT INTO `ingredients` VALUES (2,'Bánh phở'),(20,'Bánh tráng'),(39,'Bí đỏ'),(43,'Bột bánh xèo'),(33,'Bột năng'),(6,'Bún tươi'),(15,'Cà chua'),(7,'Cá lóc'),(48,'Đá bào'),(36,'Da heo'),(42,'Dầu ăn'),(16,'Đậu bắp'),(29,'Dầu hào'),(8,'Đậu hũ non'),(13,'Đu đủ xanh'),(14,'Dứa'),(34,'Đường đen'),(25,'Đường phèn'),(49,'Gan heo'),(10,'Gạo tấm'),(24,'Gelatin'),(45,'Giá đỗ'),(26,'Hạnh nhân lát'),(40,'Hành tím'),(38,'Mật ong'),(17,'Me chua'),(41,'Ngò gai'),(46,'Nước cốt dừa'),(4,'Nước mắm'),(28,'Sa tế'),(32,'Sữa đặc'),(22,'Sữa tươi không đường'),(35,'Sườn cốt lết'),(9,'Sườn non'),(37,'Thính gạo'),(11,'Thịt ba chỉ'),(3,'Thịt bò'),(5,'Thịt heo'),(27,'Thịt heo băm'),(18,'Thịt lợn luộc'),(12,'Thịt nạc vai băm'),(50,'Tiêu đen'),(30,'Tỏi băm'),(44,'Tôm đất'),(19,'Tôm tươi'),(31,'Trà đen'),(23,'Whipping cream'),(21,'Xà lách'),(47,'Xoài chín'),(1,'Xương bò');
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
INSERT INTO `recipe_ingredients` VALUES (1,1,'1','kg'),(1,2,'500','g'),(1,3,'300','g'),(2,4,'5','muỗng'),(2,6,'1','kg'),(2,11,'500','g'),(2,12,'300','g'),(2,13,'1/2','quả'),(3,7,'500','g'),(3,14,'1/2','quả'),(3,15,'2','quả'),(3,16,'100','g'),(3,17,'50','g'),(4,18,'300','g'),(4,19,'300','g'),(4,20,'1','xấp'),(4,21,'200','g'),(5,22,'500','ml'),(5,23,'500','ml'),(5,24,'25','g'),(5,25,'150','g'),(5,26,'50','g'),(6,8,'2','hộp'),(6,27,'150','g'),(6,28,'2','muỗng'),(6,29,'1','muỗng'),(6,30,'1','củ'),(7,31,'50','g'),(7,32,'100','ml'),(7,33,'150','g'),(7,34,'100','g'),(8,10,'500','g'),(8,35,'500','g'),(8,36,'200','g'),(8,37,'50','g'),(8,38,'2','muỗng'),(9,39,'500','g'),(9,40,'2','củ'),(9,41,'1','mớ'),(9,42,'1','muỗng'),(10,43,'400','g'),(10,44,'300','g'),(10,45,'200','g'),(10,46,'200','ml'),(11,47,'2','quả'),(11,48,'1','ly'),(13,49,'300','g'),(13,50,'1','muỗng');
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
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recipes`
--

LOCK TABLES `recipes` WRITE;
/*!40000 ALTER TABLE `recipes` DISABLE KEYS */;
INSERT INTO `recipes` VALUES (1,'Phở Bò','ph-b-','Món nước nổi tiếng của Việt Nam, gồm bánh phở mềm, nước dùng xương bò hầm thơm, thịt bò tái hoặc chín, ăn kèm rau thơm, chanh, ớt','https://upload.wikimedia.org/wikipedia/commons/5/53/Pho-Beef-Noodles-2008.jpg',120,4,'Khó',1,'Bước 1: Rửa sạch xương bò, luộc sơ qua nước sôi để khử mùi hôi, sau đó hầm với gừng, hành tím nướng và các gia vị phở trong 2-3 tiếng.\r\nBước 2: Thái mỏng thịt bò tái. Rửa sạch các loại rau thơm, ngò gai, húng quế và thái nhỏ hành lá.\r\nBước 3: Trần bánh phở qua nước sôi cho mềm rồi cho vào bát. Xếp thịt bò thái mỏng lên trên cùng với hành ngò.\r\nBước 4: Chan nước dùng đang sôi sùng sục vào bát để làm chín thịt bò tái, ăn kèm với quẩy và chanh ớt.','2026-10-10 08:19:01','2026-10-10 11:49:31'),(2,'Bún Chả','b-n-ch-','Đặc sản Hà Nội: bún tươi ăn cùng chả viên và chả miếng nướng than hoa, chan nước mắm chua ngọt, kèm rau sống.','https://upload.wikimedia.org/wikipedia/commons/4/4d/B%C3%BAn_ch%E1%BA%A3_Vietnamese_food.jpg',60,4,'Trung bình',1,'Bước 1: Thái mỏng thịt ba chỉ và nặn thịt nạc băm thành từng viên nhỏ. Ướp thịt với nước mắm, đường, tiêu, hành tỏi trong 30 phút.\r\nBước 2: Nướng thịt ba chỉ và thịt viên trên bếp than hoa cho đến khi chín vàng, xém cạnh và dậy mùi thơm.\r\nBước 3: Pha nước chấm chua ngọt với tỷ lệ mắm, đường, chanh, nước lọc vừa ăn, thêm tỏi ớt băm và đu đủ xanh thái mỏng.\r\nBước 4: Bày bún tươi, rau sống và chả nướng ra đĩa, thưởng thức bằng cách chấm bún và chả vào bát nước chấm chua ngọt.','2026-10-10 08:19:01','2026-10-10 11:49:53'),(3,'Canh Chua Cá Lóc','canh-chua-c-l-c','Món canh miền Tây, nước dùng chua thanh từ me hoặc cà chua, nấu với cá lóc, đậu bắp, bạc hà, giá, thơm ngon và thanh mát.','https://upload.wikimedia.org/wikipedia/commons/a/a0/Canhchua2.jpg',30,4,'Dễ',2,'Bước 1: Làm sạch cá lóc, cắt khúc vừa ăn. Nhặt và rửa sạch giá, dọc mùng, đậu bắp, dứa và cà chua.\r\nBước 2: Phi thơm tỏi, xào sơ cà chua và dứa để tạo màu, đổ nước vào đun sôi và dầm cốt me chua.\r\nBước 3: Cho cá lóc vào đun chín, tiếp tục thêm đậu bắp, dọc mùng, giá đỗ vào nấu vừa chín tới để rau vẫn giữ độ giòn.\r\nBước 4: Nêm nếm lại gia vị cho cân bằng chua ngọt, tắt bếp, rắc rau ngò ôm, ngò gai thái nhỏ vài lát ớt lên trên.','2026-10-10 08:19:01','2026-10-10 11:50:08'),(4,'Gỏi Cuốn','goi-cuon','Gỏi cuốn tôm thịt là món ăn vặt hoặc khai vị cực kỳ thanh mát và dễ ăn. Bên trong lớp bánh tráng mỏng dai là tôm tươi đỏ au, thịt luộc thái mỏng, bún tươi và ngập tràn các loại rau thơm, chấm kèm tương đen đậu phộng đậm đà.','https://upload.wikimedia.org/wikipedia/commons/9/90/G%E1%BB%8Fi_cu%E1%BB%91n.jpg',20,4,'Dễ',1,'Bước 1: Thịt ba chỉ luộc chín, thái lát mỏng. Tôm luộc chín, bóc vỏ và chẻ đôi.\nBước 2: Rửa sạch xà lách, hẹ và các loại rau thơm. Bún tươi gỡ tơi để sẵn.\nBước 3: Làm ướt bánh tráng, xếp xà lách, rau thơm, bún, thịt lợn và tôm lên trên, cuộn thật chặt tay.\nBước 4: Pha nước chấm tương đen với bơ đậu phộng, rắc thêm chút hành phi và đậu phộng rang giã nhỏ để chấm gỏi.','2026-10-10 08:19:01','2026-10-10 11:41:09'),(5,'Chè Khúc Bạch','che-khuc-bach','Chè khúc bạch gây thương nhớ bởi sự thanh tao và đẹp mắt. Những khối thạch núng nính, mềm tan béo ngậy vị sữa tươi, ăn cùng nước đường phèn lá dứa dịu mát, điểm xuyết thêm nhãn lồng hay vải thiều và hạnh nhân rang giòn rụm.','https://upload.wikimedia.org/wikipedia/commons/c/c3/Ch%C3%A8_b%C3%A0_ba.jpg',40,6,'Trung bình',4,'Bước 1: Ngâm gelatin cho nở. Đun nóng sữa tươi không đường và whipping cream, hòa tan đường và gelatin đã ngâm vào.\nBước 2: Đổ hỗn hợp sữa ra khuôn, cất vào tủ lạnh khoảng 3-4 tiếng để thạch đông lại, sau đó cắt thành khối vuông nhỏ.\nBước 3: Đun sôi nước với đường phèn và lá dứa làm nước chan chè, để nguội. Ngâm hạnh nhân lát qua nước và rang vàng rụm.\nBước 4: Múc thạch khúc bạch ra bát, thêm vải thiều, chan nước đường lá dứa ướp lạnh và rắc hạnh nhân rang lên trên cùng.','2026-10-10 08:19:01','2026-10-10 11:41:09'),(6,'Đậu Hũ Tứ Xuyên','-u-h-t-xuy-n','Món Trung Hoa phổ biến ở Việt Nam: đậu hũ non mềm nấu với thịt băm, sốt cay Tứ Xuyên (đậu tương, ớt, tiêu), vị đậm đà, cay nồng.','https://upload.wikimedia.org/wikipedia/commons/8/88/Chen_Mapo_Tofu.jpg',20,2,'Dễ',3,'Bước 1: Đậu hũ non cắt khối vuông nhỏ, trần sơ qua nước sôi có pha chút muối để đậu không bị nát.\r\nBước 2: Băm nhỏ thịt heo. Tỏi, gừng băm nhuyễn. Pha sẵn bát nước xốt gồm nước tương, dầu hào, đường và sa tế.\r\nBước 3: Xào tỏi gừng cho thơm, đổ thịt băm vào xào săn lại, sau đó thêm nước xốt cay đã pha và một chút nước đun sôi.\r\nBước 4: Thả đậu hũ non vào chảo, lắc nhẹ chảo để đậu ngấm xốt, đun liu riu thêm 3 phút rồi rắc hành lá thái nhỏ tắt bếp.','2026-10-10 08:19:01','2026-10-10 11:50:30'),(7,'Trà Sữa Trân Châu','tra-sua-tran-chau','Trà sữa trân châu không chỉ là một thức uống mà còn là đam mê của giới trẻ. Sự hòa quyện hoàn hảo giữa vị đắng nhẹ của hồng trà, vị béo của sữa và những hạt trân châu dai dẻo, ngọt ngào tạo nên một hương vị khó cưỡng.','https://upload.wikimedia.org/wikipedia/commons/a/a2/Bubble_Tea.png',30,2,'Dễ',5,'Bước 1: Nhào bột năng với cacao và nước sôi để tạo thành khối bột mịn, sau đó nặn thành từng viên trân châu nhỏ.\nBước 2: Đun sôi nước, thả trân châu vào luộc đến khi nổi lên, vớt ra ngâm ngay vào bát nước đá, rồi trộn với mật ong.\nBước 3: Hãm trà đen với nước sôi trong 10 phút, lọc bỏ bã trà để lấy nước cốt.\nBước 4: Lắc đều nước cốt trà, sữa đặc, sữa tươi và đá viên trong bình shaker, đổ ra ly đã có sẵn trân châu ở đáy.','2026-10-10 08:19:01','2026-10-10 11:41:09'),(8,'Cơm Tấm Sườn Bì','c-m-t-m-s-n-b-','Cơm tấm Sài Gòn ăn kèm sườn nướng thơm, bì heo trộn thính, đồ chua, mỡ hành và nước mắm pha, rất đậm đà.','https://upload.wikimedia.org/wikipedia/commons/b/b0/C%C6%A1m_T%E1%BA%A5m%2C_Da_Nang%2C_Vietnam.jpg',60,4,'Trung bình',1,'Bước 1: Ướp sườn cốt lết với mật ong, nước mắm, tỏi băm, hành tím băm, dầu hào và chút nước cốt dừa trong 2 tiếng.\r\nBước 2: Nướng sườn trên bếp than hồng ở lửa vừa, lật đều tay để sườn chín vàng ươm và không bị cháy xém.\r\nBước 3: Làm bì heo: Trộn thịt nạc heo ram thái sợi với da heo luộc thái mỏng và thính gạo rang cho thật đều.\r\nBước 4: Trình bày cơm ra đĩa, đặt sườn nướng, bì heo, chả trứng hấp lên trên, ăn kèm với mỡ hành, đồ chua và nước mắm mặn ngọt.','2026-10-10 08:19:01','2026-10-10 11:50:40'),(9,'Canh Bí Đỏ Thịt Băm','canh-b-th-t-b-m','Canh thanh đạm, bí đỏ nấu mềm với thịt heo băm, nước dùng ngọt tự nhiên, dễ ăn, phù hợp mọi lứa tuổi.','https://upload.wikimedia.org/wikipedia/commons/c/cd/K%C3%BCrbissuppe_mit_Kern%C3%B6l.JPG',25,4,'Dễ',2,'Bước 1: Bí đỏ gọt vỏ, bỏ ruột, rửa sạch và thái miếng vuông. Thịt băm ướp với chút muối, tiêu và hành tím.\r\nBước 2: Phi thơm hành tím trong nồi với một ít dầu ăn, cho thịt băm vào xào săn lại để dậy mùi thơm.\r\nBước 3: Đổ lượng nước vừa đủ dùng vào nồi đun sôi, hớt bỏ bọt dơ, sau đó cho bí đỏ vào hầm lửa nhỏ khoảng 15 phút.\r\nBước 4: Khi bí đỏ đã chín mềm bở, nêm nếm lại gia vị vừa miệng, rắc ngò gai thái nhỏ vào rồi tắt bếp múc ra bát.','2026-10-10 08:19:01','2026-10-10 11:50:54'),(10,'Bánh Xèo','b-nh-x-o','Bánh bột gạo chiên vàng giòn, nhân tôm thịt, giá đỗ, ăn cuốn với rau sống và chấm nước mắm chua ngọt.','https://upload.wikimedia.org/wikipedia/commons/5/59/Banh_Xeo_with_fish_sauce_and_vegetables.jpg',45,4,'Trung bình',1,'Bước 1: Pha bột bánh xèo với nước cốt dừa, bột nghệ, hành lá thái nhỏ và một xíu muối, để bột nghỉ khoảng 30 phút.\r\nBước 2: Xào xơ tôm, thịt ba chỉ thái mỏng và đậu xanh xát vỏ đã hấp chín với một chút gia vị để làm nhân.\r\nBước 3: Quét dầu ăn lên chảo nóng, múc một vá bột đổ vào chảo xoay đều, thả tôm, thịt, giá đỗ vào đậy nắp 2 phút.\r\nBước 4: Khi viền bánh tróc và giòn, gập đôi bánh lại và vớt ra đĩa, dùng kèm với rau sống các loại và nước mắm chua ngọt.','2026-10-10 08:19:01','2026-10-10 11:51:11'),(11,'Sinh Tố Xoài','sinh-to-xoai','Sinh tố xoài là món đồ uống giải khát tuyệt vời cho những ngày hè oi bức. Vị ngọt lịm, thơm lừng của xoài chín kết hợp cùng chút béo ngậy của sữa mang đến một ly sinh tố mát lạnh, bổ sung nhiều vitamin C và năng lượng.','https://upload.wikimedia.org/wikipedia/commons/0/09/Fresh-mango-smoothie_01.jpg',10,2,'Dễ',5,'Bước 1: Chọn xoài chín ngọt, gọt vỏ và cắt lấy phần thịt xoài thành các hạt lựu lớn.\nBước 2: Cho thịt xoài vào cối xay sinh tố cùng với sữa tươi, sữa đặc và một chút nước cốt chanh để làm tăng hương vị.\nBước 3: Thêm đá bào vào cối, xay ở tốc độ cao cho đến khi hỗn hợp nhuyễn mịn và bông xốp.\nBước 4: Rót sinh tố ra ly, trang trí bằng vài lát xoài tươi cắt khối hoặc lá bạc hà trên miệng ly rồi thưởng thức ngay.','2026-10-10 08:19:01','2026-10-10 11:41:09'),(13,'Gan cháy tỏi ','gan-ch-y-t-i-','Gan cháy tỏi là món ăn Việt Nam làm từ gan (thường là gan heo hoặc gà), xào hoặc chiên với nhiều tỏi đến khi tỏi thơm, vàng và hơi cháy, tạo vị đậm đà, thơm nức.','https://images.unsplash.com/photo-1604908176997-125f25cc6f3d?q=80&w=800&auto=format&fit=crop',35,1,'Khó',1,'1. Rửa sạch gan, thái mỏng, ướp với muối, tiêu, nước mắm khoảng 10–15 phút.\r\n2. Phi thơm nhiều tỏi băm với dầu.\r\n3. Cho gan vào xào nhanh trên lửa lớn đến khi chín, tỏi vàng cháy thơm.\r\n4. Nêm nếm lại cho vừa miệng, tắt bếp.','2026-10-10 10:48:44','2026-10-10 11:59:44'),(14,'Bún Bò Huế Đặc Biệt','bun-bo-hue-dac-biet','Món bún bò cay nồng đậm đà chuẩn vị cố đô Huế, ngon nhức nách, làm tại nhà nhanh chóng đơn giản. ','https://i2.ex-cdn.com/crystalbay.com/files/content/2024/08/15/bun-bo-hue-6-0935.jpeg',90,4,'Khó',1,'Bước 1: Sơ chế và hầm xương bò + bắp bò\r\nRửa sạch xương bò và bắp bò với nước muối loãng. Chần qua nước sôi 3–5 phút để loại bỏ tạp chất và mùi hôi, rồi rửa lại bằng nước lạnh. Cho xương vào nồi lớn cùng 4–5 lít nước, đun sôi rồi hạ lửa nhỏ, hầm trong 2 tiếng. Trong quá trình hầm, thường xuyên vớt bọt để nước dùng trong.\r\n\r\nBước 2: Sơ chế sả, ớt, hành tím và các gia vị\r\nSả rửa sạch, đập dập, cắt khúc (phần gốc để phi, phần lá để cho vào nồi nước dùng). Ớt bỏ hạt, băm nhuyễn. Hành tím, tỏi bóc vỏ, băm nhỏ. Chuẩn bị mắm ruốc Huế, dầu điều, đường phèn, nước mắm để sẵn sàng nêm nếm.\r\n\r\nBước 3: Phi thơm sả – ớt – hành tỏi tạo màu\r\nBắc chảo, cho dầu ăn + dầu điều vào đun nóng. Cho sả băm, ớt băm, hành tím, tỏi vào phi thơm vàng đến khi dậy mùi. Hỗn hợp này sẽ tạo màu đỏ cam đặc trưng và mùi thơm nồng cho nồi bún bò.\r\n\r\nBước 4: Nêm mắm ruốc Huế và gia vị vào nồi nước dùng\r\nHòa mắm ruốc với một ít nước dùng nóng, lọc bỏ cặn rồi đổ vào nồi. Thêm hỗn hợp sả – ớt – hành tỏi đã phi ở bước 3. Nêm đường phèn, nước mắm, hạt nêm, muối sao cho vị đậm đà, hơi cay nhẹ, thơm mùi ruốc nhưng không gắt. Đun sôi lại rồi hạ lửa, tiếp tục hầm thêm 30–45 phút cho gia vị thấm đều.\r\n\r\nBước 5: Luộc bắp bò, giò heo và thái lát\r\nVớt bắp bò và giò heo đã chín mềm trong nồi hầm ra, ngâm vào nước lạnh cho săn chắc. Thái bắp bò thành lát mỏng vừa ăn, giò heo chặt miếng vừa. Phần nước hầm giữ nguyên để làm nước dùng.\r\n\r\nBước 6: Trần bún và chuẩn bị tô\r\nBún tươi trần qua nước sôi khoảng 30 giây cho nóng, vớt ra để ráo. Xếp bún vào tô, lần lượt xếp thịt bắp bò, giò heo, chả lụa (nếu có) lên trên. Rắc hành ngò cắt nhỏ, tiêu, ớt tươi thái lát.\r\n\r\nBước 7: Chan nước dùng sôi và thưởng thức\r\nĐun nước dùng thật sôi (sôi sùng sục), rồi chan trực tiếp lên tô bún vừa xếp. Nước dùng nóng sẽ làm chín tái phần thịt mỏng và tỏa mùi thơm đặc trưng. Dọn kèm rau sống, bắp chuối bào, giá đỗ, chanh, ớt tươi và thưởng thức ngay khi còn nóng.','2026-10-12 12:36:12','2026-10-12 13:02:41');
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

-- Dump completed on 2026-10-12 13:32:56
