<?php

namespace App\Controller\App\Admin\UserManagement;

use App\Entity\Enum\RolesEnum;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

// #[IsGranted(new Expression(
//     '"ROLE_ADMIN" in role_names or (is_authenticated() and user.isSuperAdmin())'
// ))]
#[IsGranted(RolesEnum::ROLE_ADMIN->value)]
#[Route(path: '/user-management')]
class IndexController extends AbstractController
{
    #[Route(path: '/', name: 'app_admin_user_management')]
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this->render('admin/user-management/index.html.twig', ['users' => $users]);
    }

    #[Route(path: '/view', name: 'app_admin_user_management_view')]
    public function view(): Response
    {
        return $this->redirectToRoute('app_admin_user_management');
    }
}
