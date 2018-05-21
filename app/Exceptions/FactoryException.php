<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 13/09/17
 * Time: 18:14
 */

namespace Equinox\Exceptions;

/**
 * Class ModelException
 * @package Equinox\Exceptions
 */
class FactoryException extends Exception
{
    const INVALID_COLUMN_TYPE_RECEIVED = 'Invalid column type received';
}