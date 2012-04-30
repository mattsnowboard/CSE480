
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Add a flag to say a player is in a game
--

ALTER TABLE guess DROP INDEX unique_letter_game;

CREATE UNIQUE INDEX unique_letter_game ON guess(game_id, letter);
CREATE UNIQUE INDEX unique_word_game ON guess(game_id, word);
