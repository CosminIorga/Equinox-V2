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
use Equinox\Services\General\Config;
use Illuminate\Support\Facades\Cache;

class CapsuleFactory
{
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
     * The Cache Service
     * @var Cache
     */
    protected $cache;

    /**
     * The Config Service
     * @var Config
     */
    protected $config;

    /**
     * CapsuleFactory constructor.
     * @param Cache $cache
     * @param Config $config
     * @param ColumnFactory $columnFactory
     * @param RecordFactory $recordFactory
     */
    public function __construct(
        Cache $cache,
        Config $config,
        ColumnFactory $columnFactory,
        RecordFactory $recordFactory
    ) {
        $this->columnFactory = $columnFactory;
        $this->cache = $cache;
        $this->config = $config;
        $this->recordFactory = $recordFactory;
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
            $this->config,
            $this->columnFactory,
            $this->recordFactory
        );
    }
}