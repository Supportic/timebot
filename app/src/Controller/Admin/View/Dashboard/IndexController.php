<?php

declare(strict_types=1);

namespace App\Controller\Admin\View\Dashboard;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

// #[IsGranted(new Expression(
//     '"ROLE_ADMIN" in role_names or (is_authenticated() and user.isSuperAdmin())'
// ))]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
#[Route(path: '/admin/dashboard')]
class IndexController extends AbstractController
{
    #[Route(path: '/', name: 'admin_dashboard')]
    public function dashboard(): Response
    {
        return $this->render('admin/dashboard/index.html.twig');
    }
}
