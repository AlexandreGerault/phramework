<?php

namespace AGerault\Framework\Contracts\Core;

use AGerault\Framework\Contracts\Routing\RouteCollectionInterface;
use AGerault\Framework\Contracts\Routing\RouteInterface;
use AGerault\Framework\Contracts\Services\ServiceContainerInterface;

interface ApplicationInterface
{
    /**
     * Get the framework's version following semantic versioning
     * See: https://semver.org for more information
     *
     * @return string
     */
    public function version(): string;

    /**
     * Get the base path of the application's installation
     *
     * @param string $path
     * @return string
     */
    public function basePath($path = ''): string;

    /**
     * Get the path to the application configuration files folder
     *
     * @param string $path Optionally, a path to append to the config path
     * @return string
     */
    public function configPath($path = ''): string;

    /**
     * Get or check the current application environment.
     *
     * @param  string|array  $environments
     * @return string|bool
     */
    public function environment(string | array ...$environments): bool | string;

    /**
     * The path were the templates are located
     */
    public function templatePath(): string;

    /**
     * Get the services container
     *
     * @return ServiceContainerInterface
     */
    public function container(): ServiceContainerInterface;

    /**
     * @param array<RouteInterface> $router
     */
    public function registerRoutes(array $router): RouteCollectionInterface;

    /**
     * Return all registered routes
     */
    public function routes(): RouteCollectionInterface;
}
