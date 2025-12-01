<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DashboardController extends AbstractController
{
    #[Route('/candidat/dashboard', name: 'candidat_dashboard')]
    #[IsGranted('ROLE_CANDIDAT')]
    public function candidat(): Response
    {
        return $this->render('dashboard/candidat.html.twig', [
            'message' => 'Bienvenue Candidat !',
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
}
