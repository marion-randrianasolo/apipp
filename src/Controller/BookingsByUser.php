<?php

namespace App\Controller;

use App\Entity\Booking;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[AsController]
class BookingsByUser
{
    /**
     * Fonction appelée pour récupérer les réservations d'un utilisateur en fonction de son ID.
     *
     * @param EntityManagerInterface $em
     * @param int $id
     * @return array
     */
    public function __invoke(EntityManagerInterface $em, int $id): array
    {
        // Récupère les réservations associées à l'utilisateur via son ID
        $bookings = $em
            ->getRepository(Booking::class)
            ->findBy(['user' => $id]);

        // Si aucune réservation n'est trouvée, lance une exception
        if (!$bookings) {
            throw new NotFoundHttpException('No bookings found for this user');
        }

        // Retourne la liste des réservations trouvées
        return $bookings;
    }
}

