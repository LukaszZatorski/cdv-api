<?php
  
namespace App\Controller;
  
use App\Entity\User;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN', message: 'You are not allowed to register users.')]
#[Route('/api', name: 'api_')]
class RegistrationController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserService $userService,
    ) {
    }

    #[Route('/register', name: 'register', methods: 'post')]
    public function index(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $email = $data['email'];
        $name = $data['name'];
        $surname = $data['surname'];
        $roles = $data['roles'];

        if (!isset($email, $name, $surname, $roles)) {
            return $this->json(['error' => 'Missing required fields'], JsonResponse::HTTP_BAD_REQUEST);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->json(['error' => 'Invalid email format'], JsonResponse::HTTP_BAD_REQUEST);
        }

        if (empty($name) || empty($surname)) {
            return $this->json(['error' => 'Name and surname cannot be empty'], JsonResponse::HTTP_BAD_REQUEST);
        }

        if (!is_array($roles) || empty($roles)) {
            return $this->json(['error' => 'Roles must be a non-empty array'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($existingUser) {
            return $this->json(['error' => 'User with this email already exists'], JsonResponse::HTTP_CONFLICT);
        }

        $this->userService->createUser($email, $name, $surname, $roles);

        return $this->json(['message' => 'Registered Successfully'], JsonResponse::HTTP_CREATED);
    }
}