<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 23/01/18
 * Time: 17:25
 */

namespace Equinox\Services\General;


/**
 * Class BaseService
 * @package Equinox\Services\General
 */
abstract class BaseService
{
    /**
     * The logger service
     * @var LoggerService
     */
    protected $loggerService;

    /**
     * The debugging service
     * @var DebuggingService
     */
    protected $debuggingService;

    /**
     * The config getter
     * @var Config
     */
    protected $config;

    /**
     * BaseService constructor.
     * @param LoggerService $loggerService
     * @param DebuggingService $debuggingService
     * @param Config $config
     */
    public function __construct(
        LoggerService $loggerService,
        DebuggingService $debuggingService,
        Config $config
    ) {
        /* Instantiate fields */
        $this->loggerService = $loggerService;
        $this->debuggingService = $debuggingService;
        $this->config = $config;

        /* Initialize variables */
        $this->loggerService->setChannel($this->getLoggerChannel());
    }

    /**
     * Short function used to return the logger channel
     * @return string
     */
    abstract protected function getLoggerChannel(): string;
}