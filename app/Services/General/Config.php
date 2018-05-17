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
}