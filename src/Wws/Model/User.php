<?php

namespace Wws\Model;

class User
{
    private $id;
    
    public function __construct()
    {
        
    }
    
    public function GetId()
    {
        return $this->id;
    }
    
    public function SetId($id)
    {
        $this->id = (int)$id;
    }
}