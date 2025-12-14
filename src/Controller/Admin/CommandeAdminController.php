<?php

namespace App\Controller\Admin;

use App\Entity\Commande;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/commandes')]
#[IsGranted('ROLE_ADMIN')]
class CommandeAdminController extends AbstractController
{
    #[Route('/', name: 'admin_commande_index')]
    public function index(CommandeRepository $commandeRepository): Response
    {
        // Récupérer les commandes par statut
        $commandesPending = $commandeRepository->findBy(['statut' => 'en_attente'], ['dateCommande' => 'DESC']);
        $commandesValidated = $commandeRepository->findBy(['statut' => 'validee'], ['validatedAt' => 'DESC']);
        $commandesRejected = $commandeRepository->findBy(['statut' => 'rejetee'], ['rejectedAt' => 'DESC']);

        return $this->render('admin/commande/index.html.twig', [
            'commandesPending' => $commandesPending,
            'commandesValidated' => $commandesValidated,
            'commandesRejected' => $commandesRejected,
        ]);
    }

    #[Route('/{id}/valider', name: 'admin_commande_valider', methods: ['POST'])]
    public function valider(
        Commande $commande,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid('valider'.$commande->getId(), $request->request->get('_token'))) {
            // Changer le statut en validée
            $commande->setStatut('validee');
            $commande->setValidatedAt(new \DateTime());
            $commande->setValidatedBy($this->getUser());

            // Récupérer la note admin si fournie
            $adminNote = $request->request->get('note');
            if ($adminNote) {
                $commande->setAdminNote($adminNote);
            }

            $entityManager->flush();

            $this->addFlash('success', sprintf(
                'Commande #%d validée avec succès !',
                $commande->getId()
            ));
        }

        return $this->redirectToRoute('admin_commande_index');
    }

    #[Route('/{id}/rejeter', name: 'admin_commande_rejeter', methods: ['POST'])]
    public function rejeter(
        Commande $commande,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid('rejeter'.$commande->getId(), $request->request->get('_token'))) {
            // Changer le statut en rejetée
            $commande->setStatut('rejetee');
            $commande->setRejectedAt(new \DateTime());

            // Récupérer la raison du rejet
            $adminNote = $request->request->get('note');
            if ($adminNote) {
                $commande->setAdminNote($adminNote);
            } else {
                $commande->setAdminNote('Commande rejetée sans raison spécifiée');
            }

            $entityManager->flush();

            $this->addFlash('warning', sprintf(
                'Commande #%d rejetée.',
                $commande->getId()
            ));
        }

        return $this->redirectToRoute('admin_commande_index');
    }

    #[Route('/{id}', name: 'admin_commande_show')]
    public function show(Commande $commande): Response
    {
        return $this->render('admin/commande/show.html.twig', [
            'commande' => $commande,
        ]);
    }
}
