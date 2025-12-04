<?php

namespace App\Service;

use App\Entity\Livre;
use App\Repository\LivreRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class PanierService
{
    private $session;
    private $livreRepository;

    public function __construct(RequestStack $requestStack, LivreRepository $livreRepository)
    {
        $this->session = $requestStack->getSession();
        $this->livreRepository = $livreRepository;
    }

    /**
     * Ajouter un livre au panier
     */
    public function ajouter(int $id, int $quantite = 1): void
    {
        $panier = $this->session->get('panier', []);

        if (!empty($panier[$id])) {
            $panier[$id] += $quantite;
        } else {
            $panier[$id] = $quantite;
        }

        $this->session->set('panier', $panier);
    }

    /**
     * Retirer un livre du panier
     */
    public function retirer(int $id): void
    {
        $panier = $this->session->get('panier', []);

        if (!empty($panier[$id])) {
            unset($panier[$id]);
        }

        $this->session->set('panier', $panier);
    }

    /**
     * Modifier la quantité d'un livre
     */
    public function modifierQuantite(int $id, int $quantite): void
    {
        $panier = $this->session->get('panier', []);

        if ($quantite <= 0) {
            $this->retirer($id);
        } else {
            $panier[$id] = $quantite;
            $this->session->set('panier', $panier);
        }
    }

    /**
     * Vider le panier
     */
    public function vider(): void
    {
        $this->session->remove('panier');
    }

    /**
     * Obtenir le panier complet avec détails des livres
     */
    public function getPanier(): array
    {
        $panier = $this->session->get('panier', []);
        $panierAvecDetails = [];

        foreach ($panier as $id => $quantite) {
            $livre = $this->livreRepository->find($id);
            if ($livre) {
                $panierAvecDetails[] = [
                    'livre' => $livre,
                    'quantite' => $quantite,
                    'sousTotal' => $livre->getPrix() * $quantite,
                ];
            }
        }

        return $panierAvecDetails;
    }

    /**
     * Calculer le total du panier
     */
    public function getTotal(): float
    {
        $total = 0;
        $panierAvecDetails = $this->getPanier();

        foreach ($panierAvecDetails as $item) {
            $total += $item['sousTotal'];
        }

        return $total;
    }

    /**
     * Obtenir le nombre d'articles dans le panier
     */
    public function getNombreArticles(): int
    {
        $panier = $this->session->get('panier', []);
        return array_sum($panier);
    }

    /**
     * Vérifier si le panier est vide
     */
    public function estVide(): bool
    {
        $panier = $this->session->get('panier', []);
        return empty($panier);
    }
}
