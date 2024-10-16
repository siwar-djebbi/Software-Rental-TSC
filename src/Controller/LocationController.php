<?php

namespace App\Controller;

use App\Entity\Location;
use App\Entity\Logiciel;
use App\Form\LocationType;
use App\Repository\LocationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/location')]
class LocationController extends AbstractController
{
    #[Route('/', name: 'app_location_index', methods: ['GET'])]
    public function index(LocationRepository $locationRepository): Response
    {
        return $this->render('location/index.html.twig', [
            'locations' => $locationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_location_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $location = new Location();
        $form = $this->createForm(LocationType::class, $location);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($location);
            $entityManager->flush();

            return $this->redirectToRoute('app_location_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('location/new.html.twig', [
            'location' => $location,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_location_show', methods: ['GET'])]
    public function show(Location $location): Response
    {
        return $this->render('location/show.html.twig', [
            'location' => $location,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_location_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Location $location, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LocationType::class, $location);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_location_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('location/edit.html.twig', [
            'location' => $location,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_location_delete', methods: ['POST'])]
    public function delete(Request $request, Location $location, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$location->getId(), $request->request->get('_token'))) {
            $entityManager->remove($location);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_location_index', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/add/{id}', name: 'app_location_add')]
    public function add(Request $request, EntityManagerInterface $entityManager, $id): Response
    {
        // Récupérer l'événement basé sur $idevt
        $logicielRepository = $this->getDoctrine()->getRepository(Logiciel::class);
        $logiciel = $logicielRepository->find($id);
    
        // Vérifier si l'événement existe
        if (!$logiciel) {
            throw $this->createNotFoundException('L\'événement n\'existe pas');
        }
    
        // Créer une nouvelle réservation
        $location1 = new Location();
    
        // Créer le formulaire de réservation
        $form = $this->createForm(LocationType::class, $location1);
        $form->handleRequest($request);
    
        // Vérifier si le formulaire a été soumis et est valide
        if ($form->isSubmitted() && $form->isValid()) {
            $confirme = $form->get('confirme')->getData();
    
            // Si oui est coché, ajouter la réservation à la base de données
            if ($confirme) {
                $location1->setIdevt($logiciel); // Associer la réservation à l'événement
                $entityManager->persist($location1);
                $entityManager->flush();
    
                $this->addFlash('success', 'Réservation ajoutée avec succès.');
            } else {
                $this->addFlash('warning', 'La réservation n\'a pas été confirmée.');
            }
    
            // Rediriger vers la page de l'événement ou une autre page appropriée
            return $this->redirectToRoute('app_logiciel_moreinfoClient', ['id' => $id]);
        }
    
        // Afficher le formulaire de réservation
        return $this->render('location/louerfront.html.twig', [
            'location1' => $location1,
            'form' => $form->createView(),
            'logiciel' => $logiciel,
            'button_label' => 'Save',
        ]);
    }
}
