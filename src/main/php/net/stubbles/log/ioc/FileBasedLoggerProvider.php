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
use net\stubbles\log\appender\FileLogAppender;
/**
 * Injection provider for logger instances with a file appender.
 *
 * @since  2.0.0
 */
class FileBasedLoggerProvider implements InjectionProvider
{
    /**
     * logger instance provider
     *
     * @type  LoggerProvider
     */
    private $loggerProvider;
    /**
     * path where logfiles should be stored
     *
     * @type  string
     */
    private $logPath;
    /**
     * file mode for file appender
     *
     * @type  int
     */
    private $fileMode       = 0700;

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
     * sets the mode for new log directories
     *
     * @param   int  $fileMode
     * @return  FileBasedLoggerProvider
     * @Inject(optional=true)
     * @Named('net.stubbles.log.filemode')
     */
    public function setFileMode($fileMode)
    {
        $this->fileMode = $fileMode;
        return $this;
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
            $logger->addLogAppender(new FileLogAppender($this->logPath))
                   ->setMode($this->fileMode);
        }

        return $logger;
    }
}
?>