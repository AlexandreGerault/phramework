<?php

namespace AGerault\Framework\Contracts\Database;

use PDO;

interface QueryBuilderInterface
{
    /**
     * Select the table to perform the query on.
     *
     * @return QueryBuilderInterface
     */
    public function from(string $tableName, ?string $tableAlias = null): self;

    /**
     * Select the columns to fetch.
     *
     * @param array<string> $columns
     *
     * @return $this
     */
    public function select(array $columns): self;

    /**
     * Select the columns to fetch from the joining table
     *
     * @param array<string> $columns
     * @return $this
     */
    public function selectOnJoinTable(array $columns): self;

    /**
     * Specify the amount of results to fetch.
     *
     * @param int $amount
     * @return QueryBuilderInterface
     */
    public function limit(int $amount): self;

    /**
     * Specify the amount of results to skip.
     *
     * @param int $offset
     * @return QueryBuilderInterface
     */
    public function offset(int $offset): self;

    /**
     * @param string $direction Should be either ASC either DESC
     *
     * @return $this
     */
    public function orderBy(string $key, string $direction): self;

    /**
     * @return $this
     */
    public function where(string $key, string $operator, ?string $value = null): self;

    /**
     * Return the built query as a SQL query.
     */
    public function toSQL(): string;

    /**
     * Returns results of the query using a PDO instance.
     *
     * @return array
     */
    // public function fetch(): array;

    /**
     * Insert a set of data to a row.
     *
     * @param array<string, string> $data An associative array where key is the column name and value is... the value
     *
     * @return QueryBuilderInterface
     */
    public function insert(array $data): self;

    /**
     * Delete rows that matches the query.
     *
     * @return QueryBuilderInterface
     */
    public function delete(): self;

    /**
     * @param array<string, string> $data
     *
     * @return $this
     */
    public function update(array $data): self;

    /**
     * Init an inner join statement, but not the join condition. See QueryBuilderInterface::on for joining condition.
     *
     * @return $this
     */
    public function innerJoin(string $table, ?string $alias = null): self;

    /**
     * Add a joining condition.
     *
     * @return $this
     */
    public function on(string $left, string $right): self;

    /**
     * Aliases if needed the column names
     *
     * @param bool $enable
     * @return $this
     */
    public function withAliasPrefixOnColumns(bool $enable = true): self;
}
