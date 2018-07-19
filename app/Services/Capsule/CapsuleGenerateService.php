<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 19/01/18
 * Time: 17:32
 */

namespace Equinox\Services\Capsule;

use Carbon\Carbon;
use Equinox\Definitions\LoggerDefinitions;
use Equinox\Factories\CapsuleFactory;
use Equinox\Repositories\CapsuleRepository;
use Equinox\Services\General\BaseService;
use Equinox\Services\General\Config;
use Equinox\Services\General\DebuggingService;
use Equinox\Services\General\LoggerService;


/**
 * Class CapsuleGenerateService
 * @package Equinox\Services\Capsule
 */
class CapsuleGenerateService extends BaseService
{
    /**
     * The capsule factory
     * @var CapsuleFactory
     */
    protected $capsuleFactory;

    /**
     * The capsule repository
     * @var CapsuleRepository
     */
    protected $capsuleRepository;

    /**
     * CapsuleService constructor.
     * @param CapsuleFactory $capsuleGenerateFactory
     * @param CapsuleRepository $capsuleRepository
     * @param LoggerService $loggerService
     * @param DebuggingService $debuggingService
     * @param Config $config
     */
    public function __construct(
        CapsuleFactory $capsuleGenerateFactory,
        CapsuleRepository $capsuleRepository,
        LoggerService $loggerService,
        DebuggingService $debuggingService,
        Config $config
    ) {
        parent::__construct($loggerService, $debuggingService, $config);

        $this->capsuleFactory = $capsuleGenerateFactory;
        $this->capsuleRepository = $capsuleRepository;
    }

    /**
     * Function used to create all defined capsules given reference date
     * @param Carbon $referenceDate
     * @return array
     * @throws \Equinox\Exceptions\ModelException
     */
    public function createCapsulesByReferenceDate(Carbon $referenceDate): array
    {
        $startTime = $this->debuggingService->startTimer();

        $capsules = [];

        $capsulesConfig = $this->config->getDefinedCapsulesConfig();
        $aggregatesConfig = $this->config->getDefinedAggregatesConfig();

        foreach ($capsulesConfig as $capsuleConfig) {
            foreach ($aggregatesConfig as $aggregateConfig) {
                $capsule = $this->capsuleFactory->build(
                    $capsuleConfig['capsule_elasticity'],
                    $capsuleConfig['interval_elasticity'],
                    $referenceDate,
                    $aggregateConfig['output_name']
                );

                $capsules[] = $capsule;
            }
        }

        $elapsed = $this->debuggingService->endTimer($startTime);
        $this->loggerService->debugTimer($elapsed, "Create capsule");

        return $capsules;
    }

    /**
     * Short function used to return the logger channel
     * @return string
     */
    protected function getLoggerChannel(): string
    {
        return LoggerDefinitions::GENERATION_STORAGE;
    }
}