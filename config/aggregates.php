<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 26/04/18
 * Time: 14:18
 */

return [
    /**
     * Timestamp key is used to compute the storage and interval needed
     */
    'timestamp_key' => [
        'input_name' => 'start_date',
    ],

    /**
     * Pivot keys ... TODO: Explain
     */
    'pivot_keys' => [
        'client' => [
            'input_name' => 'client',
            'output_name' => 'client',
        ],
        'carrier' => [
            'input_name' => 'carrier',
            'output_name' => 'carrier',
        ],
        'destination' => [
            'input_name' => 'destination',
            'output_name' => 'destination',
        ],
    ],

    /**
     * Hash key ... TODO: Explain
     */
    'hash_key' => [
        'output_name' => 'hash_id',
    ],

    /**
     * Interval column aggregates contain information regarding the data that will be stored in interval columns
     * Each aggregate is represented as a key => value pair as follows:
     *      input_name => from which key should it fetch the data
     *      aggregate_key => in which key it should place the parsed data
     *      input_function => function to apply to input_name
     *      output_functions => array containing available operations on the key
     *      extra => output formatting options such as:
     *          round => round output to X decimals
     */
    'interval_column_aggregates' => [
        'interval_duration' => [
            'input_name' => 'duration',
            'aggregate_key' => 'interval_duration',
            'input_function' => 'sum',
            'output_functions' => [
                'sum',
                'max',
                'min',
            ],
            'extra' => [
                'round' => 4,
            ],
        ],
        'interval_cost' => [
            'input_name' => 'cost',
            'aggregate_key' => 'interval_cost',
            'input_function' => 'sum',
            'output_functions' => [
                'sum',
                'max',
                'min',
            ],
            'extra' => [
                'round' => 4,
            ],
        ],
/*        'interval_records' => [
            'input_name' => null,
            'aggregate_key' => 'interval_records',
            'input_function' => 'count',
            'output_functions' => [
                'sum',
                'max',
                'min',
            ],
        ],
        'interval_full_records' => [
            'input_name' => 'is_full_record',
            'aggregate_key' => 'interval_full_records',
            'input_function' => 'count',
            'output_functions' => [
                'sum',
            ],
        ],*/
    ],
    /**
     * Meta aggregates contain information used internally by the application
     * Respects same format as a normal aggregate column
     */
    'interval_column_meta_aggregates' => [
        'meta_record_count' => [
            'input_name' => null,
            'aggregate_key' => 'meta_record_count',
            'input_function' => 'count',
            'output_functions' => [
                'sum',
            ],
        ],
    ],
];