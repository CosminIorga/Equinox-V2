<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 11/09/17
 * Time: 12:40
 */

namespace Equinox\Factories;


use Carbon\Carbon;
use Equinox\Models\Capsule\Capsule;

class CapsuleFactory
{
    /**
     * The column factory
     * @var ColumnFactory
     */
    protected $columnFactory;

    /**
     * CapsuleFactory constructor.
     * @param ColumnFactory $columnFactory
     */
    public function __construct(
        ColumnFactory $columnFactory
    ) {
        $this->columnFactory = $columnFactory;
    }

    /**
     * Capsule factory builder
     * @param string $capsuleElasticity
     * @param string $intervalElasticity
     * @param Carbon $referenceDate
     * @param string $aggregateName
     * @return Capsule
     */
    public function build(
        string $capsuleElasticity,
        string $intervalElasticity,
        Carbon $referenceDate,
        string $aggregateName
    ): Capsule {
        return new Capsule(
            $capsuleElasticity,
            $intervalElasticity,
            $referenceDate,
            $aggregateName,
            $this->columnFactory
        );
    }
}