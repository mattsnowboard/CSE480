SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

CREATE SCHEMA IF NOT EXISTS `cse480` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci ;
USE `cse480` ;

-- -----------------------------------------------------
-- Table `cse480`.`player`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `cse480`.`player` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `email` VARCHAR(64) NOT NULL ,
  `password` VARCHAR(64) NOT NULL ,
  `last_active` TIMESTAMP NOT NULL ,
  `total_score` INT NULL ,
  `birthdate` DATE NULL ,
  `join_date` DATE NULL ,
  `city` VARCHAR(64) NULL ,
  `state` VARCHAR(64) NULL ,
  `country` VARCHAR(64) NULL ,
  `full_name` VARCHAR(64) NULL ,
  `phone` VARCHAR(11) NULL ,
  `is_admin` TINYINT(1) NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `email_UNIQUE` (`email` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `cse480`.`dictionary`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `cse480`.`dictionary` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `word` VARCHAR(32) NOT NULL ,
  `definition` TEXT NOT NULL ,
  `length` SMALLINT NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `word_UNIQUE` (`word` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `cse480`.`game`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `cse480`.`game` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `timestamp` TIMESTAMP NOT NULL ,
  `word_start_state` VARCHAR(32) NOT NULL ,
  `num_players` ENUM('1','2') NOT NULL ,
  `score1` SMALLINT NOT NULL DEFAULT 0 ,
  `score2` SMALLINT NULL DEFAULT 0 ,
  `player_turn` ENUM('1','2') NULL ,
  `winner_flag` ENUM('playing','1','2','draw','lose') NULL DEFAULT 'playing' ,
  `word_id` INT NOT NULL ,
  `player1_id` INT NOT NULL ,
  `player2_id` INT NULL ,
  `is_bonus` TINYINT(1) NOT NULL DEFAULT false ,
  `current_state` VARCHAR(32) NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_game_dictionary` (`word_id` ASC) ,
  INDEX `fk_game_player1` (`player1_id` ASC) ,
  INDEX `fk_game_player2` (`player2_id` ASC) ,
  CONSTRAINT `fk_game_dictionary`
    FOREIGN KEY (`word_id` )
    REFERENCES `cse480`.`dictionary` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_game_player1`
    FOREIGN KEY (`player1_id` )
    REFERENCES `cse480`.`player` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_game_player2`
    FOREIGN KEY (`player2_id` )
    REFERENCES `cse480`.`player` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `cse480`.`challenge`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `cse480`.`challenge` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `status` ENUM('pending', 'accepted', 'declined') NOT NULL ,
  `game_id` INT NULL ,
  `challenger_id` INT NOT NULL ,
  `recipient_id` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_challenge_game1` (`game_id` ASC) ,
  INDEX `fk_challenge_player1` (`challenger_id` ASC) ,
  INDEX `fk_challenge_player2` (`recipient_id` ASC) ,
  CONSTRAINT `fk_challenge_game1`
    FOREIGN KEY (`game_id` )
    REFERENCES `cse480`.`game` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_challenge_player1`
    FOREIGN KEY (`challenger_id` )
    REFERENCES `cse480`.`player` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_challenge_player2`
    FOREIGN KEY (`recipient_id` )
    REFERENCES `cse480`.`player` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `cse480`.`guess`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `cse480`.`guess` (
  `timestamp` TIMESTAMP NOT NULL ,
  `is_correct` TINYINT(1) NULL ,
  `word` VARCHAR(32) NULL DEFAULT NULL ,
  `letter` CHAR NULL DEFAULT NULL ,
  `is_full_word` TINYINT(1) NOT NULL DEFAULT false ,
  `player_id` INT NOT NULL ,
  `game_id` INT NOT NULL ,
  PRIMARY KEY (`timestamp`, `game_id`) ,
  INDEX `fk_guess_player1` (`player_id` ASC) ,
  INDEX `fk_guess_game1` (`game_id` ASC) ,
  UNIQUE INDEX `unique_letter_game` (`letter` ASC, `game_id` ASC, `word` ASC) ,
  CONSTRAINT `fk_guess_player1`
    FOREIGN KEY (`player_id` )
    REFERENCES `cse480`.`player` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_guess_game1`
    FOREIGN KEY (`game_id` )
    REFERENCES `cse480`.`game` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
