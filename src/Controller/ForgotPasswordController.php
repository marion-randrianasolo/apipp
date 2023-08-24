<?php

namespace App\Controller;

use App\DTO\ForgotPasswordRequest;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

#[AsController]
class ForgotPasswordController extends AbstractController
{
    private UserRepository $userRepository;
    private EntityManagerInterface $em;

    /**
     * Constructeur pour injecter les dépendances nécessaires
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
     * Cette fonction est appelée lorsqu'une demande de réinitialisation de mot de passe est reçue
     *
     * @param Request $request
     * @param MailerInterface $mailer
     * @param ForgotPasswordRequest $forgotPasswordRequest
     * @return JsonResponse
     * @throws \Exception
     */
    public function __invoke(Request $request, MailerInterface $mailer, ForgotPasswordRequest $forgotPasswordRequest): JsonResponse
    {
        // Extraire l'email depuis la requête
        $email = $forgotPasswordRequest->email;

        // Trouver l'utilisateur par email
        $user = $this->userRepository->findOneBy(['email' => $email]);

        // Vérifie si l'utilisateur a été trouvé
        if (!$user) {
            return $this->json([
                'message' => 'No user with this email'
            ], 404);
        }

        // Générer un code PIN et définir sa date d'expiration
        $pin = random_int(100000, 999999);
        $user->setResetPasswordPin($pin);
        $user->setResetPasswordPinExpiration(new \DateTime('now + 10 minutes'));

        // Envoyer l'email avec le code PIN
        $email = (new Email())
            ->from('mrandrianasolo@equance.com')
            ->to($user->getEmail())
            ->subject('Réinitialisation de votre mot de passe')
            ->html("<p>Votre pin de réinitialisation de mot de passe est : <strong>{$pin}</strong></p>");

        // Essayer d'envoyer l'email et gérer d'éventuelles erreurs
        try {
            $mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            return $this->json([
                'message' => $e->getMessage()
            ]);
        }

        // Sauvegarder les modifications dans la base de données
        $this->em->persist($user);
        $this->em->flush();

        // Retourner une réponse avec le message de succès
        return $this->json([
            'message' => 'Reset password link has been sent to your email',
            'email' => $user->getEmail()
        ]);
    }
}
