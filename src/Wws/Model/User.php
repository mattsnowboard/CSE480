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
     * @var string $email
     */
    private $email;
    
    /**
     * @var string $password (hashed) 
     */
    private $password;
    
    /**
     * Create a User with an optional array of parameters
     * 
     * @param array $u An associative array of parameters
     */
    public function __construct(array $u = null)
    {
        if (!is_null($u)) {
            $this->id = $u['id'];
            $this->email = $u['email'];
            $this->password = $u['password'];
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
}