<?php

namespace App\Controller\Admin;

use App\Repository\UserRepository;
use App\Repository\LivreRepository;
use App\Repository\AuteurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route; // Corrected Route import
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'admin_dashboard')]
    public function dashboard(
        UserRepository $userRepository,
        LivreRepository $livreRepository,
        AuteurRepository $auteurRepository
    ): Response {
        $stats = [
            'totalUsers' => $userRepository->count([]),
            'totalLivres' => $livreRepository->count([]),
            'totalAuteurs' => $auteurRepository->count([]),
            'recentUsers' => $userRepository->findBy([], ['id' => 'DESC'], 5),
            'recentLivres' => $livreRepository->findBy([], ['id' => 'DESC'], 5),
        ];

        return $this->render('admin/dashboard.html.twig', [
            'stats' => $stats,
        ]);
    }
}
