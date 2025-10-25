<?php

namespace App\Controller\Admin\View\UserManagement;

use App\Entity\Enum\RolesEnum;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

// #[IsGranted(new Expression(
//     '"ROLE_ADMIN" in role_names or (is_authenticated() and user.isSuperAdmin())'
// ))]
#[IsGranted(RolesEnum::ROLE_ADMIN->value)]
#[Route(path: '/admin/users')]
class IndexController extends AbstractController
{
    #[Route(path: '/', name: 'admin_users')]
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this->render('admin/pages/users/index.html.twig', ['users' => $users]);
    }
}
