<?php

namespace App\DTO;

class AddUserRequest
{
    public string $email;
    public string $username;
    public string $firstname;
    public string $lastname;
    public string $alias;
    public string $password;
    public string $service;
    public string $role;
    public string $tempsTravail;
}