<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[AsController]

class UserByService
{
    /**
     * Cette méthode permet d'obtenir un utilisateur en fonction de son service.
     *
     * @param EntityManagerInterface $em
     * @param string $service
     * @return array
     */
    public function __invoke(EntityManagerInterface $em, string $service): array
    {
        // Récupère les utilisateurs ayant le service donné
        $user = $em
            ->getRepository(User::class)
            ->findBy(['service' => $service]);

        // Si aucun utilisateur n'est trouvé pour ce service, une exception est levée
        if (!$user) {
            throw new NotFoundHttpException('No user found for this service');
        }

        return $user;
    }
}