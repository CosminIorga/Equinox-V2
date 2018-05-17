<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 26/06/17
 * Time: 15:13
 */

namespace Equinox\Exceptions;

/**
 * Class DefaultException
 * @package Equinox\Exceptions
 */
abstract class DefaultException extends \Exception
{
    /**
     * Array used to store the context of an exception
     * @var array
     */
    protected $context = [];

    /**
     * DefaultException constructor.
     * @param string $message
     * @param array $context
     * @param int $code
     */
    public function __construct(string $message, array $context = [], $code = 0)
    {
        $this->context = $context;

        parent::__construct($message, $code, null);
    }

    /**
     * Getter for context
     * @return array
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * Getter for message and context
     * @return string
     */
    public function getFullMessage(): string
    {
        return $this->getMessage() . " CONTEXT: " . json_encode($this->getContext());
    }
}