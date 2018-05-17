<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 23/04/18
 * Time: 17:53
 */

return [
    'defined_capsules' => [
        [
            'interval_elasticity' => 60,
            'capsule_elasticity' => 'daily',
        ]
    ],

    'columns' => [
        /**
         * Hash column is the primary key of the storage.
         * Some values are recommended to be not tempered with as of the nature of the column
         * Each hash, pivot and time columns are represented as an array containing:
         *    name => A string representing the column name
         *    data_type => The column type. See available column types in App\Definitions\Columns
         *    extra => Extra array containing the length for data type such as varchar(32) or int(11). Default: null
         *    index => See available indexes in App\Definitions\Columns. Default: "index"
         *    allow_null => If the column can be null
         */
        'hash_column' => [
            'name' => 'hash_id',
            'data_type' => 'string', //recommended
            'extra' => [
                'length' => 64, //recommended
            ],
            'index' => 'primary', //recommended
            'allow_null' => false //recommended
        ],
        /**
         * Pivot columns are the columns which we will group by
         * All pivot columns will be used in a unique index as to better enforce the reporting algorithm
         */
        'pivot_columns' => [
            'client' => [
                'name' => 'client',
                'data_type' => 'string',
                'extra' => [
                    'length' => 255,
                ],
                'index' => 'index', //recommended
                'allow_null' => false //recommended
            ],
            'carrier' => [
                'name' => 'carrier',
                'data_type' => 'string',
                'extra' => [
                    'length' => 255,
                ],
                'index' => 'index', //recommended
                'allow_null' => false //recommended
            ],
            'destination' => [
                'name' => 'destination',
                'data_type' => 'string',
                'extra' => [
                    'length' => 255,
                ],
                'index' => 'index', //recommended
                'allow_null' => false //recommended
            ],
        ],

        /**
         * Interval column contains only template values as interval column count depends on the storage config
         */
        'interval_column_template' => [
            'pattern' => 'interval_:index:', //recommended
            'data_type' => 'string', //recommended
            'extra' => [
                'length' => 255 //recommended
            ],
            'index' => null, //recommended
            'allow_null' => true //recommended
        ],

        'timestamp_columns' => [
            'create' => 'created_at',
            'update' => 'updated_at',
            'delete' => 'deleted_at'
        ]
    ],
];