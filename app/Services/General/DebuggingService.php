<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 23/01/18
 * Time: 17:19
 */

namespace Equinox\Services\General;

/**
 * Class DebuggingService
 * @package Equinox\Services\General
 */
class DebuggingService
{

    /**
     * Start timer for performance benchmarks
     * @return float
     */
    public function startTimer(): float
    {
        return microtime(true);
    }

    /**
     * Compute total operations time
     * @param float $startTime
     * @return float
     */
    public function endTimer(float $startTime): float
    {
        return microtime(true) - $startTime;
    }

    /**
     * Dump elapsed time with given prefix
     * @param float $elapsed
     * @param string|null $prefix
     * @return string
     */
    public function dumpTimerMessage(float $elapsed, string $prefix = null): string
    {
        $message = "";

        if (!is_null($prefix)) {
            $message = "{$prefix} took ";
        }

        $message .= round($elapsed, 4);

        return $message;
    }
}