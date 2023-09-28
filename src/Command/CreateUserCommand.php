<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreateUserCommand extends Command
{
    protected static $defaultName = 'app:create-user';

    // Initialisation des propriétés pour le hashage de mot de passe et la gestion de l'entité
    private UserPasswordHasherInterface $passwordEncoder;
    private EntityManagerInterface $entityManager;

    /**
     * Constructeur
     *
     * @param UserPasswordHasherInterface $passwordEncoder
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(UserPasswordHasherInterface $passwordEncoder, EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->passwordEncoder = $passwordEncoder; // Initialisation du hasher de mot de passe
        $this->entityManager = $entityManager;  // Initialisation du gestionnaire d'entités
    }

    /**
     * Configuration de la commande
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'The email of the user.')
            ->addArgument('password', InputArgument::REQUIRED, 'The plain password of the user.')
            ->addArgument('lastname', InputArgument::REQUIRED, 'The lastname of the user.')
            ->addArgument('firstname', InputArgument::REQUIRED, 'The firstname of the user.')
            ->addArgument('alias', InputArgument::REQUIRED, 'The alias of the user.')
            ->addArgument('role', InputArgument::REQUIRED, 'The role of the user.')
            ->addArgument('tempsTravail', InputArgument::REQUIRED, 'The amount of time worked of the user.')
            ->addArgument('service', InputArgument::REQUIRED, 'The service of the user.')
            ->setDescription('Creates a new user.')
            ->setHelp('This command allows you to create a user...')
        ;
    }

    /**
     * Exécution de la commande
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Création d'une nouvelle instance utilisateur
        $user = new User();

        // Configuration des propriétés de l'utilisateur à partir des arguments
        $user->setEmail($input->getArgument('email'));
        $user->setLastname($input->getArgument('lastname'));
        $user->setFirstname($input->getArgument('firstname'));
        $user->setAlias($input->getArgument('alias'));
        $user->setRole($input->getArgument('role'));
        $user->setTempsTravail($input->getArgument('tempsTravail'));
        $user->setService($input->getArgument('service'));

        // Hashage et configuration du mot de passe
        $password = $this->passwordEncoder->hashPassword($user, $input->getArgument('password'));
        $user->setPassword($password);

        // Persistance et sauvegarde de l'utilisateur
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // Affichage d'une confirmation de création
        $output->writeln('User created!');

        return Command::SUCCESS;
    }
}
