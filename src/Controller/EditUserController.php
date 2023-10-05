<?php

namespace App\Controller;

use App\DTO\EditUserRequest;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class EditUserController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private UserRepository $userRepository;

    /**
     * Constructeur pour injecter les dépendances nécessaires
     *
     * @param EntityManagerInterface $entityManager
     * @param UserRepository $userRepository
     */
    public function __construct(EntityManagerInterface $entityManager, UserRepository $userRepository)
    {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
    }

    /**
     * Cette méthode est invoquée pour la création d'un utilisateur
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request, User $user, EditUserRequest $editUserRequest): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Vérifiez si un utilisateur avec cet email ou alias existe déjà
        $existingMail = $this->userRepository->findOneByEmail($data['email']);
        $existingAlias = $this->userRepository->findOneByAlias($data['alias']);

        if ($existingMail && $existingMail->getId() !== $user->getId()) {
            return new JsonResponse(['error' => 'Already existing email'], Response::HTTP_BAD_REQUEST);
        }

        if ($existingAlias && $existingAlias->getId() !== $user->getId()) {
            return new JsonResponse(['error' => 'Already existing alias'], Response::HTTP_BAD_REQUEST);
        }

        // Update user details
        if (isset($data['email'])) {
            $user->setEmail($data['email']);
        }
        if (isset($data['lastname'])) {
            $user->setLastname($data['lastname']);
        }
        if (isset($data['firstname'])) {
            $user->setFirstname($data['firstname']);
        }
        if (isset($data['alias'])) {
            $user->setAlias($data['alias']);
        }
        if (isset($data['role'])) {
            $user->setRole($data['role']);
        }
        if (isset($data['tempsTravail'])) {
            $user->setTempsTravail($data['tempsTravail']);
        }
        if (isset($data['service'])) {
            $user->setService($data['service']);
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'User updated!'], Response::HTTP_CREATED);
    }
}
