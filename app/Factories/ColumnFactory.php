<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 11/09/17
 * Time: 12:40
 */

namespace Equinox\Factories;


use Equinox\Models\Capsule\Column;

class ColumnFactory
{
    /**
     * Column factory builder
     * @param string $type
     * @param array $config
     * @return Column
     */
    public function build(string $type, array $config): Column
    {
        return new Column($type, $config);
    }
}