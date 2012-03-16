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
    private $word_start_state;
    
    /**
     * Create a Game with an optional array of parameters
     * 
     * @param array $u An associative array of parameters
     */
    public function __construct(array $u = null)
    {
        if (!is_null($u)) {
            $this->id = $u['id'];
            $this->timestamp = $u['timestamp'];
            $this->word_start_states = $u['word_start_state'];
        }
    }
    
    public function GetId()
    {
        return $this->id;
    }
    public function SetId($id)
    {
        $this->id = (int)$id;
    }
    
    public function GetTimestamp()
    {
        return $this->timestamp;
    }
    public function SetTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
    }
    
    public function GetWordStartState()
    {
        return $this->word_start_state;
    }
    public function SetWordStartState($wss)
    {
        $this->word_start_state = $wss;
    }
}