<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 03/07/17
 * Time: 13:03
 */

namespace Equinox\Models;


use Equinox\Exceptions\ValidationException;
use Equinox\Services\Data\DataFetchService;
use Equinox\Services\General\Config;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Class FetchDataModel
 * @package Equinox\Models
 * @property array $columns
 * @property string $interval_start
 * @property string $interval_end
 * @property array $where
 * @property array $group_by
 * @property array $order_by
 */
class FetchDataModel extends NonPersistentModel
{
    const INTERVAL_START = 'interval_start';
    const INTERVAL_END = 'interval_end';
    const COLUMNS = 'columns';
    const WHERE_CLAUSE = 'where';
    const GROUP_CLAUSE = 'group_by';
    const ORDER_CLAUSE = 'order_by';

    const COLUMN_NAME = 'column_name';
    const FUNCTION_NAME = 'function_name';
    const COLUMN_ALIAS = 'column_alias';


    /**
     * Array used to store the information necessary to fetch the reporting data
     * @var array
     */
    protected $fetchData = [];

    /**
     * The config getter
     * @var Config
     */
    protected $config;

    /**
     * The Data Service
     * @var DataFetchService
     */
    protected $dataFetchService;

    /**
     * FetchDataModel constructor.
     * @param Config $config
     * @param DataFetchService $dataFetchService
     */
    public function __construct(
        Config $config,
        DataFetchService $dataFetchService
    ) {
        $this->config = $config;
        $this->dataFetchService = $dataFetchService;
    }

    /**
     * Initialize query builder
     * @return $this
     */
    public function select()
    {
        $this->fetchData = [
            self::INTERVAL_START => null,
            self::INTERVAL_END => null,
            self::COLUMNS => [],
            self::WHERE_CLAUSE => null,
            self::GROUP_CLAUSE => null,
            self::ORDER_CLAUSE => null,
        ];

        return $this;
    }

    /**
     * Function used to set a fetch column
     * @param string $column
     * @param string $columnAlias
     * @param string $function
     * @return FetchDataModel
     * @throws ValidationException
     */
    public function column(string $column, string $columnAlias, string $function): self
    {
        $this->validateColumnData($column, $columnAlias, $function);

        $this->fetchData[self::COLUMNS][$columnAlias] = [
            self::COLUMN_NAME => $column,
            self::FUNCTION_NAME => $function,
            self::COLUMN_ALIAS => $columnAlias,
        ];

        return $this;
    }

    /**
     * Getter for column attribute
     * @return array
     */
    protected function getColumnsAttribute()
    {
        return $this->fetchData[self::COLUMNS];
    }

    /**
     * Function used to validate column data
     * @param string $column
     * @param string $columnAlias
     * @param string $function
     * @return FetchDataModel
     * @throws ValidationException
     */
    protected function validateColumnData(string $column, string $columnAlias, string $function): self
    {
        $validator = Validator::make(
            [
                'column' => $column,
                'columnAlias' => $columnAlias,
                'function' => $function,
            ],
            [
                'column' => Rule::in(array_keys($this->config->get('data.interval_column_aggregates'))),
                'columnAlias' => [
                    'string',
                ],
                'function' => Rule::in($this->config->get("data.interval_column_aggregates.{$column}.output_function")),
            ]
        );

        if ($validator->fails()) {
            throw new ValidationException(
                ValidationException::VALIDATION_FAILED,
                $validator->errors()->toArray()
            );
        }

        return $this;
    }

    /**
     * Function used to set the start of fetch interval
     * @param string $startDate
     * @return FetchDataModel
     * @throws ValidationException
     */
    public function fromInterval(string $startDate): self
    {
        $this->validateInterval($startDate);

        $this->fetchData[self::INTERVAL_START] = $startDate;

        return $this;
    }

    /**
     * Getter for start interval
     * @return string
     */
    protected function getStartIntervalAttribute()
    {
        return $this->fetchData[self::INTERVAL_START];
    }

    /**
     * Function used to set the end of fetch interval
     * @param string $endDate
     * @return FetchDataModel
     * @throws ValidationException
     */
    public function toInterval(string $endDate): self
    {
        $this->validateInterval($endDate);

        $this->fetchData[self::INTERVAL_END] = $endDate;

        return $this;
    }

    /**
     * Getter for start interval
     * @return string
     */
    protected function getEndIntervalAttribute()
    {
        return $this->fetchData[self::INTERVAL_END];
    }

    /**
     * Function used to validate interval
     * @param string $interval
     * @return FetchDataModel
     * @throws ValidationException
     */
    protected function validateInterval(string $interval): self
    {
        $validator = Validator::make(
            [
                'interval' => $interval,
            ],
            [
                'interval' => [
                    'required',
                    'date_format:Y-m-d H:i:s',
                ],
            ]
        );

        if ($validator->fails()) {
            throw new ValidationException(
                ValidationException::VALIDATION_FAILED,
                $validator->errors()->toArray()
            );
        }

        return $this;
    }

    /**
     * Function used to set a where clause
     * @param array $whereClause
     * @return FetchDataModel
     */
    public function where(array $whereClause): self
    {
        $this->fetchData[self::WHERE_CLAUSE] = $whereClause;

        return $this;
    }

    /**
     * Getter for where clause
     * @return array
     */
    protected function getWhereAttribute()
    {
        return $this->fetchData[self::WHERE_CLAUSE];
    }

    /**
     * Function used to set the group by clause
     * @param array $groupByClause
     * @return FetchDataModel
     */
    public function groupBy(array $groupByClause): self
    {
        $this->fetchData[self::GROUP_CLAUSE] = $groupByClause;

        return $this;
    }

    /**
     * Getter for where clause
     * @return array
     */
    protected function getGroupByAttribute()
    {
        return $this->fetchData[self::GROUP_CLAUSE];
    }

    /**
     * Function used to set the order by clause
     * @param array $orderByClause
     * @return FetchDataModel
     */
    public function orderBy(array $orderByClause): self
    {
        $this->fetchData[self::ORDER_CLAUSE] = $orderByClause;

        return $this;
    }

    /**
     * Getter for where clause
     * @return array
     */
    protected function getOrderByAttribute()
    {
        return $this->fetchData[self::ORDER_CLAUSE];
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->fetchData;
    }
}