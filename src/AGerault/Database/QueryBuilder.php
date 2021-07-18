<?php

namespace AGerault\Framework\Database;

use AGerault\Framework\Contracts\Database\QueryBuilderInterface;
use AGerault\Framework\Database\Exceptions\NoConditionsBeforeDeleteException;
use AGerault\Framework\Database\Exceptions\NoDataProvidedException;

class QueryBuilder implements QueryBuilderInterface
{
    /**
     * @var string[]
     */
    protected array $select = ['*'];

    protected string $from;
    protected ?string $fromAlias = null;

    /**
     * @var array<string>|null
     */
    protected ?array $orders = null;
    protected ?string $orderDirection = null;

    protected ?int $limit = null;

    protected ?int $offset = null;

    /**
     * Array of conditions where each line is a condition stored like
     * [KEY => [OPERATOR, VALUE]].
     *
     * @var array<string, array<string, string>>|null
     */
    protected ?array $conditions = null;

    /**
     * @var array<string, string>|null
     */
    protected ?array $insertData = null;

    /**
     * @var array<string, string>|null
     */
    protected ?array $updateData = null;

    protected string $action = 'select';

    protected ?string $joinTable = null;

    protected ?string $joinTableAlias = null;

    protected ?string $joinCondition = null;

    protected bool $aliasPrefixOnColumn = false;

    public function from(string $tableName, ?string $tableAlias = null): QueryBuilderInterface
    {
        $this->from = $tableName;
        if ($tableAlias) {
            $this->fromAlias = $tableAlias;
        }

        return $this;
    }

    public function orderBy(string $key, string $direction): QueryBuilderInterface
    {
        $this->orders[] = $key;
        $this->orderDirection = $direction;

        return $this;
    }

    public function select(array $columns): QueryBuilderInterface
    {
        $this->select = $columns;

        return $this;
    }

    public function limit(int $amount): QueryBuilderInterface
    {
        $this->limit = $amount;

        return $this;
    }

    public function offset(int $offset): QueryBuilderInterface
    {
        $this->offset = $offset;

        return $this;
    }

    public function where(string $key, string $operator, ?string $value = null): QueryBuilderInterface
    {
        $this->conditions[$key] = ['operator' => $operator, 'value' => $value];

        return $this;
    }

    /**
     * @throws NoDataProvidedException
     * @throws NoConditionsBeforeDeleteException
     */
    public function toSQL(): string
    {
        $query = match ($this->action) {
            'insert' => $this->buildInsertQuery(),
            'update' => $this->buildUpdateQuery(),
            'delete' => $this->buildDeleteQuery(),
            default => $this->buildSelectQuery()
        };

        if ('insert' === $this->action) {
            return $query;
        }

        $query = $this->appendInnerJoin($query);

        return $this->appendConditions($query);
    }

    public function insert(array $data): self
    {
        $this->action = 'insert';
        $this->insertData = $data;

        return $this;
    }

    public function delete(): self
    {
        $this->action = 'delete';

        return $this;
    }

    private function buildSelectQuery(): string
    {
        $select = $this->aliasPrefixOnColumn
            ? implode(', ', array_map(fn (string $a) => $a . ' as '. $this->from . '_' . $a, $this->select))
            : implode(', ', $this->select);

        $query = "SELECT {$select} FROM {$this->from}";

        if ($this->fromAlias) {
            $query .= " {$this->fromAlias}";
        }

        if ($this->orders) {
            $orders = implode(', ', $this->orders);
            $query .= " ORDER BY {$orders} {$this->orderDirection}";
        }

        if ($this->limit) {
            $query .= " LIMIT {$this->limit}";
        }

        if ($this->offset) {
            $query .= " OFFSET {$this->offset}";
        }

        return $query;
    }

    /**
     * @throws NoDataProvidedException
     */
    private function buildInsertQuery(): string
    {
        if (!$this->insertData) {
            throw new NoDataProvidedException('No data provided for insertion');
        }

        $columns = implode(', ', array_keys($this->insertData));
        $values = implode(', ', array_map(fn ($value) => ":{$value}", array_keys($this->insertData)));

        return "INSERT INTO {$this->from} ({$columns}) VALUES ({$values});";
    }

    /**
     * @throws NoConditionsBeforeDeleteException
     */
    private function buildDeleteQuery(): string
    {
        if (!$this->conditions) {
            throw new NoConditionsBeforeDeleteException('Please provide conditions to delete rows');
        }

        return "DELETE FROM {$this->from}";
    }

    /**
     * @param array<string, string> $data
     */
    public function update(array $data): QueryBuilderInterface
    {
        $this->action = 'update';
        $this->updateData = $data;

        return $this;
    }

    /**
     * @throws NoDataProvidedException
     */
    private function buildUpdateQuery(): string
    {
        if (!$this->updateData) {
            throw new NoDataProvidedException('No data provided for update');
        }

        $columns = implode(', ', array_map(fn ($key) => "{$key} = :{$key}", array_keys($this->updateData)));

        return "UPDATE {$this->from} SET {$columns}";
    }

    public function innerJoin(string $table, ?string $alias = null): QueryBuilderInterface
    {
        $this->joinTable = $table;
        if ($alias) {
            $this->joinTableAlias = $alias;
        }

        return $this;
    }

    public function on(string $left, string $right): QueryBuilderInterface
    {
        $this->joinCondition = "{$left} = {$right}";

        return $this;
    }

    private function appendInnerJoin(string $query): string
    {
        if (!$this->joinTable || !$this->joinCondition) {
            return $query;
        }

        $query .= " INNER JOIN {$this->joinTable}";

        if ($this->joinTableAlias) {
            $query .= " {$this->joinTableAlias}";
        }

        $query .= " ON {$this->joinCondition}";

        return $query;
    }

    private function appendConditions(string $query): string
    {
        if ($this->conditions) {
            $conditions = implode(
                ', ',
                array_map(
                    function ($name, $payload) {
                        $value = $payload['value'] ?? ":{$name}";
                        return "{$name} {$payload['operator']} {$value}";
                    },
                    array_keys($this->conditions),
                    array_values($this->conditions)
                )
            );

            $query .= " WHERE {$conditions}";
        }

        return $query;
    }

    public function withAliasPrefixOnColumns(bool $enable = true): QueryBuilderInterface
    {
        $this->aliasPrefixOnColumn = $enable;

        return $this;
    }
}
