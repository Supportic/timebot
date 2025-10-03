<?php

declare(strict_types=1);

namespace App\Controller\Frontend;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class IndexController extends AbstractController
{
    #[Route(path: '/', name: 'app_index')]
    public function index(#[CurrentUser] ?User $user): Response
    {
        if ($user instanceof User) {
            return $this->redirectToRoute('app_admin');
        }

        return $this->redirectToRoute('app_login');
    }
}
