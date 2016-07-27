<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\log
 */
namespace stubbles\log\ioc;
use stubbles\ioc\InjectionProvider;
use stubbles\log\Logger;
use stubbles\log\appender\FileLogAppender;
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
     * @type  \stubbles\log\ioc\LoggerProvider
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
    private $fileMode;

    /**
     * constructor
     *
     * @param  \stubbles\log\ioc\LoggerProvider  $loggerProvider  provider which creates logger instances
     * @param  string                            $logPath         path where logfiles should be stored
     * @param  int                               $fileMode        optional  file mode for file appender
     * @Named{logPath}('stubbles.log.path')
     * @Named{fileMode}('stubbles.log.filemode')
     */
    public function __construct(LoggerProvider $loggerProvider, string $logPath, int $fileMode = 0700)
    {
        $this->loggerProvider = $loggerProvider;
        $this->logPath        = $logPath;
        $this->fileMode       = $fileMode;
    }

    /**
     * returns the value to provide
     *
     * @param   string  $name  optional
     * @return  \stubbles\log\Logger
     */
    public function get(string $name = null): Logger
    {
        $logger = $this->loggerProvider->get($name);
        if (!$logger->hasLogAppenders()) {
            $logger->addAppender(
                    new FileLogAppender($this->logPath, $this->fileMode)
            );
        }

        return $logger;
    }
}
