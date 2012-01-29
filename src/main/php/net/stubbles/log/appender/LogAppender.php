<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\log
 */
namespace net\stubbles\log\appender;
use net\stubbles\lang\Object;
use net\stubbles\log\LogEntry;
/**
 * Interface for log appenders.
 *
 * A log appender takes log entries and writes them to the target. The target
 * can be a file, a database or anything else.
 */
interface LogAppender extends Object
{
    /**
     * append the log entry to the log target
     *
     * @param   LogEntry  $logEntry
     * @return  LogAppender
     */
    public function append(LogEntry $logEntry);

    /**
     * finalize the log target
     *
     * This will be called in case a logger is destroyed and can be used
     * to close file or database handlers or to write the log data if
     * append() just collects the data.
     *
     * @return  LogAppender
     */
    public function finalize();
}
?>