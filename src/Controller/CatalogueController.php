<?php

namespace App\Controller;

use App\Repository\AuteurRepository;
use App\Repository\CommandeRepository;
use App\Repository\LivreRepository;
use App\Repository\ReclamationRepository;
use App\Service\PanierService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CatalogueController extends AbstractController
{
    #[Route('/catalogue', name: 'app_catalogue')]
    public function index(
        Request $request,
        LivreRepository $livreRepository,
        AuteurRepository $auteurRepository,
        PanierService $panierService,
        CommandeRepository $commandeRepository,
        ReclamationRepository $reclamationRepository
    ): Response {
        // Récupération des paramètres de filtre
        $auteurIdParam = $request->query->get('auteur');
        $genre = $request->query->get('genre');
        $recherche = $request->query->get('q'); // 'q' comme dans le formulaire
        $tri = $request->query->get('tri', 'recent');

        // Conversion de auteurId en int (null si vide ou invalide)
        $auteurId = $auteurIdParam ? (int) $auteurIdParam : null;

        // Récupération des livres filtrés
        $livres = $livreRepository->findFiltres($auteurId, $genre, $recherche);

        // Tri des livres
        $livresArray = $livres instanceof \Doctrine\Common\Collections\Collection ? $livres->toArray() : (array) $livres;

        switch ($tri) {
            case 'prix_asc':
                usort($livresArray, fn($a, $b) => $a->getPrix() <=> $b->getPrix());
                break;
            case 'prix_desc':
                usort($livresArray, fn($a, $b) => $b->getPrix() <=> $a->getPrix());
                break;
            case 'titre':
                usort($livresArray, fn($a, $b) => strcasecmp($a->getTitre(), $b->getTitre()));
                break;
            case 'recent':
            default:
                usort($livresArray, fn($a, $b) => $b->getId() <=> $a->getId());
                break;
        }

        // Récupération de tous les auteurs pour le filtre
        $auteurs = $auteurRepository->findAll();

        // Récupération des genres uniques
        $genres = $livreRepository->findAllGenres();

        // Créer le tableau filtres pour le template
        $filtres = [
            'recherche' => $recherche ?? '',
            'auteur' => $auteurId,
            'genre' => $genre ?? '',
            'tri' => $tri,
        ];

        // Statistiques pour le dashboard utilisateur
        $stats = null;
        if ($this->getUser()) {
            $user = $this->getUser();
            $panier = $panierService->getPanier();
            $panierCount = array_sum(array_column($panier, 'quantite'));
            $panierTotal = $panierService->getTotal();

            $commandes = $commandeRepository->findBy(['user' => $user]);
            $commandesValidees = count(array_filter($commandes, fn($c) => $c->getStatut() === 'validee'));

            $reclamations = $reclamationRepository->findBy(['user' => $user]);
            $reclamationsEnAttente = count(array_filter($reclamations, fn($r) => $r->getStatut() === 'en_attente'));

            $derniereCommande = $commandeRepository->findOneBy(['user' => $user], ['dateCommande' => 'DESC']);

            $stats = [
                'panier_count' => $panierCount,
                'panier_total' => $panierTotal,
                'commandes_total' => count($commandes),
                'commandes_validees' => $commandesValidees,
                'reclamations_en_attente' => $reclamationsEnAttente,
                'derniere_commande' => $derniereCommande,
            ];
        }

        return $this->render('catalogue/index.html.twig', [
            'livres' => $livresArray,
            'auteurs' => $auteurs,
            'genres' => $genres,
            'filtres' => $filtres,
            'stats' => $stats,
        ]);
    }

    #[Route('/catalogue/{id}', name: 'app_catalogue_show', requirements: ['id' => '\d+'])]
    public function show(int $id, LivreRepository $livreRepository): Response
    {
        $livre = $livreRepository->find($id);

        if (!$livre) {
            throw $this->createNotFoundException('Le livre n\'existe pas');
        }

        return $this->render('catalogue/show.html.twig', [
            'livre' => $livre,
        ]);
    }
}
