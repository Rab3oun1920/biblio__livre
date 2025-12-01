<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-admin',
    description: 'Créer l\'utilisateur administrateur par défaut',
)]
class CreateAdminCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Vérifier si l'admin existe déjà
        $existingAdmin = $this->entityManager->getRepository(User::class)
            ->findOneBy(['email' => 'admin@gmail.com']);

        if ($existingAdmin) {
            $io->warning('L\'utilisateur admin existe déjà!');
            return Command::FAILURE;
        }

        // Créer l'utilisateur admin
        $admin = new User();
        $admin->setEmail('admin@gmail.com');
        $admin->setNom('Admin');
        $admin->setPrenom('Super');
        $admin->setRoles(['ROLE_ADMIN']);

        $hashedPassword = $this->passwordHasher->hashPassword($admin, 'admin123');
        $admin->setPassword($hashedPassword);

        $this->entityManager->persist($admin);
        $this->entityManager->flush();

        $io->success('Utilisateur administrateur créé avec succès!');
        $io->table(
            ['Email', 'Mot de passe'],
            [['admin@gmail.com', 'admin123']]
        );

        return Command::SUCCESS;
    }
}
