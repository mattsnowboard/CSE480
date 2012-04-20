<?php

namespace Wws\Model;

/**
 * Stores information about a User
 * 
 * @author Matt Durak <durakmat@msu.edu> 
 */
class User
{
    /**
     * @var int $id
     */
    private $id;
    
    /**
     * @var string $username
     */
    private $username;
    
    /**
     * @var string $email
     */
    private $email;
    
    /**
     * @var string $password (hashed) 
     */
    private $password;
    /**
     * @var string $password (hashed) 
     */
    private $origPassword;
    
    /**
     * @var int $lastActive Used to determine if a player is probably online
     */
    private $lastActive;
    
    /**
     * @var bool $inGame Determine if player is in a game (if active)
     */
    private $inGame;
    
    /**
     * @var int $totalScore
     */
    private $totalScore;
    
    /**
     * @var Date $birthDate
     */
    private $birthDate;
    
    /**
     * @var Date $joinDate
     */
    private $joinDate;
    
    /**
     * @var string $city
     */
    private $city;
    
    /**
     * @var string $state
     */
    private $state;
    
    /**
     * @var string $state
     */
    private $country;
    
    /**
     * @var string $firstName
     */
    private $firstName;

    /**
     * @var string $lastName
     */
    private $lastName;
    
    /**
     * @var string $phone
     */
    private $phone;
    
    /**
     * @var boolean $isAdmin Flag for admin users
     */
    private $isAdmin;
    
    /**
     * Create a User with an optional array of parameters
     * 
     * @param array $u An associative array of parameters
     */
    public function __construct(array $u = null)
    {
        if (!is_null($u)) {
            $this->id = $u['id'];
            $this->username = $u['username'];
            $this->email = $u['email'];
            $this->password = $u['password'];
            $this->origPassword = $u['password'];
            $this->lastActive = $u['last_active'];
            $this->inGame = $u['in_game'];
            $this->totalScore = $u['total_score'];
            $this->birthDate = $u['birthdate'];
            $this->joinDate = $u['join_date'];
            $this->city = $u['city'];
            $this->state = $u['state'];
            $this->country = $u['country'];
            $this->firstName = $u['first_name'];
            $this->lastName = $u['last_name'];
            $this->phone = $u['phone'];
            $this->isAdmin = $u['is_admin'];
        }
    }
    
    public function GetId()
    {
        return $this->id;
    }
    public function SetId($id)
    {
        $this->id = (int)$id;
    }
    
    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    
    public function GetEmail()
    {
        return $this->email;
    }
    public function SetEmail($email)
    {
        $this->email = $email;
    }
    
    public function GetPassword()
    {
        return $this->password;
    }
    public function GetOriginalPassword()
    {
        return $this->origPassword;
    }
    public function SetPassword($password)
    {
        $this->password = $password;
    }
    

    public function GetLastActive()
    {
        return $this->lastActive;
    }
    public function SetLastActive($lastActive)
    {
        $this->lastActive = $lastActive;
    }
    
    public function getInGame()
    {
        return $this->inGame;
    }

    public function setInGame($inGame)
    {
        $this->inGame = $inGame;
    }

    
    public function getTotalScore()
    {
        return $this->totalScore;
    }
    public function setTotalScore($totalScore)
    {
        $this->totalScore = $totalScore;
    }

    public function getBirthDate()
    {
        if ($this->birthDate instanceof \DateTime) {
            return $this->birthDate;
        } else {
            return new \DateTime($this->birthDate);
        }
    }
    public function setBirthDate($birthDate)
    {
        $this->birthDate = $birthDate;
    }

    public function getJoinDate()
    {
        return $this->joinDate;
    }
    public function setJoinDate($joinDate)
    {
        $this->joinDate = $joinDate;
    }

    public function getCity()
    {
        return $this->city;
    }
    public function setCity($city)
    {
        $this->city = $city;
    }

    public function getState()
    {
        return $this->state;
    }
    public function setState($state)
    {
        $this->state = $state;
    }

    public function getCountry()
    {
        return $this->country;
    }
    public function setCountry($country)
    {
        $this->country = $country;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }
    public function setFirstName($first_name)
    {
        $this->firstName = $first_name;
    }

    public function getLastName()
    {
        return $this->lastName;
    }
    public function setLastName($last_name)
    {
        $this->lastName = $last_name;
    }


    public function getPhone()
    {
        return $this->phone;
    }

    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    public function getIsAdmin()
    {
        return $this->isAdmin;
    }
    public function setIsAdmin($isAdmin)
    {
        $this->isAdmin = $isAdmin;
    }
}