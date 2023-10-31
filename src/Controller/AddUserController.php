<?php

namespace App\Controller;

use App\DTO\AddUserRequest;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsController]
class AddUserController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordEncoder;
    private UserRepository $userRepository;

    /**
     * Constructeur pour injecter les dépendances nécessaires
     *
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordHasherInterface $passwordEncoder
     * @param UserRepository $userRepository
     */
    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordEncoder, UserRepository $userRepository)
    {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->userRepository = $userRepository;
    }

    /**
     * Cette méthode est invoquée pour la création d'un utilisateur
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request, AddUserRequest $addUserRequest): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Vérifiez si un utilisateur avec cet email ou alias existe déjà
        $existingMail = $this->userRepository->findOneByEmail($data['email']);
        $existingAlias = $this->userRepository->findOneByAlias($data['alias']);
        $existingUsername = $this->userRepository->findOneByUsername($data['username']);

        if ($existingMail) {
            return new JsonResponse(['error' => 'Already existing email'], Response::HTTP_BAD_REQUEST);
        }

        if ($existingAlias) {
            return new JsonResponse(['error' => 'Already existing alias'], Response::HTTP_BAD_REQUEST);
        }

        if ($existingUsername) {
            return new JsonResponse(['error' => 'Already existing username'], Response::HTTP_BAD_REQUEST);
        }

        $user = new User();
        $user->setEmail($data['email']);
        $user->setLastname($data['lastname']);
        $user->setFirstname($data['firstname']);
        $user->setAlias($data['alias']);
        $user->setUsername($data['username']);
        $user->setRole($data['role']);
        $user->setTempsTravail($data['tempsTravail']);
        $user->setService($data['service']);
        $password = $this->passwordEncoder->hashPassword($user, $data['password']);
        $user->setPassword($password);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new JsonResponse([
            'status' => 'User created!',
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'lastname' => $user->getLastname(),
            'firstname' => $user->getFirstname(),
            'alias' => $user->getAlias(),
            'role' => $user->getRole(),
            'tempsTravail' => $user->getTempsTravail(),
            'service' => $user->getService(),
        ], Response::HTTP_CREATED);
    }
}
