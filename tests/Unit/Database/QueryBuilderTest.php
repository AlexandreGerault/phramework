<?php

use AGerault\Framework\Database\QueryBuilder;

it(
    'should be able to select a table with an alias',
    function () {
        $query = new QueryBuilder();

        $query->from("posts", "p");

        expect($query->toSQL())->toBeString()->toBe("SELECT * FROM posts p");
    }
);

it(
    'should be able to select a table without an alias',
    function () {
        $query = new QueryBuilder();

        $query->from("posts");

        expect($query->toSQL())->toBeString()->toBe("SELECT * FROM posts");
    }
);

it(
    'should be able to order by a key',
    function () {
        $query = new QueryBuilder();

        $query->from("posts", "p")->orderBy("created_at", "ASC");

        expect($query->toSQL())->toBeString()->toBe("SELECT * FROM posts p ORDER BY created_at ASC");
    }
);

it(
    'should be able to order by multiple key',
    function () {
        $query = new QueryBuilder();

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
        $query = new QueryBuilder();

        $query->from("posts")->limit(10);

        expect($query->toSQL())->toBeString()->toBe("SELECT * FROM posts LIMIT 10");
    }
);

it(
    'should be able to offset the results',
    function () {
        $query = new QueryBuilder();

        $query->from("posts")->offset(3);

        expect($query->toSQL())->toBeString()->toBe("SELECT * FROM posts OFFSET 3");
    }
);


it(
    'should be able to add condition for prepared request',
    function () {
        $query = new QueryBuilder();

        $query->from("posts")->where('title', '=', 'Mon premier article');

        expect($query->toSQL())->toBeString()->toBe("SELECT * FROM posts WHERE title = :title");
    }
);

it(
    'should be able to perform a fetch',
    function () {
        $pdo = new PDO('sqlite::memory:');
        $pdo->exec('CREATE TABLE IF NOT EXISTS posts (`id` INTEGER PRIMARY KEY AUTOINCREMENT, `name` TEXT NOT NULL);');
        $pdo->exec('INSERT INTO posts (name) VALUES (\'my titre\')');
        $query = new QueryBuilder();

        $results = $query->select(['*'])
            ->from('posts')
            ->where('name', '=', 'my titre')
            ->fetch($pdo);

        expect($results)->toBeArray();
        expect($results[0])->toBeArray()->toBe(['id' => '1', 'name' => 'my titre']);
    }
);
