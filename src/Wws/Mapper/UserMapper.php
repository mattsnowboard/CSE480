<?php

namespace Wws\Mapper;

use Wws\Model\User;

class UserMapper
{
    
    public function __construct()
    {
        
    }
    
    public function FindById($id)
    {
        // temp
        $user = new User();
        $user->SetId($id);
        return $user;
    }
}