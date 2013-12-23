SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

CREATE SCHEMA IF NOT EXISTS `xumb` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `xumb` ;
-- This version (2013-07-10) includes the following updates:
-- > Translate the database properties (column names, table names, foreign key names) from pt_BR to EN
-- > Updates in the physical model to include the migration of the foreign keys:
--   >> From user to marker
-- 	>> From marker to update, protocol, comment, image
-- 	>> From comment to image
-- > This will further increase the database referential integrity and simplify some queries

-- -----------------------------------------------------
-- Table `xumb`.`user`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `xumb`.`user` ;

CREATE  TABLE IF NOT EXISTS `xumb`.`user` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(100) NOT NULL ,
  `surname` VARCHAR(100) NOT NULL ,
  `email` VARCHAR(200) NOT NULL ,
  `login` VARCHAR(12) NOT NULL,
  `city` VARCHAR(100) NULL ,
  `country` VARCHAR(45) NULL,
  `zip_code` VARCHAR(20) NULL ,
  `avatar` VARCHAR(100) NULL ,
  `password` VARCHAR(32) NOT NULL ,
  `creation_date` DATETIME NOT NULL ,
  `latitude` float(10,6) NULL ,
  `longitude` float(10,6)  NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;

DROP TABLE IF EXISTS `xumb`.`category` ;

CREATE TABLE IF NOT EXISTS `category` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(400) DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `category` (`id`,`name`,`description`,`icon`) VALUES (1,'Street Paving',NULL,'construction.png');
INSERT INTO `category` (`id`,`name`,`description`,`icon`) VALUES (2,'Parking',NULL,'car.png');
INSERT INTO `category` (`id`,`name`,`description`,`icon`) VALUES (3,'Street Lighting',NULL,'power.png');
INSERT INTO `category` (`id`,`name`,`description`,`icon`) VALUES (4,'Parks and Trees',NULL,'tree.png');
INSERT INTO `category` (`id`,`name`,`description`,`icon`) VALUES (99,'Default Category',NULL,NULL);

-- -----------------------------------------------------
-- Table `xumb`.`marker`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `xumb`.`marker` ;

CREATE  TABLE IF NOT EXISTS `xumb`.`marker` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `latitude` float(10,6) NOT NULL ,
  `longitude` float(10,6) NOT NULL ,
  `name` VARCHAR(100) NOT NULL ,
  `description` LONGTEXT NULL ,
  `creation_date` DATETIME NOT NULL ,
  `user_id` INT NOT NULL, -- created column to point to xumb.user
  `category_id` INT NOT NULL,
  PRIMARY KEY (`id`, `user_id`) ,
  CONSTRAINT `user_marker`
    FOREIGN KEY (`user_id` )
    REFERENCES `xumb`.`user` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION ,
  CONSTRAINT `category_marker`
    FOREIGN KEY (`category_id` )
    REFERENCES `xumb`.`category` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

alter table xumb.marker add unique index `lat_lng_unqidx` (`latitude`, `longitude`);

-- -----------------------------------------------------
-- Table `xumb`.`comment`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `xumb`.`comment` ;

CREATE  TABLE IF NOT EXISTS `xumb`.`comment` (
  `id` INT NOT NULL ,
  `text` LONGTEXT NOT NULL ,
  `marker_id` INT NOT NULL ,
  `user_id` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  CONSTRAINT `marker_comment`
    FOREIGN KEY (`marker_id`,`user_id` )
    REFERENCES `xumb`.`marker` (`id`, `user_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `xumb`.`image`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `xumb`.`image` ;

CREATE  TABLE IF NOT EXISTS `xumb`.`image` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `filename` VARCHAR(100) NULL ,
  `description` VARCHAR(400) NULL ,
  `marker_id` INT NOT NULL ,
  `user_id` INT NOT NULL ,
  `comment_id` INT NULL,
  PRIMARY KEY (`id`) ,
  CONSTRAINT `marker_image`
    FOREIGN KEY (`marker_id`,`user_id` )
    REFERENCES `xumb`.`marker` (`id`, `user_id` )
    ON DELETE NO ACTION 
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `xumb`.`geocoding`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `xumb`.`geocoding` ;

CREATE TABLE IF NOT EXISTS `xumb`.`geocoding` (
  `address` varchar(255) NOT NULL DEFAULT '',
  `latitude` float DEFAULT NULL,
  `longitude` float DEFAULT NULL,
  PRIMARY KEY (`address`) )
ENGINE = InnoDB;


USE `xumb` ;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
