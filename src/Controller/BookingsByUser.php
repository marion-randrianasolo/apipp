<?php

namespace App\Controller;

use App\Entity\Booking;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[AsController]
class BookingsByUser
{
    public function __invoke(EntityManagerInterface $em, int $id): array
    {
        $bookings = $em
            ->getRepository(Booking::class)
            ->findBy(['user' => $id]);

        if (!$bookings) {
            throw new NotFoundHttpException('No bookings found for this user');
        }

        return $bookings;
    }
}

