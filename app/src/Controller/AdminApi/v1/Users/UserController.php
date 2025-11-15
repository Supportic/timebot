<?php

declare(strict_types=1);

namespace App\Controller\AdminApi\v1\Users;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
class UserController extends AbstractController
{
    public function __construct(
        protected readonly UserRepository $userRepository,
    ) {}

    #[Route(path: '/admin/api/v1/users/', name: 'admin_api_v1_users', methods: [Request::METHOD_GET])]
    public function index(): JsonResponse
    {

        $users = $this->userRepository->findAll();

        return new JsonResponse([
            'status' => 'ok',
            'code' => Response::HTTP_OK,
            'users' => $users ?? [],
        ]);
    }
}
