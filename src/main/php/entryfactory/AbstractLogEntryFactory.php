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
 * Abstract base implementation of a log entry factory.
 *
 * @since  1.1.0
 * @api
 */
abstract class AbstractLogEntryFactory implements LogEntryFactory
{
    /**
     * recreates given log entry
     *
     * @param   \stubbles\log\LogEntry  $logEntry
     * @param   \stubbles\log\Logger    $logger
     * @return  \stubbles\log\LogEntry
     */
    public function recreate(LogEntry $logEntry, Logger $logger)
    {
        return $logEntry;
    }
}
