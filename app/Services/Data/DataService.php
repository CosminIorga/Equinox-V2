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
use Equinox\Services\Data\CapsuleService;
use Equinox\Services\General\BaseService;
use Equinox\Services\General\Config;
use Equinox\Services\General\DebuggingService;
use Equinox\Services\General\LoggerService;

class DataService extends BaseService
{

    /**
     * The Data repository
     * @var DataRepository
     */
    protected $dataRepository;

    /**
     * The capsule service
     * @var CapsuleService
     */
    protected $capsuleService;

    /**
     * DataService constructor.
     * @param CapsuleService $capsuleService
     * @param DataRepository $dataRepository
     * @param LoggerService $loggerService
     * @param DebuggingService $debuggingService
     * @param Config $config
     */
    public function __construct(
        CapsuleService $capsuleService,
        DataRepository $dataRepository,
        LoggerService $loggerService,
        DebuggingService $debuggingService,
        Config $config
    ) {
        parent::__construct($loggerService, $debuggingService, $config);

        $this->dataRepository = $dataRepository;
        $this->capsuleService = $capsuleService;
    }

    /**
     * Function used to group given records to a specific Capsule and return the grouped information
     * @param array $data
     * @return array
     */
    public function groupRecordsByDefinedCapsules(array $data): array
    {
        $mapping = [];
        $definedCapsulesConfig = $this->getDefinedCapsulesConfig();
        $timestampKey = $this->config->get('aggregates.timestamp_key.input_name');

        foreach ($definedCapsulesConfig as $config) {
            $capsuleElasticity = $config['capsule_elasticity'];

            foreach ($data as $record) {
                $recordDate = new Carbon($record[$timestampKey]);
                $uniqueCapsuleID = Capsule::getUniqueCapsuleIdentifier(
                    $recordDate,
                    $capsuleElasticity
                );

                $this->mapAndGroupRecord($uniqueCapsuleID, $record, $recordDate, $mapping);
            }
        }

        return $mapping;
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
     * Function used to group record to a specific Capsule
     * @param string $uniqueCapsuleID
     * @param array $record
     * @param Carbon $recordDate
     * @param array $mapping
     * @return DataService
     */
    protected function mapAndGroupRecord(
        string $uniqueCapsuleID,
        array $record,
        Carbon $recordDate,
        array &$mapping
    ): self {
        if (! array_key_exists($uniqueCapsuleID, $mapping)) {
            $mapping[$uniqueCapsuleID]['capsule'] = $this->capsuleService->createOneCapsule($recordDate);
        }

        $mapping[$uniqueCapsuleID]['records'][] = $record;


        return $this;
    }


    public function modifyData(array $data): self
    {

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