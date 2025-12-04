<?php
namespace App\Controller;

use App\Repository\AuteurRepository;
use App\Repository\LivreRepository;
use App\Repository\CommandeRepository;
use App\Repository\ReclamationRepository;
use App\Service\PanierService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CatalogueController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    #[Route('/catalogue', name: 'app_catalogue')]
    public function index(
        Request $request,
        LivreRepository $livreRepository,
        AuteurRepository $auteurRepository,
        CommandeRepository $commandeRepository,
        ReclamationRepository $reclamationRepository,
        PanierService $panierService
    ): Response
    {
        // ===== RÉCUPÉRATION DES FILTRES =====
        $auteurId = $request->query->get('auteur');
        $genre = $request->query->get('genre');
        $recherche = $request->query->get('recherche');
        $tri = $request->query->get('tri', 'titre_asc');

        // ===== FILTRAGE DES LIVRES =====
        $livres = $livreRepository->findFiltres(
            auteurId: $auteurId,
            genre: $genre,
            recherche: $recherche,
            tri: $tri,
            disponible: true
        );

        // ===== DONNÉES POUR LES FILTRES =====
        // Tous les auteurs pour le filtre
        $auteurs = $auteurRepository->findAll();

        // Tous les genres uniques pour le filtre
        $tousLesLivres = $livreRepository->findAll();
        $genres = [];
        foreach ($tousLesLivres as $livre) {
            $genres[$livre->getGenre()] = ['genre' => $livre->getGenre()];
        }
        $genres = array_values($genres); // Conversion en tableau indexé

        // ===== DASHBOARD UTILISATEUR =====
        $stats = [];
        if ($this->getUser()) {
            $user = $this->getUser();

            // Panier - Utilisez les méthodes de votre PanierService
            $stats['panier_count'] = $panierService->getNombreArticles();
            $stats['panier_total'] = $panierService->getTotal();

            // Commandes
            $stats['commandes_validees'] = $commandeRepository->count([
                'user' => $user,
                'statut' => 'validee'
            ]);
            $stats['commandes_total'] = $commandeRepository->count(['user' => $user]);

            // Réclamations
            $stats['reclamations_en_attente'] = $reclamationRepository->count([
                'user' => $user,
                'statut' => 'en_attente'
            ]);

            // Dernière commande
            $stats['derniere_commande'] = $commandeRepository->findOneBy(
                ['user' => $user],
                ['dateCommande' => 'DESC']
            );
        }

        return $this->render('catalogue/index.html.twig', [
            'livres' => $livres,
            'auteurs' => $auteurs,
            'genres' => array_column($genres, 'genre'),
            'filtres' => [
                'auteur' => $auteurId,
                'genre' => $genre,
                'recherche' => $recherche,
                'tri' => $tri,
            ],
            'stats' => $stats,
        ]);
    }

    #[Route('/catalogue/{id}', name: 'app_catalogue_show', requirements: ['id' => '\d+'])]
    public function show(int $id, LivreRepository $livreRepository): Response
    {
        $livre = $livreRepository->find($id);

        if (!$livre) {
            throw $this->createNotFoundException('Livre non trouvé');
        }

        // Récupérer les commentaires validés
        $commentaires = $livre->getCommentairesValides();

        // Livres du même auteur
        $livresMemeAuteur = $livreRepository->createQueryBuilder('l')
            ->where('l.auteur = :auteur')
            ->andWhere('l.id != :currentId')
            ->andWhere('l.estDisponible = :disponible')
            ->setParameter('auteur', $livre->getAuteur())
            ->setParameter('currentId', $id)
            ->setParameter('disponible', true)
            ->setMaxResults(4)
            ->getQuery()
            ->getResult();

        return $this->render('catalogue/show.html.twig', [
            'livre' => $livre,
            'commentaires' => $commentaires,
            'livresMemeAuteur' => $livresMemeAuteur,
        ]);
    }
}
