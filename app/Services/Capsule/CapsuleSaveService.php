<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 19/01/18
 * Time: 17:32
 */

namespace Equinox\Services\Capsule;

use Equinox\Definitions\LoggerDefinitions;
use Equinox\Models\Capsule\Capsule;
use Equinox\Models\Capsule\Column;
use Equinox\Repositories\CapsuleRepository;
use Equinox\Services\General\BaseService;
use Equinox\Services\General\Config;
use Equinox\Services\General\DebuggingService;
use Equinox\Services\General\LoggerService;
use Illuminate\Database\QueryException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;


/**
 * Class CapsuleSaveService
 * @package Equinox\Services\Capsule
 */
class CapsuleSaveService extends BaseService
{
    /**
     * The capsule repository
     * @var CapsuleRepository
     */
    protected $capsuleRepository;

    /**
     * CapsuleService constructor.
     * @param CapsuleRepository $capsuleRepository
     * @param LoggerService $loggerService
     * @param DebuggingService $debuggingService
     * @param Config $config
     */
    public function __construct(
        CapsuleRepository $capsuleRepository,
        LoggerService $loggerService,
        DebuggingService $debuggingService,
        Config $config
    ) {
        parent::__construct($loggerService, $debuggingService, $config);

        $this->capsuleRepository = $capsuleRepository;
    }

    /**
     * Function used to generate a new storage
     * @param Capsule $capsule
     * @return CapsuleSaveService
     * @throws \Exception
     */
    public function saveCapsule(Capsule $capsule): self
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
     * @return CapsuleSaveService
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