<?php
// src/Controller/CommandeController.php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\LigneCommande;
use App\Repository\LivreRepository;
use App\Service\PanierService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/commande')]
#[IsGranted('ROLE_USER')]
class CommandeController extends AbstractController
{
    #[Route('/valider', name: 'app_commande_valider')]
    public function valider(
        Request $request,
        PanierService $panierService,
        LivreRepository $livreRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $panier = $panierService->getPanier();

        if (empty($panier)) {
            $this->addFlash('warning', 'Votre panier est vide.');
            return $this->redirectToRoute('app_panier_index');
        }

        // Vérifier le stock des livres
        foreach ($panier as $item) {
            $livre = $item['livre'];
            if (!$livre->stockSuffisant($item['quantite'])) {
                $this->addFlash('danger', sprintf(
                    'Stock insuffisant pour "%s". Il ne reste que %d exemplaire(s).',
                    $livre->getTitre(),
                    $livre->getStock()
                ));
                return $this->redirectToRoute('app_panier_index');
            }
        }

        if ($request->isMethod('POST')) {
            // Créer la commande
            $commande = new Commande();
            $commande->setUser($this->getUser());
            $commande->setDateCommande(new \DateTime());
            $commande->setStatut('en_attente');

            $montantTotal = 0;

            // Créer les lignes de commande
            foreach ($panier as $item) {
                $livre = $item['livre'];
                $quantite = $item['quantite'];

                $ligneCommande = new LigneCommande();
                $ligneCommande->setCommande($commande);
                $ligneCommande->setLivre($livre);
                $ligneCommande->setQuantite($quantite);
                $ligneCommande->setPrixUnitaire($livre->getPrix());

                $montantTotal += $livre->getPrix() * $quantite;

                // Décrémenter le stock
                $livre->decrementerStock($quantite);

                $entityManager->persist($ligneCommande);
            }

            $commande->setMontantTotal($montantTotal);
            $entityManager->persist($commande);
            $entityManager->flush();

            // Vider le panier
            $panierService->vider();

            $this->addFlash('success', sprintf(
                'Votre commande #%d a été validée avec succès ! Montant total : %s TND',
                $commande->getId(),
                number_format($montantTotal, 2, ',', ' ')
            ));

            return $this->redirectToRoute('app_mes_commandes');
        }

        return $this->render('commande/valider.html.twig', [
            'panier' => $panier,
            'total' => $panierService->getTotal(),
        ]);
    }
}
