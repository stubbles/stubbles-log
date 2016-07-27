<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\log
 */
namespace stubbles\log\entryfactory;
use stubbles\log\LogEntry;
use stubbles\log\Logger;
/**
 * Interface for factories which create log entry containers.
 *
 * @api
 */
interface LogEntryFactory
{
    /**
     * creates a log entry container
     *
     * @param   string                $target  target where the log data should go to
     * @param   \stubbles\log\Logger  $logger  logger instance to create log entry container for
     * @return  \stubbles\log\LogEntry
     */
    public function create(string $target, Logger $logger): LogEntry;

    /**
     * recreates given log entry
     *
     * @param   \stubbles\log\LogEntry  $logEntry
     * @param   \stubbles\log\Logger    $logger
     * @return  \stubbles\log\LogEntry
     * @since   1.1.0
     */
    public function recreate(LogEntry $logEntry, Logger $logger): LogEntry;
}
