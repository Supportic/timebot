<?php

declare(strict_types=1);

namespace App\Controller\Admin\Internal;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_FULLY')] // using isGranted makes the user not nullable
#[Route(path: '/admin')]
class SessionController extends AbstractController
{
    const WHITELIST = ['sidebar_expanded'];

    #[Route(path: '/session/set', name: 'admin_session_set', methods: [Request::METHOD_POST])]
    public function set(RequestStack $requestStack): Response
    {
        $payload = $requestStack->getCurrentRequest()->getPayload()->all();
        $session = $requestStack->getSession();

        foreach ($payload as $key => $value) {
            if (!in_array($key, self::WHITELIST)) continue;

            $session->set($key, $value);
        }

        return new Response('Session updated.');
    }
}
