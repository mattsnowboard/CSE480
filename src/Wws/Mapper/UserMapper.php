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
        $userArr = $this->db->fetchAssoc('SELECT * FROM player WHERE username = ?', array($username));
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
     * @param array $u Associative array with all user info (password is hashed)
     * @return boolean True if successful
     */
    public function CreateUser($u)
    {
        // make sure user doesn't exist
        $existing = $this->FindByEmail($u['email']);
        $existing2 = $this->FindByUsername($u['username']);
        if (is_null($existing) && is_null($existing2)) {
            $count = $this->db->executeUpdate("INSERT INTO player (username, email, password) VALUES (:username, :email, :password)",
                array(
                    'username' => $u['username'],
                    'email' => $u['email'],
                    'password' => $u['password']
                )
            );

            return $count == 1;
        } else {
            // already exists
            return false;
        }
    }
}