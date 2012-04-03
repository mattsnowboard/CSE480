<?php

namespace Wws\Model;

use Wws\Exception\GamePlayException;

/**
 * Manages the actual game play logic
 * 
 * @author Matt Durak <durakmat@msu.edu> 
 */
 
class GamePlay
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $conn;
    
    /**
     * @var Wws\Mapper\DictionaryMapper
     */
    protected $dictionaryMapper;
    
    /**
     * @var Wws\Mapper\GameMapper
     */
    protected $gameMapper;
    
    /**
     * @var Wws\Mapper\GuessMapper
     */
    protected $guessMapper;
    
    /**
     * @var Wws\Mapper\UserMapper
     */
    protected $userMapper;
    
    public function __construct(\Doctrine\DBAL\Connection $conn,
            \Wws\Mapper\DictionaryMapper $dmap,
            \Wws\Mapper\GameMapper $gmap,
            \Wws\Mapper\GuessMapper $guessMap,
            \Wws\Mapper\UserMapper $umap)
    {
        $this->conn = $conn;
        $this->dictionaryMapper = $dmap;
        $this->gameMapper = $gmap;
        $this->guessMapper = $guessMap;
        $this->userMapper = $umap;
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
     * 
     * @return bool is correct guess
     */
    public function makeLetterGuess(Game $game, User $user, $letter)
    {
        if (!$this->userCanGuessLetter($game, $user)) {
            throw new GamePlayException('User is not allowed to guess more letters');
        }
        
        $dictionary = $game->getDictionary();
        $wordLetters = str_split($dictionary->getWord());
        
        $correct = in_array(strtolower($letter), $wordLetters);
        
        $guess = new Guess();
        $guess->SetPlayerId($user->GetId());
        $guess->SetGameId($game->getId());
        $guess->SetIsFullWord(false);
        $guess->SetLetter(strtolower($letter));
        $guess->SetIsCorrect($correct);
                
        // tell the game that the guess was made so it can update the score and turn
        $game->updateGuess($user, $correct);
        if ($correct) {
            // update the Game state
            $game->updateState(strtolower($letter));
        }
        
        $this->conn->beginTransaction();
        try {
            // store new guess in database
            $this->guessMapper->CreateGuess($guess);
            
            // check if the game is now over
            if ($game->isGuessed()) {

                // they guessed it, end the game
                $game->endGame();
                $game->isOver();
                // update scores
                $this->userMapper->UpdateScore($game->getPlayer1Id(), $game->getScore1());
                if ($game->getNumPlayers() > 1 && !is_null($game->getPlayer2Id())) {
                    $this->userMapper->UpdateScore($game->getPlayer2Id(), $game->getScore2());
                }
            }

            $this->gameMapper->UpdateGame($game);
            
            $this->conn->commit();
        } catch (\Exception $e) {
            // already exists
            $this->conn->rollback();
        }
		if ($game->isOver())
		{
			$game->endGame();
		}
        
        return $correct;
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
      /*if (!$this->userCanGuessWord($game, $user)) {
            throw new GamePlayException('User is not allowed to guess more letters');
	    }*/
        
        $dictionary = $game->getDictionary();
        $correctWord = $dictionary->getWord();

        $correct = ($correctWord == $word);
        $guess = new Guess();
        $guess->SetPlayerId($user->GetId());
        $guess->SetGameId($game->getId());
        $guess->SetIsFullWord(true);
        $guess->SetWord($word);
        $guess->SetIsCorrect($correct);
        
            // update the Game state
            $game->SetCurrentState(strtolower($correctWord));
        
        $this->conn->beginTransaction();
        try {
            // store new guess in database
            $this->guessMapper->CreateGuess($guess);
            
            // check if the game is now over (1 player games only get one shot)
            if ($game->getNumPlayers() == 1 || $game->isGuessed()) {

                // they guessed it, end the game
                $game->endGame();
                // update scores
                $this->userMapper->UpdateScore($game->getPlayer1Id(), $game->getScore1());
                if ($game->getNumPlayers() > 1 && !is_null($game->getPlayer2Id())) {
                    $this->userMapper->UpdateScore($game->getPlayer2Id(), $game->getScore2());
                }
            }

            $this->gameMapper->UpdateGame($game);
            
            $this->conn->commit();
        } catch (\Exception $e) {
            // already exists
            $this->conn->rollback();
        }
		
		if ($game->isOver())
		{
			$game->endGame();
		}
        
        return $correct;
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
    
    
    public function userCanGuessLetter(Game $game, User $user)
    {
        if ($game->getNumPlayers() == 1) {
            $guesses = $game->getGuesses();
            if (is_null($guesses)) {
                throw new \Exception('The guesses were not retrieved from the database');
            }
            return count($guesses) < 3;
        }
        return false;
    }

    public function userCanGuessWord(Game $game, User $user)
    {
        if ($game->getNumPlayers() == 1) {
            $guesses = $game->getGuesses();
            if (is_null($guesses)) {
                throw new \Exception('The guesses were not retrieved from the database');
            }
            return count($guesses) < 4;
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
        /** @todo check something first maybe? */
        // only exit on single player?
        if ($game->getNumPlayers() == 1 && is_null($game->getPlayer2Id())) {
            $game->endGame(true);
            $this->userMapper->UpdateScore($game->getPlayer1Id(), $game->getScore1());
            $this->gameMapper->UpdateGame($game);
        }
    }
}