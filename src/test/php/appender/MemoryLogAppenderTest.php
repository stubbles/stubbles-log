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
use bovigo\callmap\NewInstance;
use stubbles\log\LogEntry;
use stubbles\log\Logger;

use function bovigo\assert\assert;
use function bovigo\assert\assertEmptyArray;
use function bovigo\assert\predicate\equals;
/**
 * Test for stubbles\log\appender\MemoryLogAppender.
 *
 * @group  appender
 */
class MemoryLogAppenderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  MemoryLogAppender
     */
    private $memoryLogAppender;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->memoryLogAppender = new MemoryLogAppender();
    }

    private function createLogEntry($target): LogEntry
    {
        $logEntry = new LogEntry(
                $target,
                NewInstance::stub(Logger::class)
        );
        return $logEntry->addData('bar')
                        ->addData('baz');
    }

    /**
     * @since  1.1.0
     * @test
     */
    public function countLogEntriesForNonExistingTargetReturns0()
    {
        assert(
                $this->memoryLogAppender->countLogEntries('myTestTarget'),
                equals(0)
        );
    }

    /**
     * @since  1.1.0
     * @test
     */
    public function countLogEntriesForExistingTargetReturnsAmountOfEnries()
    {
        assert(
                $this->memoryLogAppender
                        ->append($this->createLogEntry('myTestTarget'))
                        ->append($this->createLogEntry('myTestTarget'))
                        ->append($this->createLogEntry('otherTestTarget'))
                        ->countLogEntries('myTestTarget'),
                equals(2)
        );
    }

    /**
     * @since  1.1.0
     * @test
     */
    public function returnLogEntryDataForNonExistingTargetReturnsEmptyArray()
    {
        assertEmptyArray(
                $this->memoryLogAppender->getLogEntryData('myTestTarget', 0)
        );
    }

    /**
     * @since  1.1.0
     * @test
     */
    public function returnLogEntryDataForNonExistingPositionReturnsEmptyArray()
    {
        assertEmptyArray(
                $this->memoryLogAppender
                        ->append($this->createLogEntry('myTestTarget'))
                        ->getLogEntryData('myTestTarget', 1)
        );
    }

    /**
     * @test
     */
    public function returnLogEntryDataForExistingPosition()
    {
        assert(
                $this->memoryLogAppender
                        ->append($this->createLogEntry('myTestTarget'))
                        ->getLogEntryData('myTestTarget', 0),
                equals(['bar', 'baz'])
        );
    }

    /**
     * @since  1.1.0
     * @test
     */
    public function returnLogEntriesForNonExistingTargetReturnsEmptyArray()
    {
        assertEmptyArray(
                $this->memoryLogAppender->getLogEntries('myTestTarget')
        );
    }

    /**
     * @test
     */
    public function returnAllLogEntriesForGivenTarget()
    {
        $logEntry1 = $this->createLogEntry('myTestTarget');
        $logEntry2 = $this->createLogEntry('myTestTarget');
        assert(
                $this->memoryLogAppender
                        ->append($logEntry1)
                        ->append($logEntry2)
                        ->getLogEntries('myTestTarget'),
                equals([$logEntry1, $logEntry2])
        );
    }

    /**
     * @test
     */
    public function finalizeClearsMemory()
    {
        assertEmptyArray(
                $this->memoryLogAppender
                        ->append($this->createLogEntry('myTestTarget'))
                        ->append($this->createLogEntry('myTestTarget'))
                        ->finalize()
                        ->getLogEntries('myTestTarget')
        );
    }
}
