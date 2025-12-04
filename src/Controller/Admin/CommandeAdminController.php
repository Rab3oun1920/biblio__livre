<?php
// src/Controller/Admin/CommandeAdminController.php

namespace App\Controller\Admin;

use App\Repository\CommandeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/commande')]
#[IsGranted('ROLE_ADMIN')]
class CommandeAdminController extends AbstractController
{
    #[Route('/', name: 'admin_commande_index')]
    public function index(CommandeRepository $commandeRepository): Response
    {
        $commandes = $commandeRepository->findBy([], ['dateCommande' => 'DESC']);

        return $this->render('admin/commande/index.html.twig', [
            'commandes' => $commandes,
        ]);
    }
}
