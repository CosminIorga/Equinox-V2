<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 19/01/18
 * Time: 17:04
 */

namespace Equinox\Services\General;

/**
 * Class CachingService
 * Service used to persist information in Redis
 * @package Equinox\Services\General
 */
class CachingService
{

    /**
     * The Redis Service
     * @var \Redis
     */
    protected $redisService;

    /**
     * CachingService constructor.
     * @param \Redis $redisService
     */
    public function __construct(
        \Redis $redisService
    ) {
        $this->redisService = $redisService;
    }

    /**
     * Retrieve value from cache given key
     * @param string $key
     * @return string
     */
    public function get(string $key)
    {
        return $this->redisService->get($key);
    }

    /**
     * Persist a value in cache given its key
     * @param string $key
     * @param string $payload
     * @return bool
     */
    public function set(string $key, string $payload)
    {
        return $this->redisService->set($key, $payload);
    }

    /**
     * Check if a key exists in cache
     * @param string $key
     * @return bool
     */
    public function exists(string $key): bool
    {
        return $this->redisService->exists($key);
    }
}