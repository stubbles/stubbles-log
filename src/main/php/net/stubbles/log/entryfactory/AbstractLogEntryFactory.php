<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\log
 */
namespace net\stubbles\log\entryfactory;
use net\stubbles\lang\BaseObject;
use net\stubbles\log\LogEntry;
use net\stubbles\log\Logger;
/**
 * Abstract base implementation of a log entry factory.
 *
 * @since  1.1.0
 */
abstract class AbstractLogEntryFactory extends BaseObject implements LogEntryFactory
{
    /**
     * recreates given log entry
     *
     * @param   LogEntry  $logEntry
     * @param   Logger    $logger
     * @return  LogEntry
     */
    public function recreate(LogEntry $logEntry, Logger $logger)
    {
        return $logEntry;
    }
}
?>