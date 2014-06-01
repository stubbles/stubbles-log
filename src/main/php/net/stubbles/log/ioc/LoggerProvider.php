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
use stubbles\ioc\InjectionProvider;
use net\stubbles\log\Logger;
use net\stubbles\log\entryfactory\LogEntryFactory;
/**
 * Injection provider for logger instances.
 *
 * Each logger has a name. If no name is provided on a call to get() __default
 * will be used. Each named logger is only created once, requesting a logger
 * with the same name again returns the previously created instance.
 */
class LoggerProvider implements InjectionProvider
{
    /**
     * factory to be used to create log entry containers
     *
     * @type  LogEntryFactory
     */
    private $logEntryFactory;
    /**
     * list of available loggers
     *
     * @type  array
     */
    private $logger;

    /**
     * constructor
     *
     * @param  LogEntryFactory  $logEntryFactory  factory to be used to create log entry containers
     * @Inject
     */
    public function __construct(LogEntryFactory $logEntryFactory)
    {
        $this->logEntryFactory = $logEntryFactory;
    }

    /**
     * returns the value to provide
     *
     * @param   string  $name  optional
     * @return  mixed
     */
    public function get($name = null)
    {
        if (empty($name)) {
            $name = '__default';
        }

        if (!isset($this->logger[$name])) {
            $this->logger[$name] = new Logger($this->logEntryFactory);
        }

        return $this->logger[$name];
    }
}
