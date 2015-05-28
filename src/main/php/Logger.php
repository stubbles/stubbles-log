<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\log
 */
namespace stubbles\log;
use stubbles\log\appender\LogAppender;
use stubbles\log\entryfactory\LogEntryFactory;
/**
 * Class for logging.
 *
 * The logger is the interface to log data into differant targets. The logger
 * itself does not know where to write the log data - it just uses log appenders
 * which in turn do the real work. A logger is a collection of such appenders.
 */
class Logger
{
    /**
     * factory to be used to create log data containers
     *
     * @type  \stubbles\log\entryfactory\LogEntryFactory
     */
    private $logEntryFactory;
    /**
     * list of log appenders to log data to
     *
     * @type  \stubbles\log\appender\LogAppender[]
     */
    private $logAppender       = [];
    /**
     * list of delayed log entries
     *
     * @type  \stubbles\log\LogEntry[]
     */
    private $delayedLogEntries = [];

    /**
     * constructor
     *
     * @param  \stubbles\log\entryfactory\LogEntryFactory  $logEntryFactory  factory to be used to create log data containers
     */
    public function __construct(LogEntryFactory $logEntryFactory)
    {
        $this->logEntryFactory = $logEntryFactory;
        register_shutdown_function([$this, 'cleanup']);
    }

    /**
     * shutdown activity
     *
     * Calls all log appenders that they should finalize their work.
     */
    public function cleanup()
    {
        $this->processDelayedLogEntries();
        foreach ($this->logAppender as $logAppender) {
            $logAppender->finalize();
        }
    }

    /**
     * adds a log appender to the logger
     *
     * A log appender is responsible for writing the log data.
     *
     * @param   \stubbles\log\appender\LogAppender  $logAppender
     * @return  \stubbles\log\appender\LogAppender  the freshly added log appender instance
     */
    public function addAppender(LogAppender $logAppender)
    {
        $this->logAppender[] = $logAppender;
        return $logAppender;
    }

    /**
     * checks whether logger has any log appenders
     *
     * @return  bool
     */
    public function hasLogAppenders()
    {
        return (count($this->logAppender) > 0);
    }

    /**
     * creates the log entry
     *
     * @api
     * @param   string  $target
     * @return  \stubbles\log\LogEntry
     */
    public function createLogEntry($target)
    {
        return $this->logEntryFactory->create($target, $this);
    }

    /**
     * sends log data to all registered log appenders
     *
     * @param  \stubbles\log\LogEntry  $logEntry  contains data to log
     */
    public function log(LogEntry $logEntry)
    {
        foreach ($this->logAppender as $logAppender) {
            $logAppender->append($logEntry);
        }
    }

    /**
     * collects log entries but delays logging of them until destruction of the
     * logger or processDelayedLogEntries() gets called
     *
     * @param  \stubbles\log\LogEntry  $logEntry
     * @since  1.1.0
     */
    public function logDelayed(LogEntry $logEntry)
    {
        $this->delayedLogEntries[] = $logEntry;
    }

    /**
     * returns number of unprocessed delayed log entries
     *
     * @return  int
     * @since   1.1.0
     */
    public function hasUnprocessedDelayedLogEntries()
    {
        return (count($this->delayedLogEntries) > 0);
    }

    /**
     * processes all delayed log entries
     *
     * @return  int  amount of processed delayed entries
     * @since   1.1.0
     */
    public function processDelayedLogEntries()
    {
        if (!$this->hasUnprocessedDelayedLogEntries()) {
            return 0;
        }

        foreach ($this->delayedLogEntries as $logEntry) {
            $this->log($this->logEntryFactory->recreate($logEntry, $this));
        }

        $amount = count($this->delayedLogEntries);
        $this->delayedLogEntries = [];
        return $amount;
    }
}
