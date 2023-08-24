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
    // Encodeur de mots de passe
    private UserPasswordHasherInterface $passwordEncoder;
    // Gestionnaire d'entités
    private EntityManagerInterface $entityManager;

    /**
     * Constructeur pour injecter les dépendances nécessaires
     *
     * @param UserPasswordHasherInterface $passwordEncoder
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(UserPasswordHasherInterface $passwordEncoder, EntityManagerInterface $entityManager)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->entityManager = $entityManager;
    }

    /**
     * Cette méthode est invoquée pour réinitialiser le mot de passe
     *
     * @param Request $request
     * @param UserProviderInterface $userProvider
     * @param ResetPasswordRequest $resetPasswordRequest
     * @return JsonResponse
     */
    public function __invoke(Request $request, UserProviderInterface $userProvider, ResetPasswordRequest $resetPasswordRequest): JsonResponse
    {
        // Extraire l'ancien et le nouveau mot de passe depuis la requête
        $oldPassword = $resetPasswordRequest->oldPassword;
        $newPassword = $resetPasswordRequest->newPassword;

        // Obtenir l'utilisateur actuel
        $user = $this->getUser();

        // Si l'ancien mot de passe est invalide, lancer une exception
        if (!$this->passwordEncoder->isPasswordValid($user, $oldPassword)) {
            throw new InvalidArgumentException("Invalid old password");
        }

        // À ce stade, l'ancien mot de passe est valide. On va donc encoder et définir le nouveau mot de passe
        $encodedNewPassword = $this->passwordEncoder->hashPassword($user, $newPassword);
        $user->setPassword($encodedNewPassword);

        // Sauvegarder l'utilisateur avec le nouveau mot de passe
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // Si la validation est réussie, retourner une réponse de succès
        return $this->json([
            'message' => 'Password changed successfully'
        ]);
    }
}
