<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 23/04/18
 * Time: 13:30
 */

namespace Equinox\Models\Capsule\Modules;


use Equinox\Definitions\ColumnsDefinitions;
use Equinox\Factories\ColumnFactory;
use Equinox\Services\General\Config;
use Illuminate\Support\Collection;

trait ColumnsModule
{
    /**
     * @var Collection
     */
    protected $columns;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @return self
     */
    protected function computeColumns(): self
    {
        return $this->initColumns()
            ->computeHashColumn()
            ->computePivotColumns()
            ->computeIntervalColumns();
    }

    /**
     * Initialize columns to empty Collection
     * @return $this
     */
    protected function initColumns()
    {
        $this->columns = collect();

        return $this;
    }

    /**
     * Function used to compute hash column
     * @return self
     */
    protected function computeHashColumn(): self
    {
        $hashConfig = $this->config->get('capsule.columns.hash_column');

        return $this->buildAndStoreColumn(ColumnsDefinitions::HASH_COLUMN, $hashConfig);
    }

    /**
     * Function used to compute pivot columns
     * @return self
     */
    protected function computePivotColumns(): self
    {
        $pivotsConfig = $this->config->get('capsule.columns.pivot_columns');

        foreach ($pivotsConfig as $pivotConfig) {
            $this->buildAndStoreColumn(ColumnsDefinitions::PIVOT_COLUMN, $pivotConfig);
        }

        return $this;
    }

    /**
     * Function used to compute interval columns
     * @return self
     */
    protected function computeIntervalColumns(): self
    {
        $intervalConfig = $this->config->get('capsule.columns.interval_column_template');

        foreach (range(0, $this->intervalColumnsCount - 1) as $columnIndex) {
            $intervalConfig['name'] = $this->computeIntervalColumnName($columnIndex, $intervalConfig['pattern']);

            $this->buildAndStoreColumn(ColumnsDefinitions::INTERVAL_COLUMN, $intervalConfig);
        }

        return $this;
    }

    /**
     * Short function used to compute interval column name
     * @param int $columnIndex
     * @param string $patternName
     * @return string
     */
    protected function computeIntervalColumnName(int $columnIndex, string $patternName): string
    {
        return preg_replace('/:index:/', $columnIndex, $patternName);
    }

    /**
     * Function used to build column given config and store it in $columns Collection
     * @param string $type
     * @param array $config
     * @return $this
     */
    protected function buildAndStoreColumn(string $type, array $config)
    {
        /** @var ColumnFactory $columnFactory */
        $columnFactory = $this->columnFactory;

        $column = $columnFactory->build($type, $config);

        $this->columns->push($column);

        return $this;
    }
}