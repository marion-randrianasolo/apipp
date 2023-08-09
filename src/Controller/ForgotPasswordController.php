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

    public function __construct(UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        $this->userRepository = $userRepository;
        $this->em = $entityManager;
    }

    public function __invoke(Request $request, MailerInterface $mailer, ForgotPasswordRequest $forgotPasswordRequest): JsonResponse
    {
        // Extract email from request
        $email = $forgotPasswordRequest->email;

        // Find the user by email
        $user = $this->userRepository->findOneBy(['email' => $email]);

        // Check if the user is found
        if (!$user) {
            return $this->json([
                'message' => 'No user with this email'
            ], 404);
        }

        // Generate pin and set expiration date
        $pin = random_int(100000, 999999);
        $user->setResetPasswordPin($pin);
        $user->setResetPasswordPinExpiration(new \DateTime('now + 10 minutes'));

        // Send email
        $email = (new Email())
            ->from('mrandrianasolo@equance.com')
            ->to($user->getEmail())
            ->subject('Password reset request')
            ->html("<p>Votre pin de réinitialisation de mot de passe est : <strong>{$pin}</strong></p>");

        try {
            $mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            return $this->json([
                'message' => $e->getMessage()
            ]);
        }

        $this->em->persist($user);
        $this->em->flush();

        return $this->json([
            'message' => 'Reset password link has been sent to your email - pin : ' . $pin,
            'email' => $user->getEmail()
        ]);
    }
}
