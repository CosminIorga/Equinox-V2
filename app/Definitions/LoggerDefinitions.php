<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 14/09/17
 * Time: 14:39
 */

namespace Equinox\Definitions;

class LoggerDefinitions
{
    /**
     * Available channels
     */
    const DEFAULT_CHANNEL = 'default_channel';
    const GENERATION_STORAGE = 'generation';
    const MODIFY_DATA_CHANNEL = 'alter_data';
    const FETCH_DATA_CHANNEL = 'fetch_data';
    const GEARMAN_CHANNEL = 'gearman';

}