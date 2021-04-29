<?php

namespace AGerault\Framework\Database;

use AGerault\Framework\Contracts\Database\QueryBuilderInterface;
use PDO;

class QueryBuilder implements QueryBuilderInterface
{
    protected PDO $pdo;

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
     * QueryBuilder constructor.
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }


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

    public function toSQL(): string
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

    public function fetch(): array
    {

        $query = $this->pdo->prepare($this->toSQL());

        if ($this->conditions) {
            foreach ($this->conditions as $key => $condition) {
                $query->bindParam($key, $condition['value']);
            }
        }

        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insert(string $table, array $data): void
    {
        $columns = implode(', ', array_keys($data));
        $values = implode(', ', array_map(fn($value) => ":{$value}", array_keys($data)));
        $query = $this->pdo->prepare("INSERT INTO {$table} ({$columns}) VALUES ({$values});");

        foreach ($data as $key => $value)
        {
            $query->bindParam($key, $value);
        }

        $query->execute();
    }
}
