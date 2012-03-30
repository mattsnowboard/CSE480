<?php

namespace Wws\Model;

/**
 * Manages the actual game play logic
 * 
 * @author Matt Durak <durakmat@msu.edu> 
 */
 
class GamePlay
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
     * Guess a single letter for a game. Also checks that this user is a member
     * of the game and it is their turn.  Updates the score in the game table
     * 
     * If the game is over after this, update the score on the user table
     * 
     * @param Game $game
     * @param User $user
     * @param char $letter 
     */
    public function makeLetterGuess(Game $game, User $user, $letter)
    {
        
    }
    
    /**
     * Guess a full word for a game.  Checks that the user is allowed to guess
     * a word for the game and updates the game score.
     * 
     * Updates the user score if the game ends.
     * 
     * @param Game $game
     * @param User $user
     * @param string $word 
     */
    public function makeWordGuess(Game $game, User $user, $word)
    {
        
    }
    
    /**
     * Checks if it is the users turn to play in a given game
     * 
     * @param Game $game
     * @param User $user 
     */
    public function isUserTurn(Game $game, User $user)
    {
        $turn = $game->getPlayerTurn();
        if ($turn == 1 && $game->getPlayer1Id() == $user->GetId()) {
            return true;
        } else if ($turn == 2 && $game->getPlayer1Id() == $user->GetId()) {
            return true;
        }
        return false;
    }
    
    /**
     * Exits the game, which causes the user to lose the game and lose points.
     * This will update the user score
     * 
     * @param Game $game
     * @param User $user 
     */
    public function exitGame(Game $game, User $user)
    {
        
    }
}