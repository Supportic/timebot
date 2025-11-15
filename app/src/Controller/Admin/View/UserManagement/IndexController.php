<?php

namespace App\Controller\Admin\View\UserManagement;

use App\Controller\AdminApi\v1\Users\UserController;
use App\Enum\Role;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

// #[IsGranted(new Expression(
//     '"ROLE_ADMIN" in role_names or (is_authenticated() and user.isSuperAdmin())'
// ))]
#[IsGranted(Role::ROLE_ADMIN->value)]
class IndexController extends AbstractController
{
    #[Route(path: '/admin/users/', name: 'admin_users')]
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this->render('admin/pages/users/index.html.twig', ['users' => $users]);
    }
}
