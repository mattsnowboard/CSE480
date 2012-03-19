<?php

namespace Wws\Model;

/**
 * Stores information about a Game
 * 
 * @author Chelsea Carr <carrche2@msu.edu>
 */
 
 class Game
{
    /**
     * @var int $timestamp
     */
    private $timestamp;
    
    /**
     * @var int $is_correct
     */
    private $is_correct;
    
    /**
     * @var string $word
     */
    private $word;
    
    /**
     * @var string $letter
     */
    private $letter;

    /**
     * @var string $is_full_word
     */
    private $is_full_word;

    /**
     * @var string $player_id
     */
    private $player_id;

    /**
     * @var string $game_id
     */
    private $game_id;

    /**
     * Create a Guess with an optional array of parameters
     * 
     * @param array $u An associative array of parameters
     */
    public function __construct(array $u = null)
    {
        if (!is_null($u)) {
            $this->timestamp = $u['timestamp'];
	    $this->is_correct = $u['is_correct'];
	    $this->word = $u['word'];
	    $this->letter = $u['letter'];
            $this->is_full_word = $u['is_full_word'];
	    $this->player_id = $u['player_id'];
	    $this->game_id = $u['game_id'];
        }
    }
    
    public function GetTimestamp()
    {
        return $this->timestamp;
    }
    public function SetTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
    }
    
    public function GetIsCorect()
    {
        return $this->is_correct;
    }
    public function SetIsCorrect($ic)
    {
        $this->is_correct = (bool)$ic;
    }

 public function GetWord()
    {
        return $this->word;
    }
    public function Setword($word)
    {
        $this->word = $word;
    }

    public function GetLetter()
    {
        return $this->letter;
    }
    public function SetLetter($letter)
    {
        $this->letter = $letter;
    }


    public function GetWordStartState()
    {
        return $this->is_full_word;
    }
    public function SetIsFullWord($ifw)
    {
        $this->is_full_word = $ifw;
    }

    public function GetPlayerId()
    {
        return $this->player_id;
    }
    public function SetPlayerId($pid)
    {
        $this->player_id = (int)$pid;
    }

    public function GetGameId()
    {
        return $this->game_id;
    }
    public function SetGameId($gid)
    {
        $this->game_id = (int)$gid;
    }
}