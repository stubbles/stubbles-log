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
namespace stubbles\log\ioc;
use bovigo\callmap\NewInstance;
use stubbles\log\LogEntry;
use stubbles\log\entryfactory\LogEntryFactory;

use function bovigo\assert\assert;
use function bovigo\assert\predicate\isNotSameAs;
use function bovigo\assert\predicate\isSameAs;
/**
 * Test for stubbles\log\ioc\LoggerProvider.
 *
 * @group  ioc
 */
class LoggerProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  LoggerProvider
     */
    private $loggerProvider;
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
        $this->loggerProvider  = new LoggerProvider($this->logEntryFactory);
    }

    /**
     * @test
     */
    public function createdLoggerUsesGivenLogEntryFactory()
    {
        $logger   = $this->loggerProvider->get();
        $logEntry = new LogEntry('testTarget', $logger);
        $this->logEntryFactory->mapCalls(['create' => $logEntry]);
        assert( $logger->createLogEntry('testTarget'), isSameAs($logEntry));
    }

    /**
     * @test
     */
    public function createsDifferentInstancesForDifferentNames()
    {
        assert(
                $this->loggerProvider->get('foo'),
                isNotSameAs($this->loggerProvider->get())
        );
    }

    /**
     * @test
     */
    public function returnsSameInstanceForSameName()
    {
        assert($this->loggerProvider->get(), isSameAs($this->loggerProvider->get()));
    }
}
