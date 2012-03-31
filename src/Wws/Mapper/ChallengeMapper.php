<?php

namespace Wws\Mapper;

use Wws\Model\Challenge;

/**
 * This class can look up Challenges in the database
 * 
 * @author Matt Durak <durakmat@msu.edu>
 */
class ChallengeMapper
{
    /**
     * @var Doctrine\DBAL\Connection 
     */
    protected $db;
    
    public function __construct(\Doctrine\DBAL\Connection $db)
    {
        $this->db = $db;
    }
    
    /**
     * Find by id
     * 
     * @param int $id
     * @return \Wws\Model\Challenge|null
     */
    public function FindById($id)
    {
        $challengeArr = $this->db->fetchAssoc('SELECT * FROM challenge WHERE id = ?', array((int)$id));
        return $this->returnChallenge($challengeArr);
    }
	
	/**
     * Find Challenges recieved by userID
     * 
     * @param int $id
     * @return \Wws\Model\Challenge|null
     */
    public function FindRecievedChallengesByUserId($id, $status)
    {
        $challengeArr = $this->db->fetchAll('SELECT * FROM challenge, player WHERE challenger_id=player.id AND recipient_id = :id AND status = :status',
			array('id' => (int)$id, 'status' => $status));
			
        return $this->returnChallenges($challengeArr);
    }
	
	
        
    /**
     * Return a Challenge object for an associative array result set
     * Also checks for empty/no result
     * @param mixed $sqlResult
     * @return \Wws\Model\User|null
     */
    protected function returnChallenge($sqlResult)
    {
        if (!is_null($sqlResult) && $sqlResult !== false && !empty($sqlResult)) {
            $challenge = new Challenge($sqlResult);
            return $challenge;
        }
        return null;
    }
	
    protected function returnChallenges($sqlResult)
    {
        if (!is_null($sqlResult) && $sqlResult !== false && !empty($sqlResult)) {
            foreach ($sqlResult as $challenge)
				$challenges[] =$this->returnChallenge($challenge);
        }
		return $challenges;
    }
	
	
}