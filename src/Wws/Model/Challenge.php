<?php

namespace Wws\Model;

/**
 * Stores information about a Challenge
 * 
 * @author Matt Durak <durakmat@msu.edu> 
 */
class Challenge
{
    /**
     * @var int $id
     */
    private $id;
    
    /**
     * Create a Challenge with an optional array of parameters
     * 
     * @param array $c An associative array of parameters
     */
    public function __construct(array $c = null)
    {
        if (!is_null($c)) {
            $this->id = $c['id'];
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
}