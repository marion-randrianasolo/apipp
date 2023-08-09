<?php

namespace App\DTO;

class ResetForgottenPasswordRequest
{
    public string $pin;
    public string $newPassword;
}