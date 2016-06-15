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
use bovigo\callmap\NewInstance;
use stubbles\log\Logger;

use function bovigo\assert\assert;
use function bovigo\assert\predicate\equals;
use function bovigo\assert\predicate\isGreaterThan;
use function bovigo\assert\predicate\isLessThan;
use function bovigo\assert\predicate\isSameAs;
use function bovigo\callmap\verify;
/**
 * Test for stubbles\log\entryfactory\TimedLogEntryFactory.
 *
 * @group  entryfactory
 */
class TimedLogEntryFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  TimedLogEntryFactory
     */
    private $timedLogEntryFactory;
    /**
     * created instance without session
     *
     * @type  LogEntry
     */
    private $logEntry;
    /**
     * mocked logger instance
     *
     * @type  \bovigo\callmap\Proxy
     */
    private $logger;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->logger           = NewInstance::stub(Logger::class);
        $this->timedLogEntryFactory = new TimedLogEntryFactory();
        $this->logEntry             = $this->timedLogEntryFactory->create(
                'testTarget',
                $this->logger
        );
    }

    /**
     * @test
     */
    public function createdLogEntryHasCorrectTarget()
    {
        assert($this->logEntry->target(), equals('testTarget'));
    }

    /**
     * @test
     */
    public function createdLogEntryContainsTime()
    {
        $currentTime = time();
        $loggedTime  = strtotime($this->logEntry);
        assert(
                $loggedTime,
                isGreaterThan($currentTime - 2)->and(isLessThan($currentTime + 2))
        );
    }

    /**
     * @test
     */
    public function createdLogEntryCallsGivenLogger()
    {
        $this->logEntry->log();
        verify($this->logger, 'log')->received($this->logEntry);
    }

    /**
     * @test
     */
    public function recreateOnlyReturnsGivenLogEntryUnmodified()
    {
        assert(
                $this->timedLogEntryFactory->recreate(
                        $this->logEntry,
                        $this->logger
                ),
                isSameAs($this->logEntry)
        );
    }
}
