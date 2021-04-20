<?php

use AGerault\Framework\Routing\Route;
use AGerault\Framework\Routing\RouteCollection;
use AGerault\Framework\Routing\UrlMatcher;

it(
    "should be able to register and match an incoming url pattern",
    function () {
        $routeIndex = new Route(
            "/article",
            "article.index",
            "GET",
            function () {
                return "Liste des articles";
            }
        );
        $routeShow = new Route(
            "/article/(.+)-(\d+)",
            "article.show",
            "GET",
            function () {
                return "Page d'un article";
            },
            ['slug', 'id']
        );

        $collection = new RouteCollection();
        $collection->registerRoute($routeIndex);
        $collection->registerRoute($routeShow);

        $matcher = new UrlMatcher($collection);

        $matched = $matcher->match('/article/magnifique-article-21', 'GET');

        expect($matched->name())->toBeString()->toEqual('article.show');
        expect($matched->parameter('slug'))->toBeString()->toEqual('magnifique-article');
        expect($matched->parameter('id'))->toBeString()->toEqual(21);
    }
);
