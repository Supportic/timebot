<?php

namespace App\Controller\App;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_FULLY')] // using isGranted makes the user not nullable
class SessionController extends AbstractController
{
    const keyWhitelist = ['sidebar_expanded'];

    #[Route(path: '/session/set', methods: ['post'])]
    public function set(Request $request): Response
    {
        $payload = json_decode($request->getContent(), JSON_OBJECT_AS_ARRAY, JSON_UNESCAPED_UNICODE);
        $session = $request->getSession();

        foreach ($payload as $key => $value) {
            if (!in_array($key, self::keyWhitelist)) continue;

            $session->set($key, $value);
        }

        return new Response('Session updated.');
    }
}
