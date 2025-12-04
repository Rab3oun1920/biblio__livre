<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Form\ReclamationType;
use App\Repository\ReclamationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/reclamation')]
#[IsGranted('ROLE_USER')]
class ReclamationUserController extends AbstractController
{
    #[Route('/', name: 'app_mes_reclamations')]
    public function index(ReclamationRepository $reclamationRepository): Response
    {
        $user = $this->getUser();

        $reclamations = $reclamationRepository->createQueryBuilder('r')
            ->where('r.user = :user')
            ->setParameter('user', $user)
            ->orderBy('r.dateCreation', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->render('reclamation/index.html.twig', [
            'reclamations' => $reclamations,
        ]);
    }

    #[Route('/nouvelle', name: 'app_reclamation_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reclamation = new Reclamation();
        $reclamation->setUser($this->getUser());
        $reclamation->setDateCreation(new \DateTime());
        $reclamation->setStatut('en_attente');

        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reclamation);
            $entityManager->flush();

            $this->addFlash('success', 'Votre réclamation a été enregistrée. Nous vous répondrons dans les plus brefs délais.');
            return $this->redirectToRoute('app_mes_reclamations');
        }

        return $this->render('reclamation/new.html.twig', [
            'form' => $form->createView(), // <- Notez le createView()
        ]);
    }

    #[Route('/{id}', name: 'app_reclamation_show', requirements: ['id' => '\d+'])]
    public function show(int $id, ReclamationRepository $reclamationRepository): Response
    {
        $reclamation = $reclamationRepository->find($id);

        if (!$reclamation) {
            throw $this->createNotFoundException('Réclamation non trouvée');
        }

        // Vérifier que la réclamation appartient bien à l'utilisateur connecté
        if ($reclamation->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette réclamation.');
        }

        return $this->render('reclamation/show.html.twig', [
            'reclamation' => $reclamation,
        ]);
    }

    #[Route('/{id}/modifier', name: 'app_reclamation_edit', requirements: ['id' => '\d+'])]
    public function edit(Request $request, int $id, EntityManagerInterface $entityManager, ReclamationRepository $reclamationRepository): Response
    {
        $reclamation = $reclamationRepository->find($id);

        if (!$reclamation) {
            throw $this->createNotFoundException('Réclamation non trouvée');
        }

        // Vérifier que la réclamation appartient bien à l'utilisateur connecté
        if ($reclamation->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette réclamation.');
        }

        // Vérifier qu'on ne peut modifier que les réclamations en attente
        if ($reclamation->getStatut() !== 'en_attente') {
            $this->addFlash('warning', 'Vous ne pouvez modifier que les réclamations en attente de traitement.');
            return $this->redirectToRoute('app_mes_reclamations');
        }

        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Votre réclamation a été modifiée avec succès.');
            return $this->redirectToRoute('app_mes_reclamations');
        }

        return $this->render('reclamation/edit.html.twig', [
            'form' => $form->createView(),
            'reclamation' => $reclamation,
        ]);
    }

    #[Route('/{id}/supprimer', name: 'app_reclamation_delete', requirements: ['id' => '\d+'])]
    public function delete(Request $request, int $id, EntityManagerInterface $entityManager, ReclamationRepository $reclamationRepository): Response
    {
        $reclamation = $reclamationRepository->find($id);

        if (!$reclamation) {
            throw $this->createNotFoundException('Réclamation non trouvée');
        }

        // Vérifier que la réclamation appartient bien à l'utilisateur connecté
        if ($reclamation->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette réclamation.');
        }

        // Vérifier qu'on ne peut supprimer que les réclamations en attente
        if ($reclamation->getStatut() !== 'en_attente') {
            $this->addFlash('warning', 'Vous ne pouvez supprimer que les réclamations en attente de traitement.');
            return $this->redirectToRoute('app_mes_reclamations');
        }

        if ($request->isMethod('POST')) {
            // Vérifier le token CSRF
            if ($this->isCsrfTokenValid('delete'.$reclamation->getId(), $request->request->get('_token'))) {
                $entityManager->remove($reclamation);
                $entityManager->flush();

                $this->addFlash('success', 'Votre réclamation a été supprimée avec succès.');
            }
        }

        return $this->redirectToRoute('app_mes_reclamations');
    }
}
