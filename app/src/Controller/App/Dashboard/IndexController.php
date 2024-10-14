<?php

namespace App\Controller\App\Dashboard;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(new Expression(
    '"ROLE_ADMIN" in role_names or (is_authenticated() and user.isSuperAdmin())'
))]

#[Route(path: '/admin')]
class IndexController extends AbstractController
{
    #[Route(path: '/', name: 'app_admin')]
    public function index(): Response
    {
        return $this->redirectToRoute('app_admin_dashboard');
    }

    #[Route(path: '/dashboard', name: 'app_admin_dashboard')]
    public function dashboard(): Response
    {
        return $this->render('admin/dashboard/index.html.twig');
    }
}
