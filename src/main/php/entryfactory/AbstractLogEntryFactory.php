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
namespace stubbles\log\entryfactory;
use stubbles\log\LogEntry;
use stubbles\log\Logger;
/**
 * Abstract base implementation of a log entry factory.
 *
 * @since  1.1.0
 * @api
 * @deprecated  since  6.0.0, extends LogEntryFactory directly, will be removed with 7.0.0
 */
abstract class AbstractLogEntryFactory extends LogEntryFactory
{
    // intentionally empty
}
