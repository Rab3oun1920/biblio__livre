<?php
// src/Controller/PanierController.php

namespace App\Controller;

use App\Service\PanierService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/panier')]
#[IsGranted('ROLE_USER')]
class PanierController extends AbstractController
{
    #[Route('/', name: 'app_panier_index')]
    public function index(PanierService $panierService): Response
    {
        $panier = $panierService->getPanier();
        $total = $panierService->getTotal();

        return $this->render('panier/index.html.twig', [
            'panier' => $panier,
            'total' => $total,
        ]);
    }

    #[Route('/ajouter/{id}', name: 'app_panier_ajouter')]
    public function ajouter(int $id, PanierService $panierService): Response
    {
        $panierService->ajouter($id);

        $this->addFlash('success', 'Livre ajouté au panier avec succès.');
        return $this->redirectToRoute('app_panier_index');
    }

    #[Route('/retirer/{id}', name: 'app_panier_retirer')]
    public function retirer(int $id, PanierService $panierService): Response
    {
        $panierService->retirer($id);

        $this->addFlash('success', 'Livre retiré du panier avec succès.');
        return $this->redirectToRoute('app_panier_index');
    }

    #[Route('/modifier/{id}/{quantite}', name: 'app_panier_modifier')]
    public function modifier(int $id, int $quantite, PanierService $panierService): Response
    {
        $panierService->modifierQuantite($id, $quantite);

        $this->addFlash('success', 'Quantité modifiée avec succès.');
        return $this->redirectToRoute('app_panier_index');
    }

    #[Route('/vider', name: 'app_panier_vider')]
    public function vider(PanierService $panierService): Response
    {
        $panierService->vider();

        $this->addFlash('success', 'Panier vidé avec succès.');
        return $this->redirectToRoute('app_panier_index');
    }
}
