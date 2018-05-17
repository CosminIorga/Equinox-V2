<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 23/04/18
 * Time: 14:09
 */

namespace Equinox\Definitions;


class ColumnsDefinitions
{
    /**
     * Available column types
     */
    const HASH_COLUMN = 'hash';
    const PIVOT_COLUMN = 'pivot';
    const TIME_COLUMN = 'time';
    const INTERVAL_COLUMN = 'interval';

    const COLUMN_TYPES = [
        self::HASH_COLUMN,
        self::PIVOT_COLUMN,
        self::TIME_COLUMN,
        self::INTERVAL_COLUMN,
    ];

    /**
     * Available column data types
     */
    const STRING_DATA_TYPE = 'string';
    const INT_DATA_TYPE = 'integer';
    const JSON_DATA_TYPE = 'json';
    const DATETIME_DATA_TYPE = 'datetime';
    const TIMESTAMP_DATA_TYPE = 'timestamp';

    const DATA_TYPES = [
        self::INT_DATA_TYPE,
        self::STRING_DATA_TYPE,
        self::JSON_DATA_TYPE,
        self::DATETIME_DATA_TYPE,
        self::TIMESTAMP_DATA_TYPE,
    ];

    /**
     * Available column indexes
     */
    const PRIMARY_INDEX = 'primary';
    const UNIQUE_INDEX = 'unique';
    const SIMPLE_INDEX = 'index';
    const NO_INDEX = null;

    const INDEXES = [
        ColumnsDefinitions::SIMPLE_INDEX,
        ColumnsDefinitions::UNIQUE_INDEX,
        ColumnsDefinitions::PRIMARY_INDEX,
        ColumnsDefinitions::NO_INDEX,
    ];

    /**
     * Timestamps
     */
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';
}