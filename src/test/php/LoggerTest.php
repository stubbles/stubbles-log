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
namespace stubbles\log;
use bovigo\callmap\NewInstance;
use stubbles\log\appender\LogAppender;
use stubbles\log\entryfactory\LogEntryFactory;

use function bovigo\assert\assert;
use function bovigo\assert\assertFalse;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\equals;
use function bovigo\assert\predicate\isSameAs;
use function bovigo\callmap\verify;
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
        $this->logEntryFactory = NewInstance::of(LogEntryFactory::class);
        $this->logger          = new Logger($this->logEntryFactory);
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
        $logAppender1 = NewInstance::of(LogAppender::class);
        $logger->addAppender($logAppender1);
        $logAppender2 = NewInstance::of(LogAppender::class);
        $logger->addAppender($logAppender2);
        $logger->cleanup();
        verify($logAppender1, 'finalize')->wasCalledOnce();
        verify($logAppender2, 'finalize')->wasCalledOnce();
    }

    /**
     * @test
     */
    public function createLogEntryUsesLogEntryFactory()
    {
        $logEntry = new LogEntry('testTarget', $this->logger);
        $this->logEntryFactory->mapCalls(['create' => $logEntry]);
        assert($this->logger->createLogEntry('testTarget'), isSameAs($logEntry));
        verify($this->logEntryFactory, 'create')
                ->received('testTarget', $this->logger);
    }

    /**
     * @test
     */
    public function logAppendsLogEntryToAllLogAppender()
    {
        $logEntry     = new LogEntry('testTarget', $this->logger);
        $logAppender1 = NewInstance::of(LogAppender::class);
        $logAppender2 = NewInstance::of(LogAppender::class);
        $this->logger->addAppender($logAppender1);
        $this->logger->addAppender($logAppender2);
        $this->logger->log($logEntry);
        verify($logAppender1, 'append')->received($logEntry);
        verify($logAppender2, 'append')->received($logEntry);
    }

    /**
     * @since  1.1.0
     * @test
     */
    public function processDelayedLogEntriesWithoutDelayedLogEntriesReturn0()
    {
        assert($this->logger->processDelayedLogEntries(), equals(0));
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
        $logAppender1 = NewInstance::of(LogAppender::class);
        $logAppender2 = NewInstance::of(LogAppender::class);
        $this->logger->addAppender($logAppender1);
        $this->logger->addAppender($logAppender2);
        assert($this->logger->processDelayedLogEntries(), equals(1));
        verify($logAppender1, 'append')->received($logEntry);
        verify($logAppender2, 'append')->received($logEntry);
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
        $logAppender1 = NewInstance::of(LogAppender::class);
        $logAppender2 = NewInstance::of(LogAppender::class);
        $this->logger->addAppender($logAppender1);
        $this->logger->addAppender($logAppender2);
        $this->logger->logDelayed($logEntry);
        $this->logger->cleanup();
        verify($logAppender1, 'append')->received($logEntry);
        verify($logAppender2, 'append')->received($logEntry);
    }
}
