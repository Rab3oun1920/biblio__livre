<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Form\CommentaireType;
use App\Repository\LivreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/commentaire')]
#[IsGranted('ROLE_USER')]
class CommentaireUserController extends AbstractController
{
    #[Route('/ajouter/{id}', name: 'app_commentaire_ajouter', requirements: ['id' => '\d+'])]
    public function ajouter(
        int $id,
        Request $request,
        LivreRepository $livreRepository,
        EntityManagerInterface $entityManager
    ): Response
    {
        $livre = $livreRepository->find($id);

        if (!$livre) {
            throw $this->createNotFoundException('Livre non trouvé');
        }

        $user = $this->getUser();

        // ⭐ VÉRIFICATION : L'utilisateur peut-il commenter ?
        if (!$user->isCanComment()) {
            $this->addFlash('error', 'Vous n\'êtes pas autorisé à commenter. Votre accès aux commentaires a été restreint par l\'administration.');
            return $this->redirectToRoute('app_catalogue_show', ['id' => $id]);
        }

        $commentaire = new Commentaire();
        $commentaire->setLivre($livre);
        $commentaire->setUser($user);

        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // ⭐ DOUBLE VÉRIFICATION (au cas où l'utilisateur serait bloqué entre-temps)
            if (!$user->isCanComment()) {
                $this->addFlash('error', 'Vous n\'êtes pas autorisé à commenter.');
                return $this->redirectToRoute('app_catalogue_show', ['id' => $id]);
            }

            $commentaire->setEstValide(false); // En attente de validation

            $entityManager->persist($commentaire);
            $entityManager->flush();

            $this->addFlash('success', 'Votre commentaire a été enregistré et sera publié après validation par un administrateur.');
            return $this->redirectToRoute('app_catalogue_show', ['id' => $id]);
        }

        return $this->render('commentaire/ajouter.html.twig', [
            'livre' => $livre,
            'form' => $form,
        ]);
    }
}
