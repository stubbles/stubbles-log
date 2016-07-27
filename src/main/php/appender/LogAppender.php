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
namespace stubbles\log\appender;
use stubbles\log\LogEntry;
/**
 * Interface for log appenders.
 *
 * A log appender takes log entries and writes them to the target. The target
 * can be a file, a database or anything else.
 *
 * @api
 */
interface LogAppender
{
    /**
     * append the log entry to the log target
     *
     * @param   \stubbles\log\LogEntry  $logEntry
     * @return  \stubbles\log\appender\LogAppender
     */
    public function append(LogEntry $logEntry): self;

    /**
     * finalize the log target
     *
     * This will be called in case a logger is destroyed and can be used
     * to close file or database handlers or to write the log data if
     * append() just collects the data.
     *
     * @return  \stubbles\log\appender\LogAppender
     */
    public function finalize(): self;
}
