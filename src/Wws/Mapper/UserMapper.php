<?php

namespace Wws\Mapper;

use Wws\Model\User;

/**
 * This class can look up users by ID
 * 
 * @author Matt Durak <durakmat@msu.edu>
 */
class UserMapper
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
     * Find a user by id (used for cookie auth)
     * 
     * @param int $id
     * @return \Wws\Model\User|null
     */
    public function FindById($id)
    {
        $userArr = $this->db->fetchAssoc('SELECT * FROM player WHERE id = ?', array((int)$id));
        return $this->returnUser($userArr);
    }
    
    /**
     * Find a user by email
     * 
     * @param string $email
     * @return \Wws\Model\User|null
     */
    public function FindByEmail($email)
    {
        $userArr = $this->db->fetchAssoc('SELECT * FROM player WHERE email = ?', array($email));
        return $this->returnUser($userArr);
    }
    
    /**
     * Find a user by username (used for login)
     * 
     * @param string $username
     * @return \Wws\Model\User|null
     */
    public function FindByUsername($username)
    {
        $userArr = $this->db->fetchAssoc('SELECT * FROM player WHERE username = ? FOR UPDATE', array($username));
        return $this->returnUser($userArr);
    }
    
    /**
     * Update the last active column for a user to the current time
     * @param int $id Which user
     */
    public function UpdateActivity($id)
    {
        $this->db->executeUpdate("UPDATE player SET last_active = :now WHERE id = :id", array(
            'now' => date('Y-m-d H:i:s', time()),
            'id' => $id
        ));
    }
        
    
    /**
     * Return a User object for an associative array result set
     * Also checks for empty/no result
     * @param mixed $sqlResult
     * @return \Wws\Model\User|null
     */
    protected function returnUser($sqlResult)
    {
        if (!is_null($sqlResult) && $sqlResult !== false && !empty($sqlResult)) {
            $user = new User($sqlResult);
            return $user;
        }
        return null;
    }
    
    /**
     * Creates a User and adds them to the database
     * 
     * @param array $u Associative array with all user info (password is hashed)
     * @return boolean True if successful
     */
    public function CreateUser($u)
    {
        // transaction
        $this->db->beginTransaction();
        try {
            // make sure user doesn't exist
            $existing = $this->FindByUsername($u['username']);
            if (is_null($existing)) {
                $count = $this->db->executeUpdate("INSERT INTO player (username, email, password, first_name, last_name, birthdate, city, state, country, join_date) VALUES (:username, :email, :password, :first_name, :last_name, :birthdate, :city, :state, :country, :join_date)",
                    array(
                        'username' => $u['username'],
                        'email' => $u['email'],
                        'password' => $u['password'],
                        'first_name' => $u['first_name'],
                        'last_name' => $u['last_name'],
                        'birthdate' => $u['birthdate']->format('Y-m-d'),
                        'city' => $u['city'],
                        'state' => $u['state'],
                        'country' => $u['country'],
                        'join_date' => date("Y-m-d H:i:s")
                    )
                );

                $this->db->commit();
                return $count == 1;
            } else {
                // already exists
                return false;
            }
        } catch (\Exception $e) {
            // already exists
            $this->db->rollback();
            return false;
        }
    }
    
    /**
     * Update a player profile
     * 
     * @param User  $user
     * 
     * @return bool True if successful 
     */
    public function UpdateProfile(User $user)
    {
        $count = $this->db->executeUpdate("UPDATE player "
                . "SET email = :email, first_name = :fname, last_name = :lname, birthdate = :bday, city = :city, state = :state, country = :country, password = :password "
                . "WHERE id = :id",
            array(
                'email' => $user->getEmail(),
                'fname' => $user->getFirstName(),
                'lname' => $user->getLastName(),
                'bday' => $user->getBirthDate()->format('Y-m-d'),
                'city' => $user->getCity(),
                'state' => $user->getState(),
                'country' => $user->getCountry(),
                'password' => $user->GetPassword(),
                'id' => $user->getId()
            )
        );
        
        return $count == 1;
    }
    
    /**
     * Update a player score by adding points
     * 
     * @param int  $userId
     * @param int  $pointsToAdd
     * @return bool True if successful 
     */
    public function UpdateScore($userId, $pointsToAdd)
    {
        $count = $this->db->executeUpdate("UPDATE player "
                . "SET total_score = total_score + :points "
                . "WHERE id = :id",
            array(
                'points' => $pointsToAdd,
                'id' => $userId
            )
        );
        
        return $count == 1;
    }
	
	/**
	* Generate leaderboard
	*/
	public function GetLeaderboard()
	{
		$sqlResult = $this->db->fetchAll('SELECT username, total_score, last_active FROM player ORDER BY total_score DESC');
		$leaderResults = array();
        if (!is_null($sqlResult) && $sqlResult !== false && !empty($sqlResult)) {
            foreach ($sqlResult as $leader) {
				$newLead = new User();
				$newLead->setUsername($leader['username']);
				$newLead->setTotalScore($leader['total_score']);
				$newLead->setLastActive($leader['last_active']);
				$leaderResults[] = $newLead;
			}
        }
        return $leaderResults;
		
	}
}