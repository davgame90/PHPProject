<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventFormType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Knp\Component\Pager\PaginatorInterface;

class EventController extends AbstractController
{
    #[Route('/create-event', name: 'event_create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $event = new Event();
        $form = $this->createForm(EventFormType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $event->setOwner($this->getUser());
            $entityManager->persist($event);
            $entityManager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('event/create.html.twig', [
            'eventForm' => $form->createView(),
        ]);
    }

    #[Route('/events', name: 'event_list')]
    public function listEvents(EventRepository $eventRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $queryBuilder = $eventRepository->createQueryBuilder('e'); // Créez une QueryBuilder ou utilisez une méthode personnalisée du repository

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1), // Numéro de page
            2 // Nombre d'éléments par page
        );

        return $this->render('event/list.html.twig', [
            'pagination' => $pagination,
        ]);
    }
    
    #[Route('/registrations', name: 'event_registrations')]
    public function registrations(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $events = $entityManager->getRepository(Event::class)->findByParticipant($user);

        return $this->render('event/registrations.html.twig', [
            'events' => $events,
        ]);
    }

    #[Route('/update-event/{id}', name: 'event_update')]
    public function update(Request $request, EntityManagerInterface $entityManager, Event $event): Response
    {
        //$this->denyAccessUnlessGranted('update', $event);

        $form = $this->createForm(EventFormType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('event_list');
        }

        return $this->render('event/update.html.twig', [
            'eventForm' => $form->createView(),
            'event' => $event,
        ]);
    }

    #[Route('/delete-event/{id}', name: 'event_delete', methods: ['POST'])]
    public function delete(Request $request, EntityManagerInterface $entityManager, Event $event): Response
    {
        //$this->denyAccessUnlessGranted('delete', $event);

        if ($this->isCsrfTokenValid('delete'.$event->getId(), $request->request->get('_token'))) {
            $entityManager->remove($event);
            $entityManager->flush();
        }

        return $this->redirectToRoute('event_list');
    }

    #[Route('/register-event/{id}', name: 'event_register', methods: ['POST'])]
    public function register(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer, Event $event): Response
    {
        $user = $this->getUser();

        if ($event->addParticipant($user)) {
            $entityManager->persist($event);
            $entityManager->flush();

            $email = (new Email())
                ->from('monnier.david15@gmail.com')
                ->to($user->getUserIdentifier())
                ->subject('Inscription à l\'événement')
                ->text(sprintf('Vous êtes inscrit à l\'événement "%s" prévu le %s.', $event->getTitle(), $event->getDate()->format('Y-m-d H:i')));

            $mailer->send($email);
        }

        return $this->redirectToRoute('event_list');
    }

    #[Route('/unregister-event/{id}', name: 'event_unregister', methods: ['POST'])]
    public function unregister(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer, Event $event): Response
    {
        $user = $this->getUser();
        if ($event->removeParticipant($user)) {
            $entityManager->persist($event);
            $entityManager->flush();

            // Send unregistration email
            $email = (new Email())
                ->from('monnier.david15@gmail.com')
                ->to($user->getUserIdentifier())
                ->subject('Désinscription de l\'événement')
                ->text(sprintf('Vous êtes désinscrit de l\'événement "%s" prévu le %s.', $event->getTitle(), $event->getDate()->format('Y-m-d H:i')));

            $mailer->send($email);
        }
        return $this->redirectToRoute('event_list');
    }
}