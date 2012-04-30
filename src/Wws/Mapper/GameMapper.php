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
        $gameArr = $this->db->fetchAssoc('SELECT *,  game.id AS id FROM game, dictionary '
                . 'WHERE game.word_id = dictionary.id and game.id = ? FOR UPDATE',
            array((int)$id));
        $game = $this->returnGame($gameArr);
        if (!is_null($game) && !is_null($user)
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
			else if ((int)$numPlayers == 2)
			{	// returns in-progress multi-player games involving given userID
				$gameArr = $this->db->fetchAll('SELECT *, game.id AS id FROM game, dictionary WHERE game.word_id = dictionary.id '
					. 'AND num_players = :numPlayers AND (player1_id = :uid OR player2_id = :uid) AND game.winner_flag = :result',
                    array('numPlayers' => (int) $numPlayers, 'uid' => $uid, 'result' => $result));
			}
		}
		else
		{
			$gameArr = $this->db->fetchAll('SELECT *, game.id AS id FROM game WHERE game.word_id = dictionary.id AND (player1_id = :uid OR player2_id = :uid)', array ('uid' => $uid));
		}
		
        return $this->returnGames($gameArr);
    }
	
	public function GetGamesForHistory($uid)
	{
		// returns all finished games associated with a given userID 
		//  to be displayed on the History page
		$sqlResult = $this->db->fetchAll('SELECT dictionary.*, game.id AS gameId, game.num_players, game.timestamp as gameTimestamp, '
				. 'p1.username as P1username, p2.username as P2username FROM game JOIN dictionary ON game.word_id = dictionary.id '
				. 'LEFT JOIN player p1 ON game.player1_id = p1.id LEFT JOIN player p2 ON game.player2_id = p2.id WHERE winner_flag <> "playing" '
				. 'AND (player1_id = :uid OR player2_id = :uid) ORDER BY timestamp DESC', array( 'uid' => $uid));
		
		$games = array();
		if (!is_null($sqlResult) && $sqlResult !== false && !empty($sqlResult)) 
		{
            foreach ($sqlResult as $result)
			{
				// create new Word object
				$dict = new Dictionary();
				$dict->setID($result['id']);
				$dict->setWord($result['word']);
				$dict->setDefinition($result['definition']);

				$game = new Game();
				$game->setDictionary($dict);
				$game->setPlayer1Name($result['P1username']);
				$game->setPlayer2Name($result['P2username']);
				$game->setNumPlayers($result['num_players']);
				$game->setTimestamp($result['gameTimestamp']);
				$game->setId($result['gameId']);
			
				$games[] = $game;
			}
		}
		return $games;
	}
	/**
     * Get details for a specific game to be displayed from the History page
     * 
     * @param int $id
     * @return type 
     */ 
	public function GetGameDetails($id)
	{	
		$sqlResult = $this->db->fetchAssoc('SELECT game.*, p1.username as P1username, p2.username as P2username FROM game LEFT JOIN player p1 ON game.player1_id = p1.id '
			. 'LEFT JOIN player p2 ON game.player2_id = p2.id WHERE game.id = :id ', array('id' => $id));
		$game = new Game($sqlResult);
		$game->setPlayer1Name($sqlResult['P1username']);
		$game->setPlayer2Name($sqlResult['P2username']);
		
		return $game;
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
            foreach ($sqlResult as $game) {
                $games[] =$this->returnGame($game);
            }
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
    
    /**
     * Update a Game (state, turn, score, is it over?)
     * @param \Wws\Model\Game $game
     * 
     * @return boolean True if successful
     */
    public function UpdateGame(\Wws\Model\Game $game)
    {
        $count = $this->db->executeUpdate("UPDATE game "
                . "SET player_turn = :turn, current_state = :state, winner_flag = :flag, score1 = :s1, score2 = :s2 "
                . "WHERE id = :id",
            array(
                'turn' => $game->getPlayerTurn(),
                'state' => $game->getCurrentState(),
                'flag' => $game->getWinnerFlag(),
                's1' => $game->getScore1(),
                's2' => $game->getScore2(),
                'id' => $game->getId()
            )
        );
        
        return $count == 1;
    }
	
    /**
     * Get the last 3 single player games, used to check if we do a bonus round
     * @param \Wws\Model\User $user
     * @return Game[]
     */
    public function GetLastThreeSingle(\Wws\Model\User $user)
    {
        $gameArr = $this->db->fetchAll('SELECT *, game.id AS id FROM game '
                . 'JOIN dictionary ON game.word_id = dictionary.id  '
                . 'WHERE num_players = 1 AND player1_id = :uid '
                . 'ORDER BY timestamp DESC '
                . 'LIMIT 3',
            array('uid' => $user->GetId()));
        
        return $this->returnGames($gameArr);
    }
	
	public function CountSinglePlayerGamesById($uid)
	{
		$sqlResult = $this->db->fetchAssoc('SELECT COUNT(*) as gameCount FROM game WHERE num_players = 1 AND player1_id = :uid' 
					. ' AND winner_flag<>"playing"', array('uid'=>$uid));
		
		return $sqlResult;
	}
	
	public function CountMultiPlayerGamesById($uid)
	{
		$sqlResult = $this->db->fetchAssoc('SELECT COUNT(*) as gameCount FROM game WHERE num_players = 2 AND (player1_id = :uid' 
					. ' OR player2_id = :uid) AND winner_flag<>"playing"', array('uid'=>$uid));
		
		return $sqlResult;
	}
	
	public function CountWinsById($uid)
	{
		$sqlResult = $this->db->fetchAssoc('SELECT COUNT(*) as winCount FROM game WHERE (winner_flag = "1" AND player1_id = :uid) '
				. 'OR (winner_flag = "2" AND player2_id = :uid)', array('uid'=>$uid));
		
		return $sqlResult;
	}
	
	public function CountDrawsById($uid)
	{
		$sqlResult = $this->db->fetchAssoc('SELECT COUNT(*) as drawCount FROM game WHERE (player1_id = :uid OR player2_id = :uid) AND winner_flag = "draw"', array('uid'=>$uid));
		
		return $sqlResult;
	}
	
	public function CountLossesById($uid)
	{
		$sqlResult = $this->db->fetchAssoc('SELECT COUNT(*) as lossCount FROM game WHERE '
				. '(winner_flag = "lose" AND num_players = 1 AND player1_id = :uid) OR '
				. '(winner_flag = "1" AND player1_id <> :uid AND player2_id = :uid) OR '
				. '(winner_flag = "2" AND player1_id = :uid AND player2_id <> :uid)', array('uid'=>$uid));
		
		return $sqlResult;
	}
	
}