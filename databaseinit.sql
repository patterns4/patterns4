-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------
-- -----------------------------------------------------
-- Schema ebike
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema ebike
-- -----------------------------------------------------
-- CREATE SCHEMA IF NOT EXISTS `ebike` DEFAULT CHARACTER SET latin1 ;
USE `ebike` ;

-- -----------------------------------------------------
-- Table `ebike`.`bike`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ebike`.`bike` (
  `bike_id` INT(11) NOT NULL AUTO_INCREMENT,
  `position` VARCHAR(35) NULL DEFAULT NULL,
  `speed` FLOAT NULL DEFAULT NULL,
  `battery` INT(11) NULL DEFAULT NULL,
  `status` TINYINT(1) NULL DEFAULT NULL,
  `state` VARCHAR(20) NULL DEFAULT NULL,
  `city_name` VARCHAR(80) NULL DEFAULT NULL,
  PRIMARY KEY (`bike_id`))
ENGINE = InnoDB
AUTO_INCREMENT = 2001
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `ebike`.`city`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ebike`.`city` (
  `city_id` INT(11) NOT NULL AUTO_INCREMENT,
  `city_name` VARCHAR(100) NULL DEFAULT NULL,
  `position` VARCHAR(35) NULL DEFAULT NULL,
  PRIMARY KEY (`city_id`))
ENGINE = InnoDB
AUTO_INCREMENT = 16
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `ebike`.`user`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ebike`.`user` (
  `user_id` INT(11) NOT NULL AUTO_INCREMENT,
  `first_name` VARCHAR(100) NOT NULL,
  `last_name` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(15) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `birth_year` VARCHAR(10) NOT NULL,
  `payment` VARCHAR(7) NULL DEFAULT NULL,
  `saldo` INT(11) NULL DEFAULT NULL,
  PRIMARY KEY (`user_id`))
ENGINE = InnoDB
AUTO_INCREMENT = 5
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `ebike`.`log`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ebike`.`log` (
  `log_id` INT(11) NOT NULL AUTO_INCREMENT,
  `start_time` DATETIME NULL DEFAULT NULL,
  `start_point` VARCHAR(35) NULL DEFAULT NULL,
  `end_time` DATETIME NULL DEFAULT NULL,
  `end_point` VARCHAR(35) NULL DEFAULT NULL,
  `user_id` INT(11) NULL DEFAULT NULL,
  `bike_id` INT(11) NULL DEFAULT NULL,
  PRIMARY KEY (`log_id`),
  INDEX `user_id` (`user_id` ASC),
  INDEX `bike_id` (`bike_id` ASC),
  CONSTRAINT `log_ibfk_1`
    FOREIGN KEY (`user_id`)
    REFERENCES `ebike`.`user` (`user_id`),
  CONSTRAINT `log_ibfk_2`
    FOREIGN KEY (`bike_id`)
    REFERENCES `ebike`.`bike` (`bike_id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `ebike`.`parking`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ebike`.`parking` (
  `parking_id` INT(11) NOT NULL AUTO_INCREMENT,
  `parking_name` VARCHAR(100) NULL DEFAULT NULL,
  `position` VARCHAR(35) NULL DEFAULT NULL,
  `city_name` VARCHAR(80) NULL DEFAULT NULL,
  PRIMARY KEY (`parking_id`))
ENGINE = InnoDB
AUTO_INCREMENT = 4
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `ebike`.`station`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ebike`.`station` (
  `station_id` INT(11) NOT NULL AUTO_INCREMENT,
  `station_name` VARCHAR(100) NULL DEFAULT NULL,
  `position` VARCHAR(35) NULL DEFAULT NULL,
  PRIMARY KEY (`station_id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;




SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
