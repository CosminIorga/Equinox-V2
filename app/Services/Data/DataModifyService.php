<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 22/05/18
 * Time: 10:21
 */

namespace Equinox\Services\Data;


use Equinox\Models\Capsule\Capsule;
use Equinox\Models\Capsule\Record;
use Equinox\Repositories\DataRepository;
use Equinox\Services\General\BaseService;
use Equinox\Services\General\Config;
use Equinox\Services\General\DebuggingService;
use Equinox\Services\General\LoggerService;

/**
 * Class DataModifyService
 * @package Equinox\Services\Data
 */
class DataModifyService extends BaseService
{
    /**
     * The data repository
     * @var DataRepository
     */
    protected $dataRepository;

    /**
     * DataModifyService constructor.
     * @param LoggerService $loggerGenerateService
     * @param DebuggingService $debuggingService
     * @param Config $config
     * @param DataRepository $dataRepository
     */
    public function __construct(
        LoggerService $loggerGenerateService,
        DebuggingService $debuggingService,
        Config $config,
        DataRepository $dataRepository
    ) {
        parent::__construct($loggerGenerateService, $debuggingService, $config);

        $this->dataRepository = $dataRepository;
    }


    public function modifyRecords(array $mapping): self
    {
        foreach ($mapping as $capsuleName => $mapData) {
            $parsedRecords = collect();

            /** @var Capsule $capsule */
            $capsule = $mapData['capsule'];
            /** @var array $capsuleRecords */
            $capsuleRecords = $mapData['records'];

            $startTime = $this->debuggingService->startTimer();
            foreach ($capsuleRecords as $record) {
                $recordStructure = clone $capsule->extractRecord();

                $this->computeDataRecord($record, $recordStructure, $capsule);

                $parsedRecords->push($recordStructure);
            }

            $elapsed = $this->debuggingService->endTimer($startTime);

            echo $this->debuggingService->dumpTimerMessage($elapsed) . PHP_EOL;

            $this->dataRepository->modifyCapsuleData($capsule, $parsedRecords);
        }


        return $this;
    }

    /**
     * Function used to map the given record to a dataRecord for ease of inserting it into database
     * @param array $record
     * @param Record $recordStructure
     * @param Capsule $capsule
     * @return array
     */
    protected function computeDataRecord(array $record, Record $recordStructure, Capsule $capsule): array
    {
        $this->setHashValue($record, $recordStructure)
            ->setPivotValues($record, $recordStructure)
            ->setIntervalValues($record, $recordStructure, $capsule);

        return [];
    }

    /**
     * Set hash value based on given record
     * @param array $record
     * @param Record $recordStructure
     * @return DataModifyService
     */
    protected function setHashValue(array $record, Record $recordStructure): self
    {
        $recordStructure->setHashValue($record['pivots']);

        return $this;
    }

    /**
     * Set pivot values based on given record
     * @param array $record
     * @param Record $recordStructure
     * @return DataModifyService
     */
    protected function setPivotValues(array $record, Record $recordStructure): self
    {
        foreach ($record['pivots'] as $pivotKey => $pivotValue) {
            $recordStructure->setPivotValue($pivotKey, $pivotValue);
        }

        return $this;
    }

    /**
     * Set interval value based on given record
     * @param array $record
     * @param Record $recordStructure
     * @param Capsule $capsule
     * @return DataModifyService
     */
    protected function setIntervalValues(array $record, Record $recordStructure, Capsule $capsule): self
    {
        $intervalKey = $capsule->getColumnNameByReferenceDate($record['timestamp_key']);

        $recordStructure->setIntervalValue($intervalKey, $record['aggregate_value']);

        return $this;
    }

    /**
     * Short function used to return the logger channel
     * @return string
     */
    protected function getLoggerChannel(): string
    {
        // TODO: Implement getLoggerChannel() method.
        return "";
    }
}