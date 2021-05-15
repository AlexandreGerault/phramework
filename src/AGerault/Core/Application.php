<?php

namespace AGerault\Framework\Core;

use AGerault\Framework\Contracts\Core\ApplicationInterface;
use AGerault\Framework\Contracts\Routing\RouteCollectionInterface;
use AGerault\Framework\Contracts\Services\ServiceContainerInterface;
use AGerault\Framework\Routing\Route;
use AGerault\Framework\Routing\RouteCollection;
use AGerault\Framework\Services\ServiceContainer;
use Psr\Container\ContainerInterface;

class Application implements ApplicationInterface
{
    protected ContainerInterface $container;
    protected RouteCollectionInterface $routes;

    public function __construct(ServiceContainerInterface $container, RouteCollectionInterface $routes)
    {
        $this->container = $container;
        $this->routes = $routes;
    }

    public function version(): string
    {
        return '0.0.1-BETA';
    }

    public function basePath($path = ''): string
    {
        return './';
    }

    public function configPath($path = ''): string
    {
        return './config';
    }

    public function environment(string|array ...$environments): bool|string
    {
        return false;
    }

    public function templatePath(): string
    {
        return './views';
    }

    public function container(): ServiceContainerInterface
    {
        return $this->container;
    }

    /**
     * @throws \Exception
     */
    public function registerRoutes(array $router): RouteCollectionInterface
    {
        foreach ($router as $route) {
            $action = $route['action'];

            if (is_array($action)) {
                $controller = $this->container->get($route['action'][0]);
                $method = '__invoke';
                if (count($action) > 1) {
                    $method = $action[1];
                }

                $action = [$controller, $method];
            }

            $this->routes->registerRoute(
                new Route(
                    $route['path'],
                    $route['name'],
                    $route['method'],
                    $action,
                    $route['parameters'] ?? []
                )
            );
        }

        return $this->routes;
    }

    public function routes(): RouteCollectionInterface
    {
        return $this->routes;
    }
}
