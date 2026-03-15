<?php

declare(strict_types=1);

namespace App\Twig\Component\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\ValidatableComponentTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;

#[AsLiveComponent(
    // use this.prop in the twig template to access the properties.
    exposePublicProps: false,
)]
class Sidebar
{
    use DefaultActionTrait;
    use ValidatableComponentTrait;

    #[LiveProp(writable: true)]
    #[Assert\Type('bool')]
    public bool $isExpanded = true;

    /**
     * Used to detect active route for active NavItem class
     */
    #[LiveProp]
    public ?string $currentPath = null;

    public function __construct(protected readonly RequestStack $requestStack) {}

    // include params only when you want to validate them
    public function mount(): void
    {
        $request = $this->requestStack->getCurrentRequest();
        if ($request instanceof Request) {
            $this->currentPath = $request->getPathInfo();
        }
        $session = $this->requestStack->getSession();
        $this->isExpanded = (bool) $session->get('sidebar_expanded', true);
    }

    #[LiveAction]
    public function saveSidebarStateInSession(#[LiveArg] bool $isExpanded)
    {
        $this->isExpanded = $isExpanded;
        $session = $this->requestStack->getSession();
        $session->set('sidebar_expanded', $isExpanded);
    }
}
