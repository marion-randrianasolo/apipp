<?php

namespace App\DTO;

class ResetPasswordRequest
{
    public string $oldPassword;
    public string $newPassword;
}