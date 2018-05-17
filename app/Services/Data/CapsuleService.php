<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 19/01/18
 * Time: 17:32
 */

namespace Equinox\Services\Data;

use Carbon\Carbon;
use Equinox\Definitions\LoggerDefinitions;
use Equinox\Factories\CapsuleFactory;
use Equinox\Models\Capsule\Capsule;
use Equinox\Models\Capsule\Column;
use Equinox\Repositories\CapsuleRepository;
use Equinox\Services\General\BaseService;
use Equinox\Services\General\Config;
use Equinox\Services\General\DebuggingService;
use Equinox\Services\General\LoggerService;
use Illuminate\Database\QueryException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;


/**
 * Class CapsuleService
 * @package Equinox\Services\Structure
 */
class CapsuleService extends BaseService
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
     * @param CapsuleFactory $capsuleFactory
     * @param CapsuleRepository $capsuleRepository
     * @param LoggerService $loggerService
     * @param DebuggingService $debuggingService
     * @param Config $config
     */
    public function __construct(
        CapsuleFactory $capsuleFactory,
        CapsuleRepository $capsuleRepository,
        LoggerService $loggerService,
        DebuggingService $debuggingService,
        Config $config
    ) {
        parent::__construct($loggerService, $debuggingService, $config);

        $this->capsuleFactory = $capsuleFactory;
        $this->capsuleRepository = $capsuleRepository;
    }

    /**
     * Function used to create all defined capsules given reference date
     * @param Carbon $referenceDate
     * @return Collection
     */
    public function createCapsulesByReferenceDate(Carbon $referenceDate): Collection
    {
        $startTime = $this->debuggingService->startTimer();

        $capsules = collect();

        $capsulesConfig = $this->getDefinedCapsulesConfig();
        $aggregatesConfig = $this->getDefinedAggregatesConfig();

        foreach ($capsulesConfig as $capsuleConfig) {
            foreach ($aggregatesConfig as $aggregateConfig) {
                $capsule = $this->createOneCapsule(
                    $capsuleConfig,
                    $aggregateConfig,
                    $referenceDate
                );

                $capsules->push($capsule);
            }
        }

        $elapsed = $this->debuggingService->endTimer($startTime);
        $this->loggerService->debugTimer($elapsed, "Create capsule");

        return $capsules;
    }

    /**
     * Short function used to build a capsule
     * @param array $capsuleConfig
     * @param array $aggregateConfig
     * @param Carbon $referenceDate
     * @return Capsule
     */
    public function createOneCapsule(
        array $capsuleConfig,
        array $aggregateConfig,
        Carbon $referenceDate
    ): Capsule {
        return $this->capsuleFactory->build(
            $capsuleConfig['capsule_elasticity'],
            $capsuleConfig['interval_elasticity'],
            $referenceDate,
            $aggregateConfig['aggregate_key']
        );
    }

    /**
     * Function used to retrieve the defined capsules config
     * @return array
     */
    protected function getDefinedCapsulesConfig(): array
    {
        return $this->config->get('capsule.defined_capsules');
    }

    /**
     * Function used to retrieve the defined aggregates config
     * @return array
     */
    protected function getDefinedAggregatesConfig(): array
    {
        return array_merge(
            $this->config->get('aggregates.interval_column_aggregates'),
            $this->config->get('aggregates.interval_column_meta_aggregates')
        );
    }

    /**
     * Helper function used to generate multiple capsules
     * @param Collection $capsules
     * @return CapsuleService
     */
    public function generateCapsules(Collection $capsules): self
    {
        $capsules->map([$this, 'generateCapsule']);

        return $this;
    }

    /**
     * Function used to generate a new storage
     * @param Capsule $capsule
     * @return CapsuleService
     * @throws \Exception
     */
    public function generateCapsule(Capsule $capsule): self
    {
        $this->loggerService->debug("Creating capsule " . $capsule->capsuleId);

        /* Create storage */
        try {
            $this->capsuleRepository->createCapsuleFromClosure(
                $capsule->capsuleId,
                $this->createCapsuleGenerator($capsule)
            );
        } catch (QueryException $exception) {
            $this->loggerService->warning("Table {$capsule->capsuleId} already exists");
        }

        return $this;
    }

    /**
     * Function used to return another function that creates the capsule
     * @param Capsule $capsule
     * @return \Closure
     */
    protected function createCapsuleGenerator(Capsule $capsule): \Closure
    {
        return function (Blueprint $table) use ($capsule) {
            /* Set table engine */
            $table->engine = 'InnoDB';

            /* Add the rest of the columns */
            $capsule->columns->each(function (Column $columnModel) use (&$table) {
                $column = $table->addColumn(
                    $columnModel->dataType,
                    $columnModel->name,
                    $columnModel->extra
                );

                if ($columnModel->allowNull) {
                    /* @noinspection PhpUndefinedMethodInspection */
                    $column->nullable();
                }

                if ($columnModel->index) {
                    $table->{$columnModel->index}($columnModel->name);
                }
            });

            /* Add timestamp columns such as created_at, updated_at and deleted_at*/
            /* @noinspection PhpUndefinedMethodInspection */
            $table->timestamp($this->config->get('capsule.columns.timestamp_columns.create'))
                ->nullable()
                ->default(DB::raw('CURRENT_TIMESTAMP'));
            /* @noinspection PhpUndefinedMethodInspection */
            $table->timestamp($this->config->get('capsule.columns.timestamp_columns.update'))
                ->nullable()
                ->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            /* @noinspection PhpUndefinedMethodInspection */
            $table->timestamp($this->config->get('capsule.columns.timestamp_columns.delete'))
                ->nullable();
        };
    }

    /**
     * Short function used to drop a capsule
     * @param string $capsuleName
     * @return CapsuleService
     */
    public function dropCapsule(string $capsuleName): self
    {
        $this->capsuleRepository->dropCapsuleIfExists($capsuleName);

        return $this;
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