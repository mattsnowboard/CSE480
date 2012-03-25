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
	public function FindGamesByUserId($uid, $numPlayers, $isInProgress)
    {
		if ((int)$numPlayers == 1)
		{
			 $gameArr = $this->db->fetchAll('SELECT * FROM game WHERE num_players = :numPlayers and player1_id = :uid', array( 'numPlayers' => (int) $numPlayers,
					'uid' => $uid));
		}
		else
		{
			$gameArr = $this->db->fetchAll('SELECT * FROM game WHERE num_players = :numPlayers and (player1_id = :uid or player2_id = :uid', array( 'numPlayers' => $numPlayers,
									   'uid' => $uid));
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
}