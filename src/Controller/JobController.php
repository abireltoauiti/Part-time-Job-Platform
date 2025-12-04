<?php

namespace App\Controller;

use App\Entity\Job;
use App\Form\JobType;
use App\Repository\JobRepository;
use App\Entity\CategorieJob;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/job')]
class JobController extends AbstractController
{
    #[Route('/', name: 'job_index', methods: ['GET'])]
    public function index(JobRepository $jobRepository): Response
    {
        return $this->render('job/index.html.twig', [
            'jobs' => $jobRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'job_new', methods: ['GET','POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $job = new Job();

        // Pour éviter les erreurs de champ non null, tu peux définir des valeurs par défaut
        $job->setDateLimite(new \DateTimeImmutable('+7 days'));

        $form = $this->createForm(JobType::class, $job);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            // Vérifier le formulaire et afficher les erreurs si invalides
            if ($form->isValid()) {

                // 🔹 Optionnel : si tu veux assigner un utilisateur connecté
                // $job->setUser($this->getUser());

                // 🔹 Optionnel : assigner une catégorie par défaut
                // $categorie = $em->getRepository(CategorieJob::class)->find(1);
                // if ($categorie) { $job->setCategorieJob($categorie); }

                $em->persist($job);
                $em->flush();

                $this->addFlash('success', 'Job créé avec succès !');
                return $this->redirectToRoute('job_index');

            } else {
                // Affiche toutes les erreurs du formulaire dans le debug toolbar
                dump($form->getErrors(true, false));
            }
        }

        return $this->render('job/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/{id}', name: 'job_show', methods: ['GET'])]
    public function show(Job $job): Response
    {
        return $this->render('job/show.html.twig', [
            'job' => $job,
        ]);
    }

    #[Route('/{id}/edit', name: 'job_edit', methods: ['GET','POST'])]
    public function edit(Request $request, Job $job, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(JobType::class, $job);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Job modifié avec succès !');
            return $this->redirectToRoute('job_index');
        }

        return $this->render('job/edit.html.twig', [
            'job' => $job,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'job_delete', methods: ['POST'])]
    public function delete(Request $request, Job $job, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$job->getId(), $request->request->get('_token'))) {
            $em->remove($job);
            $em->flush();
            $this->addFlash('success', 'Job supprimé avec succès !');
        }

        return $this->redirectToRoute('job_index');
    }
}
