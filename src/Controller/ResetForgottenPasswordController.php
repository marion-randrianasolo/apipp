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

    /**
     * Constructeur pour injecter les dépendances nécessaires
     *
     * @param UserPasswordHasherInterface $passwordEncoder
     * @param EntityManagerInterface $entityManager
     * @param UserRepository $userRepository
     */
    public function __construct(UserPasswordHasherInterface $passwordEncoder, EntityManagerInterface $entityManager, UserRepository $userRepository)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->em = $entityManager;
        $this->userRepository = $userRepository;
    }

    /**
     * Cette méthode est invoquée pour réinitialiser le mot de passe
     *
     * @param Request $request
     * @param ResetForgottenPasswordRequest $resetForgottenPasswordRequest
     * @return JsonResponse
     */
    public function __invoke(Request $request, ResetForgottenPasswordRequest $resetForgottenPasswordRequest): JsonResponse
    {
        // Extraire le nouveau mot de passe et le PIN depuis la requête
        $pin = $resetForgottenPasswordRequest->pin;
        $newPassword = $resetForgottenPasswordRequest->newPassword;

        // Trouver l'utilisateur par PIN
        $user = $this->userRepository->findOneBy(['resetPasswordPin' => $pin]);

        // Vérifier la validité du PIN
        if (!$user) {
            return $this->json([
                'error' => 'invalid_pin'
            ], 400);
        }

        // Vérifier si le PIN a expiré
        if ($user->getResetPasswordPinExpiration() < new \DateTime('now')) {
            return $this->json([
                'error' => 'expired_pin'
            ], 400);
        }

        // Si le PIN est valide, on encode et défini le nouveau mot de passe
        $encodedNewPassword = $this->passwordEncoder->hashPassword($user, $newPassword);
        $user->setPassword($encodedNewPassword);

        // Effacer le PIN et son expiration
        $user->setResetPasswordPin(null);
        $user->setResetPasswordPinExpiration(null);

        // Sauvegarder les modifications dans la base de données
        $this->em->persist($user);
        $this->em->flush();

        // Retourner une réponse indiquant le succès de la réinitialisation
        return $this->json([
            'message' => 'Password changed successfully'
        ]);
    }
}