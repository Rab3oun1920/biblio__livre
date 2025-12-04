<?php

namespace App\Entity;

use App\Repository\LivreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: LivreRepository::class)]
class Livre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le titre est obligatoire")]
    #[Assert\Length(max: 255, maxMessage: "Le titre ne peut pas dépasser 255 caractères")]
    private ?string $titre = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(max: 255, maxMessage: "L'ISBN ne peut pas dépasser 255 caractères")]
    private ?string $isbn = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Range(
        min: 1000,
        max: 2100,
        notInRangeMessage: "L'année de publication doit être entre {{ min }} et {{ max }}"
    )]
    private ?int $anneePublication = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[Assert\Length(max: 100, maxMessage: "Le genre ne peut pas dépasser 100 caractères")]
    private ?string $genre = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Positive(message: "Le nombre de pages doit être positif")]
    private ?int $nombrePages = null;

    #[ORM\ManyToOne(inversedBy: 'livres')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "L'auteur est obligatoire")]
    private ?Auteur $auteur = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageCouverture = null;

    // 🆕 NOUVEAUX CHAMPS POUR LE COMMERCE
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotBlank(message: "Le prix est obligatoire")]
    #[Assert\PositiveOrZero(message: "Le prix doit être positif ou zéro")]
    private string $prix = '0.00';

    #[ORM\Column]
    #[Assert\NotBlank(message: "Le stock est obligatoire")]
    #[Assert\PositiveOrZero(message: "Le stock doit être positif ou zéro")]
    private int $stock = 0;

    #[ORM\Column]
    private bool $estDisponible = true;

    // 🆕 RELATION AVEC LES COMMENTAIRES
    #[ORM\OneToMany(targetEntity: Commentaire::class, mappedBy: 'livre', orphanRemoval: true)]
    private Collection $commentaires;

    public function __construct()
    {
        $this->commentaires = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;
        return $this;
    }

    public function getIsbn(): ?string
    {
        return $this->isbn;
    }

    public function setIsbn(?string $isbn): static
    {
        $this->isbn = $isbn;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getAnneePublication(): ?int
    {
        return $this->anneePublication;
    }

    public function setAnneePublication(?int $anneePublication): static
    {
        $this->anneePublication = $anneePublication;
        return $this;
    }

    public function getGenre(): ?string
    {
        return $this->genre;
    }

    public function setGenre(?string $genre): static
    {
        $this->genre = $genre;
        return $this;
    }

    public function getNombrePages(): ?int
    {
        return $this->nombrePages;
    }

    public function setNombrePages(?int $nombrePages): static
    {
        $this->nombrePages = $nombrePages;
        return $this;
    }

    public function getAuteur(): ?Auteur
    {
        return $this->auteur;
    }

    public function setAuteur(?Auteur $auteur): static
    {
        $this->auteur = $auteur;
        return $this;
    }

    public function getImageCouverture(): ?string
    {
        return $this->imageCouverture;
    }

    public function setImageCouverture(?string $imageCouverture): static
    {
        $this->imageCouverture = $imageCouverture;
        return $this;
    }

    // 🆕 GETTERS ET SETTERS POUR LE COMMERCE
    public function getPrix(): string
    {
        return $this->prix;
    }

    public function setPrix(string $prix): static
    {
        $this->prix = $prix;
        return $this;
    }

    public function getStock(): int
    {
        return $this->stock;
    }

    public function setStock(int $stock): static
    {
        $this->stock = $stock;
        // Mettre à jour automatiquement la disponibilité
        $this->estDisponible = ($stock > 0);
        return $this;
    }

    public function isEstDisponible(): bool
    {
        return $this->estDisponible;
    }

    public function setEstDisponible(bool $estDisponible): static
    {
        $this->estDisponible = $estDisponible;
        return $this;
    }

    // 🆕 GESTION DES COMMENTAIRES
    /**
     * @return Collection<int, Commentaire>
     */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    /**
     * Retourne seulement les commentaires validés par l'admin
     */
    public function getCommentairesValides(): Collection
    {
        return $this->commentaires->filter(function(Commentaire $commentaire) {
            return $commentaire->isEstValide();
        });
    }

    /**
     * @return Collection<int, Commentaire>
     */
    public function getCommentairesNonValides(): Collection
    {
        return $this->commentaires->filter(function(Commentaire $commentaire) {
            return !$commentaire->isEstValide();
        });
    }

    public function addCommentaire(Commentaire $commentaire): static
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires->add($commentaire);
            $commentaire->setLivre($this);
        }
        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): static
    {
        if ($this->commentaires->removeElement($commentaire)) {
            if ($commentaire->getLivre() === $this) {
                $commentaire->setLivre(null);
            }
        }
        return $this;
    }

    /**
     * Calcule la note moyenne des commentaires validés
     */
    public function getMoyenneNotes(): float
    {
        $totalNotes = 0;
        $nombreNotes = 0;

        foreach ($this->commentaires as $commentaire) {
            if ($commentaire->isEstValide() && $commentaire->getNote() !== null) {
                $totalNotes += $commentaire->getNote();
                $nombreNotes++;
            }
        }

        return $nombreNotes > 0 ? round($totalNotes / $nombreNotes, 1) : 0;
    }

    /**
     * Nombre total de commentaires validés
     */
    public function getNombreCommentairesValides(): int
    {
        return $this->getCommentairesValides()->count();
    }

    /**
     * Vérifie si le livre a des commentaires validés
     */
    public function hasCommentairesValides(): bool
    {
        return $this->getNombreCommentairesValides() > 0;
    }

    /**
     * Décrémente le stock d'une quantité donnée
     */
    public function decrementerStock(int $quantite): static
    {
        if ($this->stock >= $quantite) {
            $this->stock -= $quantite;
            $this->estDisponible = ($this->stock > 0);
        }
        return $this;
    }

    /**
     * Incrémente le stock d'une quantité donnée
     */
    public function incrementerStock(int $quantite): static
    {
        $this->stock += $quantite;
        $this->estDisponible = true;
        return $this;
    }

    /**
     * Vérifie si le stock est suffisant pour une commande
     */
    public function stockSuffisant(int $quantite): bool
    {
        return $this->stock >= $quantite;
    }

    /**
     * Vérifie si le livre est en stock
     */
    public function estEnStock(): bool
    {
        return $this->stock > 0;
    }

    public function __toString(): string
    {
        return $this->titre . ' (' . $this->auteur . ')';
    }
}
