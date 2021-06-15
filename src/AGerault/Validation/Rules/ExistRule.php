<?php


namespace AGerault\Framework\Validation\Rules;


use AGerault\Framework\Database\QueryBuilder;

class ExistRule extends Rule
{
    public function __construct(
        mixed $value,
        protected \PDO $pdo,
        protected string $table,
        protected string $column
    ) {
        parent::__construct($value);
    }

    public function validate(): bool
    {
        $query = new QueryBuilder();
        $query = $query->from($this->table)->where($this->column, '=')->toSql();

        $pdoQuery = $this->pdo->prepare($query);
        $pdoQuery->bindParam(":{$this->column}", $this->value);
        $pdoQuery->execute();

        return count($pdoQuery->fetchAll()) >= 1;
    }

    public function onFailMessage(): string
    {
        return "{$this->value} does not exist in table {$this->table} for column `{$this->column}`";
    }
}
