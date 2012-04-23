<?php

namespace Wws\Factory;

use Wws\Model\Challenge;
use Wws\Model\User;

/**
 * Challenge Factory can be used to create a new challenge
 * 
 * @author Matt Durak 
 */
class ChallengeFactory
{
    /**
     * @var \Wws\Mapper\UserMapper
     */
    protected $userMapper;
    
    /**
     * @var \Wws\Mapper\ChallengeMapper
     */
    protected $challengeMapper;
    
    public function __construct(\Wws\Mapper\ChallengeMapper $cmap,
        \Wws\Mapper\UserMapper $umap)
    {
        $this->challengeMapper = $cmap;
        $this->userMapper = $umap;
    }
    
    /**
     * Create a single player game for a given user and random word
     * This will also persist the Game in the database
     * 
     * @param \Wws\Model\User $user
     * @param integer $recipient
     * 
     * @return \Wws\Model\Challenge
     */
    public function CreateChallenge(User $user, $recipient)
    {
        $userRecipient = $this->userMapperr->FindById($recipient);
        // check that user exists and is active/not in game
        if (is_null($userRecipient)) {
            // user does not exist
            throw new \Wws\Exception\NotFoundException('That stranger doesn\'t exist');
        } else if (!$userRecipient->getIsActiveNotInGame()) {
            // user is not available
            throw new \Wws\Exception\GamePlayException('The stranger is not available to play a game');
        }
        
        $challenge = new Challenge();
        $challenge->SetChallengerId($user->getId());
        $challenge->SetRecipientId($userRecipient->getId());
        $challenge->SetStatus('pending');

        // Now persist in DB
        //$this->challengeMapper->
        
        return $challenge;
    }
    
}