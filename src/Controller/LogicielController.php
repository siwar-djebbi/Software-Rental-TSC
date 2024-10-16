<?php

namespace App\Controller;

use App\Entity\Logiciel;
use App\Entity\Location;
use App\Entity\Categorie;
use App\Form\LogicielType;
use App\Repository\LogicielRepository;
use App\Repository\LocationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use App\Repository\CategorieRepository;

#[Route('/logiciel')]
class LogicielController extends AbstractController
{
    #[Route('/', name: 'app_logiciel_index', methods: ['GET'])]
    public function index(LogicielRepository $logicielRepository, CategorieRepository $categorieRepository): Response
    {

        $categories = $categorieRepository->findAll();

        return $this->render('logiciel/index.html.twig', [
            'logiciels' => $logicielRepository->findAll(),
            'categories' => $categories,
        ]);
    }

   #[Route('/pageClient', name: 'app_logiciel_pageCl', methods: ['GET'])]
public function indexClient(Request $request, LogicielRepository $logicielRepository,
LocationRepository $locationRepository, CategorieRepository $categorieRepository): Response
{
    
    // Create the form
    $form = $this->createForm(LogicielType::class);
    $form->handleRequest($request);
    


    $logiciels = $logicielRepository->findAll();
    $location = $locationRepository->findAll(); // Fetch reservation data
    $categories = $categorieRepository->findAll();

    return $this->render('logiciel/indexClient.html.twig', [
        'logiciels' => $logiciels,
        'location' => $location,
        'categories' => $categories,
        ]);
}
    
#[Route('/new', name: 'app_logiciel_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
{
    $logiciel = new Logiciel();
    $form = $this->createForm(LogicielType::class, $logiciel);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $imageFile = $form->get('imagelogiciel')->getData();
        $videoFile = $form->get('videologiciel')->getData();

        if ($imageFile) {
            $newImageFilename = uniqid().'.'.$imageFile->guessExtension();
            $imageFile->move(
                $this->getParameter('image_directory'),
                $newImageFilename
            );

            $logiciel->setImagelogiciel($newImageFilename);
        }

        if ($videoFile) {
            $originalFilename = pathinfo($videoFile->getClientOriginalName(), PATHINFO_FILENAME);
            // this is needed to safely include the file name as part of the URL
            $safeFilename = $slugger->slug($originalFilename);
            $newVideoFilename = $safeFilename.'-'.uniqid().'.'.$videoFile->guessExtension();

            // Move the file to the directory where videos are stored
            try {
                $videoFile->move(
                    $this->getParameter('video_directory'),
                    $newVideoFilename
                );
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }

            // Set the 'videoevt' property of the 'Evenement' entity with the new filename
            $logiciel->setVideologiciel($newVideoFilename);
        }

        // Persist and flush the 'Evenement' entity
        $entityManager->persist($logiciel);
        $entityManager->flush();

        return $this->redirectToRoute('app_logiciel_index', [], Response::HTTP_SEE_OTHER);
    }

    return $this->renderForm('logiciel/new.html.twig', [
        'logiciel' => $logiciel,
        'form' => $form,
    ]);
}

    #[Route('/{id}', name: 'app_logiciel_show', methods: ['GET'])]
    public function show(Logiciel $logiciel): Response
    {
        return $this->render('logiciel/show.html.twig', [
            'logiciel' => $logiciel,
        ]);
    }

    #[Route('/{id}/Cl', name: 'app_logiciel_moreinfo', methods: ['GET'])]

    public function showCl(Logiciel $logiciel, LocationRepository $LocationRepository,EntityManagerInterface $em, $id): Response
{
    $location = $em->getRepository(Location::class)->find($id);
// $likesAndDislikes = $participationRepository->countLikesAndDislikes($evenement->getId_evenement());

return $this->render('logiciel/moreinfoClient.html.twig', [
    'logiciel' => $logiciel,
    'location' => $location,
]);
}

    #[Route('/{id}/edit', name: 'app_logiciel_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Logiciel $logiciel, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LogicielType::class, $logiciel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_logiciel_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('logiciel/edit.html.twig', [
            'logiciel' => $logiciel,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_logiciel_delete', methods: ['POST'])]
    public function delete(Request $request, Logiciel $logiciel, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$logiciel->getId(), $request->request->get('_token'))) {
            $entityManager->remove($logiciel);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_logiciel_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/live-search', name: 'live_search')]
public function liveSearch(Request $request): Response
{
    $q = $request->query->get('q');
    $response = '...';

    return new Response($response);
}

public function filterLogicielsByCategory(Request $request, LogicielRepository $logicielRepository): Response
{
    $categoryId = $request->query->get('category');

    if (!$categoryId) {
        $logiciels = $logicielRepository->findAll();
    } else {
        $categorie = $this->getDoctrine()->getRepository(Categorie::class)->find($categoryId);
        $logiciels = $categorie ? $categorie->getLogiciels() : [];
    }

    return $this->render('logiciel/filtered_logiciels.html.twig', [
        'logiciels' => $logiciels,
    ]);
}
}
