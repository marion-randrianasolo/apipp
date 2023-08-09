<?php

namespace App\Controller;

use App\DTO\ResetForgottenPasswordRequest;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsController]
class ResetForgottenPasswordController extends AbstractController
{
    private UserPasswordHasherInterface $passwordEncoder;
    private EntityManagerInterface $em;
    private UserRepository $userRepository;

    public function __construct(UserPasswordHasherInterface $passwordEncoder, EntityManagerInterface $entityManager, UserRepository $userRepository)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->em = $entityManager;
        $this->userRepository = $userRepository;
    }

    public function __invoke(Request $request, ResetForgottenPasswordRequest $resetForgottenPasswordRequest): JsonResponse
    {
        // Extract new password and PIN from request
        $pin = $resetForgottenPasswordRequest->pin;
        $newPassword = $resetForgottenPasswordRequest->newPassword;

        // Find the user by PIN
        $user = $this->userRepository->findOneBy(['resetPasswordPin' => $pin]);

        // Check if the pin is valid
        if (!$user) {
            return $this->json([
                'error' => 'invalid_pin'
            ], 400);
        }

        // Check if the pin is expired
        if ($user->getResetPasswordPinExpiration() < new \DateTime('now')) {
            return $this->json([
                'error' => 'expired_pin'
            ], 400);
        }

        // At this point, the pin is valid. So we'll encode and set the new password
        $encodedNewPassword = $this->passwordEncoder->hashPassword($user, $newPassword);
        $user->setPassword($encodedNewPassword);

        // Clear the reset password pin and expiration
        $user->setResetPasswordPin(null);
        $user->setResetPasswordPinExpiration(null);

        $this->em->persist($user);
        $this->em->flush();

        return $this->json([
            'message' => 'Password changed successfully'
        ]);
    }
}