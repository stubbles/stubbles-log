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
use net\stubbles\ioc\Binder;
use net\stubbles\ioc\module\BindingModule;
use net\stubbles\lang\BaseObject;
/**
 * Bindung module for a default log configuration.
 */
class LogBindingModule extends BaseObject implements BindingModule
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
    private $logEntryFactory = 'net\\stubbles\\log\\entryfactory\\TimedLogEntryFactory';
    /**
     * name of class which provides the logger instance
     *
     * @type   string
     * @since  1.3.0
     */
    private $loggerProvider  = 'net\\stubbles\\log\\ioc\\FileBasedLoggerProvider';

    /**
     * constructor
     *
     * Please note that the log path is only optional if it is bound by another
     * module.
     *
     * @param  string  $logPath  optional  path where logfiles should be stored
     */
    public function __construct($logPath = null)
    {
        $this->logPath = $logPath;
    }

    /**
     * static constructor
     *
     * Uses no path and relies on another module to bind the logpath constant
     * net.stubbles.log.path.
     *
     * @api
     * @return  LogBindingModule
     * @since   2.0.0
     */
    public static function create()
    {
        return new self();
    }

    /**
     * static constructor
     *
     * Uses the $projectPath to bind the logpath constant net.stubbles.log.path
     * to $projectPath/log.
     *
     * @api
     * @param   string  $projectPath
     * @return  LogBindingModule
     * @since   2.0.0
     */
    public static function createWithProjectPath($projectPath)
    {
        return new self($projectPath . DIRECTORY_SEPARATOR . 'log');
    }

    /**
     * static constructor
     *
     * Uses given path to bind the constant net.stubbles.log.path to.
     *
     * @api
     * @param   string  $logPath  optional
     * @return  LogBindingModule
     * @since   2.0.0
     */
    public static function createWithLogPath($logPath)
    {
        return new self($logPath);
    }

    /**
     * sets the class name of log entry factory class to be bound
     *
     * @api
     * @param   string  $logEntryFactory  class name of log entry factory
     * @return  LogBindingModule
     */
    public function setLogEntryFactory($logEntryFactory)
    {
        $this->logEntryFactory = $logEntryFactory;
        return $this;
    }

    /**
     * sets name of class which provides the logger instance
     *
     * @api
     * @param   string  $loggerProvider  class name of logger provider
     * @return  LogBindingModule
     * @since   1.3.0
     */
    public function setLoggerProvider($loggerProvider)
    {
        $this->loggerProvider = $loggerProvider;
        return $this;
    }

    /**
     * configure the binder
     *
     * @param  Binder  $binder
     */
    public function configure(Binder $binder)
    {
        if (!empty($this->logPath)) {
            $binder->bindConstant('net.stubbles.log.path')
                   ->to($this->logPath);
        }

        $binder->bind('net\\stubbles\\log\\entryfactory\\LogEntryFactory')
               ->to($this->logEntryFactory)
               ->asSingleton();
        $binder->bind('net\\stubbles\\log\\Logger')
               ->toProviderClass($this->loggerProvider);
    }
}
?>