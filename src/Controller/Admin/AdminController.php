<?php
// src/Controller/Admin/AdminController.php

namespace App\Controller\Admin;

use App\Repository\UserRepository;
use App\Repository\LivreRepository;
use App\Repository\AuteurRepository;
use App\Repository\CommandeRepository;
use App\Repository\ReclamationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'admin_dashboard')]
    public function dashboard(
        UserRepository $userRepository,
        LivreRepository $livreRepository,
        AuteurRepository $auteurRepository,
        CommandeRepository $commandeRepository,
        ReclamationRepository $reclamationRepository
    ): Response {
        $stats = [
            'totalUsers' => $userRepository->count([]),
            'totalLivres' => $livreRepository->count([]),
            'totalAuteurs' => $auteurRepository->count([]),
            'totalCommandes' => $commandeRepository->count([]),
            'reclamationsEnAttente' => $reclamationRepository->count(['statut' => 'en_attente']),
            'recentUsers' => $userRepository->findBy([], ['id' => 'DESC'], 5),
            'recentLivres' => $livreRepository->findBy([], ['id' => 'DESC'], 5),
            'livresFaibleStock' => $livreRepository->createQueryBuilder('l')
                ->where('l.stock < 10')
                ->andWhere('l.stock > 0')
                ->setMaxResults(5)
                ->getQuery()
                ->getResult(),
            'livresIndisponibles' => $livreRepository->findBy(['estDisponible' => false], [], 5),
        ];

        return $this->render('admin/dashboard.html.twig', [
            'stats' => $stats,
        ]);
    }
}
