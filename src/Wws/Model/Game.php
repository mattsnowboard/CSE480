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
    
	private $player1Name;
	private $player2Name = NULL;
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
     * @var Wws\Model\Guess[] array
     */
    private $guesses = array();
	
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
        return $this->numPlayers;
    }
    public function setNumPlayers($wss)
    {
        $this->numPlayers = $wss;
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
	
	// names for players
	public function getPlayer1Name()
    {
        return $this->player1Name;
    }

    public function setPlayer1Name($player1Name)
    {
        $this->player1Name = $player1Name;
    }
    
    public function getPlayer2Name()
    {
        return $this->player2Name;
    }

    public function setPlayer2Name($player2Name)
    {
        $this->player2Name = $player2Name;
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
    
    public function getLettersArray()
    {
        // if the game is over, return the correct word
        if ($this->isOver()) {
            return str_split($this->dictionary->getWord());
        }
        return str_split($this->currentState);
    }

    public function setCurrentState($currentState)
    {
        $this->currentState = $currentState;
    }
	
	public function setDictionary(Dictionary $dict)
	{
		$this->dictionary = $dict;
        $this->wordId = $dict->getId();
	}
	
	public function getDictionary()
	{
		return $this->dictionary;
	}
    
    public function getGuesses()
    {
        return $this->guesses;
    }

    public function setGuesses(array $guesses)
    {
        $this->guesses = $guesses;
    }
    
    public function addGuess(Guess $guess)
    {
        $this->guesses[] = $guess;
    }

    
    /**
     * Update the game state by revealing more letters
     * @param char $letter 
     */
    public function updateState($letter)
    {
        $wordLetters = str_split($this->dictionary->getWord());
        $newState = '';
        for ($i = 0; $i < strlen($this->currentState); $i++) {
            $newState .= ($wordLetters[$i] == $letter) ? $wordLetters[$i]
                : $this->currentState[$i];
        }
        $this->currentState = $newState;
    }
    
    /**
     * Update the score based on a letter guess.  Also updates the player turn
     * 
     * @param User $user    Who guessed
     * @param type $correct Whether it was correct or not
     */
    public function updateGuess(User $user, $correct)
    {
        $whichPlayer = ($this->player1Id == $user->getId()) ? 1 : (
                ($this->player2Id == $user->getId()) ? 2 : -1);
        
        $points = ($correct) ? 1 : -1;
        if ($this->isBonus) {
            $points *= 2;
        }
        
        if ($whichPlayer === 1) {
            $this->score1 += $points;
        } else if ($whichPlayer === 2) {
            $this->score2 += $points;
        }
        
        $this->updateTurn();
    }
    
    /**
     * Update whose turn it is (does nothing for 1 player game) 
     */
    public function updateTurn()
    {
        if ($this->numPlayers > 1) {
            $this->playerTurn++;
            if ($this->playerTurn > $this->numPlayers) {
                $this->playerTurn = 1;
            }
        }
    }
    
    /**
     * End the game. The current user wins if the word is guessed otherwise it is
     * a lose or draw
     * 
     * @param bool $isExit True if the player chose to quit and they will lose points
     */
    public function endGame($isExit = false)
    {
        if ($this->numPlayers == 1) {
            if ($isExit) {
                $this->score1 -= 1;
            }
            if ($this->isGuessed()) {
                // correct/they won, they get 5 points (8 for bonus round)
                $this->score1 += ($this->isBonus) ? 8 : 5;
                // update state to Player 1 wins
                $this->winnerFlag = '1';
            } else {
                // update state to lose
                $this->winnerFlag = 'lose';
            }
        } else {
            /** @todo End multi-player game */
        }
    }
    
    /**
     * Checks if the word has been fully guessed
     * @return bool True if the game is now over
     */
    public function isGuessed()
    {
        return $this->currentState === $this->dictionary->getWord();
    }
    
    /**
     * Check if the game is over
     * @return bool True if the game is over, can't make more guesses if true
     */
    public function isOver()
    {
        return $this->winnerFlag != 'playing';
    }

}
