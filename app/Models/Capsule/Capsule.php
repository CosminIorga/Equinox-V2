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
use Equinox\Models\Capsule\Modules\ColumnsModule;
use Equinox\Models\NonPersistentModel;
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
     * The column factory
     * @var ColumnFactory
     */
    protected $columnFactory;

    /**
     * Capsule constructor.
     * @param string $capsuleElasticity
     * @param string $intervalElasticity
     * @param Carbon $referenceDate
     * @param string $aggregateName
     * @param ColumnFactory $columnFactory
     */
    public function __construct(
        string $capsuleElasticity,
        string $intervalElasticity,
        Carbon $referenceDate,
        string $aggregateName,
        ColumnFactory $columnFactory
    ) {
        $this->capsuleElasticity = $capsuleElasticity;
        $this->intervalElasticity = $intervalElasticity;
        $this->referenceDate = $referenceDate;
        $this->aggregateName = $aggregateName;

        $this->columnFactory = $columnFactory;

        $this->bootstrap();
    }

    /**
     * Bootstrap Capsule by computing columns etc
     */
    protected function bootstrap()
    {
        $this->computeCapsuleId()
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