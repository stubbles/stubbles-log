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
use bovigo\callmap;
use bovigo\callmap\NewInstance;
/**
 * Test for stubbles\log\Logger.
 *
 * @group  log
 * @group  log_core
 */
class LoggerTest extends \PHPUnit_Framework_TestCase
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
     * @type  \bovigo\callmap\Proxy
     */
    private $logEntryFactory;

    /**
     * set up the test environment
     */
    public function setUp()
    {
        $this->logEntryFactory = NewInstance::of('stubbles\log\entryfactory\LogEntryFactory');
        $this->logger          = new Logger($this->logEntryFactory);
    }

    /**
     * @test
     * @deprecated  since 3.0.0, will be removed with 4.0.0
     */
    public function initialInstanceHasNoLogAppenders()
    {
        assertFalse($this->logger->hasLogAppenders());
        assertEquals([], $this->logger->getLogAppenders());
    }

    /**
     * @since  1.1.0
     * @test
     */
    public function initialInstanceHasNoDelayedLogEntries()
    {
        assertFalse($this->logger->hasUnprocessedDelayedLogEntries());
    }

    /**
     * @test
     */
    public function cleanupFinalizesAppenders()
    {
        $logger           = new Logger($this->logEntryFactory);
        $logAppender1 = NewInstance::of('stubbles\log\appender\LogAppender');
        $logger->addAppender($logAppender1);
        $logAppender2 = NewInstance::of('stubbles\log\appender\LogAppender');
        $logger->addAppender($logAppender2);
        $logger->cleanup();
        callmap\verify($logAppender1, 'finalize')->wasCalledOnce();
        callmap\verify($logAppender2, 'finalize')->wasCalledOnce();
    }

    /**
     * @test
     */
    public function createLogEntryUsesLogEntryFactory()
    {
        $logEntry = new LogEntry('testTarget', $this->logger);
        $this->logEntryFactory->mapCalls(['create' => $logEntry]);
        assertSame($logEntry, $this->logger->createLogEntry('testTarget'));
        callmap\verify($this->logEntryFactory, 'create')
                ->received('testTarget', $this->logger);
    }

    /**
     * @test
     */
    public function logAppendsLogEntryToAllLogAppender()
    {
        $logEntry     = new LogEntry('testTarget', $this->logger);
        $logAppender1 = NewInstance::of('stubbles\log\appender\LogAppender');
        $logAppender2 = NewInstance::of('stubbles\log\appender\LogAppender');
        $this->logger->addLogAppender($logAppender1);
        $this->logger->addLogAppender($logAppender2);
        $this->logger->log($logEntry);
        callmap\verify($logAppender1, 'append')->received($logEntry);
        callmap\verify($logAppender2, 'append')->received($logEntry);
    }

    /**
     * @since  1.1.0
     * @test
     */
    public function processDelayedLogEntriesWithoutDelayedLogEntriesReturn0()
    {
        assertEquals(0, $this->logger->processDelayedLogEntries());
    }

    /**
     * @since  1.1.0
     * @test
     */
    public function hasUnprocessedLogEntriesIfAdded()
    {
        $logEntry = new LogEntry('testTarget', $this->logger);
        $this->logger->logDelayed($logEntry);
        $this->logEntryFactory->mapCalls(['recreate' => $logEntry]);
        assertTrue($this->logger->hasUnprocessedDelayedLogEntries());
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
        $this->logEntryFactory->mapCalls(['recreate' => $logEntry]);
        $logAppender1 = NewInstance::of('stubbles\log\appender\LogAppender');
        $logAppender2 = NewInstance::of('stubbles\log\appender\LogAppender');
        $this->logger->addAppender($logAppender1);
        $this->logger->addAppender($logAppender2);
        assertEquals(1, $this->logger->processDelayedLogEntries());
        callmap\verify($logAppender1, 'append')->received($logEntry);
        callmap\verify($logAppender2, 'append')->received($logEntry);
    }

    /**
     * @since  1.1.0
     * @test
     */
    public function hasNoUnprocessedLogEntriesAfterDelayedAreProcessed()
    {
        $logEntry = new LogEntry('testTarget', $this->logger);
        $this->logger->logDelayed($logEntry);
        $this->logEntryFactory->mapCalls(['recreate' => $logEntry]);
        $this->logger->processDelayedLogEntries();
        assertFalse($this->logger->hasUnprocessedDelayedLogEntries());
    }

    /**
     * @since  1.1.0
     * @test
     */
    public function shutdownProcessesDelayedLogEntries()
    {
        $logEntry = new LogEntry('testTarget', $this->logger);
        $this->logEntryFactory->mapCalls(['recreate' => $logEntry]);
        $logAppender1 = NewInstance::of('stubbles\log\appender\LogAppender');
        $logAppender2 = NewInstance::of('stubbles\log\appender\LogAppender');
        $this->logger->addAppender($logAppender1);
        $this->logger->addAppender($logAppender2);
        $this->logger->logDelayed($logEntry);
        $this->logger->cleanup();
        callmap\verify($logAppender1, 'append')->received($logEntry);
        callmap\verify($logAppender2, 'append')->received($logEntry);
    }
}
