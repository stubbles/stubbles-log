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
use bovigo\callmap\NewInstance;
use stubbles\log\LogEntry;
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

    /**
     * creates log entry
     *
     * @return  stubLogEntry
     */
    protected function createLogEntry($target)
    {
        $logEntry = new LogEntry(
                $target,
                NewInstance::stub('stubbles\log\Logger')
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
        assertEquals(
                0,
                $this->memoryLogAppender->countLogEntries('myTestTarget')
        );
    }

    /**
     * @since  1.1.0
     * @test
     */
    public function countLogEntriesForExistingTargetReturnsAmountOfEnries()
    {
        assertEquals(
                2,
                $this->memoryLogAppender
                        ->append($this->createLogEntry('myTestTarget'))
                        ->append($this->createLogEntry('myTestTarget'))
                        ->append($this->createLogEntry('otherTestTarget'))
                        ->countLogEntries('myTestTarget')
        );
    }

    /**
     * @since  1.1.0
     * @test
     */
    public function returnLogEntryDataForNonExistingTargetReturnsEmptyArray()
    {
        assertEquals(
                [],
                $this->memoryLogAppender->getLogEntryData('myTestTarget', 0)
        );
    }

    /**
     * @since  1.1.0
     * @test
     */
    public function returnLogEntryDataForNonExistingPositionReturnsEmptyArray()
    {
        assertEquals(
                [],
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
        assertEquals(
                ['bar', 'baz'],
                $this->memoryLogAppender
                        ->append($this->createLogEntry('myTestTarget'))
                        ->getLogEntryData('myTestTarget', 0)
        );
    }

    /**
     * @since  1.1.0
     * @test
     */
    public function returnLogEntriesForNonExistingTargetReturnsEmptyArray()
    {
        assertEquals(
                [],
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
        assertEquals(
                [$logEntry1, $logEntry2],
                $this->memoryLogAppender
                        ->append($logEntry1)
                        ->append($logEntry2)
                        ->getLogEntries('myTestTarget')
        );
    }

    /**
     * @test
     */
    public function finalizeClearsMemory()
    {
        assertEquals(
                [],
                $this->memoryLogAppender
                        ->append($this->createLogEntry('myTestTarget'))
                        ->append($this->createLogEntry('myTestTarget'))
                        ->finalize()
                        ->getLogEntries('myTestTarget')
        );
    }
}
