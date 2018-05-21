<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 04/05/18
 * Time: 11:14
 */

namespace Equinox\Services\General;


class Config
{

    /**
     * Cached config
     * @var array
     */
    protected $cachedConfig = [];

    /**
     * Getter for config
     * @param string $configPath
     * @return mixed
     */
    public function get(string $configPath)
    {
        if (array_key_exists($configPath, $this->cachedConfig)) {
            return $this->cachedConfig[$configPath];
        }

        $value = config($configPath);

        $this->cachedConfig[$configPath] = $value;

        return $value;
    }

    /**
     * Function used to retrieve the defined capsules config
     * @return array
     */
    public function getDefinedCapsulesConfig(): array
    {
        return $this->get('capsule.defined_capsules');
    }

    /**
     * Function used to retrieve the defined aggregates config
     * @return array
     */
    public function getDefinedAggregatesConfig(): array
    {
        return array_merge(
            $this->get('data.interval_column_aggregates'),
            $this->get('data.interval_column_meta_aggregates')
        );
    }

}