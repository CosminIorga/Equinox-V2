<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 13/09/17
 * Time: 14:24
 */

namespace Equinox\Services\Repositories;


use Carbon\Carbon;
use Equinox\Definitions\LoggerDefinitions;
use Equinox\Models\Capsule\Capsule;
use Equinox\Repositories\DataRepository;
use Equinox\Services\Capsule\CapsuleGenerateService;
use Equinox\Services\General\BaseService;
use Equinox\Services\General\Config;
use Equinox\Services\General\DebuggingService;
use Equinox\Services\General\LoggerService;

class DataMapService extends BaseService
{

    /**
     * The Data repository
     * @var DataRepository
     */
    protected $dataRepository;

    /**
     * The capsule service
     * @var CapsuleGenerateService
     */
    protected $capsuleGenerateService;

    /**
     * DataService constructor.
     * @param CapsuleGenerateService $capsuleGenerateService
     * @param DataRepository $dataRepository
     * @param LoggerService $loggerService
     * @param DebuggingService $debuggingService
     * @param Config $config
     */
    public function __construct(
        CapsuleGenerateService $capsuleGenerateService,
        DataRepository $dataRepository,
        LoggerService $loggerService,
        DebuggingService $debuggingService,
        Config $config
    ) {
        parent::__construct($loggerService, $debuggingService, $config);

        $this->dataRepository = $dataRepository;
        $this->capsuleGenerateService = $capsuleGenerateService;
    }

    /**
     * Function used to group given records to a specific Capsule and return the grouped information
     * @param array $data
     * @return array
     */
    public function groupRecordsByDefinedCapsules(array $data): array
    {
        $mapping = [];

        $definedCapsulesConfig = $this->config->getDefinedCapsulesConfig();
        $dataConfig = $this->config->get('data');

        foreach ($definedCapsulesConfig as $definedCapsuleConfig) {
            $capsuleElasticity = $definedCapsuleConfig['capsule_elasticity'];

            foreach ($data as $record) {
                $recordComponents = $this->splitRecord($record, $dataConfig);

                foreach ($recordComponents['aggregates'] as $recordAggregateName => $recordAggregate) {
                    $uniqueCapsuleID = Capsule::getUniqueCapsuleIdentifier(
                        $recordComponents['timestamp_key'],
                        $recordAggregateName,
                        $capsuleElasticity
                    );

                    $dataToMap = $this->computeDataToMap(
                        $recordComponents,
                        $recordAggregateName
                    );

                    $this->mapAndGroupRecord(
                        $uniqueCapsuleID,
                        $definedCapsuleConfig,
                        [
                            'output_name' => $recordAggregateName,
                        ],
                        $dataToMap,
                        $mapping
                    );
                }
            }
        }

        return $mapping;
    }

    /**
     * Function used to split record into its main divisions like timestamp key, pivots and aggregates
     * @param array $record
     * @param array $dataConfig
     * @return array
     */
    protected function splitRecord(array $record, array $dataConfig): array
    {
        return [
            'timestamp_key' => $this->extractTimestampKey($record, $dataConfig),
            'pivots' => $this->extractPivotKeys($record, $dataConfig),
            "aggregates" => $this->extractAggregateKeys($record, $dataConfig),
        ];
    }

    /**
     * Function used to extract the timestamp key
     * @param array $record
     * @param array $dataConfig
     * @return Carbon
     */
    protected function extractTimestampKey(array $record, array $dataConfig): Carbon
    {
        return new Carbon($record[$dataConfig['timestamp_key']['input_name']]);
    }

    /**
     * Function used to extract the pivot keys
     * @param array $record
     * @param array $dataConfig
     * @return array
     */
    protected function extractPivotKeys(array $record, array $dataConfig): array
    {
        $pivots = [];

        foreach ($dataConfig['pivot_keys'] as $pivotConfig) {
            $pivots[$pivotConfig['output_name']] = $record[$pivotConfig['input_name']];
        }

        return $pivots;
    }

    /**
     * Function used to extract the aggregate keys
     * @param array $record
     * @param array $dataConfig
     * @return array
     */
    protected function extractAggregateKeys(array $record, array $dataConfig): array
    {
        $aggregatesConfig = array_merge(
            $dataConfig['interval_column_aggregates'],
            $dataConfig['interval_column_meta_aggregates']
        );

        $aggregates = [];

        foreach ($aggregatesConfig as $aggregateConfig) {
            $aggregates[$aggregateConfig['output_name']] = is_null($aggregateConfig['input_name']) ?
                1 : $record[$aggregateConfig['input_name']];
        }

        return $aggregates;
    }

    /**
     * Short function used to compute the data to be inserted after mapping
     * @param array $recordComponents
     * @param $recordAggregateName
     * @return array
     */
    protected function computeDataToMap(array $recordComponents, $recordAggregateName): array
    {
        $recordComponents['aggregate_value'] = $recordComponents['aggregates'][$recordAggregateName];

        return $recordComponents;
    }

    /**
     * Function used to group record to a specific Capsule
     * @param string $uniqueCapsuleID
     * @param array $definedCapsuleConfig
     * @param array $aggregatesConfig
     * @param array $recordComponents
     * @param array $mapping
     * @return DataMapService
     */
    protected function mapAndGroupRecord(
        string $uniqueCapsuleID,
        array $definedCapsuleConfig,
        array $aggregatesConfig,
        array $recordComponents,
        array &$mapping
    ): self {
        if (! array_key_exists($uniqueCapsuleID, $mapping)) {
            $mapping[$uniqueCapsuleID]['capsule'] = $this->capsuleGenerateService->createOneCapsule(
                $definedCapsuleConfig,
                $aggregatesConfig,
                $recordComponents['timestamp_key']
            );
        }

        $mapping[$uniqueCapsuleID]['records'][] = $recordComponents;

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