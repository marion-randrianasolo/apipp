<?php

namespace App\Controller;

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

    public function __invoke(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $token = $data['token'];
        $newPassword = $data['newPassword'];

        $user = $this->userRepository->findOneBy(['resetPasswordToken' => $token]);

        if (!$user || $user->getResetPasswordTokenExpiration() < new \DateTime('now')) {
            return $this->json([
                'message' => 'Invalid or expired token'
            ], 400);
        }

        // At this point, the token is valid. So we'll encode and set the new password
        $encodedNewPassword = $this->passwordEncoder->hashPassword($user, $newPassword);
        $user->setPassword($encodedNewPassword);

        // Clear the reset password token and expiration
        $user->setResetPasswordToken(null);
        $user->setResetPasswordTokenExpiration(null);

        $this->em->persist($user);
        $this->em->flush();

        return $this->json([
            'message' => 'Password changed successfully'
        ]);
    }
}