<?php

namespace App\Controller;

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

    public function __invoke(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $email = $data['email'];
        $token = $data['token']; // The PIN
        $user = $this->userRepository->findOneBy(['email' => $email]);

        if (!$user) {
            return $this->json([
                'error' => 'No user with this email'
            ], 404);
        }

        $storedToken = $user->getResetPasswordToken();
        $expiration = $user->getResetPasswordTokenExpiration();

        // Validate token
        if ($storedToken != $token) {
            return $this->json([
                'error' => 'invalid_pin'
            ], 400);
        }

        // Check token expiration
        $currentDateTime = new \DateTime('now');
        if ($expiration <= $currentDateTime) {
            return $this->json([
                'error' => 'expired_pin'
            ], 400);
        }

        return $this->json([
            'success' => 'PIN is valid',
            'pin' => $token
        ]);
    }
}
