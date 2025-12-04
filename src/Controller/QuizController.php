<?php

namespace App\Controller;

use App\Entity\Quiz;
use App\Repository\QuizRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/quiz')]
class QuizController extends AbstractController
{
    /**
     * List quizzes. If none are persisted, provide a demo quiz so the feature can be tested without DB or login.
     */
    #[Route('/', name: 'quiz_index', methods: ['GET'])]
    public function index(QuizRepository $quizRepository): Response
    {
        $quizzes = $quizRepository->findAll();

        if (empty($quizzes)) {
            // Demo quiz metadata
            $quizzes = [
                [
                    'id' => 'demo',
                    'title' => 'Quiz de démonstration',
                ],
            ];
            $demo = true;
        } else {
            $demo = false;
        }

        return $this->render('quiz/index.html.twig', [
            'quizzes' => $quizzes,
            'demo' => $demo,
        ]);
    }

    /**
     * Show a quiz. Supports a demo quiz at `/quiz/demo` that requires no DB or login.
     */
    #[Route('/demo', name: 'quiz_demo', methods: ['GET', 'POST'])]
    public function demo(Request $request): Response
    {
        $quiz = $this->getDemoQuiz();

        if ($request->isMethod('POST')) {
            $answers = $request->request->all('answers');
            $score = $this->grade($quiz['questions'], $answers);

            return $this->render('quiz/show.html.twig', [
                'quiz' => $quiz,
                'score' => $score,
                'submitted' => true,
            ]);
        }

        return $this->render('quiz/show.html.twig', [
            'quiz' => $quiz,
            'submitted' => false,
        ]);
    }

    /**
     * Show a persisted quiz by id. If the DB is not available, an informative message will be shown.
     */
    #[Route('/{id}', name: 'quiz_show', requirements: ['id' => '\\d+'], methods: ['GET', 'POST'])]
    public function show(string $id, Request $request, QuizRepository $quizRepository, EntityManagerInterface $em): Response
    {
        $quiz = $quizRepository->find($id);

        if (!$quiz) {
            // Fallback: show helpful message and provide link to demo
            return $this->render('quiz/no_db.html.twig', []);
        }

        $data = [
            'id' => $quiz->getId(),
            'title' => $quiz->getTitle(),
            'questions' => $quiz->getQuestions(),
        ];

        if ($request->isMethod('POST')) {
            $answers = $request->request->all('answers');
            $score = $this->grade($data['questions'], $answers);

            return $this->render('quiz/show.html.twig', [
                'quiz' => $data,
                'score' => $score,
                'submitted' => true,
            ]);
        }

        return $this->render('quiz/show.html.twig', [
            'quiz' => $data,
            'submitted' => false,
        ]);
    }

    private function getDemoQuiz(): array
    {
        return [
            'id' => 'demo',
            'title' => 'Quiz de démonstration',
            'questions' => [
                [
                    'question' => 'Quelle est la couleur du ciel par temps clair ?',
                    'options' => ['Rouge', 'Bleu', 'Vert', 'Jaune'],
                    'answer' => 1,
                ],
                [
                    'question' => 'Combien font 2 + 2 ?',
                    'options' => ['3', '4', '5', '22'],
                    'answer' => 1,
                ],
            ],
        ];
    }

    private function grade(array $questions, array $answers): int
    {
        $score = 0;
        foreach ($questions as $i => $q) {
            $given = $answers[$i] ?? null;
            if ($given !== null && (int)$given === (int)$q['answer']) {
                $score++;
            }
        }
        return $score;
    }
}
