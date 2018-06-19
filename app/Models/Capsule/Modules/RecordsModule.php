<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 04/06/18
 * Time: 11:43
 */

namespace Equinox\Models\Capsule\Modules;


use Equinox\Definitions\ColumnsDefinitions;
use Equinox\Factories\RecordFactory;
use Equinox\Models\Capsule\Column;
use Equinox\Models\Capsule\Record;
use Illuminate\Support\Collection;

trait RecordsModule
{
    /**
     * The record structure extracted from the Columns collection
     * @var Record
     */
    protected $record = null;

    /**
     * Short function used to return an empty structure of a Capsule record
     * @return Record
     */
    public function extractRecord(): Record
    {
        if (!is_null($this->record)) {
            return $this->record;
        }

        $this->initNewRecord()
            ->setHashKey()
            ->setPivotKeys()
            ->setIntervalKeys();

        return $this->record;
    }

    /**
     * Function used to initialize a new record
     * @return self
     */
    protected function initNewRecord(): self
    {
        /** @var RecordFactory $recordFactory */
        $recordFactory = $this->recordFactory;

        $this->record = $recordFactory->build();

        return $this;
    }

    /**
     * Set hash key
     * @return self
     */
    protected function setHashKey(): self
    {
        $hashKey = $this->extractHashKeyFromColumns($this->columns);

        $this->record->setHashKey($hashKey);

        return $this;
    }

    /**
     * Extract the hash key from the columns
     * @param Collection $columns
     * @return string
     */
    protected function extractHashKeyFromColumns(Collection $columns): string
    {
        return $columns->map(function (Column $column) {
            if ($column->getType() == ColumnsDefinitions::HASH_COLUMN) {
                return $column->name;
            }

            return null;
        })->filter()->first();
    }

    /**
     * Set pivot keys
     * @return self
     */
    protected function setPivotKeys(): self
    {
        $pivots = $this->extractPivotKeysFromColumns($this->columns);

        foreach ($pivots as $pivot) {
            $this->record->setPivotKey($pivot);
        }

        return $this;
    }

    /**
     * Extract the pivot keys from the columns
     * @param Collection $columns
     * @return array
     */
    protected function extractPivotKeysFromColumns(Collection $columns): array
    {
        return $columns->map(function (Column $column) {
            if ($column->getType() == ColumnsDefinitions::PIVOT_COLUMN) {
                return $column->name;
            }

            return null;
        })->filter()->toArray();
    }

    /**
     * Set interval keys
     * @return self
     */
    protected function setIntervalKeys(): self
    {
        $intervals = $this->extractIntervalKeysFromColumns($this->columns);

        foreach ($intervals as $interval) {
            $this->record->setIntervalKey($interval);
        }

        return $this;
    }

    /**
     * Extract the interval keys from the columns
     * @param Collection $columns
     * @return array
     */
    protected function extractIntervalKeysFromColumns(Collection $columns): array
    {
        return $columns->map(function (Column $column) {
            if ($column->getType() == ColumnsDefinitions::INTERVAL_COLUMN) {
                return $column->name;
            }

            return null;
        })->filter()->toArray();
    }

}