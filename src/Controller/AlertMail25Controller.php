<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\Request;


#[AsController]
class AlertMail25Controller extends AbstractController
{

    /**
     * Cette fonction est appelée pour envoyer une alerte par e-mail à partir du 25 du mois
     *
     * @param MailerInterface $mailer
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function __invoke(MailerInterface $mailer, Request $request): JsonResponse
    {
        // Obtenir l'utilisateur actuel
        $user = $this->getUser();

        // Vérifie si l'utilisateur a été trouvé
        if (!$user) {
            return $this->json([
                'message' => 'Pas d\'utilisateur avec ce mail'
            ], 404);
        }

        // Récupérez la date du booking depuis la requête
        $data = json_decode($request->getContent(), true);

        // Récupérer les dates du booking depuis la requête
        $bookingDates = $data['bookingDates'] ?? [];
        $action = $data['action'] ?? 'effectué';

        $formattedDates = [];
        foreach ($bookingDates as $date) {
            $dateObject = new \DateTime($date);
            $formattedDates[] = $dateObject->format('d/m/Y');
        }

        $formattedDateList = implode(', ', $formattedDates);
        if (count($formattedDates) > 1) {
            $formattedDateList = '<ul><li>' . implode('</li><li>', $formattedDates) . '</li></ul>';
            $emailContent = "<p>L'utilisateur {$user->getEmail()} a {$action} sa présence pour les dates ci-dessous, enregistrées après le 25 du mois :</p> {$formattedDateList}";
        } else {
            $emailContent = "<p>L'utilisateur {$user->getEmail()} a {$action} sa présence pour le <strong>{$formattedDateList}</strong>, enregistrée après le 25 du mois.</p>";
        }

        $email = (new Email())
            ->from('mrandrianasolo@equance.com')
            ->to('virgosgroove@yopmail.com') // envoyer à la RH
            ->subject('Alerte Planning Présence')
            ->html($emailContent);

        try {
            $mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            return $this->json([
                'message' => $e->getMessage()
            ], 500);
        }

        // Retourner une réponse avec le message de succès
        return $this->json([
            'message' => 'L\'e-mail d\'alerte a été envoyé avec succès.'
        ]);
    }
}
