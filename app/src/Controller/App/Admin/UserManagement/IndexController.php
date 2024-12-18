<?php

namespace App\Controller\App\Admin\UserManagement;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

// #[IsGranted(new Expression(
//     '"ROLE_ADMIN" in role_names or (is_authenticated() and user.isSuperAdmin())'
// ))]
#[IsGranted('ROLE_ADMIN')]
#[Route(path: '/user-management')]
class IndexController extends AbstractController
{
    #[Route(path: '/', name: 'app_admin_user_management')]
    public function index(): Response
    {
        return $this->render('admin/user-management/index.html.twig');
    }
}
