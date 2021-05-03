<?php

use AGerault\Framework\Contracts\Database\QueryBuilderInterface;
use AGerault\Framework\Database\Exceptions\NoDataProvidedException;
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

        $query->from('posts', 'p');

        expect($query->toSQL())->toBeString()->toBe('SELECT * FROM posts p');
    }
);

it(
    'should be able to select a table without an alias',
    function () {
        $query = getQueryBuilder();

        $query->from('posts');

        expect($query->toSQL())->toBeString()->toBe('SELECT * FROM posts');
    }
);

it(
    'should be able to specify select columns',
    function () {
        $query = getQueryBuilder();

        $query->select(['title'])->from('posts');

        expect($query->toSQL())->toBeString()->toBe('SELECT title FROM posts');
    }
);

it(
    'should be able to order by a key',
    function () {
        $query = getQueryBuilder();

        $query->from('posts', 'p')->orderBy('created_at', 'ASC');

        expect($query->toSQL())->toBeString()->toBe('SELECT * FROM posts p ORDER BY created_at ASC');
    }
);

it(
    'should be able to order by multiple key',
    function () {
        $query = getQueryBuilder();

        $query
            ->from('posts', 'p')
            ->orderBy('created_at', 'ASC')
            ->orderBy('updated_at', 'ASC');

        expect($query->toSQL())->toBeString()->toBe('SELECT * FROM posts p ORDER BY created_at, updated_at ASC');
    }
);

it(
    'should be able to limit the number of results',
    function () {
        $query = getQueryBuilder();

        $query->from('posts')->limit(10);

        expect($query->toSQL())->toBeString()->toBe('SELECT * FROM posts LIMIT 10');
    }
);

it(
    'should be able to offset the results',
    function () {
        $query = getQueryBuilder();

        $query->from('posts')->offset(3);

        expect($query->toSQL())->toBeString()->toBe('SELECT * FROM posts OFFSET 3');
    }
);

it(
    'should be able to add condition for prepared request',
    function () {
        $query = getQueryBuilder();

        $query->from('posts')->where('title', '=', 'Mon premier article');

        expect($query->toSQL())->toBeString()->toBe('SELECT * FROM posts WHERE title = :title');
    }
);

it(
    'should be able to perform a prepared INSERT statement',
    function () {
        $query = getQueryBuilder();

        $query->insert(['name' => 'My title', 'slug' => 'my-title'])->from('posts');

        expect($query->from('posts')->toSQL())->toBeString()->toBe(
            'INSERT INTO posts (name, slug) VALUES (:name, :slug);'
        );
    }
);

it(
    'should throw an exception if no insert data are provided',
    function () {
        $query = getQueryBuilder();

        $query->insert([])->toSQL();
    }
)->throws(NoDataProvidedException::class);

it(
    'should be able to delete rows',
    function () {
        $query = getQueryBuilder();

        $query->from('posts')->where('name', '=', 'my title')->delete();

        expect($query->from('posts')->toSQL())->toBeString()->toBe('DELETE FROM posts WHERE name = :name');
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

        expect($query->from('posts')->toSQL())->toBeString()->toBe(
            'DELETE FROM posts WHERE name = :name, slug = :slug'
        );
    }
);

it(
    'should throw an exception if no conditions are set',
    function () {
        $query = getQueryBuilder();

        $query->from('posts')->delete()->toSQL();
    }
)->throws(Exception::class);

it(
    'should be able to update rows',
    function () {
        $query = getQueryBuilder();

        $query->from('posts')->update(['title' => 'New title', 'slug' => 'new-title']);

        expect($query->toSQL())->toBeString()->toBe('UPDATE posts SET title = :title, slug = :slug');
    }
);

it(
    'should be able to update rows with one condition',
    function () {
        $query = getQueryBuilder();

        $query
            ->from('posts')
            ->update(['title' => 'New title', 'slug' => 'new-title'])
            ->where('title', '=', 'title')
            ->toSql();

        expect($query->toSQL())->toBeString()->toBe(
            'UPDATE posts SET title = :title, slug = :slug WHERE title = :title'
        );
    }
);

it(
    'should be able to update rows with multiple conditions',
    function () {
        $query = getQueryBuilder();

        $query
            ->from('posts')
            ->update(['title' => 'New title', 'slug' => 'new-title'])
            ->where('title', '=', 'title')
            ->where('slug', '=', 'my-title')
            ->toSql();

        expect($query->toSQL())
            ->toBeString()
            ->toBe('UPDATE posts SET title = :title, slug = :slug WHERE title = :title, slug = :slug');
    }
);

it(
    'should throw an exception if no data are provided for an update statement',
    function () {
        $query = getQueryBuilder();

        $query->update([])->toSQL();
    }
)->throws(NoDataProvidedException::class);

it(
    'should be able to write an inner join statement',
    function () {
        $query = getQueryBuilder();

        $query->select(['title'])
            ->from('posts', 'p')
            ->innerJoin('authors', 'a')
            ->on('p.author_id', 'a.id');

        expect($query->toSQL())
            ->toBeString()
            ->toBe('SELECT title FROM posts p INNER JOIN authors a ON p.author_id = a.id');
    }
);
