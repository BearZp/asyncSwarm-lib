<?php
declare(strict_types=1);

namespace App\lib\logger;

use Psr\Log\LogLevel;
use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;

/**
 * Class UdpLogger
 * @package common\lib\logger
 */
class FileLogger extends AbstractLogger implements LoggerInterface
{
    /**
     * @var string
     */
    private $path;

    /**
     * @var int
     */
    private $limitLevel;

    /**
     * @var array
     */
    private $levelMap = [
        LogLevel::EMERGENCY => 0,
        LogLevel::ALERT => 1,
        LogLevel::CRITICAL => 2,
        LogLevel::ERROR => 3,
        LogLevel::WARNING => 4,
        LogLevel::NOTICE => 5,
        LogLevel::INFO => 6,
        LogLevel::DEBUG => 7,
    ];

    /**
     * @param string $path
     * @param string $limitLevel
     */
    public function __construct(string $path, string $limitLevel = '')
    {
        $this->path = $path . date('Ymd') . '.log';
        $limitLevel = getenv('LOG_LEVEL_LIMIT') !== ''
            ? (string)getenv('LOG_LEVEL_LIMIT')
            : $limitLevel;

        $this->limitLevel = array_key_exists($limitLevel, $this->levelMap)
            ? $this->levelMap[$limitLevel]
            : $this->limitLevel = $this->levelMap[LogLevel::DEBUG];
    }

    /**
     * @param string $level
     *
     * @return bool
     */
    private function filterLevel(string $level): bool
    {
        return isset($this->levelMap[$level]) && $this->levelMap[$level] <= $this->limitLevel;
    }

    /**
     * @param mixed  $level
     * @param string $message
     * @param array  $context
     *
     * @throws \Exception
     */
    public function log($level, $message, array $context = []): void
    {
        if ($this->filterLevel($level)) {
            file_put_contents(
                $this->path,
                $this->getPacket($level, $message, $context) . PHP_EOL,
                FILE_APPEND
            );
        }
    }

    /**
     * Prepares data for sending to logstash
     *
     * @param string $level
     * @param string $message
     * @param array  $context
     *
     * @return string
     */
    public function getPacket(string $level, string $message, array $context = []): string
    {
        $packet = [];
        $packet['service-name'] = 'SERVICE NAME';
        $packet['action-name'] = 'ACTION NAME';
        $packet['pattern'] = $message;
        $packet['message'] = $this->interpolate($message, $context);
        $packet['log-level'] = $level;
        $packet['event-time'] = $this->getRfcTime();
        $packet['request-id'] = ''; //togo get request-id from context
        $packet['object'] = null;
        $packet['metadata'] = [
            'type' => 'logs',
            'beat' => 'service-logger',
        ];
        if (isset($context['object'])) {
            $packet['object'] = is_object($context['object'])
                ? str_replace('\\', '.', get_class($context['object']))
                : (string)$context['object'];
            unset($context['object']);
        }

        if (isset($context['actionName'])) {
            $packet['action-name'] = $context['actionName'];
            unset($context['actionName']);
        }

        if (isset($context['serviceName'])) {
            $packet['service-name'] = $context['serviceName'];
            unset($context['serviceName']);
        }

        if (isset($context['exception']) && $context['exception'] instanceof \Throwable) {
            $packet = array_merge($packet, $this->flattenException($context['exception']));
            unset($context['exception']);
        }

        $packet['tags'] = null;
        if (isset($context['tags']) && is_array($context['tags'])) {
            $packet['tags'] = $context['tags'];
            unset($context['tags']);
        }

        $data = $this->filterContext($context);
        $packet['data'] = count($data) === 0 ? null : \json_encode($data);

        return \json_encode($packet, JSON_UNESCAPED_UNICODE + JSON_UNESCAPED_SLASHES);
    }

    /**
     * @param \Throwable $exception
     *
     * @return array
     */
    public function flattenException(\Throwable $exception): array
    {
        $dump = [];
        $dump['exception-class'][] = str_replace('\\', '.', get_class($exception));
        $dump['exception-code'][] = (int)$exception->getCode();
        $dump['exception-message'][] = (string)$exception->getMessage();
        $exceptionTrace = [];
        $exceptionTrace[] = $this->truncateFilename($exception->getFile()) . ':' . $exception->getLine();
        foreach ($exception->getTrace() as $row) {
            $file = 'anonymous';
            if (isset($row['file'])) {
                $file = $this->truncateFilename($row['file']);
            }
            if (isset($row['line'])) {
                $file .= ':' . $row['line'];
            }
            $exceptionTrace[] = $file;
        }

        $dump['exception-trace'][] = str_replace('/', '.', implode(' <= ', $exceptionTrace));
        if ($exception->getPrevious() !== null) {
            $previousDump = $this->flattenException($exception->getPrevious());
            $dump['exception-class'][] = $previousDump['exception-class'];
            $dump['exception-code'][] = $previousDump['exception-code'];
            $dump['exception-message'][] = $previousDump['exception-message'];
            $dump['exception-trace'][] = $previousDump['exception-trace'];
        }

        return [
            'exception-class' => implode('; ', $dump['exception-class']),
            'exception-code' => implode('; ', $dump['exception-code']),
            'exception-message' => implode('; ', $dump['exception-message']),
            'exception-trace' => implode('; ', $dump['exception-trace']),
        ];
    }

    /**
     * Utility method to truncate long filename
     *
     * @param string $filename
     *
     * @return string
     */
    private function truncateFilename(string $filename): string
    {
        if (empty($filename)) {
            return $filename;
        }

        $chunks = explode('/', $filename);
        if (count($chunks) < 4) {
            return $filename;
        }

        return implode('/', array_slice($chunks, count($chunks) - 3));
    }

    /**
     * Filters context fields to send into logstash
     *
     * @param array $context
     *
     * @return array
     */
    public function filterContext(array $context): array
    {
        return $context;
    }

    /**
     * Replaces placeholders in message
     *
     * @param string $message
     * @param array  $context
     *
     * @return string
     */
    public function interpolate(string $message, array $context): string
    {
        if (!is_string($message) || !is_array($context) || count($context) === 0) {
            // Something wrong - fallback mode
            return $message;
        }

        return preg_replace_callback(
            '/(^|\W):([\w\-]+)/',
            function ($matches) use ($context) {
                $key = $matches[2];
                if (!isset($context[$key]) || !is_scalar($context[$key])) {
                    return $matches[0];
                }
                $value = $context[$key];
                if ($value === null) {
                    return $matches[1] . 'NULL';
                } elseif (is_bool($value)) {
                    return $matches[1] . ($value ? 'true' : 'false');
                } else {
                    return $matches[1] . $value;
                }
            },
            $message
        );
    }

    /**
     * Returns RFC-formatted time in format "2016-02-24T09:45:21.184300+00:00"
     * @return string
     */
    public function getRfcTime(): string
    {
        [$ms, $ts] = explode(' ', microtime());

        return date('Y-m-d\TH:i:s.', (int)$ts) . sprintf('%06d', (int)$ms * 1000000) . date('P');
    }
}
