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
     * Find a user by email (used for login)
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
     * @param type $email
     * @param type $password Hashed
     * @return boolean True if successful
     */
    public function CreateUser($email, $password)
    {
        // make sure user doesn't exist
        $existing = $this->FindByEmail($email);
        if (is_null($existing)) {
            $count = $this->db->executeUpdate("INSERT INTO player (email, password) VALUES (:email, :password)",
                array(
                    "email" => $email,
                    "password" => $password
                )
            );

            return $count == 1;
        } else {
            // already exists
            return false;
        }
    }
}