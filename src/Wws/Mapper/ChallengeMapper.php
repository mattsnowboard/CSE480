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
        $challengeArr = $this->db->fetchAssoc('SELECT c.*, p1.username AS challenger_name, p2.username AS recipient_name
                FROM challenge c
                LEFT JOIN game g ON c.game_id = g.id
                LEFT JOIN player p1 ON c.challenger_id = p1.id
                LEFT JOIN player p2 ON c.recipient_id = p2.id
                WHERE id = ?', array((int) $id));
        return $this->returnChallenge($challengeArr);
    }

    /**
     * Find Challenges recieved by userID
     * 
     * @param int $id
     * @return \Wws\Model\Challenge|null
     */
    public function FindSentChallengesByUserId($id, $status, $gamesInProgress = false)
    {
        if ($gamesInProgress) {
            $challengeArr = $this->db->fetchAll('SELECT c.*, p1.username AS challenger_name, p2.username AS recipient_name
                FROM challenge c
                LEFT JOIN game g ON c.game_id = g.id
                LEFT JOIN player p1 ON c.challenger_id = p1.id
                LEFT JOIN player p2 ON c.recipient_id = p2.id
                WHERE challenger_id=:id AND status = :status AND g.winner_flag = \'playing\'',
                array('id' => (int) $id, 'status' => $status));
        } else {
            $challengeArr = $this->db->fetchAll('SELECT c.*, p1.username AS challenger_name, p2.username AS recipient_name
                FROM challenge c
                LEFT JOIN game g ON c.game_id = g.id
                LEFT JOIN player p1 ON c.challenger_id = p1.id
                LEFT JOIN player p2 ON c.recipient_id = p2.id
                WHERE challenger_id=:id AND status = :status',
                array('id' => (int) $id, 'status' => $status));
        }

        return $this->returnChallenges($challengeArr);
    }
    
    /**
     * Find Challenges sent to userID
     * 
     * @param int $id
     * @return \Wws\Model\Challenge|null
     */
    public function FindRecievedChallengesByUserId($id, $status)
    {
        $challengeArr = $this->db->fetchAll('SELECT c.*, p1.username AS challenger_name, p2.username AS recipient_name
            FROM challenge c
            LEFT JOIN game g ON c.game_id = g.id
            LEFT JOIN player p1 ON c.challenger_id = p1.id
            LEFT JOIN player p2 ON c.recipient_id = p2.id
            WHERE recipient_id = :id AND status = :status',
                array('id' => (int) $id, 'status' => $status));

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
        $challenges = array();
        if (!is_null($sqlResult) && $sqlResult !== false && !empty($sqlResult)) {
            foreach ($sqlResult as $challenge) {
                $challenges[] = new Challenge($challenge);
            }
        }
        return $challenges;
    }

    public function createChallenge(\Wws\Model\Challenge $challenge)
    {
        $count = $this->db->executeUpdate("INSERT INTO challenge "
                . "(status, challenger_id, recipient_id) "
                . "VALUES (:status, :challenger, :recipient)",
            array(
                'status' => $challenge->GetStatus(),
                'challenger' => $challenge->GetChallengerId(),
                'recipient' => $challenge->GetRecipientId()
            )
        );
        
        $challenge->SetId($this->db->lastInsertId());

        return $count == 1;

    }
	
	public function removeChallenges($id)
	{
		$this->db->executeUpdate("DELETE FROM challenge WHERE status = 'pending' AND challenger_id = :id1 OR recipient_id = :id2", array('id1' => (int)$id, 'id2' => (int)$id));
	}
    
    public function addGame(\Wws\Model\Challenge $challenge, \Wws\Model\Game $game)
    {
        $count = $this->db->executeUpdate("UPDATE challenge "
                . "SET game_id = :gid, status = 'accepted' "
                . "WHERE id = :cid",
            array(
                'gid' => $game->getId(),
                'cid' => $challenge->getId()
            )
        );
        
        $challenge->setGameId($game->getId());
        $challenge->SetStatus('accepted');
        
        return $count == 1;
    }
    
    public function declineChallenge(\Wws\Model\Challenge $challenge)
    {
        $count = $this->db->executeUpdate("UPDATE challenge "
                . "SET status = 'declined' "
                . "WHERE id = :cid",
            array(
                'cid' => $challenge->getId()
            )
        );
        
        $challenge->SetStatus('declined');
        
        return $count == 1;
    }
}