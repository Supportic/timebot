<?php

declare(strict_types=1);

namespace App\Controller\Admin\View;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

// #[IsGranted(new Expression(
//     '"ROLE_ADMIN" in role_names or (is_authenticated() and user.isSuperAdmin())'
// ))]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
#[Route(path: '/admin')]
class IndexController extends AbstractController
{
    #[Route(path: '/', name: 'app_admin')]
    public function index(): Response
    {
        return $this->redirectToRoute('admin_dashboard');
    }
}
