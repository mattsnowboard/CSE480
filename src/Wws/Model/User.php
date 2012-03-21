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
     * @var int $lastActive Used to determine if a player is probably online
     */
    private $lastActive;
    
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
     * @var string $fullName
     */
    private $fullName;
    
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
            $this->lastActive = $u['last_active'];
            $this->totalScore = $u['total_score'];
            $this->birthDate = $u['birthdate'];
            $this->joinDate = $u['join_date'];
            $this->city = $u['city'];
            $this->state = $u['state'];
            $this->country = $u['country'];
            $this->fullName = $u['full_name'];
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
        return $this->birthDate;
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

    public function getFullName()
    {
        return $this->fullName;
    }
    public function setFullName($fullName)
    {
        $this->fullName = $fullName;
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