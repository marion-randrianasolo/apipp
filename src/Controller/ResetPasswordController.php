<?php

namespace App\Controller;

use ApiPlatform\Exception\InvalidArgumentException;
use App\DTO\ResetPasswordRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;

#[AsController]
class ResetPasswordController extends AbstractController
{
    private UserPasswordHasherInterface $passwordEncoder;
    private EntityManagerInterface $entityManager;

    public function __construct(UserPasswordHasherInterface $passwordEncoder, EntityManagerInterface $entityManager)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->entityManager = $entityManager;
    }

    public function __invoke(Request $request, UserProviderInterface $userProvider, ResetPasswordRequest $resetPasswordRequest): JsonResponse
    {
        // Extract old and new password from request
        $oldPassword = $resetPasswordRequest->oldPassword;
        $newPassword = $resetPasswordRequest->newPassword;

        $user = $this->getUser();

        // If the password is invalid, throw exception
        if (!$this->passwordEncoder->isPasswordValid($user, $oldPassword)) {
            throw new InvalidArgumentException("Invalid old password");
        }

        // At this point, the old password is valid. So we'll encode and set the new password
        $encodedNewPassword = $this->passwordEncoder->hashPassword($user, $newPassword);
        $user->setPassword($encodedNewPassword);

        // Save the user with the new password
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // If validation is successful, return success response
        return $this->json([
            'message' => 'Password changed successfully'
        ]);
    }
}
