<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\log
 */
namespace stubbles\log;
/**
 * Test for stubbles\log\Logger.
 *
 * @group  log
 * @group  log_core
 */
class LoggerTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  Logger
     */
    private $logger;
    /**
     * mocked log entry factory
     *
     * @type  \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockLogEntryFactory;

    /**
     * set up the test environment
     */
    public function setUp()
    {
        $this->mockLogEntryFactory = $this->getMock('stubbles\log\entryfactory\LogEntryFactory');
        $this->logger              = new Logger($this->mockLogEntryFactory);
    }

    /**
     * @test
     */
    public function initialInstanceHasNoLogAppenders()
    {
        $this->assertFalse($this->logger->hasLogAppenders());
        $this->assertEquals([], $this->logger->getLogAppenders());
    }

    /**
     * @since  1.1.0
     * @test
     */
    public function initialInstanceHasNoDelayedLogEntries()
    {
        $this->assertFalse($this->logger->hasUnprocessedDelayedLogEntries());
    }

    /**
     * @test
     */
    public function cleanupFinalizesAppenders()
    {
        $logger           = new Logger($this->mockLogEntryFactory);
        $mockLogAppender1 = $this->getMock('stubbles\log\appender\LogAppender');
        $mockLogAppender1->expects($this->once())
                         ->method('finalize');
        $logger->addLogAppender($mockLogAppender1);
        $mockLogAppender2 = $this->getMock('stubbles\log\appender\LogAppender');
        $mockLogAppender2->expects($this->once())
                         ->method('finalize');
        $logger->addLogAppender($mockLogAppender2);
        $logger->cleanup();
    }

    /**
     * @test
     */
    public function createLogEntryUsesLogEntryFactory()
    {
        $logEntry = new LogEntry('testTarget', $this->logger);
        $this->mockLogEntryFactory->expects($this->once())
                                  ->method('create')
                                  ->with($this->equalTo('testTarget'), $this->equalTo($this->logger))
                                  ->will($this->returnValue($logEntry));
        $this->assertSame($logEntry, $this->logger->createLogEntry('testTarget'));
    }

    /**
     * @test
     */
    public function logAppendsLogEntryToAllLogAppender()
    {
        $logEntry = new LogEntry('testTarget', $this->logger);
        $mockLogAppender1 = $this->getMock('stubbles\log\appender\LogAppender');
        $mockLogAppender1->expects($this->once())
                         ->method('append')
                         ->with($this->equalTo($logEntry));
        $mockLogAppender2 = $this->getMock('stubbles\log\appender\LogAppender');
        $mockLogAppender2->expects($this->once())
                         ->method('append')
                         ->with($this->equalTo($logEntry));
        $this->logger->addLogAppender($mockLogAppender1);
        $this->logger->addLogAppender($mockLogAppender2);
        $this->logger->log($logEntry);
    }

    /**
     * @since  1.1.0
     * @test
     */
    public function processDelayedLogEntriesWithoutDelayedLogEntriesReturn0()
    {
        $this->assertEquals(0, $this->logger->processDelayedLogEntries());
    }

    /**
     * @since  1.1.0
     * @test
     */
    public function hasUnprocessedLogEntriesIfAdded()
    {
        $logEntry = new LogEntry('testTarget', $this->logger);
        $this->logger->logDelayed($logEntry);
        $this->mockLogEntryFactory->expects($this->once())
                                  ->method('recreate')
                                  ->will($this->returnValue($logEntry));
        $this->assertTrue($this->logger->hasUnprocessedDelayedLogEntries());
        $this->logger->cleanup();
    }

    /**
     * @since  1.1.0
     * @test
     */
    public function processDelayedLogEntriesReturnsAmountOfProcessedLogEntries()
    {
        $logEntry = new LogEntry('testTarget', $this->logger);
        $this->logger->logDelayed($logEntry);
        $this->mockLogEntryFactory->expects($this->once())
                                  ->method('recreate')
                                  ->will($this->returnValue($logEntry));
        $mockLogAppender1 = $this->getMock('stubbles\log\appender\LogAppender');
        $mockLogAppender1->expects($this->once())
                         ->method('append')
                         ->with($this->equalTo($logEntry));
        $mockLogAppender2 = $this->getMock('stubbles\log\appender\LogAppender');
        $mockLogAppender2->expects($this->once())
                         ->method('append')
                         ->with($this->equalTo($logEntry));
        $this->logger->addLogAppender($mockLogAppender1);
        $this->logger->addLogAppender($mockLogAppender2);
        $this->assertEquals(1, $this->logger->processDelayedLogEntries());
    }

    /**
     * @since  1.1.0
     * @test
     */
    public function hasNoUnprocessedLogEntriesAfterDelayedAreProcessed()
    {
        $logEntry = new LogEntry('testTarget', $this->logger);
        $this->logger->logDelayed($logEntry);
        $this->mockLogEntryFactory->expects($this->once())
                                  ->method('recreate')
                                  ->will($this->returnValue($logEntry));
        $this->logger->processDelayedLogEntries();
        $this->assertFalse($this->logger->hasUnprocessedDelayedLogEntries());
    }

    /**
     * @since  1.1.0
     * @test
     */
    public function shutdownProcessesDelayedLogEntries()
    {
        $logEntry = new LogEntry('testTarget', $this->logger);
        $this->mockLogEntryFactory->expects($this->once())
                                  ->method('recreate')
                                  ->will($this->returnValue($logEntry));
        $mockLogAppender1 = $this->getMock('stubbles\log\appender\LogAppender');
        $mockLogAppender1->expects($this->once())
                         ->method('append')
                         ->with($this->equalTo($logEntry));
        $mockLogAppender2 = $this->getMock('stubbles\log\appender\LogAppender');
        $mockLogAppender2->expects($this->once())
                         ->method('append')
                         ->with($this->equalTo($logEntry));
        $this->logger->addLogAppender($mockLogAppender1);
        $this->logger->addLogAppender($mockLogAppender2);
        $this->logger->logDelayed($logEntry);
        $this->logger->cleanup();
    }
}
