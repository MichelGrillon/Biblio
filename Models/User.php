<?php

namespace App\Models;

class User
{
    private $email;
    private $password;
    private $username;
    private $id;

    public function __construct($email, $password, $username, $id = null)
    {
        $this->email = $email;
        $this->password = $password;
        $this->username = $username;
        $this->id = $id;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getId()
    {
        return $this->id;
    }
}
