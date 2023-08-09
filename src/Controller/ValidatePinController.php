<?php

namespace App\Controller;

use App\DTO\ValidatePinRequest;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class ValidatePinController extends AbstractController
{
    private UserRepository $userRepository;
    private EntityManagerInterface $em;

    public function __construct(UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        $this->userRepository = $userRepository;
        $this->em = $entityManager;
    }

    public function __invoke(Request $request, ValidatePinRequest $validatePinRequest): JsonResponse
    {
        // Extract email and PIN from request
        $email = $validatePinRequest->email;
        $pin = $validatePinRequest->pin;

        // Find the user by email
        $user = $this->userRepository->findOneBy(['email' => $email]);

        // Check if the user is found
        if (!$user) {
            return $this->json([
                'error' => 'No user with this email'
            ], 400);
        }

        // Retrieve the stored PIN and expiration from the user
        $storedPin = $user->getResetPasswordPin();
        $expiration = $user->getResetPasswordPinExpiration();

        // Validate pin
        if ($storedPin != $pin) {
            return $this->json([
                'error' => 'invalid_pin'
            ], 400);
        }

        // Check pin expiration
        $currentDateTime = new \DateTime('now');
        if ($expiration <= $currentDateTime) {
            return $this->json([
                'error' => 'expired_pin'
            ], 400);
        }

        // If validation is successful, return success response
        return $this->json([
            'success' => 'PIN is valid',
            'pin' => $pin
        ]);
    }
}
