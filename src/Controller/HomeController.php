<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\EventRepository;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(EventRepository $eventRepository): Response
    {
        $publicEvents = $eventRepository->findByIsPublic(true);

        return $this->render('home/index.html.twig', [
            'publicEvents' => $publicEvents,
        ]);
    }
}