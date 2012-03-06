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
use net\stubbles\log\LogEntry;
/**
 * Test for net\stubbles\log\appender\MemoryLogAppender.
 *
 * @group  appender
 */
class MemoryLogAppenderTestCase extends \PHPUnit_Framework_TestCase
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
        $logEntry = new LogEntry($target,
                                 $this->getMock('net\\stubbles\\log\\Logger',
                                                array(),
                                                array(),
                                                '',
                                                false
                                )
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
        $this->assertEquals(0,
                            $this->memoryLogAppender->countLogEntries('myTestTarget')
        );
    }

    /**
     * @since  1.1.0
     * @test
     */
    public function countLogEntriesForExistingTargetReturnsAmountOfEnries()
    {
        $this->assertEquals(2,
                            $this->memoryLogAppender->append($this->createLogEntry('myTestTarget'))
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
        $this->assertEquals(array(),
                            $this->memoryLogAppender->getLogEntryData('myTestTarget', 0)
        );
    }

    /**
     * @since  1.1.0
     * @test
     */
    public function returnLogEntryDataForNonExistingPositionReturnsEmptyArray()
    {
        $this->assertEquals(array(),
                            $this->memoryLogAppender->append($this->createLogEntry('myTestTarget'))
                                                    ->getLogEntryData('myTestTarget', 1)
        );
    }

    /**
     * @test
     */
    public function returnLogEntryDataForExistingPosition()
    {
        $this->assertEquals(array('bar', 'baz'),
                            $this->memoryLogAppender->append($this->createLogEntry('myTestTarget'))
                                                    ->getLogEntryData('myTestTarget', 0)
        );
    }

    /**
     * @since  1.1.0
     * @test
     */
    public function returnLogEntriesForNonExistingTargetReturnsEmptyArray()
    {
        $this->assertEquals(array(),
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
        $this->assertEquals(array($logEntry1, $logEntry2),
                            $this->memoryLogAppender->append($logEntry1)
                                                    ->append($logEntry2)
                                                    ->getLogEntries('myTestTarget')
        );
    }

    /**
     * @test
     */
    public function finalizeClearsMemory()
    {
        $this->assertEquals(array(),
                            $this->memoryLogAppender->append($this->createLogEntry('myTestTarget'))
                                                    ->append($this->createLogEntry('myTestTarget'))
                                                    ->finalize()
                                                    ->getLogEntries('myTestTarget')
        );
    }
}
?>