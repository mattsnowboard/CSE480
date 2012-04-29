<?php

namespace Wws\Model;

/**
 * Stores information about a Challenge
 * 
 * @author Matt Durak <durakmat@msu.edu> 
 * @author Devan Sayles <saylesd1@msu.edu>
 */
class Challenge
{

    /**
     * @var int $id
     */
    private $id;
    
    private $status;
    
    private $gameId;
    
    private $challengerId;
    
    private $recipientId;

    /**
     * Create a Challenge with an optional array of parameters
     * 
     * @param array $c An associative array of parameters
     */
    public function __construct(array $c = null)
    {
        if (!is_null($c)) {
            $this->id = $c['id'];
            $this->status = $c['status'];
            $this->challengerId = $c['challenger_id'];
            $this->recipientId = $c['recipient_id'];
            $this->gameId = $c['game_id'];
        }
    }

    public function GetId()
    {
        return $this->id;
    }
    public function SetId($id)
    {
        $this->id = (int) $id;
    }

    public function GetStatus()
    {
        return $this->status;
    }
    public function SetStatus($status)
    {
        $this->status = $status;
    }

    public function GetChallengerId()
    {
        return $this->challengerId;
    }
    public function SetChallengerId($id)
    {
        $this->challengerId = (int) $id;
    }

    public function GetRecipientId()
    {
        return $this->recipientId;
    }
    public function SetRecipientId($id)
    {
        $this->recipientId = (int) $id;
    }
    
    public function getGameId()
    {
        return $this->gameId;
    }

    public function setGameId($gameId)
    {
        $this->gameId = $gameId;
    }

        public function toArray() 
    {
        $properties = get_object_vars($this);
        $array = array();
        foreach ($properties as $name => $value) {
            $array[$name] = $value;
        }
        return $array;
    }
}