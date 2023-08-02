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
    private UserPasswordHasherInterface $passwordEncoder;
    private EntityManagerInterface $entityManager;

    public function __construct(UserPasswordHasherInterface $passwordEncoder, EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->passwordEncoder = $passwordEncoder;
        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'The email of the user.')
            ->addArgument('password', InputArgument::REQUIRED, 'The plain password of the user.')
            ->addArgument('lastname', InputArgument::REQUIRED, 'The lastname of the user.')
            ->addArgument('firstname', InputArgument::REQUIRED, 'The firstname of the user.')
            ->addArgument('alias', InputArgument::REQUIRED, 'The alias of the user.')
            ->addArgument('role', InputArgument::REQUIRED, 'The role of the user.')
            ->addArgument('service', InputArgument::REQUIRED, 'The service of the user.')
            ->setDescription('Creates a new user.')
            ->setHelp('This command allows you to create a user...')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $user = new User();
        $user->setEmail($input->getArgument('email'));
        $user->setLastname($input->getArgument('lastname'));
        $user->setFirstname($input->getArgument('firstname'));
        $user->setAlias($input->getArgument('alias'));
        $user->setRole($input->getArgument('role'));
        $user->setService($input->getArgument('service'));

        $password = $this->passwordEncoder->hashPassword($user, $input->getArgument('password'));
        $user->setPassword($password);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $output->writeln('User created!');

        return Command::SUCCESS;
    }
}
