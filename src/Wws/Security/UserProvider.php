<?php

namespace Wws\Security;

require_once(__DIR__.'/../../../vendor/phpass-0.3/PasswordHash.php');

/**
 * This is used to authenticate users or retrieve users by cookie 
 */
class UserProvider
{
    /**
     * @var Wws\Mapper\UserMapper
     */
    protected $mapper;
    
    /**
     * @var Symfony\Component\HttpFoundation\Session\Session 
     */
    protected $session;
    
    /**
     * @var PasswordHash
     */
    protected $hasher;
    
    public function __construct(\Wws\Mapper\UserMapper $mapper, \Symfony\Component\HttpFoundation\Session\Session $session)
    {
        $this->mapper = $mapper;
        $this->session = $session;
        // not dependency injected
        $this->hasher = new \PasswordHash(8, false);
    }
    
    /**
     * Gets the User object for a logged in user based on their cookie
     * This could be changed to use Sessions or be more secure...
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request Has Cookies and other stuff, but is more testable than the GLOBALS
     * @return Wws\Model\User|null
     */
    public function GetUser()
    {
        // check for the cookie
        if ($this->session->has('user')) {
            $user = $this->mapper->FindById((int)$this->session->get('user'));
            return $user;
        }
        return null;
    }
    
    /**
     * Authenticate a user by email and password
     * 
     * @param string $email Email of user
     * @param string $password Plaintext password of user
     * @return Wws\Model\User|false
     * @throws Exception for invalide password
     */
    public function Authenticate($email, $password)
    {
        if (strlen($password) > 72) {
            throw new Exception('Password must be 72 characters or less');
        }

        // get a user by email, MUST CHECK PASSWORD STILL
        $temp = $this->mapper->FindByEmail($email);
        if (is_null($temp)) {
            // @todo return an error code for bad email
            return false;
        }

        // now we authenticate against the given password
        $check = $this->hasher->CheckPassword($password, $temp->GetPassword());
        
        if ($check === true) {
            // store the cookie, return the new user
            $this->session->set('user', $temp->GetId());
            return $temp;
        } else {
            // make sure we don't allow using the user with wrong password
            unset($temp);
            // @todo return an error code for bad password
            return false;
        }
    }
    
    /**
     * Register a new user
     * @param string $email
     * @param string $password Plaintext
     * @return bool True if successful
     * @throws Exception If failure to hash password
     */
    public function RegisterUser($email, $password)
    {
        if (strlen($password) > 72) {
            throw new Exception('Password must be 72 characters or less');
        }

        $hash = $this->hasher->HashPassword($password);

        if (strlen($hash) >= 20) {
            // store the hashed password
            return $this->mapper->CreateUser($email, $hash);
        } else {
            // something went wrong
            throw new Exception('Failed to hash the password for storage');
        }
    }
    
    public function Logout()
    {
        $this->session->remove('user');
    }
}
