<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

#[AsController]
class SecurityController extends AbstractController
{
    // Gestionnaire de tokens JWT
    private JWTTokenManagerInterface $jwtManager;
    // Encodeur de mots de passe
    private UserPasswordHasherInterface $passwordEncoder;

    /**
     * Constructeur pour injecter les dépendances nécessaires
     *
     * @param JWTTokenManagerInterface $jwtManager
     * @param UserPasswordHasherInterface $passwordEncoder
     */
    public function __construct(JWTTokenManagerInterface $jwtManager, UserPasswordHasherInterface $passwordEncoder)
    {
        $this->jwtManager = $jwtManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * Cette méthode est invoquée pour la sécurité et l'authentification des utilisateurs
     *
     * @param Request $request
     * @param UserProviderInterface $userProvider
     * @return JsonResponse
     */
    public function __invoke(Request $request, UserProviderInterface $userProvider): JsonResponse
    {
        // Décoder le contenu JSON de la requête
        $data = json_decode($request->getContent(), true);

        $email = $data['email'];
        $password = $data['password'];

        // Charger l'utilisateur par son identifiant (email)
        $user = $userProvider->loadUserByIdentifier($email);

        // Vérifier si le mot de passe est valide pour cet utilisateur
        if (!$this->passwordEncoder->isPasswordValid($user, $password)) {
            throw new AuthenticationException('Invalid password');
        }

        // Retourner les informations de l'utilisateur ainsi que son token JWT
        return $this->json([
            'email' => $user->getUserIdentifier(),
            'token' => $this->jwtManager->create($user),
            'lastname' => $user->getLastname(),
            'firstname' => $user->getFirstname(),
            'alias' => $user->getAlias(),
            'role' => $user->getRole(),
            'service' => $user->getService(),
            'id' => $user->getId(),

        ]);
    }
}
