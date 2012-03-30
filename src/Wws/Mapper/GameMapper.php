<?php

namespace Wws\Mapper;

use Wws\Model\Game;
use Wws\Model\Dictionary;

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
     * @param \Wws\Model\User $user Which user the game must belong to
     * @return \Wws\Model\Game|null
     */
	public function FindById($id, \Wws\Model\User $user = null)
    {
        $gameArr = $this->db->fetchAssoc('SELECT *,  game.id AS id FROM game, dictionary WHERE game.word_id = dictionary.id and game.id = ?', array((int)$id));
        $game = $this->returnGame($gameArr);
        if (!is_null($user)
                && !($game->getPlayer1Id() == $user->getId()
                    || $game->getPlayer2Id() == $user->getId())) {
            // this game is for someone else
            throw new \Wws\Exception\NotAuthorizedException('You\'re creeping on someone elses game!');
        }
        
        return $game;
    }
	
	/**
     * Find all games for a User
     * 
     * @param int $uid
     * @param int $numPlayers
     * @param string $result What state the game is in
     * @return type 
     */
	public function FindGamesByUserId($uid, $numPlayers, $result)
    {
		if ($result == 'playing')
		{
			if ((int)$numPlayers == 1)
			{
				// returns in-progress single-player games involving given userID
				$gameArr = $this->db->fetchAll('SELECT *, game.id AS id FROM game, dictionary '
                        . 'WHERE game.word_id = dictionary.id and num_players = :numPlayers and winner_flag = :result and player1_id = :uid',
                    array( 'numPlayers' => (int) $numPlayers, 'uid' => $uid, 'result' => $result));
			}
			else
			{	// returns in-progress multi-player games involving given userID
				$gameArr = $this->db->fetchAll('SELECT *, game.id AS id FROM game, dictionary '
                        .'WHERE num_players = :numPlayers and (player1_id = :uid or player2_id = :uid',
                    array( 'numPlayers' => $numPlayers,
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
		
		var_dump($gameArr);
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
			
			// create new Word object
			$dict = new Dictionary();
			$dict->setID($sqlResult['word_id']);
			$dict->setWord($sqlResult['word']);
			$dict->setDefinition($sqlResult['definition']);

			$game->setDictionary($dict);
			
            return $game;
        }
        return null;
    }
	
	protected function returnGames($sqlResult)
    {
        $games = array();
        if (!is_null($sqlResult) && $sqlResult !== false && !empty($sqlResult)) {
            foreach ($sqlResult as $game)
            $games[] =$this->returnGame($game);
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
                'start' => $game->getWordStartState(),
                'num' => $game->getNumPlayers(),
                'turn' => $game->getPlayerTurn(),
                'word' => $game->getWordId(),
                'player1' => $game->getPlayer1Id(),
                'player2' => $game->getPlayer2Id(),
                'bonus' => $game->getIsBonus(),
                'start' => $game->getWordStartState()
            )
        );
        
        $game->setId($this->db->lastInsertId());

        return $count == 1;
    }
	
}