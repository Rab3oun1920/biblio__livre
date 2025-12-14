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
    description: 'Créer ou mettre à jour l\'utilisateur administrateur',
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
        $admin = $this->entityManager->getRepository(User::class)
            ->findOneBy(['email' => 'admin@gmail.com']);

        if ($admin) {
            // L'admin existe : mettre à jour ses rôles
            $io->warning('L\'utilisateur admin existe déjà. Mise à jour des rôles...');

            $roles = $admin->getRoles();
            if (!in_array('ROLE_ADMIN', $roles)) {
                $roles[] = 'ROLE_ADMIN';
                $admin->setRoles($roles);
                $this->entityManager->flush();

                $io->success('✅ Rôle ADMIN ajouté avec succès!');
                $io->info('Rôles actuels : ' . implode(', ', $admin->getRoles()));
            } else {
                $io->success('L\'utilisateur a déjà le rôle ADMIN');
            }

            return Command::SUCCESS;
        }

        // Créer l'utilisateur admin s'il n'existe pas
        $admin = new User();
        $admin->setEmail('admin@gmail.com');
        $admin->setNom('Admin');
        $admin->setPrenom('Super');
        $admin->setRoles(['ROLE_USER', 'ROLE_ADMIN']);

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
