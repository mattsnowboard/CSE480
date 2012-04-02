<?php

namespace Wws\Mapper;

use Wws\Model\Guess;

/**
 * This class can look up users by ID
 * 
 * @author Chelsea Carr <carrche2@msu.edu>
 */
class GuessMapper
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
    public function FindById($gid, $time)
    {
        $guessArr = $this->db->fetchAssoc('SELECT * FROM guess WHERE game_id = :game and timestamp = :time', array(
            'game' => (int) $gid,
            'time' => $time));
        return $this->returnGuess($guessArr);
    }
    
    /**
     * Find a guess by gameId
     * 
     * @param int $gid
     * @return array \Wws\Model\Guess
     */
    public function FindByGame($gid)
    {
        $guessArr = $this->db->fetchAll('SELECT * FROM guess WHERE game_id = :game', array(
            'game' => (int) $gid));
        return $this->returnGuesses($guessArr);
    }

    /**
     * Find a guess by gameId and letter
     * 
     * @param int $gid, $letter
     * @return \Wws\Model\Guess|null
     */
    public function FindByLetterGuessed($gid, $letter)
    {
        $guessArr = $this->db->fetchAssoc('SELECT * FROM guess WHERE game_id = :game and letter = :letter', array(
            'game'   => (int) $gid,
            'letter' => $letter));
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
    
    protected function returnGuesses($sqlResult)
    {
        $guesses = array();
        if (!is_null($sqlResult) && $sqlResult !== false && !empty($sqlResult)) {
            foreach ($sqlResult as $guess)
            $guesses[] = new Guess($guess);
        }
        return $guesses;
    }
    
    /**
     * Creates a Guess in the database from a Guess model
     * 
     * @param Wws\Model\Guess $guess
     * 
     * @return boolean True if successful
     */
    public function CreateGuess(\Wws\Model\Guess $guess)
    {
        $count = $this->db->executeUpdate("INSERT INTO guess "
                . "(is_correct, word, letter, is_full_word, player_id, game_id) "
                . "VALUES (:correct, :word, :letter, :isWord, :player, :game)",
            array(
                'correct' => $guess->GetIsCorect(),
                'word' => $guess->GetWord(),
                'letter' => $guess->GetLetter(),
                'isWord' => $guess->GetIsFullWord(),
                'player' => $guess->GetPlayerId(),
                'game' => $guess->GetGameId()
            )
        );
        
        return $count == 1;
    }

}