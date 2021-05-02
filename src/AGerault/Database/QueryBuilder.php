<?php

namespace AGerault\Framework\Database;

use AGerault\Framework\Contracts\Database\QueryBuilderInterface;

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
     * [KEY => [OPERATOR, VALUE]]
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

    protected string $action = "select";

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

    public function where(string $key, string $operator, string $value): QueryBuilderInterface
    {
        $this->conditions[$key] = ['operator' => $operator, 'value' => $value];

        return $this;
    }

    /**
     * @throws NoDataForInsertException
     * @throws NoConditionsBeforeDeleteException
     * @throws UnsupportedSqlActionException
     */
    public function toSQL(): string
    {
        return match ($this->action) {
            "select" => $this->buildSelectQuery(),
            "insert" => $this->buildInsertQuery(),
            "update" => $this->buildUpdateQuery(),
            "delete" => $this->buildDeleteQuery(),
            default => throw new UnsupportedSqlActionException("Unhandled SQL action")
        };
    }

    public function insert(array $data): self
    {
        $this->action = "insert";
        $this->insertData = $data;
        return $this;
    }

    public function delete(): self
    {
        $this->action = "delete";
        return $this;
    }

    private function buildSelectQuery(): string
    {
        $select = implode(', ', $this->select);
        $query = "SELECT {$select} FROM {$this->from}";

        if ($this->fromAlias) {
            $query .= " {$this->fromAlias}";
        }

        if ($this->conditions) {
            $query .= " WHERE";
            foreach ($this->conditions as $key => $condition) {
                $query .= " {$key} {$condition['operator']} :{$key}";
            }
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
     * @throws NoDataForInsertException
     */
    private function buildInsertQuery(): string
    {
        if (!$this->insertData) {
            throw new NoDataForInsertException('No data provided for insertion');
        }

        $columns = implode(', ', array_keys($this->insertData));
        $values = implode(', ', array_map(fn($value) => ":{$value}", array_keys($this->insertData)));

        return "INSERT INTO {$this->from} ({$columns}) VALUES ({$values});";
    }

    /**
     * @throws NoConditionsBeforeDeleteException
     */
    private function buildDeleteQuery(): string
    {
        if (!$this->conditions) {
            throw new NoConditionsBeforeDeleteException("Please provide conditions to delete rows");
        }

        $conditions = implode(
            ', ',
            array_map(
                fn($name, $payload) => "{$name} {$payload['operator']} :{$name}",
                array_keys($this->conditions),
                array_values($this->conditions)
            )
        );

        return "DELETE FROM {$this->from} WHERE {$conditions}";
    }

    /**
     * @param array<string, string> $data
     * @return QueryBuilderInterface
     */
    public function update(array $data): QueryBuilderInterface
    {
        $this->action = "update";
        $this->updateData = $data;
        return $this;
    }

    private function buildUpdateQuery(): string
    {
        if (!$this->updateData) {
            return '';
        }

        $columns = implode(', ', array_map(fn($key) => "{$key} = :{$key}", array_keys($this->updateData)));

        $query = "UPDATE {$this->from} SET {$columns}";

        if ($this->conditions) {
            $conditions = implode(
                ', ',
                array_map(
                    fn($name, $payload) => "{$name} {$payload['operator']} :{$name}",
                    array_keys($this->conditions),
                    array_values($this->conditions)
                )
            );

            $query .= " WHERE {$conditions}";
        }

        return $query;
    }
}
