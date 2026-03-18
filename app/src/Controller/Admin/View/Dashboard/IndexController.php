<?php

declare(strict_types=1);

namespace App\Controller\Admin\View\Dashboard;

use App\Component\Routing\Attributes\Seo;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

// #[IsGranted(new Expression(
//     '"ROLE_ADMIN" in role_names or (is_authenticated() and user.isSuperAdmin())'
// ))]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class IndexController extends AbstractController
{
    #[Route(path: '/admin/dashboard/', name: 'admin_dashboard')]
    #[Seo('seo.title.page.dashboard', canonicalUrl: false)]
    public function dashboard(): Response
    {
        return $this->render('admin/pages/dashboard/index.html.twig');
    }
}
