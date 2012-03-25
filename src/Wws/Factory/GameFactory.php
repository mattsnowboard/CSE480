<?php

namespace Wws\Factory;

use Wws\Model\Game;

/**
 * Game Factory can be used to create a new game with a random word
 * 
 * @author Matt Durak 
 */
class GameFactory
{
    /**
     * @var Wws\Mapper\DictionaryMapper
     */
    protected $dictionaryMapper;
    
    /**
     * @var Wws\Mapper\GameMapper
     */
    protected $gameMapper;
    
    public function __construct(\Wws\Mapper\DictionaryMapper $dmap,
         \Wws\Mapper\GameMapper $gmap)
    {
        $this->dictionaryMapper = $dmap;
        $this->gameMapper = $gmap;
    }
    
    public function CreateSinglePlayerGame(\Wws\Model\User $user)
    {
        
    }
}