<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\log
 */
namespace net\stubbles\log;
use net\stubbles\lang\BaseObject;
use net\stubbles\log\appender\LogAppender;
use net\stubbles\log\entryfactory\LogEntryFactory;
/**
 * Class for logging.
 *
 * The logger is the interface to log data into differant targets. The logger
 * itself does not know where to write the log data - it just uses log appenders
 * which in turn do the real work. A logger is a collection of such appenders.
 */
class Logger extends BaseObject
{
    /**
     * factory to be used to create log data containers
     *
     * @type  LogEntryFactory
     */
    protected $logEntryFactory;
    /**
     * list of log appenders to log data to
     *
     * @type  LogAppender[]
     */
    protected $logAppender       = array();
    /**
     * list of delayed log entries
     *
     * @type  LogEntry[]
     */
    protected $delayedLogEntries = array();

    /**
     * constructor
     *
     * @param  LogEntryFactory  $logEntryFactory  factory to be used to create log data containers
     */
    public function __construct(LogEntryFactory $logEntryFactory)
    {
        $this->logEntryFactory = $logEntryFactory;
        register_shutdown_function(array($this, 'cleanup'));
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
     * @param   LogAppender  $logAppender
     * @return  LogAppender  the freshly added log appender instance
     */
    public function addLogAppender(LogAppender $logAppender)
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
     * returns a list of log appenders appended to the logger
     *
     * @return  LogAppender[]
     */
    public function getLogAppenders()
    {
        return $this->logAppender;
    }

    /**
     * creates the log entry
     *
     * @param   string  $target
     * @return  LogEntry
     */
    public function createLogEntry($target)
    {
        return $this->logEntryFactory->create($target, $this);
    }

    /**
     * sends log data to all registered log appenders
     *
     * @param  LogEntry  $logEntry  contains data to log
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
     * @param  LogEntry  $logEntry
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
        $this->delayedLogEntries = array();
        return $amount;
    }
}
?>