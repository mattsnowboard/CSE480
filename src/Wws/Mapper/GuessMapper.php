<?php

namespace Wws\Mapper;

use Wws\Model\Guess;

/**
 * This class can look up users by ID
 * 
 * @author Chelsea Carr <carrche2@msu.edu>
 */
class UserMapper
{
    /**
     * @var Doctrine\DBAL\Connection 
     */
    protected $db;
    
    public function __construct(\Doctrine\DBAL\Connection $db)
    {
        $this->db = $db;
    }
    
    /**
     * Find a guess by gameId and timestamp
     * 
     * @param int $gid, $time
     * @return \Wws\Model\Guess|null
     */
    public function FindByIds($gid, $time)
    {
      $guessArr = $this->db->fetchAssoc('SELECT * FROM guesses WHERE game_id = game and timestamp = time', array("game"=>(int)$gid, "time"=>$time);
        return $this->returnGuess($guessArr);
    }

    /**
     * Find a guess by gameId and letter
     * 
     * @param int $gid, $letter
     * @return \Wws\Model\Guess|null
     */
    public function FindByLetterGuessed($gid, $letter)
    {
      $guessArr = $this->db->fetchAssoc('SELECT * FROM guesses WHERE game_id = :game and letter = :letter', array("game"=>(int)$gid, "letter"=>$letter);
        return $this->returnGuess($guessArr);
    }
    
    /**
     * Return a Guess object for an associative array result set
     * Also checks for empty/no result
     * @param mixed $sqlResult
     * @return \Wws\Model\Guess|null
     */
    protected function returnGuess($sqlResult)
    {
        if (!is_null($sqlResult) && $sqlResult !== false && !empty($sqlResult)) {
            $guess = new Guess($sqlResult);
            return $guess;
        }
        return null;
    }
    
    /**
     * Creates a guess and adds it to the db
     * 
     * @param type $letter, $gid, $pid
     * @return boolean True if successful
     */
      public function CreateGuess($letter, $gid, $pid)
    {
        // make sure guess hasn't already been played
      $existing = $this->FindByLetterGuessed($gid, $letter);
        if (is_null($existing)) {
            $count = $this->db->executeUpdate("INSERT INTO guesses (game_id, player_id, letter) VALUES (:game, :player, :letter)",
                array(
                    "game" => $gid,
                    "player" => $pid,
		    "letter" => $letter
                )
            );

            return $count == 1;
        } else {
            // already exists
            return false;
        }
    }
}