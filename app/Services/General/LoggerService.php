<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 14/09/17
 * Time: 14:09
 */

namespace Equinox\Services\General;

use Illuminate\Support\Facades\Log;
use Monolog\Logger as MonologLogger;

/**
 * Class Logger
 * @package Equinox\Services\General
 * @method void debug(string $message, array $context = [])
 * @method void info(string $message, array $context = [])
 * @method void notice(string $message, array $context = [])
 * @method void warning(string $message, array $context = [])
 * @method void error(string $message, array $context = [])
 * @method void critical(string $message, array $context = [])
 * @method void alert(string $message, array $context = [])
 * @method void emergency(string $message, array $context = [])
 */
class LoggerService
{
    /**
     * The logger levels
     */
    protected const LEVELS = [
        'debug' => MonologLogger::DEBUG,
        'info' => MonologLogger::INFO,
        'notice' => MonologLogger::NOTICE,
        'warning' => MonologLogger::WARNING,
        'error' => MonologLogger::ERROR,
        'critical' => MonologLogger::CRITICAL,
        'alert' => MonologLogger::ALERT,
        'emergency' => MonologLogger::EMERGENCY,
    ];

    /**
     * The Log channels.
     * @var array
     */
    protected $channels = [];

    /**
     * The current channel name
     * @var string
     */
    protected $currentChannel;

    /**
     * Logger constructor.
     */
    public function __construct()
    {
        $this->channels = array_keys(config('logging.channels'));
    }

    /**
     * Short function used to set a channel
     * @param string $channelName
     * @return LoggerService
     */
    public function setChannel(string $channelName): self
    {
        /* Check if channel exists */
        if (! in_array($channelName, array_keys($this->channels))) {
            throw new \InvalidArgumentException('Invalid channel used.');
        }

        $this->currentChannel = $channelName;

        return $this;
    }

    /**
     * Magic method for calling logging methods
     * @param string $func
     * @param array $params
     */
    public function __call(string $func, array $params)
    {
        if (in_array($func, array_keys(self::LEVELS))) {
            /* @noinspection PhpUndefinedMethodInspection */
            Log::channel($this->currentChannel)->$func($params[0], $params[1] ?? []);
        }
    }

    /**
     * Function used to log function time
     * @param float $elapsed
     * @param string|null $prefix
     */
    public function debugTimer(float $elapsed, string $prefix = null)
    {
        $message = "";

        if (! is_null($prefix)) {
            $message = "{$prefix} took ";
        }

        $message .= round($elapsed, 4);

        $this->debug($message);
    }
}