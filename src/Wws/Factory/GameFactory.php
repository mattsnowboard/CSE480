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
        // get the word to start
        $wordStart = $this->CreateRandomWordStart();
    }
    
    /**
     * Creates a set of a random dictionary word and a starting game state
     * 
     * @return array with the Word model object and the start state string
     */
    public function CreateRandomWordStart()
    {
        $word = $this->dictionaryMapper->FindRandom();
        $theWord = $word->getWord();
        // shuffle the indices
        $letters = range(0, strlen($theWord) - 1);
        shuffle($letters);
        
        // pick letters so we will show at least 3 letters as hints
        $lettersPicked = array();
        $letterCount = 0;
        while ($letterCount < 3) {
            $index = array_pop($letters);
            $letter = substr($theWord, $index, 1);
            // count occurance of random letter
            $letterCount += substr_count($theWord, $letter);
            $lettersPicked[] = $letter;
        }
        
        // make a state of _ and only the letters we picked
        $wordState = '';
        for ($i = 0; $i < strlen($theWord); $i++) {
            $letter = substr($theWord, $i, 1);
            $wordState .= (in_array($letter, $lettersPicked)) ?
                $letter : '_';
        }
        
        return array(
            'word' => $word,
            'start' => $wordState
        );
    }
}