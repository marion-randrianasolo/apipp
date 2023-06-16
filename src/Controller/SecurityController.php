<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Request;

#[AsController]
class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="login", methods={"POST"})
     */
    public function login(Request $request, UserPasswordHasherInterface $encoder, UserProviderInterface $userProvider): \Symfony\Component\HttpFoundation\JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $email = $data['email'];
        $password = $data['password'];

        $user = $userProvider->loadUserByUsername($email);

        if (!$user || !$encoder->isPasswordValid($user, $password)) {
            throw new AuthenticationException('Invalid username or password');
        }

        return $this->json([
            'email' => $user->getUsername(),
            // The lexik/jwt-authentication-bundle will automatically use this token
            // in the Authorization header
            'token' => $this->get('lexik_jwt_authentication.encoder')->encode([
                'username' => $user->getUsername(),
                'exp' => time() + 3600 // 1 hour expiration
            ])
        ]);
    }
}
