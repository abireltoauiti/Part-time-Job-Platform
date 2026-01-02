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


class DashboardController extends AbstractController
{
    #[Route('/candidat/dashboard', name: 'candidat_dashboard')]
    #[IsGranted('ROLE_CANDIDAT')]
    public function candidat(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        // Création du formulaire de bio
        $form = $this->createForm(BioType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'Votre bio a été mise à jour !');

            return $this->redirectToRoute('candidat_dashboard');
        }

        return $this->render('dashboard/candidat.html.twig', [
            'message' => 'Bienvenue Candidat !',
            'formBio' => $form->createView(), // On passe le formulaire au template
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





}
