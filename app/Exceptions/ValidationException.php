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
class ValidationException extends Exception
{
    const VALIDATION_FAILED = 'Validation failed';
}