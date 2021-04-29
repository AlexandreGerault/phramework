<?php

namespace AGerault\Framework\Contracts\Database;

use PDO;

interface QueryBuilderInterface
{
    /**
     * Select the table to perform the query on
     *
     * @param string $tableName
     * @param string|null $tableAlias
     * @return QueryBuilderInterface
     */
    public function from(string $tableName, ?string $tableAlias = null): self;

    /**
     * Select the columns to fetch
     *
     * @param array<string> $columns
     * @return $this
     */
    public function select(array $columns): self;

    /**
     * Specify the amount of results to fetch
     *
     * @param int $amount
     * @return QueryBuilderInterface
     */
    public function limit(int $amount): self;

    /**
     * Specify the amount of results to skip
     *
     * @param int $offset
     * @return QueryBuilderInterface
     */
    public function offset(int $offset): self;

    /**
     * @param string $key
     * @param string $direction Should be either ASC either DESC
     * @return $this
     */
    public function orderBy(string $key, string $direction): self;

    /**
     * @param string $key
     * @param string $operator
     * @param string $value
     * @return $this
     */
    public function where(string $key, string $operator, string $value): self;

    /**
     * Return the built query as a SQL query
     */
    public function toSQL(): string;

    /**
     * Returns results of the query using a PDO instance
     *
     * @return array
     */
    public function fetch(): array;

    /**
     * Insert a set of data to a row
     *
     * @param string $table The table name
     * @param array<string, string> $data An associative array where key is the column name and value is... the value
     * @return void
     */
    public function insert(string $table, array $data): self;
}
