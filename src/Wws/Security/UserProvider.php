<?php

namespace Wws\Security;

require_once(__DIR__.'/../../../vendor/phpass-0.3/PasswordHash.php');

use Wws\Model\User;

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
     * Update the last active time
     * @param \Wws\Model\User $user 
     */
    public function UpdateActivity(\Wws\Model\User $user)
    {
        $id = $user->GetId();
        $this->mapper->UpdateActivity($id);
    }
    
    /**
     * Update the status to in game or not
     * @param \Wws\Model\User $user
     * @param boolean $in 
     */
    public function UpdateInGameStatus(\Wws\Model\User $user, $in)
    {
        $id = $user->GetId();
        $this->mapper->UpdateInGameStatus($id, $in);
    }
    
    /**
     * Authenticate a user by username and password
     * 
     * @param string $username Username of user
     * @param string $password Plaintext password of user
     * @return Wws\Model\User|false
     * @throws Exception for invalide password
     */
    public function Authenticate($username, $password)
    {
        if (strlen($password) > 72) {
            throw new \Exception('Password must be 72 characters or less');
        }

        // get a user by email, MUST CHECK PASSWORD STILL
        $temp = $this->mapper->FindByUsername($username);
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
     * @param array $u Associative array with all user info (password is plaintext)
     * @return bool True if successful
     * @throws Exception If failure to hash password
     */
    public function RegisterUser(array $u)
    {
        if (strlen($u['password']) > 72) {
            throw new Exception('Password must be 72 characters or less');
        }

        $hash = $this->hasher->HashPassword($u['password']);

        if (strlen($hash) >= 20) {
            // store the hashed password
            $u['password'] = $hash;
            return $this->mapper->CreateUser($u);
        } else {
            // something went wrong
            throw new Exception('Failed to hash the password for storage');
        }
    }
    
    /**
     * Update Profile
     * @param User $user
     * 
     * @return bool True if successful
     * @throws Exception If failure to hash password
     */
    public function UpdateUserProfile(User $user)
    {
        $pw = $user->GetPassword();
        if (!empty($pw)) {
            if (strlen($pw) > 72) {
                throw new Exception('Password must be 72 characters or less');
            }

            $hash = $this->hasher->HashPassword($user->GetPassword());

            if (strlen($hash) >= 20) {
                // store the hashed password
                $user->SetPassword($hash);
            } else {
                // something went wrong
                throw new Exception('Failed to hash the password for storage');
            }
        } else {
            $user->SetPassword($user->GetOriginalPassword());
        }
        
        return $this->mapper->UpdateProfile($user);
    }
    
    public function Logout()
    {
        $this->session->remove('user');
    }
}
