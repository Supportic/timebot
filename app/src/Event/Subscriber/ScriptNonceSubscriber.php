<?php

namespace App\Event\Subscriber;

use Pentatrion\ViteBundle\Event\RenderAssetTagEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
final class ScriptNonceSubscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents(): array
    {
        return [
            RenderAssetTagEvent::class => 'onRenderAssetTag',
        ];
    }

    public function onRenderAssetTag(RenderAssetTagEvent $event): void
    {
        $tag = $event->getTag();
        if ($tag->isInternal()) {
            return;
        }

        if ($tag->isScriptTag() && $event->isBuild()) {
            $tag->setAttribute('nonce', 'lookup nonce');
        }

        // set additional HTML attributes on scrip element
        // $tag->setAttribute('foo', 'bar-modified');
    }
}
