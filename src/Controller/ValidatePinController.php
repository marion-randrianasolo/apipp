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
    // Référence au dépôt utilisateur
    private UserRepository $userRepository;
    // Gestionnaire d'entités
    private EntityManagerInterface $em;

    /**
     * Constructeur
     *
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        $this->userRepository = $userRepository;
        $this->em = $entityManager;
    }

    /**
     *  Cette méthode est invoquée lors de l'appel du contrôleur
     *
     * @param Request $request
     * @param ValidatePinRequest $validatePinRequest
     * @return JsonResponse
     */
    public function __invoke(Request $request, ValidatePinRequest $validatePinRequest): JsonResponse
    {
        // Extraction de l'email et du PIN depuis la requête
        $email = $validatePinRequest->email;
        $pin = $validatePinRequest->pin;

        // Recherche de l'utilisateur via son email
        $user = $this->userRepository->findOneBy(['email' => $email]);

        // Vérification de l'existence de l'utilisateur
        if (!$user) {
            return $this->json([
                'error' => 'No user with this email'
            ], 400);
        }

        // Récupération du PIN stocké et de sa date d'expiration pour l'utilisateur
        $storedPin = $user->getResetPasswordPin();
        $expiration = $user->getResetPasswordPinExpiration();

        // Validation du PIN
        if ($storedPin != $pin) {
            return $this->json([
                'error' => 'invalid_pin'
            ], 400);
        }

        // Vérification de la date d'expiration du PIN
        $currentDateTime = new \DateTime('now');
        if ($expiration <= $currentDateTime) {
            return $this->json([
                'error' => 'expired_pin'
            ], 400);
        }

        // Si la validation est réussie, on renvoie une réponse positive
        return $this->json([
            'success' => 'PIN is valid',
            'pin' => $pin
        ]);
    }
}
