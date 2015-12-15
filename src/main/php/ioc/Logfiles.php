<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\log
 */
namespace stubbles\log\ioc;
use stubbles\ioc\Binder;
use stubbles\ioc\module\BindingModule;
use stubbles\log\entryfactory\LogEntryFactory;
use stubbles\log\entryfactory\TimedLogEntryFactory;
use stubbles\log\ioc\FileBasedLoggerProvider;
/**
 * Bindung module for logging configuration.
 */
class Logfiles implements BindingModule
{
    /**
     * path where logfiles should be stored
     *
     * @type  string
     */
    private $logPath;
    /**
     * class name of log entry factory class to be bound
     *
     * @type  string
     */
    private $logEntryFactory = TimedLogEntryFactory::class;
    /**
     * name of class which provides the logger instance
     *
     * @type   string
     * @since  1.3.0
     */
    private $loggerProvider  = FileBasedLoggerProvider::class;

    /**
     * sets the class name of log entry factory class to be bound
     *
     * @api
     * @param   string  $logEntryFactory  class name of log entry factory
     * @return  \stubbles\log\ioc\Logfiles
     */
    public function createEntriesWith($logEntryFactory)
    {
        $this->logEntryFactory = $logEntryFactory;
        return $this;
    }

    /**
     * sets name of class which provides the logger instance
     *
     * @api
     * @param   string  $loggerProvider  class name of logger provider
     * @return  \stubbles\log\ioc\Logfiles
     * @since   1.3.0
     */
    public function loggerProvidedBy($loggerProvider)
    {
        $this->loggerProvider = $loggerProvider;
        return $this;
    }

    /**
     * write logfiles to given path
     *
     * @param   string  $logPath
     * @return  \stubbles\log\ioc\Logfiles
     */
    public function writeTo($logPath)
    {
        $this->logPath = $logPath;
        return $this;
    }

    /**
     * configure the binder
     *
     * @param  \stubbles\ioc\Binder  $binder
     * @param  string                $projectPath  optional  project base path
     */
    public function configure(Binder $binder, $projectPath = null)
    {
        if (!empty($this->logPath)) {
            $binder->bindConstant('stubbles.log.path')
                   ->to($this->logPath);
        }

        $binder->bind(LogEntryFactory::class)
               ->to($this->logEntryFactory)
               ->asSingleton();
        $binder->bind('stubbles\log\Logger')
               ->toProviderClass($this->loggerProvider);
    }
}
