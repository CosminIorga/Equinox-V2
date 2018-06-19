<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 13/09/17
 * Time: 14:39
 */

namespace Equinox\Repositories;


use Equinox\Models\Capsule\Capsule;
use Equinox\Models\Capsule\Record;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DataRepository extends DefaultRepository
{
    /**
     * The raw sql query
     * @var string
     */
    protected $sqlQuery;

    /**
     * Function used to modify Capsule data given list of records
     * @param Capsule $capsule
     * @param Collection $records
     */
    public function modifyCapsuleData(Capsule $capsule, Collection $records)
    {
        /** @var Record $firstRecord */
        $firstRecord = $records->first();

        $tableName = $capsule->capsuleId;
        $columns = $firstRecord->extractKeys();
        $values = $records->map(function (Record $record) {
            return $record->extractValues()->toArray();
        });
        $clause = $firstRecord->intervalKeys;

        $response = $this->insertOnDuplicateKey(
            $tableName,
            $columns,
            $values,
            $clause
        );
        dump($response);
    }

    /**
     * Function used to make an insert query with ON_DUPLICATE clause
     * @param string $tableName
     * @param Collection $columns
     * @param Collection $values
     * @param Collection $clause
     * @return bool
     */
    protected function insertOnDuplicateKey(
        string $tableName,
        Collection $columns,
        Collection $values,
        Collection $clause
    ) {
        return $this->initQuery($tableName)
            ->addInsertColumns($columns)
            ->addInsertValues($values)
            ->addOnDuplicateClause($clause)
            ->executeStatement();
    }

    /**
     * Initialize new query
     * @param string $tableName
     * @return DataRepository
     */
    protected function initQuery(string $tableName): self
    {
        $this->sqlQuery = "INSERT INTO `{$tableName}` ";

        return $this;
    }

    /**
     * Add insert columns to sql query
     * @param Collection $columns
     * @return DataRepository
     */
    protected function addInsertColumns(Collection $columns): self
    {
        $columns = $columns->map(function (string $column) {
            return "`{$column}`";
        });

        $columns = $columns->implode(', ');

        $this->sqlQuery .= "($columns) ";

        return $this;
    }

    /**
     * Function used to add insert values
     * @param Collection $values
     * @return DataRepository
     */
    protected function addInsertValues(Collection $values): self
    {
        $values = $values->map(function (array $valueSet) {
            $valueSet = $this->quoteString($valueSet);

            $valueSet = collect($valueSet)->implode(', ');

            return "({$valueSet})";
        });

        $values = $values->implode(', ');

        $this->sqlQuery .= "VALUES " . $values . " ";

        return $this;
    }

    /**
     * Add on duplicate key clause
     * @param Collection $clause
     * @return DataRepository
     */
    protected function addOnDuplicateClause(Collection $clause): self
    {
        $clause = $clause->map(function (string $c) {
            return "`{$c}` = IF(VALUES(`{$c}`) IS NULL, `{$c}`, IFNULL(`{$c}`, 0) + VALUES(`{$c}`))";
        });

        $clause = $clause->implode(', ');

        $this->sqlQuery .= "ON DUPLICATE KEY UPDATE $clause";

        return $this;
    }

    /**
     * Execute the insert statement
     * @return bool
     */
    protected function executeStatement(): bool
    {
        return DB::statement($this->sqlQuery);
    }

    /**
     * Quote string given value
     * @param $value
     * @return string
     */
    protected function quoteString($value): string
    {
        if (is_array($value)) {
            return implode(', ', array_map([$this, __FUNCTION__], $value));
        }

        if (is_null($value)) {
            return "NULL";
        }

        return "'$value'";
    }
}