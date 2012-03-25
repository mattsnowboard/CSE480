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
     * Find games for a userID
     * 
     * @param int $id
     * @return \Wws\Model\Game|null
     */
	public function FindGamesByUserId($uid, $numPlayers, $result)
    {
		if ($result == 'playing')
		{
			if ((int)$numPlayers == 1)
			{
				// returns in-progress single-player games involving given userID
				$gameArr = $this->db->fetchAll('SELECT * FROM game WHERE num_players = :numPlayers and winner_flag = :result and player1_id = :uid', array( 'numPlayers' => (int) $numPlayers, 'uid' => $uid, 'result' => $result));
			}
			else
			{	// returns in-progress multi-player games involving given userID
				$gameArr = $this->db->fetchAll('SELECT * FROM game WHERE num_players = :numPlayers and (player1_id = :uid or player2_id = :uid', array( 'numPlayers' => $numPlayers,
											'uid' => $uid));
			}
		}
		else
		{
			if ((int)$numPlayers == 1)
			{
				// returns all single-player games involving given userID
			}
			else
			{
				// returns all multi-player games involving given userID
			}
		}
		
        return $this->returnGames($gameArr);
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
	
	protected function returnGames($sqlResult)
    {
        $games = array();
        if (!is_null($sqlResult) && $sqlResult !== false && !empty($sqlResult)) {
            foreach ($sqlResult as $game)
            $games[] = new Game($game);
        }
        return $games;
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
                //'start' => $game->get
            )
        );

        return $count == 1;
    }
}