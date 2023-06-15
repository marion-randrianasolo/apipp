<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[AsController]

class UserByService
{
    public function __invoke(EntityManagerInterface $em, string $service): array
    {
        $user = $em
            ->getRepository(User::class)
            ->findBy(['service' => $service]);

        if (!$user) {
            throw new NotFoundHttpException('No user found for this service');
        }

        return $user;
    }
}