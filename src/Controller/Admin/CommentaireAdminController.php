<?php

namespace App\Controller\Admin;

use App\Entity\Commentaire;
use App\Entity\User;
use App\Repository\CommentaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/commentaires')]
#[IsGranted('ROLE_ADMIN')]
class CommentaireAdminController extends AbstractController
{
    #[Route('/', name: 'admin_commentaire_index')]
    public function index(CommentaireRepository $commentaireRepository): Response
    {
        // Récupérer tous les commentaires (validés + en attente)
        $commentaires = $commentaireRepository->findBy([], ['dateCreation' => 'DESC']);

        return $this->render('admin/commentaire_admin/index.html.twig', [
            'commentaires' => $commentaires,
        ]);
    }

    #[Route('/{id}/desactiver', name: 'admin_commentaire_desactiver', methods: ['POST'])]
    public function desactiver(
        Commentaire $commentaire,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid('desactiver'.$commentaire->getId(), $request->request->get('_token'))) {
            $commentaire->setEstValide(false);
            $entityManager->flush();

            $this->addFlash('success', 'Commentaire invalidé avec succès. Il ne sera plus visible publiquement.');
        }

        return $this->redirectToRoute('admin_commentaire_index');
    }

    #[Route('/{id}/reactiver', name: 'admin_commentaire_reactiver', methods: ['POST'])]
    public function reactiver(
        Commentaire $commentaire,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid('reactiver'.$commentaire->getId(), $request->request->get('_token'))) {
            $commentaire->setEstValide(true);
            $entityManager->flush();

            $this->addFlash('success', 'Commentaire validé avec succès. Il est maintenant visible publiquement.');
        }

        return $this->redirectToRoute('admin_commentaire_index');
    }

    #[Route('/{id}/supprimer', name: 'admin_commentaire_supprimer', methods: ['POST'])]
    public function supprimer(
        Commentaire $commentaire,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid('supprimer'.$commentaire->getId(), $request->request->get('_token'))) {
            $entityManager->remove($commentaire);
            $entityManager->flush();

            $this->addFlash('success', 'Commentaire supprimé définitivement.');
        }

        return $this->redirectToRoute('admin_commentaire_index');
    }

    #[Route('/user/{id}/bloquer', name: 'admin_user_bloquer_commentaires', methods: ['POST'])]
    public function bloquerUser(
        User $user,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid('bloquer'.$user->getId(), $request->request->get('_token'))) {
            $user->setCanComment(false);
            $entityManager->flush();

            $this->addFlash('warning', sprintf(
                'Utilisateur %s bloqué : ne peut plus commenter.',
                $user->getPrenom() . ' ' . $user->getNom()
            ));
        }

        return $this->redirectToRoute('admin_commentaire_index');
    }

    #[Route('/user/{id}/debloquer', name: 'admin_user_debloquer_commentaires', methods: ['POST'])]
    public function debloquerUser(
        User $user,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid('debloquer'.$user->getId(), $request->request->get('_token'))) {
            $user->setCanComment(true);
            $entityManager->flush();

            $this->addFlash('success', sprintf(
                'Utilisateur %s débloqué : peut à nouveau commenter.',
                $user->getPrenom() . ' ' . $user->getNom()
            ));
        }

        return $this->redirectToRoute('admin_commentaire_index');
    }
}
