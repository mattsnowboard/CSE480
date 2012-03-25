<?php

namespace Wws\Model;

/**
 * Stores information about a Dictionary
 * 
 * @author Matt Durak <durakmat@msu.edu> 
 */
 
 class Dictionary
{
    /**
     * @var int $id
     */
    private $id;
    
    /**
     * @var string $word
     */
    private $word;
    
    /**
     * @var string $definition
     */
    private $definition;
	
    /**
     * Create a Game with an optional array of parameters
     * 
     * @param array $d An associative array of parameters
     */
    public function __construct(array $d = null)
    {
        if (!is_null($d)) {
            $this->id = $d['id'];
            $this->word = $d['word'];
            $this->definition = $d['definition'];
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
    
    public function getWord()
    {
        return $this->word;
    }
    public function setWord($word)
    {
        $this->word = $word;
    }
    
    public function getDefinition()
    {
        return $this->definition;
    }
    public function setDefinition($definition)
    {
        $this->definition = $definition;
    }

}