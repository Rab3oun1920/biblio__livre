<?php
// src/Controller/Admin/ReclamationAdminController.php

namespace App\Controller\Admin;

use App\Entity\Reclamation;
use App\Repository\ReclamationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/reclamation')]
#[IsGranted('ROLE_ADMIN')]
class ReclamationAdminController extends AbstractController
{
    #[Route('/', name: 'admin_reclamation_index', methods: ['GET'])]
    public function index(ReclamationRepository $reclamationRepository): Response
    {
        // Récupérer toutes les réclamations triées par date
        $reclamations = $reclamationRepository->findBy([], ['dateCreation' => 'DESC']);

        return $this->render('admin/reclamation/index.html.twig', [
            'reclamations' => $reclamations,
        ]);
    }

    #[Route('/{id}', name: 'admin_reclamation_show', methods: ['GET'])]
    public function show(Reclamation $reclamation): Response
    {
        return $this->render('admin/reclamation/show.html.twig', [
            'reclamation' => $reclamation,
        ]);
    }

    #[Route('/{id}/repondre', name: 'admin_reclamation_repondre', methods: ['GET', 'POST'])]
    public function repondre(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
            $reponse = $request->request->get('reponse');

            if (!empty($reponse)) {
                $reclamation->setReponseAdmin($reponse);
                $reclamation->setDateReponse(new \DateTime());
                $reclamation->setStatut('resolue');

                $entityManager->flush();

                $this->addFlash('success', 'Réponse envoyée avec succès.');
                return $this->redirectToRoute('admin_reclamation_show', ['id' => $reclamation->getId()]);
            }
        }

        return $this->render('admin/reclamation/repondre.html.twig', [
            'reclamation' => $reclamation,
        ]);
    }

    #[Route('/{id}/changer-statut', name: 'admin_reclamation_changer_statut', methods: ['POST'])]
    public function changerStatut(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        $statut = $request->request->get('statut');

        if (in_array($statut, ['en_attente', 'en_cours', 'resolue', 'fermee'])) {
            $reclamation->setStatut($statut);
            $entityManager->flush();

            $this->addFlash('success', sprintf('Statut changé à "%s".', $statut));
        }

        return $this->redirectToRoute('admin_reclamation_show', ['id' => $reclamation->getId()]);
    }
}
