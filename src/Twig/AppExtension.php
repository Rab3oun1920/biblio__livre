<?php

namespace App\Twig;

use Symfony\Component\Routing\RouterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('route_exists', [$this, 'routeExists']),
        ];
    }

    public function routeExists(string $routeName): bool
    {
        $routeCollection = $this->router->getRouteCollection();
        return $routeCollection->get($routeName) !== null;
    }
}
