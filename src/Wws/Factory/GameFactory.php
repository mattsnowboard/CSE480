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
    
    /**
     * Create a single player game for a given user and random word
     * This will also persist the Game in the database
     * 
     * @param \Wws\Model\User $user
     * @return \Wws\Model\Game 
     */
    public function CreateSinglePlayerGame(\Wws\Model\User $user)
    {
        // get the word to start
        $wordStart = $this->CreateRandomWordStart();
        
        $game = new Game();
        $game->setDictionary($wordStart['word']);
        $game->setWordStartState($wordStart['start']);
        $game->setNumPlayers(1);
        $game->setTimestamp(time());
        $game->setPlayer1Id($user->getId());
        /** @todo Query database for this */
        $game->setIsBonus(false);
        
        // Now persist in DB
        $this->gameMapper->CreateGame($game);
        
        return $game;
    }
	
	    /**
     * Create a multi player game for 2 users and random word
     * This will also persist the Game in the database
     * 
     * @param \Wws\Model\User $user1
	 * @param \Wws\Model\User $user2
     * @return \Wws\Model\Game 
     */
    public function CreateSinglePlayerGame(\Wws\Model\User $user1, \Wws\Model\User $user2)
    {
        // get the word to start
        $wordStart = $this->CreateRandomWordStart();
        
        $game = new Game();
        $game->setDictionary($wordStart['word']);
        $game->setWordStartState($wordStart['start']);
        $game->setNumPlayers(2);
        $game->setTimestamp(time());
        $game->setPlayer1Id($user1->getId());
		$game->setPlayer2Id($user2->getId());
        /** @todo Query database for this */
        $game->setIsBonus(false);
        
        // Now persist in DB
        $this->gameMapper->CreateGame($game);
        
        return $game;
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
        while ($letterCount < 2) {
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