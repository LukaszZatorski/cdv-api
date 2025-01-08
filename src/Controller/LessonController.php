<?php

namespace App\Controller;

use App\Entity\Lesson;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_STUDENT', message: 'You are not allowed to access lessons.')]
#[Route('/api', name: 'api_')]
class LessonController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('/lessons', name: 'lesson_index', methods: ['get'])]
    public function index(Request $request): JsonResponse
    {
        $lessons = $this->entityManager
            ->getRepository(Lesson::class)
            ->findAll();

        $data = [];

        foreach ($lessons as $lesson) {
            $data[] = [
                'id' => $lesson->getId(),
                'name' => $lesson->getName(),
                'description' => $lesson->getDescription(),
            ];
        }

        return $this->json($data);
    }

    #[Route('/lessons', name: 'lesson_create', methods: ['post'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['name'], $data['description'])) {
            return $this->json(['error' => 'Missing required fields'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $lesson = new Lesson();
        $lesson->setName($data['name']);
        $lesson->setDescription($data['description']);

        $this->entityManager->persist($lesson);
        $this->entityManager->flush();

        return $this->json(['message' => 'Lesson created successfully'], JsonResponse::HTTP_CREATED);
    }

    #[Route('/lessons/{id}', name: 'lesson_show', methods: ['get'])]
    public function show(int $id): JsonResponse
    {
        $lesson = $this->entityManager->getRepository(Lesson::class)->find($id);

        if (!$lesson) {
            return $this->json(['error' => 'Lesson not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $data = [
            'id' => $lesson->getId(),
            'name' => $lesson->getName(),
            'description' => $lesson->getDescription(),
        ];

        return $this->json($data);
    }

    #[Route('/lessons/{id}', name: 'lesson_update', methods: ['put', 'patch'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $lesson = $this->entityManager->getRepository(Lesson::class)->find($id);

        if (!$lesson) {
            return $this->json(['error' => 'Lesson not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        if (isset($data['name'])) {
            $lesson->setName($data['name']);
        }

        if (isset($data['description'])) {
            $lesson->setDescription($data['description']);
        }

        $this->entityManager->flush();

        return $this->json(['message' => 'Lesson updated successfully']);
    }

    #[Route('/lessons/{id}', name: 'lesson_delete', methods: ['delete'])]
    public function delete(int $id): JsonResponse
    {
        $lesson = $this->entityManager->getRepository(Lesson::class)->find($id);

        if (!$lesson) {
            return $this->json(['error' => 'Lesson not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($lesson);
        $this->entityManager->flush();

        return $this->json(['message' => 'Lesson deleted successfully']);
    }
}