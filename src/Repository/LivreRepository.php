<?php

namespace App\Repository;

use App\Entity\Livre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * @extends ServiceEntityRepository<Livre>
 *
 * @method Livre|null find($id, $lockMode = null, $lockVersion = null)
 * @method Livre|null findOneBy(array $criteria, array $orderBy = null)
 * @method Livre[]    findAll()
 * @method Livre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LivreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Livre::class);
    }

    /**
     * Trouve les livres avec filtres
     */
    public function findFiltres(
        ?int $auteurId = null,
        ?string $genre = null,
        ?string $recherche = null,
        ?string $tri = 'titre_asc',
        bool $disponible = true
    ): array
    {
        $qb = $this->createQueryBuilder('l')
            ->leftJoin('l.auteur', 'a')
            ->where('l.estDisponible = :disponible')
            ->setParameter('disponible', $disponible);

        // Filtre par auteur
        if ($auteurId) {
            $qb->andWhere('a.id = :auteurId')
                ->setParameter('auteurId', $auteurId);
        }

        // Filtre par genre
        if ($genre) {
            $qb->andWhere('l.genre = :genre')
                ->setParameter('genre', $genre);
        }

        // Filtre par recherche (titre, auteur ou description)
        if ($recherche) {
            $qb->andWhere('l.titre LIKE :recherche OR a.nom LIKE :recherche OR l.description LIKE :recherche')
                ->setParameter('recherche', '%' . $recherche . '%');
        }

        // Tri
        $this->applyTri($qb, $tri);

        return $qb->getQuery()->getResult();
    }

    /**
     * Applique le tri selon le paramètre
     */
    private function applyTri(QueryBuilder $qb, string $tri): void
    {
        switch ($tri) {
            case 'titre_desc':
                $qb->orderBy('l.titre', 'DESC');
                break;
            case 'prix_asc':
                $qb->orderBy('l.prix', 'ASC');
                break;
            case 'prix_desc':
                $qb->orderBy('l.prix', 'DESC');
                break;
            case 'date_desc':
                $qb->orderBy('l.datePublication', 'DESC');
                break;
            case 'date_asc':
                $qb->orderBy('l.datePublication', 'ASC');
                break;
            case 'auteur_asc':
                $qb->orderBy('a.nom', 'ASC');
                break;
            case 'auteur_desc':
                $qb->orderBy('a.nom', 'DESC');
                break;
            default: // 'titre_asc' par défaut
                $qb->orderBy('l.titre', 'ASC');
        }
    }

    /**
     * Trouve les livres d'un auteur spécifique (excluant un livre donné)
     */
    public function findLivresMemeAuteur($auteur, $excludeId = null, int $limit = 4): array
    {
        $qb = $this->createQueryBuilder('l')
            ->where('l.auteur = :auteur')
            ->andWhere('l.estDisponible = :disponible')
            ->setParameter('auteur', $auteur)
            ->setParameter('disponible', true);

        if ($excludeId) {
            $qb->andWhere('l.id != :excludeId')
                ->setParameter('excludeId', $excludeId);
        }

        return $qb->orderBy('l.datePublication', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les livres les plus récents
     */
    public function findNouveautes(int $limit = 6): array
    {
        return $this->createQueryBuilder('l')
            ->where('l.estDisponible = :disponible')
            ->setParameter('disponible', true)
            ->orderBy('l.datePublication', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les livres les plus vendus ou populaires
     */
    public function findPopulaires(int $limit = 6): array
    {
        // Note: Adaptez cette méthode selon votre modèle de données
        // Par exemple, si vous avez un champ 'ventes' ou une relation avec Commandes
        return $this->createQueryBuilder('l')
            ->where('l.estDisponible = :disponible')
            ->setParameter('disponible', true)
            ->orderBy('l.titre', 'ASC') // Temporaire, à adapter
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère tous les genres uniques
     */
    public function findAllGenres(): array
    {
        $results = $this->createQueryBuilder('l')
            ->select('DISTINCT l.genre')
            ->where('l.genre IS NOT NULL')
            ->andWhere('l.estDisponible = :disponible')
            ->setParameter('disponible', true)
            ->orderBy('l.genre', 'ASC')
            ->getQuery()
            ->getResult();

        // Formatte les résultats en tableau simple
        return array_column($results, 'genre');
    }

    /**
     * Recherche avancée avec plusieurs critères
     */
    public function searchAdvanced(array $criteria): array
    {
        $qb = $this->createQueryBuilder('l')
            ->leftJoin('l.auteur', 'a')
            ->where('l.estDisponible = :disponible')
            ->setParameter('disponible', true);

        if (!empty($criteria['titre'])) {
            $qb->andWhere('l.titre LIKE :titre')
                ->setParameter('titre', '%' . $criteria['titre'] . '%');
        }

        if (!empty($criteria['auteur'])) {
            $qb->andWhere('a.nom LIKE :auteur OR a.prenom LIKE :auteur')
                ->setParameter('auteur', '%' . $criteria['auteur'] . '%');
        }

        if (!empty($criteria['genre'])) {
            $qb->andWhere('l.genre = :genre')
                ->setParameter('genre', $criteria['genre']);
        }

        if (!empty($criteria['prix_min'])) {
            $qb->andWhere('l.prix >= :prix_min')
                ->setParameter('prix_min', $criteria['prix_min']);
        }

        if (!empty($criteria['prix_max'])) {
            $qb->andWhere('l.prix <= :prix_max')
                ->setParameter('prix_max', $criteria['prix_max']);
        }

        // Tri par défaut
        if (!empty($criteria['tri'])) {
            $this->applyTri($qb, $criteria['tri']);
        } else {
            $qb->orderBy('l.titre', 'ASC');
        }

        return $qb->getQuery()->getResult();
    }

    // Méthodes de base générées par Doctrine - vous pouvez les conserver

    public function save(Livre $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Livre $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
