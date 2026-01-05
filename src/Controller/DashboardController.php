<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Repository\UserRepository;
use App\Form\CandidateProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Form\BioType;
use App\Entity\User;
use App\Entity\Message;
use App\Form\MessageType;
use App\Repository\MessageRepository;




class DashboardController extends AbstractController
{
    #[Route('/candidat/dashboard', name: 'candidat_dashboard')]
    #[IsGranted('ROLE_CANDIDAT')]
    #[Route('/candidat/dashboard', name: 'candidat_dashboard')]
    #[IsGranted('ROLE_CANDIDAT')]
    public function candidat(Request $request, EntityManagerInterface $em, MessageRepository $messageRepository): Response
    {
        $user = $this->getUser();

        // Formulaire Bio
        $form = $this->createForm(BioType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'Votre bio a été mise à jour !');

            return $this->redirectToRoute('candidat_dashboard');
        }

        // Récupération des messages
        $unreadMessages = $messageRepository->findUnreadByRecipient($user); // messages non lus
        $allMessages = $messageRepository->findBy(['recipient' => $user], ['createdAt' => 'DESC']); // tous les messages

        // Rendu du template avec toutes les variables
        return $this->render('dashboard/candidat.html.twig', [
            'formBio' => $form->createView(),       // formulaire Bio
            'unreadMessages' => $unreadMessages,    // messages non lus
            'allMessages' => $allMessages,          // tous les messages
        ]);
    }


    #[Route('/entreprise/dashboard', name: 'entreprise_dashboard')]
    #[IsGranted('ROLE_ENTREPRISE')]
    public function entreprise(): Response
    {
        return $this->render('dashboard/entreprise.html.twig', [
            'message' => 'Bienvenue Entreprise !',
        ]);
    }

    #[Route('/admin/dashboard', name: 'admin_dashboard')]
    #[IsGranted('ROLE_ADMIN')]
    public function admin(): Response
    {
        return $this->render('dashboard/admin.html.twig', [
            'message' => 'Bienvenue Admin !',
        ]);
    }
    #[Route('/admin/users', name: 'admin_users')]
    #[IsGranted('ROLE_ADMIN')]
    public function listUsers(UserRepository $userRepository): Response
    {
        $users = $userRepository->findCandidatesAndCompanies();

        return $this->render('dashboard/admin_users.html.twig', [
            'users' => $users
        ]);
    }

    #[Route('/candidat/profil', name: 'complete_profile')]
    #[IsGranted('ROLE_CANDIDAT')]
    public function completeProfile(
        Request $request,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger
    ): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(CandidateProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Upload du CV
            $cvFile = $form->get('cv')->getData();
            if ($cvFile) {
                $originalFilename = pathinfo($cvFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$cvFile->guessExtension();

                try {
                    $cvFile->move(
                        $this->getParameter('cv_directory'),
                        $newFilename
                    );
                    $user->setCv($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l’upload du CV.');
                }
            }

            // Marquer le profil comme complété
            $user->setIsProfileComplete(true);

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Profil mis à jour !');
            return $this->redirectToRoute('candidat_dashboard');
        }

        return $this->render('dashboard/complete_profile.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/user/{id}/toggle', name: 'admin_toggle_user')]
    #[IsGranted('ROLE_ADMIN')]
    public function toggleUser(User $user, EntityManagerInterface $em): Response
    {
        // Utilise isActive() pour lire la valeur
        $user->setIsActive(!$user->isActive()); // inverse l'état
        $em->flush();

        $this->addFlash('success', 'Statut utilisateur mis à jour !');

        return $this->redirectToRoute('admin_users');
    }

    #[Route('/admin/user/{id}', name: 'admin_user_profile')]
    #[IsGranted('ROLE_ADMIN')]
    public function viewUserProfile(User $user): Response
    {
        return $this->render('dashboard/admin_user_profile.html.twig', [
            'user' => $user
        ]);
    }

    #[Route('/admin/user/{id}/delete', name: 'admin_delete_user', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteUser(User $user, EntityManagerInterface $em, Request $request): Response
    {
        // Vérification CSRF
        $submittedToken = $request->request->get('_token');
        if ($this->isCsrfTokenValid('delete-user'.$user->getId(), $submittedToken)) {
            $em->remove($user);
            $em->flush();

            $this->addFlash('success', 'Utilisateur supprimé avec succès !');
        } else {
            $this->addFlash('danger', 'Jeton CSRF invalide, suppression annulée.');
        }

        return $this->redirectToRoute('admin_users');
    }

    // Envoyer un message
    #[Route('/admin/message/send', name: 'admin_send_message')]
    #[IsGranted('ROLE_ADMIN')]
    public function sendMessage(Request $request, EntityManagerInterface $em): Response
    {
        $message = new Message();
        $message->setSender($this->getUser());

        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($message);
            $em->flush();
            $this->addFlash('success', 'Message envoyé !');
            return $this->redirectToRoute('admin_send_message');
        }

        return $this->render('dashboard/admin_send_message.html.twig', [
            'form' => $form->createView(),
        ]);
    }

// Inbox / Historique
    #[Route('/admin/message/inbox', name: 'admin_inbox')]
    #[IsGranted('ROLE_ADMIN')]
    public function inbox(MessageRepository $messageRepository): Response
    {
        // Récupère tous les messages envoyés par l'admin, triés par date
        $messages = $messageRepository->createQueryBuilder('m')
            ->where('m.sender = :admin')
            ->setParameter('admin', $this->getUser())
            ->orderBy('m.createdAt', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->render('dashboard/admin_inbox.html.twig', [
            'messages' => $messages,
        ]);
    }

    #[Route('/candidat/message/read/{id}', name: 'candidat_message_read', methods: ['POST'])]
    #[IsGranted('ROLE_CANDIDAT')]
    public function markMessageAsRead(Message $message, EntityManagerInterface $em, Request $request): JsonResponse
    {
        // Vérifier le CSRF token
        $submittedToken = $request->headers->get('X-CSRF-TOKEN');
        if (!$this->isCsrfTokenValid('read_message', $submittedToken)) {
            return $this->json(['error' => 'Token invalide'], 400);
        }

        // Vérifier que le message appartient bien à l'utilisateur
        if ($message->getRecipient() !== $this->getUser()) {
            return $this->json(['error' => 'Accès refusé'], 403);
        }

        // Marquer comme lu
        $message->setIsRead(true);
        $em->flush();

        return $this->json(['success' => true]);
    }





}
