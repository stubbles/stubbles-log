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
 * Factory which creates empty log entry containers.
 */
class EmptyLogEntryFactory extends AbstractLogEntryFactory
{
    /**
     * creates a log entry container
     *
     * @param   string                $target  target where the log data should go to
     * @param   \stubbles\log\Logger  $logger  logger instance to create log entry container for
     * @return  \stubbles\log\LogEntry
     */
    public function create($target, Logger $logger)
    {
        return new LogEntry($target, $logger);
    }
}
