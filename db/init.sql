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
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Món chính','mon-chinh','Các món ăn chính trong bữa cơm'),(2,'Món canh','mon-canh','Các món canh thanh mát'),(3,'Món chay','mon-chay','Các món ăn chay thanh đạm'),(4,'Tráng miệng','trang-mieng','Đồ ngọt sau bữa ăn'),(5,'Đồ uống','do-uong','Nước giải khát các loại'),(6,'Món Á','mon-a','Hương vị đặc trưng từ các nền ẩm thực châu Á'),(7,'Món Âu','mon-au','Tinh hoa ẩm thực phương Tây sang trọng và tinh tế'),(8,'Bữa sáng','bua-sang','Các món ăn sáng giàu năng lượng, khởi đầu ngày mới'),(9,'Bữa trưa','bua-trua','Thực đơn cơm trưa phong phú, đầy đủ dưỡng chất'),(10,'Bữa tối','bua-toi','Mâm cơm ấm cúng, gắn kết gia đình sau ngày làm việc'),(11,'Món ăn kiêng / Keto','an-kieng-keto','Công thức ăn uống lành mạnh, ít tinh bột, giữ dáng'),(12,'Món nhanh < 15 phút','mon-nhanh-15p','Nấu nhanh thần tốc cho người bận rộn');
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
) ENGINE=InnoDB AUTO_INCREMENT=101 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ingredients`
--

LOCK TABLES `ingredients` WRITE;
/*!40000 ALTER TABLE `ingredients` DISABLE KEYS */;
INSERT INTO `ingredients` VALUES (70,'Bánh mì giòn'),(2,'Bánh phở'),(20,'Bánh tráng'),(51,'Bắp bò'),(39,'Bí đỏ'),(61,'Bơ lạt'),(67,'Bơ sáp'),(93,'Bột bánh cuốn'),(43,'Bột bánh xèo'),(33,'Bột năng'),(6,'Bún tươi'),(15,'Cà chua'),(65,'Cà chua bi'),(59,'Cà chua xay nhuyễn'),(7,'Cá lóc'),(52,'Chả cua'),(95,'Chả quế'),(98,'Chanh vàng'),(76,'Cơm nguội'),(48,'Đá bào'),(36,'Da heo'),(42,'Dầu ăn'),(16,'Đậu bắp'),(78,'Đậu Hà Lan'),(29,'Dầu hào'),(8,'Đậu hũ non'),(69,'Dầu ô liu'),(13,'Đu đủ xanh'),(14,'Dứa'),(66,'Dưa chuột'),(82,'Đùi gà góc tư'),(34,'Đường đen'),(25,'Đường phèn'),(49,'Gan heo'),(10,'Gạo tấm'),(83,'Gạo tẻ thơm'),(24,'Gelatin'),(45,'Giá đỗ'),(85,'Gừng tươi'),(79,'Hành lá'),(26,'Hạnh nhân lát'),(88,'Hành phi giòn'),(60,'Hành tây'),(40,'Hành tím'),(92,'Khoai tây chiên'),(91,'Lá hương thảo'),(100,'Lá rong biển Nori'),(87,'Lạc rang giã dập'),(77,'Lạp xưởng'),(55,'Mắm ruốc Huế'),(97,'Măng tây xanh'),(38,'Mật ong'),(17,'Me chua'),(94,'Mộc nhĩ nấm hương'),(58,'Mỳ Ý (Spaghetti)'),(41,'Ngò gai'),(75,'Ngò rí'),(46,'Nước cốt dừa'),(84,'Nước cốt nghệ'),(80,'Nước dừa xiêm'),(4,'Nước mắm'),(89,'Nước mắm chua ngọt'),(86,'Nước tương tỏi ớt'),(54,'Ớt bột Huế'),(73,'Pate gan heo'),(96,'Phi lê cá hồi tươi'),(99,'Phô mai Mozzarella'),(62,'Phô mai Parmesan'),(53,'Sả'),(28,'Sa tế'),(68,'Sốt mè rang'),(32,'Sữa đặc'),(22,'Sữa tươi không đường'),(35,'Sườn cốt lết'),(9,'Sườn non'),(90,'Thăn bò Mỹ (Ribeye)'),(37,'Thính gạo'),(11,'Thịt ba chỉ'),(3,'Thịt bò'),(5,'Thịt heo'),(27,'Thịt heo băm'),(18,'Thịt lợn luộc'),(12,'Thịt nạc vai băm'),(50,'Tiêu đen'),(30,'Tỏi băm'),(44,'Tôm đất'),(19,'Tôm tươi'),(31,'Trà đen'),(71,'Trứng gà'),(81,'Trứng vịt'),(63,'Ức gà phi lê'),(23,'Whipping cream'),(21,'Xà lách'),(64,'Xà lách Romaine'),(74,'Xíu mại'),(47,'Xoài chín'),(72,'Xúc xích'),(1,'Xương bò');
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
INSERT INTO `recipe_ingredients` VALUES (1,1,'1','kg'),(1,2,'500','g'),(1,3,'300','g'),(2,4,'5','muỗng'),(2,6,'1','kg'),(2,11,'500','g'),(2,12,'300','g'),(2,13,'1/2','quả'),(3,7,'500','g'),(3,14,'1/2','quả'),(3,15,'2','quả'),(3,16,'100','g'),(3,17,'50','g'),(4,18,'300','g'),(4,19,'300','g'),(4,20,'1','xấp'),(4,21,'200','g'),(5,22,'500','ml'),(5,23,'500','ml'),(5,24,'25','g'),(5,25,'150','g'),(5,26,'50','g'),(6,8,'2','hộp'),(6,27,'150','g'),(6,28,'2','muỗng'),(6,29,'1','muỗng'),(6,30,'1','củ'),(7,31,'50','g'),(7,32,'100','ml'),(7,33,'150','g'),(7,34,'100','g'),(8,10,'500','g'),(8,35,'500','g'),(8,36,'200','g'),(8,37,'50','g'),(8,38,'2','muỗng'),(9,39,'500','g'),(9,40,'2','củ'),(9,41,'1','mớ'),(9,42,'1','muỗng'),(10,43,'400','g'),(10,44,'300','g'),(10,45,'200','g'),(10,46,'200','ml'),(11,47,'2','quả'),(11,48,'1','ly'),(13,49,'300','g'),(13,50,'1','muỗng'),(14,1,'500','g'),(14,6,'500','g'),(14,51,'400','g'),(14,52,'200','g'),(14,53,'4','cây'),(14,55,'2','muỗng'),(15,3,'250','g'),(15,30,'1','củ'),(15,58,'300','g'),(15,59,'200','ml'),(15,60,'1','củ'),(15,61,'30','g'),(15,62,'50','g'),(16,63,'300','g'),(16,64,'200','g'),(16,65,'100','g'),(16,66,'1','quả'),(16,67,'1','quả'),(16,68,'4','muỗng'),(16,69,'1','muỗng'),(17,61,'20','g'),(17,70,'2','ổ'),(17,71,'2','quả'),(17,72,'2','cây'),(17,73,'50','g'),(17,74,'2','viên'),(17,75,'30','g'),(18,19,'150','g'),(18,71,'2','quả'),(18,76,'400','g'),(18,77,'2','cây'),(18,78,'100','g'),(18,79,'2','nhánh'),(19,4,'3','muỗng'),(19,11,'500','g'),(19,25,'2','muỗng'),(19,40,'3','củ'),(19,80,'500','ml'),(19,81,'6','quả'),(20,66,'2','quả'),(20,82,'2','cái'),(20,83,'300','g'),(20,84,'1','muỗng'),(20,85,'1','củ'),(20,86,'3','muỗng'),(21,3,'300','g'),(21,6,'500','g'),(21,21,'150','g'),(21,45,'150','g'),(21,87,'50','g'),(21,88,'30','g'),(21,89,'100','ml'),(22,50,'2','muỗng'),(22,61,'30','g'),(22,69,'2','muỗng'),(22,90,'400','g'),(22,91,'2','nhánh'),(22,92,'200','g'),(23,4,'100','ml'),(23,12,'200','g'),(23,88,'50','g'),(23,93,'300','g'),(23,94,'50','g'),(23,95,'150','g'),(24,50,'1','muỗng'),(24,61,'30','g'),(24,69,'2','muỗng'),(24,96,'350','g'),(24,97,'200','g'),(24,98,'1','quả'),(25,22,'30','ml'),(25,71,'4','quả'),(25,79,'20','g'),(25,99,'80','g'),(25,100,'2','lá');
/*!40000 ALTER TABLE `recipe_ingredients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `recipe_reviews`
--

DROP TABLE IF EXISTS `recipe_reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `recipe_reviews` (
  `id` int NOT NULL AUTO_INCREMENT,
  `recipe_id` int NOT NULL,
  `user_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rating` int NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `recipe_id` (`recipe_id`),
  CONSTRAINT `fk_review_recipe` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recipe_reviews`
--

LOCK TABLES `recipe_reviews` WRITE;
/*!40000 ALTER TABLE `recipe_reviews` DISABLE KEYS */;
INSERT INTO `recipe_reviews` VALUES (6,1,'Hoàng Minh (Hà Nội)',5,'Nước dùng trong vắt, thơm nức mùi quế hồi và gừng nướng. Đúng chuẩn vị phở truyền thống!','2026-10-17 08:34:59'),(7,1,'Thanh Hằng',5,'Thịt bò tái mềm ngọt, nước dùng ngọt thanh từ xương chứ không bị gắt mì chính. Rất ngon!','2026-10-16 18:34:59'),(8,1,'Tuấn Kiệt',4,'Công thức rất dễ làm tại nhà, mình ninh xương tầm 2 tiếng là nước ngọt lịm rồi.','2026-10-16 04:34:59'),(9,2,'Quang Huy',5,'Chả viên nướng than hoa thơm nức mũi, nước chấm pha đúng tỷ lệ vàng chua ngọt vừa vặn.','2026-10-17 08:34:59'),(10,2,'Mai Phương',5,'Làm theo mẹo ướp thịt này chả không bị khô, nướng chảo hay than đều ngon bá cháy!','2026-10-16 18:34:59'),(11,3,'Út Lành Miền Tây',5,'Vị chua thanh của me dầm kết hợp cá lóc ngọt thịt, thêm bạc hà giòn rụm đúng chuẩn canh chua quê em.','2026-10-17 08:34:59'),(12,3,'Bích Thủy',4,'Bữa cơm trưa hè oi bức có bát canh này thì trôi cơm vô cùng, cả nhà ai cũng húp sạch.','2026-10-16 18:34:59'),(13,4,'Linh Đan Fit',5,'Món ruột của mình khi ăn kiêng Eat Clean, tôm thịt tươi cuốn rau thơm chấm tương đen đậu phộng béo bùi đỉnh chóp.','2026-10-17 08:34:59'),(14,4,'Trần Nam',5,'Cuốn chặt tay, ăn thanh mát không hề bị ngấy dầu mỡ. 10 điểm cho công thức!','2026-10-16 18:34:59'),(15,5,'Ngọc Anh Sweet',5,'Khúc bạch núng nính mềm tan, thơm ngậy whipping cream kết hợp hạnh nhân lát giòn rụm đã miệng lắm.','2026-10-17 08:34:59'),(16,5,'Khánh Vy',4,'Nước đường phèn lá dứa thanh tao, để ngăn mát tủ lạnh ăn giải nhiệt mùa hè tuyệt đỉnh.','2026-10-16 18:34:59'),(17,6,'Hải Đăng',5,'Vị cay tê chuẩn Tứ Xuyên, đậu non mềm mịn ngấm sốt sa tế thịt băm, ăn cùng cơm nóng là hết nồi cơm!','2026-10-17 08:34:59'),(18,6,'Vũ Phong',4,'Món này làm nhanh trong 15 phút mà đưa cơm khủng khiếp, gia đình mình rất thích.','2026-10-16 18:34:59'),(19,7,'Huyền My Tea',5,'Trân châu đường đen dẻo dai không bị sượng, cốt trà đậm đà thơm béo hơn hẳn ngoài tiệm!','2026-10-17 08:34:59'),(20,7,'Bảo Trâm',5,'Công thức nấu trân châu tại nhà siêu chi tiết, tiết kiệm mà lại an toàn cho các bé.','2026-10-16 18:34:59'),(21,8,'Chú Ba Sài Gòn',5,'Sườn nướng mật ong thấm vị, bì heo trộn thính giòn thơm, nước mắm pha kẹo sệt đúng điệu Sài Gòn.','2026-10-17 08:34:59'),(22,8,'Thế Anh',5,'Sườn mềm mọng nước không bị khô, làm theo hướng dẫn ướp 2 tiếng nướng lên thơm nức cả xóm.','2026-10-16 18:34:59'),(23,9,'Mẹ Bỉm Sữa',5,'Canh ngọt mát tự nhiên, bí đỏ bở tơi bé nhà mình rất mê. Nấu nhanh gọn cho bữa tối bận rộn.','2026-10-17 08:34:59'),(24,9,'Lan Hương',4,'Món canh dân dã dễ nấu, thanh nhiệt giải độc rất tốt cho cả nhà.','2026-10-16 18:34:59'),(25,10,'Dì Tư Quê',5,'Vỏ bánh mỏng giòn rụm lâu xẹp, nhân tôm thịt đầy đặn cuốn kèm rau rừng chấm mắm chua ngọt hết sẩy!','2026-10-17 08:34:59'),(26,10,'Việt Dũng',5,'Mẹo thêm chút bia và nước cốt dừa vào bột hiệu quả bất ngờ, vỏ bánh giòn tan ăn mãi không chán.','2026-10-16 18:34:59'),(27,11,'Hồng Nhung',5,'Xoài cát chín ngọt lịm xay cùng sữa đặc và đá bào bông xốp, uống giải khát mát lạnh sảng khoái.','2026-10-17 08:34:59'),(28,11,'Bé Bông',5,'Món này làm chưa đầy 5 phút là có ly đồ uống thơm ngon bổ dưỡng cho bữa phụ rồi.','2026-10-16 18:34:59'),(29,13,'Anh Hùng Nhậu',5,'Gan xào lửa lớn mềm mướt không bị khô xác, tỏi phi vàng cháy cạnh thơm nức mùi mắm tiêu, nhắm bia đỉnh!','2026-10-17 08:35:00'),(30,13,'Bác Sĩ Dinh Dưỡng',4,'Gan giàu sắt, làm theo cách khử mùi sữa tươi này gan sạch thơm và không hề có vị tanh.','2026-10-16 18:35:00'),(31,14,'Mệ Cố Đô',5,'Hương vị cay nồng đặc trưng mắm ruốc và sả phi, bắp bò hoa giòn sần sật, nước dùng đỏ cam bắt mắt mê ly!','2026-10-17 08:35:00'),(32,14,'Phan Thiết',5,'Mình đã nấu thử đãi bạn bè cuối tuần, ai cũng trầm trồ khen ngon hơn đi ăn hàng quán.','2026-10-16 18:35:00'),(33,15,'Lan Anh (Chef)',5,'Sợi mỳ chín chuẩn al dente, sốt bò bằm đậm đà thơm ngậy mùi thảo mộc Ý rất ngon!','2026-10-17 08:35:00'),(34,15,'Minh Hoàng',5,'Làm theo công thức này gia đình mình ai cũng khen, ngon hơn cả ngoài tiệm pizza!','2026-10-16 18:35:00'),(35,15,'David Le',5,'Classic Bolognese recipe! Perfect tomato balance and rich parmesan finish.','2026-10-16 04:35:00'),(36,16,'Thu Hà EatClean',5,'Món salad keto này rất hợp cho người giảm cân, sốt mè rang béo bùi vừa vặn, ức gà áp chảo không bị khô.','2026-10-17 08:35:00'),(37,16,'Gymmer Tuấn',5,'Bữa tối giàu protein ít carb lý tưởng sau buổi tập, rau củ giòn ngọt thanh mát.','2026-10-16 18:35:00'),(38,17,'Bảo Nam',5,'Bữa sáng nhanh gọn lẹ mà đầy đủ chất, trứng lòng đào béo ngậy pate xíu mại bánh mì giòn tan tuyệt vời.','2026-10-17 08:35:00'),(39,17,'Quỳnh Nga',5,'Chảo trứng xúc xích thơm lừng, chấm miếng bánh mì nóng giòn vào lòng đỏ trứng tan chảy ngất ngây.','2026-10-16 18:35:00'),(40,18,'Đầu Bếp Hoa kiều',5,'Hạt cơm săn bóng vàng ruộm, lạp xưởng tôm tươi và đậu Hà Lan hòa quyện rất bắt mắt và ngon miệng.','2026-10-17 08:35:00'),(41,18,'Mỹ Duyên',4,'Bí quyết bóp cơm nguội với lòng đỏ trứng trước khi rang chuẩn thật sự, cơm tơi từng hạt không dính chảo.','2026-10-16 18:35:00'),(42,19,'Hồng Phúc',5,'Thịt mềm rục thấm đượm vị ngọt thanh của nước dừa xiêm, mỡ trong veo không hề ngấy. Chuẩn cơm mẹ nấu!','2026-10-17 08:35:00'),(43,19,'Gia Đình Nhỏ',5,'Mâm cơm tối ấm cúng có nồi thịt kho hột vịt này là các con ăn thun thút hết 2 bát cơm.','2026-10-16 18:35:00'),(44,20,'Trí Dũng',5,'Da gà giòn tan rôm rốp, thịt bên trong vẫn mọng nước mềm ngọt, cơm chiên đỏ cà chua thơm phức.','2026-10-17 08:35:00'),(45,20,'Kim Oanh',5,'Bữa trưa công sở nấu hộp cơm này mang đi làm đồng nghiệp ai cũng hỏi xin bí quyết làm da gà giòn.','2026-10-16 18:35:00'),(46,21,'Hà My',5,'Bữa trưa thanh nhẹ dễ ăn, thịt bò xào vừa chín tới mềm ngọt, lạc rang hành phi giòn rụm rất ngon.','2026-10-17 08:35:00'),(47,21,'Văn Toàn',4,'Nước mắm pha chua ngọt rất khéo, ăn trưa không lo bị nặng bụng hay đầy hơi.','2026-10-16 18:35:00'),(48,22,'Chef Robert',5,'Thịt bò mềm mọng chuẩn Medium Rare, sốt tiêu đen cay nồng thơm lừng mùi bơ tỏi và thảo mộc.','2026-10-17 08:35:00'),(49,22,'Thảo Vy',5,'Làm bít tết tại nhà cho bữa tối lãng mạn cuối tuần thành công rực rỡ, thịt mềm như tan trong miệng.','2026-10-16 18:35:00'),(50,23,'Cô Bảy',5,'Bánh tráng mỏng mềm mướt tay, nhân thịt mộc nhĩ xào đậm đà, chấm nước mắm ấm nóng ăn vào buổi sáng ấm lòng.','2026-10-17 08:35:00'),(51,23,'Xuân Bắc',5,'Hành phi giòn thơm nức mũi, chả quế thơm lừng ăn kèm đúng chuẩn phong vị Hà Thành.','2026-10-16 18:35:00'),(52,24,'Huyền Trang Keto',5,'Da cá hồi giòn rụm như snack, thịt cá béo ngậy giàu dinh dưỡng, măng tây xào bơ tỏi ngọt lịm.','2026-10-17 08:35:00'),(53,24,'Tiến Đạt',5,'Món ăn sang xịn mịn chuẩn nhà hàng 5 sao mà làm tại nhà chỉ mất đúng 15 phút. Quá tuyệt!','2026-10-16 18:35:00'),(54,25,'Bé Sam Cook',5,'Trứng cuộn vàng óng mềm mịn, phô mai mozzarella kéo sợi béo ngậy các bé nhà mình thích mê!','2026-10-17 08:35:00'),(55,25,'Thúy Kiều',5,'Chỉ 10 phút là có món ăn nhanh vừa đẹp mắt vừa đủ chất, ăn kèm cơm trắng hay bánh mì đều ngon.','2026-10-16 18:35:00');
/*!40000 ALTER TABLE `recipe_reviews` ENABLE KEYS */;
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
  `rating` decimal(2,1) DEFAULT '4.8',
  `rating_count` int DEFAULT '120',
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `recipes_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recipes`
--

LOCK TABLES `recipes` WRITE;
/*!40000 ALTER TABLE `recipes` DISABLE KEYS */;
INSERT INTO `recipes` VALUES (1,'Phở Bò','ph-b-','Món nước nổi tiếng của Việt Nam, gồm bánh phở mềm, nước dùng xương bò hầm thơm, thịt bò tái hoặc chín, ăn kèm rau thơm, chanh, ớt','https://upload.wikimedia.org/wikipedia/commons/5/53/Pho-Beef-Noodles-2008.jpg',120,4,'Khó',1,'Bước 1: Rửa sạch xương bò, luộc sơ qua nước sôi để khử mùi hôi, sau đó hầm với gừng, hành tím nướng và các gia vị phở trong 2-3 tiếng.\r\nBước 2: Thái mỏng thịt bò tái. Rửa sạch các loại rau thơm, ngò gai, húng quế và thái nhỏ hành lá.\r\nBước 3: Trần bánh phở qua nước sôi cho mềm rồi cho vào bát. Xếp thịt bò thái mỏng lên trên cùng với hành ngò.\r\nBước 4: Chan nước dùng đang sôi sùng sục vào bát để làm chín thịt bò tái, ăn kèm với quẩy và chanh ớt.','2026-10-10 08:19:01','2026-10-17 10:02:07',4.9,186),(2,'Bún Chả','b-n-ch-','Đặc sản Hà Nội: bún tươi ăn cùng chả viên và chả miếng nướng than hoa, chan nước mắm chua ngọt, kèm rau sống.','https://upload.wikimedia.org/wikipedia/commons/4/4d/B%C3%BAn_ch%E1%BA%A3_Vietnamese_food.jpg',60,4,'Trung bình',1,'Bước 1: Thái mỏng thịt ba chỉ và nặn thịt nạc băm thành từng viên nhỏ. Ướp thịt với nước mắm, đường, tiêu, hành tỏi trong 30 phút.\r\nBước 2: Nướng thịt ba chỉ và thịt viên trên bếp than hoa cho đến khi chín vàng, xém cạnh và dậy mùi thơm.\r\nBước 3: Pha nước chấm chua ngọt với tỷ lệ mắm, đường, chanh, nước lọc vừa ăn, thêm tỏi ớt băm và đu đủ xanh thái mỏng.\r\nBước 4: Bày bún tươi, rau sống và chả nướng ra đĩa, thưởng thức bằng cách chấm bún và chả vào bát nước chấm chua ngọt.','2026-10-10 08:19:01','2026-10-17 10:02:07',4.8,142),(3,'Canh Chua Cá Lóc','canh-chua-c-l-c','Món canh miền Tây, nước dùng chua thanh từ me hoặc cà chua, nấu với cá lóc, đậu bắp, bạc hà, giá, thơm ngon và thanh mát.','https://upload.wikimedia.org/wikipedia/commons/a/a0/Canhchua2.jpg',30,4,'Dễ',2,'Bước 1: Làm sạch cá lóc, cắt khúc vừa ăn. Nhặt và rửa sạch giá, dọc mùng, đậu bắp, dứa và cà chua.\r\nBước 2: Phi thơm tỏi, xào sơ cà chua và dứa để tạo màu, đổ nước vào đun sôi và dầm cốt me chua.\r\nBước 3: Cho cá lóc vào đun chín, tiếp tục thêm đậu bắp, dọc mùng, giá đỗ vào nấu vừa chín tới để rau vẫn giữ độ giòn.\r\nBước 4: Nêm nếm lại gia vị cho cân bằng chua ngọt, tắt bếp, rắc rau ngò ôm, ngò gai thái nhỏ vài lát ớt lên trên.','2026-10-10 08:19:01','2026-10-17 10:02:07',4.7,98),(4,'Gỏi Cuốn','goi-cuon','Gỏi cuốn tôm thịt là món ăn vặt hoặc khai vị cực kỳ thanh mát và dễ ăn. Bên trong lớp bánh tráng mỏng dai là tôm tươi đỏ au, thịt luộc thái mỏng, bún tươi và ngập tràn các loại rau thơm, chấm kèm tương đen đậu phộng đậm đà.','https://upload.wikimedia.org/wikipedia/commons/9/90/G%E1%BB%8Fi_cu%E1%BB%91n.jpg',20,4,'Dễ',1,'Bước 1: Thịt ba chỉ luộc chín, thái lát mỏng. Tôm luộc chín, bóc vỏ và chẻ đôi.\nBước 2: Rửa sạch xà lách, hẹ và các loại rau thơm. Bún tươi gỡ tơi để sẵn.\nBước 3: Làm ướt bánh tráng, xếp xà lách, rau thơm, bún, thịt lợn và tôm lên trên, cuộn thật chặt tay.\nBước 4: Pha nước chấm tương đen với bơ đậu phộng, rắc thêm chút hành phi và đậu phộng rang giã nhỏ để chấm gỏi.','2026-10-10 08:19:01','2026-10-17 10:02:07',4.9,215),(5,'Chè Khúc Bạch','che-khuc-bach','Chè khúc bạch gây thương nhớ bởi sự thanh tao và đẹp mắt. Những khối thạch núng nính, mềm tan béo ngậy vị sữa tươi, ăn cùng nước đường phèn lá dứa dịu mát, điểm xuyết thêm nhãn lồng hay vải thiều và hạnh nhân rang giòn rụm.','uploads/che-khuc-bach.jpg',40,4,'Trung bình',4,'Bước 1: Ngâm gelatin cho nở. Đun nóng sữa tươi không đường và whipping cream, hòa tan đường và gelatin đã ngâm vào.\nBước 2: Đổ hỗn hợp sữa ra khuôn, cất vào tủ lạnh khoảng 3-4 tiếng để thạch đông lại, sau đó cắt thành khối vuông nhỏ.\nBước 3: Đun sôi nước với đường phèn và lá dứa làm nước chan chè, để nguội. Ngâm hạnh nhân lát qua nước và rang vàng rụm.\nBước 4: Múc thạch khúc bạch ra bát, thêm vải thiều, chan nước đường lá dứa ướp lạnh và rắc hạnh nhân rang lên trên cùng.','2026-10-10 08:19:01','2026-10-17 10:45:00',4.8,89),(6,'Đậu Hũ Tứ Xuyên','-u-h-t-xuy-n','Món Trung Hoa phổ biến ở Việt Nam: đậu hũ non mềm nấu với thịt băm, sốt cay Tứ Xuyên (đậu tương, ớt, tiêu), vị đậm đà, cay nồng.','https://upload.wikimedia.org/wikipedia/commons/8/88/Chen_Mapo_Tofu.jpg',20,2,'Dễ',3,'Bước 1: Đậu hũ non cắt khối vuông nhỏ, trần sơ qua nước sôi có pha chút muối để đậu không bị nát.\r\nBước 2: Băm nhỏ thịt heo. Tỏi, gừng băm nhuyễn. Pha sẵn bát nước xốt gồm nước tương, dầu hào, đường và sa tế.\r\nBước 3: Xào tỏi gừng cho thơm, đổ thịt băm vào xào săn lại, sau đó thêm nước xốt cay đã pha và một chút nước đun sôi.\r\nBước 4: Thả đậu hũ non vào chảo, lắc nhẹ chảo để đậu ngấm xốt, đun liu riu thêm 3 phút rồi rắc hành lá thái nhỏ tắt bếp.','2026-10-10 08:19:01','2026-10-17 10:02:07',4.6,76),(7,'Trà Sữa Trân Châu','tra-sua-tran-chau','Trà sữa trân châu không chỉ là một thức uống mà còn là đam mê của giới trẻ. Sự hòa quyện hoàn hảo giữa vị đắng nhẹ của hồng trà, vị béo của sữa và những hạt trân châu dai dẻo, ngọt ngào tạo nên một hương vị khó cưỡng.','https://upload.wikimedia.org/wikipedia/commons/a/a2/Bubble_Tea.png',30,2,'Dễ',5,'Bước 1: Nhào bột năng với cacao và nước sôi để tạo thành khối bột mịn, sau đó nặn thành từng viên trân châu nhỏ.\nBước 2: Đun sôi nước, thả trân châu vào luộc đến khi nổi lên, vớt ra ngâm ngay vào bát nước đá, rồi trộn với mật ong.\nBước 3: Hãm trà đen với nước sôi trong 10 phút, lọc bỏ bã trà để lấy nước cốt.\nBước 4: Lắc đều nước cốt trà, sữa đặc, sữa tươi và đá viên trong bình shaker, đổ ra ly đã có sẵn trân châu ở đáy.','2026-10-10 08:19:01','2026-10-17 10:02:07',5.0,310),(8,'Cơm Tấm Sườn Bì','c-m-t-m-s-n-b-','Cơm tấm Sài Gòn ăn kèm sườn nướng thơm, bì heo trộn thính, đồ chua, mỡ hành và nước mắm pha, rất đậm đà.','https://upload.wikimedia.org/wikipedia/commons/b/b0/C%C6%A1m_T%E1%BA%A5m%2C_Da_Nang%2C_Vietnam.jpg',60,4,'Trung bình',1,'Bước 1: Ướp sườn cốt lết với mật ong, nước mắm, tỏi băm, hành tím băm, dầu hào và chút nước cốt dừa trong 2 tiếng.\r\nBước 2: Nướng sườn trên bếp than hồng ở lửa vừa, lật đều tay để sườn chín vàng ươm và không bị cháy xém.\r\nBước 3: Làm bì heo: Trộn thịt nạc heo ram thái sợi với da heo luộc thái mỏng và thính gạo rang cho thật đều.\r\nBước 4: Trình bày cơm ra đĩa, đặt sườn nướng, bì heo, chả trứng hấp lên trên, ăn kèm với mỡ hành, đồ chua và nước mắm mặn ngọt.','2026-10-10 08:19:01','2026-10-17 10:02:07',4.9,165),(9,'Canh Bí Đỏ Thịt Băm','canh-b-th-t-b-m','Canh thanh đạm, bí đỏ nấu mềm với thịt heo băm, nước dùng ngọt tự nhiên, dễ ăn, phù hợp mọi lứa tuổi.','https://upload.wikimedia.org/wikipedia/commons/c/cd/K%C3%BCrbissuppe_mit_Kern%C3%B6l.JPG',25,4,'Dễ',2,'Bước 1: Bí đỏ gọt vỏ, bỏ ruột, rửa sạch và thái miếng vuông. Thịt băm ướp với chút muối, tiêu và hành tím.\r\nBước 2: Phi thơm hành tím trong nồi với một ít dầu ăn, cho thịt băm vào xào săn lại để dậy mùi thơm.\r\nBước 3: Đổ lượng nước vừa đủ dùng vào nồi đun sôi, hớt bỏ bọt dơ, sau đó cho bí đỏ vào hầm lửa nhỏ khoảng 15 phút.\r\nBước 4: Khi bí đỏ đã chín mềm bở, nêm nếm lại gia vị vừa miệng, rắc ngò gai thái nhỏ vào rồi tắt bếp múc ra bát.','2026-10-10 08:19:01','2026-10-17 10:02:07',4.7,64),(10,'Bánh Xèo','b-nh-x-o','Bánh bột gạo chiên vàng giòn, nhân tôm thịt, giá đỗ, ăn cuốn với rau sống và chấm nước mắm chua ngọt.','https://upload.wikimedia.org/wikipedia/commons/5/59/Banh_Xeo_with_fish_sauce_and_vegetables.jpg',45,4,'Trung bình',1,'Bước 1: Pha bột bánh xèo với nước cốt dừa, bột nghệ, hành lá thái nhỏ và một xíu muối, để bột nghỉ khoảng 30 phút.\r\nBước 2: Xào xơ tôm, thịt ba chỉ thái mỏng và đậu xanh xát vỏ đã hấp chín với một chút gia vị để làm nhân.\r\nBước 3: Quét dầu ăn lên chảo nóng, múc một vá bột đổ vào chảo xoay đều, thả tôm, thịt, giá đỗ vào đậy nắp 2 phút.\r\nBước 4: Khi viền bánh tróc và giòn, gập đôi bánh lại và vớt ra đĩa, dùng kèm với rau sống các loại và nước mắm chua ngọt.','2026-10-10 08:19:01','2026-10-17 10:02:07',4.8,130),(11,'Sinh Tố Xoài','sinh-to-xoai','Sinh tố xoài là món đồ uống giải khát tuyệt vời cho những ngày hè oi bức. Vị ngọt lịm, thơm lừng của xoài chín kết hợp cùng chút béo ngậy của sữa mang đến một ly sinh tố mát lạnh, bổ sung nhiều vitamin C và năng lượng.','https://upload.wikimedia.org/wikipedia/commons/0/09/Fresh-mango-smoothie_01.jpg',10,2,'Dễ',5,'Bước 1: Chọn xoài chín ngọt, gọt vỏ và cắt lấy phần thịt xoài thành các hạt lựu lớn.\nBước 2: Cho thịt xoài vào cối xay sinh tố cùng với sữa tươi, sữa đặc và một chút nước cốt chanh để làm tăng hương vị.\nBước 3: Thêm đá bào vào cối, xay ở tốc độ cao cho đến khi hỗn hợp nhuyễn mịn và bông xốp.\nBước 4: Rót sinh tố ra ly, trang trí bằng vài lát xoài tươi cắt khối hoặc lá bạc hà trên miệng ly rồi thưởng thức ngay.','2026-10-10 08:19:01','2026-10-17 10:02:07',4.9,95),(13,'Gan cháy tỏi ','gan-ch-y-t-i-','Gan cháy tỏi là món ăn Việt Nam làm từ gan (thường là gan heo hoặc gà), xào hoặc chiên với nhiều tỏi đến khi tỏi thơm, vàng và hơi cháy, tạo vị đậm đà, thơm nức.','https://images.unsplash.com/photo-1604908176997-125f25cc6f3d?q=80&w=800&auto=format&fit=crop',35,1,'Khó',1,'1. Rửa sạch gan, thái mỏng, ướp với muối, tiêu, nước mắm khoảng 10–15 phút.\r\n2. Phi thơm nhiều tỏi băm với dầu.\r\n3. Cho gan vào xào nhanh trên lửa lớn đến khi chín, tỏi vàng cháy thơm.\r\n4. Nêm nếm lại cho vừa miệng, tắt bếp.','2026-10-10 10:48:44','2026-10-17 10:02:07',4.8,110),(14,'Bún Bò Huế Đặc Biệt','bun-bo-hue-dac-biet','Món bún bò cay nồng đậm đà chuẩn vị cố đô Huế, ngon nhức nách, làm tại nhà nhanh chóng đơn giản. ','https://i2.ex-cdn.com/crystalbay.com/files/content/2024/08/15/bun-bo-hue-6-0935.jpeg',90,4,'Khó',1,'Bước 1: Sơ chế và hầm xương bò + bắp bò\r\nRửa sạch xương bò và bắp bò với nước muối loãng. Chần qua nước sôi 3–5 phút để loại bỏ tạp chất và mùi hôi, rồi rửa lại bằng nước lạnh. Cho xương vào nồi lớn cùng 4–5 lít nước, đun sôi rồi hạ lửa nhỏ, hầm trong 2 tiếng. Trong quá trình hầm, thường xuyên vớt bọt để nước dùng trong.\r\n\r\nBước 2: Sơ chế sả, ớt, hành tím và các gia vị\r\nSả rửa sạch, đập dập, cắt khúc (phần gốc để phi, phần lá để cho vào nồi nước dùng). Ớt bỏ hạt, băm nhuyễn. Hành tím, tỏi bóc vỏ, băm nhỏ. Chuẩn bị mắm ruốc Huế, dầu điều, đường phèn, nước mắm để sẵn sàng nêm nếm.\r\n\r\nBước 3: Phi thơm sả – ớt – hành tỏi tạo màu\r\nBắc chảo, cho dầu ăn + dầu điều vào đun nóng. Cho sả băm, ớt băm, hành tím, tỏi vào phi thơm vàng đến khi dậy mùi. Hỗn hợp này sẽ tạo màu đỏ cam đặc trưng và mùi thơm nồng cho nồi bún bò.\r\n\r\nBước 4: Nêm mắm ruốc Huế và gia vị vào nồi nước dùng\r\nHòa mắm ruốc với một ít nước dùng nóng, lọc bỏ cặn rồi đổ vào nồi. Thêm hỗn hợp sả – ớt – hành tỏi đã phi ở bước 3. Nêm đường phèn, nước mắm, hạt nêm, muối sao cho vị đậm đà, hơi cay nhẹ, thơm mùi ruốc nhưng không gắt. Đun sôi lại rồi hạ lửa, tiếp tục hầm thêm 30–45 phút cho gia vị thấm đều.\r\n\r\nBước 5: Luộc bắp bò, giò heo và thái lát\r\nVớt bắp bò và giò heo đã chín mềm trong nồi hầm ra, ngâm vào nước lạnh cho săn chắc. Thái bắp bò thành lát mỏng vừa ăn, giò heo chặt miếng vừa. Phần nước hầm giữ nguyên để làm nước dùng.\r\n\r\nBước 6: Trần bún và chuẩn bị tô\r\nBún tươi trần qua nước sôi khoảng 30 giây cho nóng, vớt ra để ráo. Xếp bún vào tô, lần lượt xếp thịt bắp bò, giò heo, chả lụa (nếu có) lên trên. Rắc hành ngò cắt nhỏ, tiêu, ớt tươi thái lát.\r\n\r\nBước 7: Chan nước dùng sôi và thưởng thức\r\nĐun nước dùng thật sôi (sôi sùng sục), rồi chan trực tiếp lên tô bún vừa xếp. Nước dùng nóng sẽ làm chín tái phần thịt mỏng và tỏa mùi thơm đặc trưng. Dọn kèm rau sống, bắp chuối bào, giá đỗ, chanh, ớt tươi và thưởng thức ngay khi còn nóng.','2026-10-12 12:36:12','2026-10-17 10:02:07',5.0,248),(15,'Mỳ Ý Sốt Bò Bằm (Spaghetti)','my-y-sot-bo-bam-spaghetti','Món Âu kinh điển với sợi mỳ dai ngon hòa quyện sốt bò bằm cà chua thơm lừng thảo mộc Ý, phủ phô mai béo ngậy.','uploads/mi-y-sot-bo-bam.jpg',25,2,'Dễ',7,'Bước 1: Luộc mỳ Ý ngập nước sôi với chút muối trong 8-10 phút cho chín al dente.\nBước 2: Phi thơm tỏi tây và hành tây với bơ, cho thịt bò băm vào xào săn.\nBước 3: Thêm sốt cà chua nghiền, lá oregano và tiêu đen, đun liu riu 10 phút.\nBước 4: Bày mỳ ra đĩa, rưới sốt bò bằm nóng hổi và rắc phô mai Parmesan bào sợi lên trên.','2026-10-17 10:02:50','2026-10-17 10:45:00',4.9,143),(16,'Salad Ức Gà Sốt Mè (Keto)','salad-uc-ga-sot-me-keto','Món ăn kiêng Eat Clean / Keto thanh mát, giàu protein từ ức gà áp chảo vàng óng, rau củ giòn ngọt kết hợp sốt mè rang.','https://images.unsplash.com/photo-1540420773420-3366772f4999?q=80&w=800&auto=format&fit=crop',15,2,'Dễ',11,'Bước 1: Ức gà rửa sạch, khía nhẹ, ướp chút muối, tiêu và dầu ô liu 10 phút.\nBước 2: Áp chảo ức gà với lửa vừa mỗi mặt 4-5 phút đến khi vàng giòn mọng nước, thái lát.\nBước 3: Rửa sạch xà lách romaine, cà chua bi, dưa chuột và bơ sáp cắt miếng vừa ăn.\nBước 4: Xếp rau củ và thịt gà ra đĩa, rưới sốt mè rang béo bùi và thưởng thức ngay.','2026-10-17 10:02:50','2026-10-17 10:15:09',4.8,118),(17,'Bánh Mì Chảo Thập Cẩm','banh-mi-chao-thap-cam','Món ăn sáng quốc dân thơm ngon, giàu năng lượng gồm trứng ốp la lòng đào, pate béo ngậy, xíu mại và xúc xích nóng hổi.','uploads/banh-mi-chao.jpg',15,1,'Dễ',8,'Bước 1: Làm nóng chảo gang với một chút bơ lạt cho tan chảy.\nBước 2: Cho xúc xích khía vát và xíu mại vào đảo sơ cho xém cạnh.\nBước 3: Đập 1-2 quả trứng gà vào chảo để ốp la lòng đào, thêm miếng pate thơm béo.\nBước 4: Rắc tiêu đen, ngò rí và vài giọt nước tương, ăn kèm bánh mì nóng giòn tan.','2026-10-17 10:05:10','2026-10-17 10:34:59',4.9,225),(18,'Cơm Chiên Dương Châu','com-chien-duong-chau','Món cơm chiên đặc trưng châu Á với hạt cơm săn vàng óng ả, hòa quyện tôm tươi, lạp xưởng, đậu Hà Lan và trứng thơm nức.','https://images.unsplash.com/photo-1603133872878-684f208fb84b?q=80&w=800&auto=format&fit=crop',20,3,'Dễ',6,'Bước 1: Cơm nguội bóp tơi trộn đều với lòng đỏ trứng gà và gia vị.\nBước 2: Thái hạt lựu lạp xưởng, tôm luộc, chả lụa và cà rốt.\nBước 3: Phi tỏi thơm, xào chín phần nhân rồi trút ra bát riêng.\nBước 4: Rang cơm trên chảo lửa lớn cho hạt cơm tơi xốp, đổ phần nhân vào đảo đều tay 5 phút rồi rắc hành hoa.','2026-10-17 10:05:10','2026-10-17 10:15:09',4.8,180),(19,'Thịt Kho Tàu Nước Dừa','thit-kho-tau-nuoc-dua','Mâm cơm tối ấm áp với từng miếng thịt ba chỉ vuông vức mềm rục, mỡ trong veo thấm đượm vị ngọt thanh của nước dừa xiêm.','uploads/thit-kho-tau.jpg',50,4,'Trung bình',10,'Bước 1: Thịt ba chỉ thái khối vuông 4-5cm, ướp nước mắm, hành tỏi băm và đường 1 tiếng.\r\nBước 2: Luộc chín trứng vịt hoặc trứng cút, bóc sạch vỏ.\r\nBước 3: Thắng nước màu caramen, cho thịt vào xào săn rồi đổ ngập nước dừa tươi đun sôi.\r\nBước 4: Hạ lửa nhỏ liu riu hầm 45 phút cho thịt mềm rục, thả trứng vào đun thêm 15 phút đến khi nước sánh vàng nâu.','2026-10-17 10:05:10','2026-10-17 10:45:00',5.0,268),(20,'Cơm Gà Xối Mỡ Da Giòn','com-ga-xoi-mo-da-gion','Cơm gà xối mỡ chuẩn vị Chợ Lớn: đùi gà chiên xối mỡ lớp da giòn rụm óng ả, cơm chiên cà chua thơm lừng, chấm nước tương tỏi ớt kèm dưa leo giòn mát.','https://images.unsplash.com/photo-1562967914-608f82629710?q=80&w=800&auto=format&fit=crop',45,2,'Trung bình',9,'Bước 1: Ướp sườn cốt lết với tỏi, hành tím băm, mật ong, dầu hào và nước mắm 2 tiếng.\nBước 2: Nướng sườn trên bếp than hoa hoặc nồi chiên không dầu ở 180°C trong 20 phút.\nBước 3: Trộn bì heo luộc thái chỉ với thịt nạc ram thái sợi và thính gạo rang thơm.\nBước 4: Bới cơm tấm ra đĩa, xếp sườn, bì, chả trứng, chan mỡ hành và đồ chua ăn kèm nước mắm tỏi ớt.','2026-10-17 10:25:44','2026-10-17 10:34:59',4.9,195),(21,'Bún Bò Nam Bộ Trộn Chua Ngọt','bun-bo-nam-bo-tron-chua-ngot','Bữa trưa thanh mát với bún tươi trộn thịt bò xào hành tây mềm ngọt, giá đỗ chần, lạc rang bùi ngậy, hành phi giòn tan và nước mắm chua ngọt đậm đà.','https://images.unsplash.com/photo-1582878826629-29b7ad1cdc43?q=80&w=800&auto=format&fit=crop',40,3,'Trung bình',9,'Bước 1: Thịt ba chỉ thái mỏng, thịt nạc băm viên tròn. Ướp nước mắm, hành tỏi, tiêu và nước màu.\nBước 2: Kẹp thịt vào vỉ nướng trên than hoa cho chín vàng thơm nức.\nBước 3: Pha nước chấm mắm chua ngọt với tỏi ớt, đu đủ xanh và cà rốt ngâm giấm giòn sần sật.\nBước 4: Thả chả nóng vào bát nước mắm, gắp bún và rau sống thưởng thức.','2026-10-17 10:25:44','2026-10-17 10:34:59',4.8,210),(22,'Bít Tết Bò Sốt Tiêu Đen (Beefsteak)','bit-tet-bo-sot-tieu-den','Món Âu thượng hạng với miếng thăn bò áp chảo mềm mọng nước (medium rare), sốt tiêu đen sánh mịn cay nồng, ăn kèm khoai tây chiên giòn.','https://images.unsplash.com/photo-1600891964599-f61ba0e24092?q=80&w=800&auto=format&fit=crop',20,2,'Trung bình',7,'Bước 1: Rã đông thăn bò, dùng khăn thấm khô, rắc muối biển và tiêu đen xay vỡ đều 2 mặt.\nBước 2: Làm nóng chảo gang với dầu ô liu, áp chảo mỗi mặt 2-3 phút, cho bơ lạt, tỏi đập dập và hương thảo vào rưới liên tục.\nBước 3: Lấy thịt ra đĩa để nghỉ 5 phút. Xào tỏi với tiêu đen, nước tương, dầu hào và kem tươi làm sốt.\nBước 4: Thái bít tết thành từng lát dày mọng nước, rưới sốt tiêu đen và thưởng thức.','2026-10-17 10:25:44','2026-10-17 10:34:59',5.0,165),(23,'Bánh Cuốn Nóng Chả Quế','banh-cuon-nong-cha-que','Bánh cuốn tráng mỏng dai mềm như lụa, cuộn mộc nhĩ thịt băm thơm phức, rắc hành phi vàng ruộm, ăn kèm chả quế và nước mắm cà cuống ấm nóng.','https://upload.wikimedia.org/wikipedia/commons/0/03/Banh_cuon_2011.jpg',35,4,'Trung bình',6,'Bước 1: Khuấy bột bánh xèo với nước cốt dừa, bột nghệ, hành lá và chút bia để tạo độ giòn lâu.\nBước 2: Xào sơ tôm, thịt ba chỉ cho chín tái.\nBước 3: Láng dầu thật nóng trong chảo sâu lòng, múc bột xoay tròn đều, cho tôm thịt và giá đỗ vào đậy nắp 2 phút.\nBước 4: Mở vung chiên lửa nhỏ đến khi viền giòn rụm, gập đôi bánh, cuốn bánh tráng rau sống.','2026-10-17 10:25:44','2026-10-17 10:34:59',4.9,178),(24,'Cá Hồi Áp Chảo Măng Tây (Keto)','ca-hoi-ap-chao-mang-tay-keto','Món ăn kiêng Keto chuẩn sao với phi lê cá hồi áp chảo giòn da ngọt thịt, giàu Omega-3, măng tây xào bơ tỏi giòn ngọt thanh tao.','https://images.unsplash.com/photo-1467003909585-2f8a72700288?q=80&w=800&auto=format&fit=crop',15,2,'Dễ',11,'Bước 1: Thấm khô miếng cá hồi, ướp với chút muối, tiêu và nước cốt chanh vàng.\nBước 2: Làm nóng chảo với dầu ô liu, áp chảo phần da trước 4 phút cho da vàng giòn rụm rồi lật mặt còn lại 2 phút.\nBước 3: Măng tây tước bỏ xơ gốc, xào nhanh với bơ lạt và tỏi băm trong 3 phút.\nBước 4: Bày cá hồi và măng tây ra đĩa, rưới sốt bơ chanh béo ngậy chuẩn Keto.','2026-10-17 10:25:44','2026-10-17 10:25:44',4.9,142),(25,'Trứng Cuộn Phô Mai Rong Biển','trung-cuon-pho-mai-rong-bien','Món ăn nhanh siêu tốc chỉ 10 phút: từng lớp trứng mềm mịn xốp vàng, phô mai mozzarella tan chảy kéo sợi và lá rong biển giòn thơm.','uploads/trung-cuon.jpg',10,2,'Dễ',12,'Bước 1: Đập 4 quả trứng gà vào tô, thêm hạt nêm, tiêu và 2 muỗng sữa tươi đánh tan đều.\nBước 2: Quét lớp dầu mỏng lên chảo chống dính nóng, đổ 1 lớp trứng mỏng tráng đều.\nBước 3: Đặt lá rong biển và thanh phô mai mozzarella lên trên, cuộn tròn dần về một góc chảo.\nBước 4: Tiếp tục đổ thêm lớp trứng mới nối tiếp và cuộn đến khi hết bột, cắt khoanh vừa ăn.','2026-10-17 10:25:44','2026-10-17 10:34:59',4.8,126);
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

-- Dump completed on 2026-10-17 10:46:05
