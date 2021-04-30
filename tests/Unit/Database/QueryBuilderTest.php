<?php

use AGerault\Framework\Contracts\Database\QueryBuilderInterface;
use AGerault\Framework\Database\QueryBuilder;
use JetBrains\PhpStorm\Pure;

#[Pure] function getQueryBuilder(): QueryBuilderInterface
{
    return new QueryBuilder();
}

it(
    'should be able to select a table with an alias',
    function () {
        $query = getQueryBuilder();

        $query->from("posts", "p");

        expect($query->toSQL())->toBeString()->toBe("SELECT * FROM posts p");
    }
);

it(
    'should be able to select a table without an alias',
    function () {
        $query = getQueryBuilder();

        $query->from("posts");

        expect($query->toSQL())->toBeString()->toBe("SELECT * FROM posts");
    }
);

it(
    'should be able to order by a key',
    function () {
        $query = getQueryBuilder();

        $query->from("posts", "p")->orderBy("created_at", "ASC");

        expect($query->toSQL())->toBeString()->toBe("SELECT * FROM posts p ORDER BY created_at ASC");
    }
);

it(
    'should be able to order by multiple key',
    function () {
        $query = getQueryBuilder();

        $query
            ->from("posts", "p")
            ->orderBy("created_at", "ASC")
            ->orderBy("updated_at", "ASC");

        expect($query->toSQL())->toBeString()->toBe("SELECT * FROM posts p ORDER BY created_at, updated_at ASC");
    }
);

it(
    'should be able to limit the number of results',
    function () {
        $query = getQueryBuilder();

        $query->from("posts")->limit(10);

        expect($query->toSQL())->toBeString()->toBe("SELECT * FROM posts LIMIT 10");
    }
);

it(
    'should be able to offset the results',
    function () {
        $query = getQueryBuilder();

        $query->from("posts")->offset(3);

        expect($query->toSQL())->toBeString()->toBe("SELECT * FROM posts OFFSET 3");
    }
);


it(
    'should be able to add condition for prepared request',
    function () {
        $query = getQueryBuilder();

        $query->from("posts")->where('title', '=', 'Mon premier article');

        expect($query->toSQL())->toBeString()->toBe("SELECT * FROM posts WHERE title = :title");
    }
);

it(
    'should be able to perform a prepared INSERT statement',
    function () {
        $query = getQueryBuilder();

        $query->insert(['name' => "My title", 'slug' => 'my-title'])->from('posts');

        expect($query->from('posts')->toSQL())->toBeString()->toBe("INSERT INTO posts (name, slug) VALUES (:name, :slug);");
    }
);

it(
    'should be able to delete rows',
    function () {
        $query = getQueryBuilder();

        $query->from('posts')->where('name', '=', 'my title')->delete();

        expect($query->from('posts')->toSQL())->toBeString()->toBe("DELETE FROM posts WHERE name = :name");
    }
);

it(
    'should be able to delete rows with multiple conditions',
    function () {
        $query = getQueryBuilder();

        $query->from('posts')
            ->where('name', '=', 'my title')
            ->where('slug', '=', 'my-title')
            ->delete();

        expect($query->from('posts')->toSQL())->toBeString()->toBe("DELETE FROM posts WHERE name = :name, slug = :slug");
    }
);

it('should throw an exception if no conditions are set', function() {
    $query = getQueryBuilder();

    $query->from('posts')->delete()->toSQL();
})->throws(Exception::class);
