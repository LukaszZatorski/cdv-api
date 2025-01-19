<?php

namespace App\Controller;

use App\Entity\Lesson;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

#[IsGranted('ROLE_STUDENT', message: 'You are not allowed to access lessons.')]
#[Route('/api', name: 'api_')]
class LessonController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private SerializerInterface $serializer,
    ) {
    }

    #[Route('/lessons', name: 'lesson_index', methods: ['get'])]
    public function index(): JsonResponse
    {
        $lessons = $this->entityManager
            ->getRepository(Lesson::class)
            ->findAll();

        return $this->json($lessons, JsonResponse::HTTP_OK, [], ['groups' => 'lesson:read']);
    }

    #[Route('/lessons', name: 'lesson_create', methods: ['post'])]
    public function create(Request $request): JsonResponse
    {
        $lesson = $this->serializer->deserialize($request->getContent(), Lesson::class, 'json');

        if (!$lesson->getName() || !$lesson->getDescription()) {
            return $this->json(['error' => 'Missing required fields'], JsonResponse::HTTP_BAD_REQUEST);
        }

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

        return $this->json($lesson, JsonResponse::HTTP_OK, [], ['groups' => 'lesson:read']);
    }

    #[Route('/lessons/{id}', name: 'lesson_update', methods: ['put', 'patch'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $lesson = $this->entityManager->getRepository(Lesson::class)->find($id);

        if (!$lesson) {
            return $this->json(['error' => 'Lesson not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $updatedLesson = $this->serializer->deserialize($request->getContent(), Lesson::class, 'json');

        if ($updatedLesson->getName()) {
            $lesson->setName($updatedLesson->getName());
        }

        if ($updatedLesson->getDescription()) {
            $lesson->setDescription($updatedLesson->getDescription());
        }

        $this->entityManager->flush();

        return $this->json(['message' => 'Lesson updated successfully'], JsonResponse::HTTP_OK);
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

        return $this->json(['message' => 'Lesson deleted successfully'], JsonResponse::HTTP_OK);
    }
}