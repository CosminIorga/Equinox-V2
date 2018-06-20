<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 23/04/18
 * Time: 12:19
 */

namespace Equinox\Models\Capsule;


use Carbon\Carbon;
use Equinox\Exceptions\ModelException;
use Equinox\Factories\ColumnFactory;
use Equinox\Factories\RecordFactory;
use Equinox\Models\Capsule\Modules\ColumnsModule;
use Equinox\Models\Capsule\Modules\RecordsModule;
use Equinox\Models\NonPersistentModel;
use Equinox\Services\General\Config;
use Illuminate\Support\Collection;

/**
 * Class Capsule
 * @package Equinox\Models\Capsule
 * @property string $capsuleId
 * @property Collection $columns
 */
class Capsule extends NonPersistentModel
{
    use ColumnsModule;
    use RecordsModule;

    /**
     * The capsule elasticity
     * @var string
     */
    protected $capsuleElasticity;

    /**
     * The interval elasticity
     * @var string
     */
    protected $intervalElasticity;

    /**
     * The reference date
     * @var Carbon
     */
    protected $referenceDate;

    /**
     * The aggregate name
     * @var string
     */
    protected $aggregateName;

    /**
     * The capsule name
     * @var string
     */
    protected $capsuleId;

    /**
     * The number of interval columns the capsule has
     * @var int
     */
    protected $intervalColumnsCount;

    /**
     * The base reference date from which the capsule will start to save information
     * @var Carbon
     */
    protected $baseReferenceDate;

    /**
     * The column factory
     * @var ColumnFactory
     */
    protected $columnFactory;

    /**
     * The record factory
     * @var RecordFactory
     */
    protected $recordFactory;

    /**
     * The config service
     * @var Config
     */
    protected $config;

    /**
     * Capsule constructor.
     * @param string $capsuleElasticity
     * @param string $intervalElasticity
     * @param Carbon $referenceDate
     * @param string $aggregateName
     * @param Config $config
     * @param ColumnFactory $columnFactory
     * @param RecordFactory $recordFactory
     * @throws ModelException
     */
    public function __construct(
        string $capsuleElasticity,
        string $intervalElasticity,
        Carbon $referenceDate,
        string $aggregateName,
        Config $config,
        ColumnFactory $columnFactory,
        RecordFactory $recordFactory
    ) {
        $this->capsuleElasticity = $capsuleElasticity;
        $this->intervalElasticity = $intervalElasticity;
        $this->referenceDate = $referenceDate;
        $this->aggregateName = $aggregateName;

        $this->columnFactory = $columnFactory;
        $this->recordFactory = $recordFactory;

        $this->config = $config;

        $this->bootstrap();
    }

    /**
     * Bootstrap Capsule by computing columns etc
     * @throws ModelException
     */
    protected function bootstrap()
    {
        $this->computeCapsuleId()
            ->computeBaseReferenceDate()
            ->computeIntervalColumnsCount()
            ->computeColumns();
    }

    /**
     * Function used to compute the capsule name
     * @return Capsule
     * @throws ModelException
     */
    protected function computeCapsuleId(): self
    {
        $this->capsuleId = self::getUniqueCapsuleIdentifier(
            $this->referenceDate,
            $this->aggregateName,
            $this->capsuleElasticity
        );

        return $this;
    }

    /**
     * Funtion used to compute the base reference date
     * @return Capsule
     * @throws ModelException
     */
    protected function computeBaseReferenceDate(): self
    {
        switch ($this->capsuleElasticity) {
            case "daily":
                $this->baseReferenceDate = clone $this->referenceDate;

                $this->baseReferenceDate->setTime(0, 0, 0);
                break;
            case "weekly":
            case "monthly":
                //TODO: take referenceDate into consideration and check how many minutes the month has
                $this->baseReferenceDate = clone $this->referenceDate;
                break;
            default:
                throw new ModelException(ModelException::UNDEFINED_CAPSULE_ELASTICITY, [
                    'capsuleElasticity' => $this->capsuleElasticity,
                ]);
        }

        return $this;
    }

    /**
     * Function used to compute the number of interval columns the capsule can hold
     * @return Capsule
     * @throws ModelException
     */
    protected function computeIntervalColumnsCount(): self
    {
        //TODO: implement fast algorithm that determines number of intervals based on interval and capsule elasticities

        switch ($this->capsuleElasticity) {
            case "daily":
                $this->intervalColumnsCount = 24 * 60 / $this->intervalElasticity;
                break;
            case "weekly":
            case "monthly":
                //TODO: take referenceDate into consideration and check how many minutes the month has
                $this->intervalColumnsCount = 0;
                break;
            default:
                throw new ModelException(ModelException::UNDEFINED_CAPSULE_ELASTICITY, [
                    'capsuleElasticity' => $this->capsuleElasticity,
                ]);
        }

        return $this;
    }

    /**
     * Get the instance as an array.
     * @return array
     */
    public function toArray()
    {
        return [
            'capsuleElasticity' => $this->capsuleElasticity,
            'intervalElasticity' => $this->intervalElasticity,
            'referenceDate' => $this->referenceDate->toDateString(),
            'aggregateName' => $this->aggregateName,
            'capsuleName' => $this->capsuleId,
            'intervalColumnCount' => $this->intervalColumnsCount,
            'columns' => $this->columns->toArray(),
        ];
    }

    /**
     * Function used to determine intervalColumn name given reference date
     * @param Carbon $recordDate
     * @return string
     */
    public function getColumnNameByReferenceDate(Carbon $recordDate): string
    {
        /* Compute minutes between Capsule baseReferenceDate and given recordDate */
        $minutesBetweenDates = $this->baseReferenceDate->diffInMinutes($recordDate);

        /* Divide by intervalElasticity to determine columnIndex in which data should reside */
        $columnIndex = floor($minutesBetweenDates / $this->intervalElasticity);

        /* Get intervalColumnNamePattern */
        $intervalColumnNamePattern = $this->config->get('capsule.columns.interval_column_template.pattern');

        return $this->computeIntervalColumnName($columnIndex, $intervalColumnNamePattern);
    }

    /**
     * Static function used to retrieve a unique capsule identifier
     * @param Carbon $referenceDate
     * @param string $aggregateName
     * @param string $capsuleElasticity
     * @return string
     * @throws ModelException
     */
    public static function getUniqueCapsuleIdentifier(
        Carbon $referenceDate,
        string $aggregateName,
        string $capsuleElasticity
    ): string {
        switch ($capsuleElasticity) {
            case "daily":
                return "Daily_" . $referenceDate->format('Y_m_d') . "_Agg_" . $aggregateName;
                break;
            case "weekly":
            case "monthly":
                return "Monthly";
                break;
            default:
                throw new ModelException(ModelException::UNDEFINED_CAPSULE_ELASTICITY, [
                    'capsuleElasticity' => $capsuleElasticity,
                ]);
        }
    }
}