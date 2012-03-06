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
use net\stubbles\lang\Object;
use net\stubbles\log\LogEntry;
use net\stubbles\log\Logger;
/**
 * Interface for factories which create log entry containers.
 *
 * @api
 */
interface LogEntryFactory extends Object
{
    /**
     * creates a log entry container
     *
     * @param   string  $target  target where the log data should go to
     * @param   Logger  $logger  logger instance to create log entry container for
     * @return  LogEntry
     */
    public function create($target, Logger $logger);

    /**
     * recreates given log entry
     *
     * @param   LogEntry  $logEntry
     * @param   Logger    $logger
     * @return  LogEntry
     * @since   1.1.0
     */
    public function recreate(LogEntry $logEntry, Logger $logger);
}
?>