-- MySQL Script generated by MySQL Workbench
-- 02/11/15 23:13:30
-- Model: New Model    Version: 1.0
-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------
-- -----------------------------------------------------
-- Schema dbfitness
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Table `tblfitness_event_type`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `tblfitness_event_type` (
  `idevent_type` INT(11) NOT NULL AUTO_INCREMENT,
  `dtname` VARCHAR(127) NOT NULL,
  `dtdescription` TEXT NULL,
  PRIMARY KEY (`idevent_type`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tblfitness_event`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `tblfitness_event` (
  `idevent` INT(11) NOT NULL AUTO_INCREMENT,
  `fievent_type` INT NOT NULL,
  `dtdate` DATETIME NOT NULL,
  `dtduration` INT(11) NOT NULL,
  PRIMARY KEY (`idevent`),
  INDEX `fk_tblfitness_event_tblfitness_event_type1_idx` (`fievent_type` ASC),
  CONSTRAINT `fk_tblfitness_event_tblfitness_event_type1`
    FOREIGN KEY (`fievent_type`)
    REFERENCES `tblfitness_event_type` (`idevent_type`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT)
ENGINE = InnoDB
AUTO_INCREMENT = 4
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `tblfitness_user`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `tblfitness_user` (
  `iduser` INT(11) NOT NULL AUTO_INCREMENT,
  `dtlast_name` VARCHAR(63) NOT NULL,
  `dtfirst_name` VARCHAR(63) NOT NULL,
  `dtpassword` TEXT NOT NULL,
  `dttype` ENUM('admin','employee','customer') NOT NULL DEFAULT 'customer',
  `dtemail` VARCHAR(127) NOT NULL,
  `dtbirthdate` DATE NULL DEFAULT NULL,
  PRIMARY KEY (`iduser`),
  UNIQUE INDEX `dtemail` (`dtemail` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 9
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `tblfitness_user2event`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `tblfitness_user2event` (
  `fiuser` INT NOT NULL,
  `fievent` INT NOT NULL,
  PRIMARY KEY (`fiuser`, `fievent`),
  INDEX `fk_tbluser2event_tblfitness_event1_idx` (`fievent` ASC),
  CONSTRAINT `fk_tbluser2event_tblfitness_user`
    FOREIGN KEY (`fiuser`)
    REFERENCES `tblfitness_user` (`iduser`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_tbluser2event_tblfitness_event1`
    FOREIGN KEY (`fievent`)
    REFERENCES `tblfitness_event` (`idevent`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
