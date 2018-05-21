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
use Illuminate\Support\Facades\Cache;

class CapsuleFactory
{
    /**
     * The column factory
     * @var ColumnFactory
     */
    protected $columnFactory;

    /**
     * @var Cache
     */
    protected $cache;

    /**
     * CapsuleFactory constructor.
     * @param Cache $cache
     * @param ColumnFactory $columnFactory
     */
    public function __construct(
        Cache $cache,
        ColumnFactory $columnFactory
    ) {
        $this->columnFactory = $columnFactory;
        $this->cache = $cache;
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