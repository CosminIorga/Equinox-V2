<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 11/09/17
 * Time: 12:40
 */

namespace Equinox\Factories;


use Equinox\Models\Capsule\Record;

class RecordFactory
{
    /**
     * Column factory builder
     * @return Record
     */
    public function build(): Record
    {
        return new Record();
    }
}