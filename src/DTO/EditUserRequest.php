<?php

namespace App\DTO;

class EditUserRequest
{
    public ?string $email = null;
    public ?string $username = null;
    public ?string $firstname = null;
    public ?string $lastname = null;
    public ?string $alias = null;
    public ?string $service = null;
    public ?string $role = null;
    public ?string $tempsTravail = null;
}
