<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\log
 */
namespace stubbles\log\appender;
use stubbles\log\LogEntry;
/**
 * A log appenders that writes log entries to files.
 *
 * This log appender writes the log entries into a logfile using the error_log()
 * function of PHP. The logfile name will be [target]-[Y-m-d].log where target
 * is the return value of stubLogEntry::getTarget().
 *
 * @uses  http://php.net/error_log
 */
class FileLogAppender implements LogAppender
{
    /**
     * the directory to write the logfiles into
     *
     * @type  string
     */
    protected $logDir = '';
    /**
     * mode for new directories
     *
     * @type  int
     */
    protected $mode   = 0700;

    /**
     * constructor
     *
     * @param  string  $logDir  optional  directory to write the logfiles into
     */
    public function __construct($logDir)
    {
        $this->logDir = $logDir;
    }

    /**
     * sets the mode for new log directories
     *
     * @param   int  $mode
     * @return  FileLogAppender
     */
    public function setMode($mode)
    {
        $this->mode = $mode;
        return $this;
    }

    /**
     * append the log entry to the log file
     *
     * The basename of the logfile will be [target]-[Y-m-d].log where target
     * is the return value of $logEntry->getTarget().
     *
     * @param   LogEntry  $logEntry
     * @return  FileLogAppender
     */
    public function append(LogEntry $logEntry)
    {
        if (!file_exists($this->logDir)) {
            mkdir($this->logDir, $this->mode, true);
        }

        error_log($logEntry->get() . "\n",
                  3,
                  $this->logDir . DIRECTORY_SEPARATOR . $logEntry->getTarget() . '-' . date('Y-m-d') . '.log'
        );
        return $this;
    }

    /**
     * finalize the log target
     *
     * @return  FileLogAppender
     */
    public function finalize()
    {
        return $this;
    }
}
