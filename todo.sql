--
-- Скрипт сгенерирован Devart dbForge Studio for MySQL, Версия 7.2.58.0
-- Домашняя страница продукта: http://www.devart.com/ru/dbforge/mysql/studio
-- Дата скрипта: 27.04.2021 22:15:09
-- Версия сервера: 5.5.5-10.3.22-MariaDB
-- Версия клиента: 4.1
--


-- 
-- Отключение внешних ключей
-- 
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;

-- 
-- Установить режим SQL (SQL mode)
-- 
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- 
-- Установка кодировки, с использованием которой клиент будет посылать запросы на сервер
--
SET NAMES 'utf8';

-- 
-- Установка базы данных по умолчанию
--

--
-- Описание для таблицы todos
--
DROP TABLE IF EXISTS todos;
CREATE TABLE IF NOT EXISTS todos (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  user_name VARCHAR(50) NOT NULL COMMENT 'Имя пользователя',
  email VARCHAR(50) NOT NULL COMMENT 'Email пользователя',
  content TINYTEXT NOT NULL COMMENT 'Текст задачи',
  status_code TINYINT(4) UNSIGNED DEFAULT 0 COMMENT 'Статус задачи 1 - исполнена',
  admin_edit TINYINT(4) UNSIGNED DEFAULT 0 COMMENT 'Признак, что отредактировано админом',
  PRIMARY KEY (id)
)
ENGINE = INNODB
AUTO_INCREMENT = 9
AVG_ROW_LENGTH = 2340
CHARACTER SET utf8mb4
COLLATE utf8mb4_general_ci
ROW_FORMAT = DYNAMIC;

--
-- Описание для таблицы users
--
DROP TABLE IF EXISTS users;
CREATE TABLE IF NOT EXISTS users (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  login VARCHAR(255) NOT NULL,
  password VARCHAR(255) NOT NULL,
  PRIMARY KEY (id)
)
ENGINE = INNODB
AUTO_INCREMENT = 2
CHARACTER SET utf8mb4
COLLATE utf8mb4_general_ci
ROW_FORMAT = DYNAMIC;

-- 
-- Вывод данных для таблицы todos
--

/*!40000 ALTER TABLE todos DISABLE KEYS */;
INSERT INTO todos VALUES
(2, 'vasya', 'asd@asd.ru', 'dfg dgfh dgh dghd fgh', 1, 0),
(3, 'Дмитрий', 'dserov@gmail.com', 'Сделать тестовое задание', 0, 0),
(4, 'Иван Петров', 'qwe@gmail.ru', 'Тест успешного добавления задачи', 0, 0),
(5, 'Сергей', 'serg@mail.ru', 'Тест пагинации', 0, 0),
(6, 'Xss - hacker', 'xss@mail.ru', 'Проверка XSS <script>alert("qwe")</script>', 0, 0),
(7, 'qweqwe!', 'qw@qwe.ty', 'qweqwe sfg fgsdf gsdf g d df dsf sdf sdf sd fdsf sd fsd fsdf', 1, 1),
(8, 'Петя', 'qwe@qwe.ru', 'цук ы упыа пыва п', 0, 0);

/*!40000 ALTER TABLE todos ENABLE KEYS */;

-- 
-- Вывод данных для таблицы users
--

/*!40000 ALTER TABLE users DISABLE KEYS */;
INSERT INTO users VALUES
(1, 'admin', 'a$pG0&32432*G!Hscaf1a3dfb505ffed0d024130f58c5cfaa$pG0&32432*G!Hs');

/*!40000 ALTER TABLE users ENABLE KEYS */;

-- 
-- Восстановить предыдущий режим SQL (SQL mode)
-- 
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;

-- 
-- Включение внешних ключей
-- 
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;