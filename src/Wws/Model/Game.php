<?php

namespace Wws\Model;

/**
 * Stores information about a Game
 * 
 * @author Devan Sayles <saylesd1@msu.edu>
 * @author Matt Durak <durakmat@msu.edu> 
 */
 
 class Game
{
    /**
     * @var int $id
     */
    private $id;
    
    /**
     * @var int $timestamp
     */
    private $timestamp;
    
    /**
     * @var string $word_start_state
     */
    private $wordStartState;
	
	 /**
     * @var int $num_players
     */
    private $numPlayers;
    
    /**
     * @var int
     */
    private $score1 = 0;
    
    /**
     * @var int
     */
    private $score2 = 0;
    
    /**
     * @var int
     */
    private $playerTurn = 1;
    
    /**
     * @var string
     */
    private $winnerFlag = 'playing';
    
    /**
     * @var int
     */
    private $wordId;
    
    /**
     * @var int
     */
    private $player1Id;
    
    /**
     * @var int
     */
    private $player2Id = NULL;
    
    /**
     * @var bool
     */
    private $isBonus;
    
    /**
     * @var string
     */
    private $currentState;
	
	 /**
     * @var Wws\Model\Dictionary
     */
    private $dictionary;
	
    /**
     * Create a Game with an optional array of parameters
     * 
     * @param array $g An associative array of parameters
     */
    public function __construct(array $g = null)
    {
        if (!is_null($g)) {
            $this->id = $g['id'];
            $this->timestamp = $g['timestamp'];
            $this->wordStartState = $g['word_start_state'];
            $this->numPlayers = $g['num_players'];
            $this->score1 = $g['score1'];
            $this->score2 = $g['score2'];
            $this->playerTurn = (int)$g['player_turn'];
            $this->winnerFlag = $g['winner_flag'];
            $this->wordId = $g['word_id'];
            $this->player1Id = $g['player1_id'];
            $this->player2Id = $g['player2_id'];
            $this->isBonus = $g['is_bonus'];
            $this->currentState = $g['current_state'];
			$this->dictionary = $g['dictionary'];
        }
    }
    
    public function getId()
    {
        return $this->id;
    }
    public function setId($id)
    {
        $this->id = (int)$id;
    }
    
    public function getTimestamp()
    {
        return $this->timestamp;
    }
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
    }
    
    public function getWordStartState()
    {
        return $this->wordStartState;
    }
    /**
     * Set the word start state, which is also the current state
     * 
     * @param string $wss 
     */
    public function setWordStartState($wss)
    {
        $this->wordStartState = $wss;
        $this->currentState = $wss;
    }
	
    public function getNumPlayers()
    {
        return $this->num_players;
    }
    public function setNumPlayers($wss)
    {
        $this->num_players = $wss;
    }
    
    public function getScore1()
    {
        return $this->score1;
    }

    public function setScore1($score1)
    {
        $this->score1 = $score1;
    }
    
    public function getScore2()
    {
        return $this->score2;
    }

    public function setScore2($score2)
    {
        $this->score2 = $score2;
    }

    public function getPlayerTurn()
    {
        return $this->playerTurn;
    }

    public function setPlayerTurn($playerTurn)
    {
        $this->playerTurn = $playerTurn;
    }
    
    public function getWinnerFlag()
    {
        return $this->winnerFlag;
    }

    public function setWinnerFlag($winnerFlag)
    {
        $this->winnerFlag = $winnerFlag;
    }
    
    public function getWordId()
    {
        return $this->wordId;
    }

    public function setWordId($wordId)
    {
        $this->wordId = $wordId;
    }

    public function getPlayer1Id()
    {
        return $this->player1Id;
    }

    public function setPlayer1Id($player1Id)
    {
        $this->player1Id = $player1Id;
    }
    
    public function getPlayer2Id()
    {
        return $this->player2Id;
    }

    public function setPlayer2Id($player2Id)
    {
        $this->player2Id = $player2Id;
    }

    public function getIsBonus()
    {
        return $this->isBonus;
    }

    public function setIsBonus($isBonus)
    {
        $this->isBonus = $isBonus;
    }
    
    public function getCurrentState()
    {
        return $this->currentState;
    }

    public function setCurrentState($currentState)
    {
        $this->currentState = $currentState;
    }
	
	public function setDictionary($dict)
	{
		$this->dictionary = $dict;
        $this->wordId = $dict->getId();
	}
	
	public function getDictionary()
	{
		return $this->dictionary;
	}

}