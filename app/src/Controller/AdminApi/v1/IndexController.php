<?php

declare(strict_types=1);

namespace App\Controller\AdminApi\v1;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
class IndexController extends AbstractController
{
    #[Route(path: '/admin/api/v1/', name: 'admin_api_v1_index', methods: [Request::METHOD_GET])]
    public function index(): RedirectResponse
    {
        return $this->redirectToRoute('admin_api_v1_health');
    }

    #[Route(path: '/admin/api/v1/health', name: 'admin_api_v1_health', methods: [Request::METHOD_GET])]
    public function health(#[CurrentUser] User $user): JsonResponse
    {
        return new JsonResponse([
            'status' => 'ok',
            'code' => Response::HTTP_OK,
            'user' => $user->getUserIdentifier(),
        ]);
    }
}
