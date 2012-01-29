<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\log
 */
namespace net\stubbles\log\ioc;
use net\stubbles\ioc\InjectionProvider;
use net\stubbles\lang\BaseObject;
use net\stubbles\log\appender\FileLogAppender;
/**
 * Injection provider for logger instances with a file appender.
 */
class FileBasedLoggerProvider extends BaseObject implements InjectionProvider
{
    /**
     * logger instance provider
     *
     * @type  LoggerProvider
     */
    protected $loggerProvider;
    /**
     * path where logfiles should be stored
     *
     * @type  string
     */
    protected $logPath;

    /**
     * constructor
     *
     * @param  LoggerProvider  $loggerProvider  provider which creates logger instances
     * @param  string          $logPath         path where logfiles should be stored
     * @Inject
     * @Named{logPath}('net.stubbles.log.path')
     */
    public function __construct(LoggerProvider $loggerProvider, $logPath)
    {
        $this->loggerProvider = $loggerProvider;
        $this->logPath        = $logPath;
    }

    /**
     * returns the value to provide
     *
     * @param   string  $name  optional
     * @return  mixed
     */
    public function get($name = null)
    {
        $logger = $this->loggerProvider->get($name);
        if (!$logger->hasLogAppenders()) {
            $logger->addLogAppender(new FileLogAppender($this->logPath));
        }

        return $logger;
    }
}
?>