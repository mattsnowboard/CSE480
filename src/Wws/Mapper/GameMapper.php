<?php

namespace Wws\Mapper;

use Wws\Model\Game;

/**
 * This class can look up games by ID
 * 
 * @author Devan Sayles <saylesd1@msu.edu>
 * @author Matt Durak <durakmat@msu.edu>
 */
class GameMapper
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
     * Find a game by id
     * 
     * @param int $id
     * @return \Wws\Model\Game|null
     */
	public function FindById($id)
    {
        $gameArr = $this->db->fetchAssoc('SELECT * FROM game WHERE id = ?', array((int)$id));
        return $this->returnGame($gameArr);
    }
	
	/**
     * Return a Game object for an associative array result set
     * Also checks for empty/no result
     * @param mixed $sqlResult
     * @return \Wws\Model\Game|null
     */
    protected function returnGame($sqlResult)
    {
        if (!is_null($sqlResult) && $sqlResult !== false && !empty($sqlResult)) {
            $game = new Game($sqlResult);
            return $game;
        }
        return null;
    }
    
    /**
     * Creates a Game in the database from a Game model
     * 
     * @param Wws\Model\Game $game
     * @return boolean True if successful
     */
    public function CreateGame(\Wws\Model\Game $game)
    {
        $count = $this->db->executeUpdate("INSERT INTO game "
                . "(word_start_state, num_players, player_turn, word_id, player1_id, player2_id, is_bonus, current_state) "
                . "VALUES (:start, :num, :turn, :word, :player1, :player2, :bonus, :start)",
            array(
                'start' => $game->getStartState(),
                'num' => $game->getNumPlayers(),
                'turn' => $game->getPlayerTurn(),
                'word' => $game->getWordId(),
                'player1' => $game->getPlayer1Id(),
                'player2' => $game->getPlayer2Id(),
                'bonus' => $game->getIsBonus(),
                'start' => $game->getWordStartState()
            )
        );

        return $count == 1;
    }
}