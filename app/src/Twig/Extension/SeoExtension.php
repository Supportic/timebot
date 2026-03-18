<?php

declare(strict_types=1);

namespace App\Twig\Extension;

use App\DTO\Seo\RouteDTO;
use App\Service\SeoService;
use App\Service\UrlService;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\AppVariable;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Attribute\AsTwigFunction;
use Twig\Markup;

class SeoExtension
{
    public function __construct(
        private readonly SeoService $seoService,
        private readonly UrlService $urlService,
        private readonly RouterInterface $router,
        private readonly LoggerInterface $logger,
        private readonly TranslatorInterface $translator,
        #[Autowire(param: 'app.seo.title.portal')]
        private readonly string $portalTitle,
        #[Autowire(param: 'app.seo.title.separator')]
        private readonly string $titleSeparator,
    ) {}

    #[AsTwigFunction('render_seo_tags', needsContext: true)]
    public function renderSeoTags(array $context, string $templateTitle, string $templateDescription): Markup
    {
        $app = $context['app'] ?? null;
        $locale = $app instanceof AppVariable ? $app->getLocale() : null;

        $html = '';
        $html .= $this->buildTitle(
            $this->portalTitle,
            $this->titleSeparator,
            '' !== trim($templateTitle) ? $templateTitle : $this->seoService->getTitle(),
            $locale
        );
        $html .= $this->buildDescription(
            '' !== trim($templateDescription)
                ? $templateDescription
                : $this->seoService->getDescription()
        );
        $html .= $this->buildMetaNames();
        $html .= $this->buildMetaProperties();
        $html .= $this->buildCanonicalUrl();

        return new Markup($html, 'UTF-8');
    }

    private function buildTitle(string $portalTitle, string $titleSeparator, bool|string $pageTitle, ?string $locale): string
    {
        // default
        if (true === $pageTitle) {
            return '<title>' . htmlspecialchars(trim($portalTitle)) . '</title>' . PHP_EOL;
        }

        if (is_string($pageTitle)) {
            $pageTitle = trim($pageTitle);

            /**
             * if the translation key cannot be found, it will take the raw value
             * TODO currently no support for parameters in translations
             */
            $pageTitle = $this->translator->trans($pageTitle, domain: 'seo', locale: $locale);

            return sprintf(
                '<title>%s %s %s</title>' . PHP_EOL,
                htmlspecialchars(trim($portalTitle)),
                htmlspecialchars(trim($titleSeparator)),
                htmlspecialchars(trim($pageTitle)),
            );
        }

        return '';
    }

    private function buildDescription(false|string $description): string
    {
        // default
        if (false === $description || '' === trim($description)) {
            return '';
        }

        return sprintf(
            '<meta name="description" content="%s">' . PHP_EOL,
            htmlspecialchars(trim($description))
        );
    }

    private function buildMetaNames(): string
    {
        $html = '';
        foreach ($this->seoService->getMetaNames() as $name => $content) {

            // use description field
            if ('description' === $name) {
                continue;
            }

            $html .= sprintf(
                '<meta name="%s" content="%s">' . PHP_EOL,
                htmlspecialchars($name),
                htmlspecialchars($content)
            );
        }
        return $html;
    }

    private function buildMetaProperties(): string
    {
        $html = '';
        foreach ($this->seoService->getMetaProperties() as $property => $content) {
            $html .= sprintf(
                '<meta property="%s" content="%s">' . PHP_EOL,
                htmlspecialchars($property),
                htmlspecialchars($content)
            );
        }
        return $html;
    }

    private function buildCanonicalUrl(): string
    {
        $canonicalUrl = $this->seoService->getCanonicalUrl();
        $url = null;

        // default
        if (true === $canonicalUrl) {
            $url = $this->urlService->generateCanonicalUrl();
        } elseif ($canonicalUrl instanceof RouteDTO) {
            try {
                $url = $this->router->generate(
                    $canonicalUrl->route,
                    $canonicalUrl->routeParams,
                    UrlGeneratorInterface::ABSOLUTE_URL
                );
            } catch (RouteNotFoundException | MissingMandatoryParametersException | InvalidParameterException) {
                $url = false;
                $this->logger->error(
                    'Could not generate custom canonical url for route "' . $canonicalUrl->route . '".'
                );
            }
        }

        if (is_string($url)) {
            return sprintf(
                '<link rel="canonical" href="%s">' . PHP_EOL,
                htmlspecialchars($url)
            );
        }

        return '';
    }
}
