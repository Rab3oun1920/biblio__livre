<?php
namespace App\Controller\Admin;

use App\Entity\Commentaire;
use App\Repository\CommentaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/commentaire')]
#[IsGranted('ROLE_ADMIN')]
class CommentaireAdminController extends AbstractController
{
    #[Route('/', name: 'admin_commentaire_index', methods: ['GET'])]
    public function index(CommentaireRepository $commentaireRepository): Response
    {
        // Commentaires en attente de validation
        $commentairesEnAttente = $commentaireRepository->findBy(
            ['estValide' => false],
            ['dateCreation' => 'DESC']
        );

        // Commentaires validés
        $commentairesValides = $commentaireRepository->findBy(
            ['estValide' => true],
            ['dateCreation' => 'DESC'],
            10
        );

        return $this->render('admin/commentaire/index.html.twig', [
            'commentairesEnAttente' => $commentairesEnAttente,
            'commentairesValides' => $commentairesValides,
        ]);
    }

    #[Route('/{id}/valider', name: 'admin_commentaire_valider', methods: ['POST'])]
    public function valider(Request $request, Commentaire $commentaire, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('valider'.$commentaire->getId(), $request->request->get('_token'))) {
            $commentaire->setEstValide(true);
            $entityManager->flush();

            $this->addFlash('success', 'Commentaire validé avec succès.');
        }

        return $this->redirectToRoute('admin_commentaire_index');
    }

    #[Route('/{id}', name: 'admin_commentaire_delete', methods: ['POST'])]
    public function delete(Request $request, Commentaire $commentaire, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$commentaire->getId(), $request->request->get('_token'))) {
            $entityManager->remove($commentaire);
            $entityManager->flush();

            $this->addFlash('success', 'Commentaire supprimé avec succès.');
        }

        return $this->redirectToRoute('admin_commentaire_index');
    }
}
