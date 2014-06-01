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
/**
 * Test for stubbles\log\entryfactory\TimedLogEntryFactory.
 *
 * @group  entryfactory
 */
class TimedLogEntryFactoryTestCase extends \PHPUnit_Framework_TestCase
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
     * @type  \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockLogger;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockLogger           = $this->getMockBuilder('stubbles\log\Logger')
                                           ->disableOriginalConstructor()
                                           ->getMock();
        $this->timedLogEntryFactory = new TimedLogEntryFactory();
        $this->logEntry             = $this->timedLogEntryFactory->create('testTarget', $this->mockLogger);
    }

    /**
     * @test
     */
    public function createdLogEntryHasCorrectTarget()
    {
        $this->assertEquals('testTarget', $this->logEntry->getTarget());
    }

    /**
     * @test
     */
    public function createdLogEntryContainsTime()
    {
        $currentTime = time();
        $loggedTime  = strtotime($this->logEntry->get());
        $this->assertGreaterThan($currentTime -2, $loggedTime);
        $this->assertLessThan($currentTime +2, $loggedTime);
    }

    /**
     * @test
     */
    public function createdLogEntryCallsGivenLogger()
    {
        $this->mockLogger->expects($this->once())
                         ->method('log')
                         ->with($this->logEntry);
        $this->logEntry->log();
    }

    /**
     * @test
     */
    public function recreateOnlyReturnsGivenLogEntryUnmodified()
    {
        $this->assertSame($this->logEntry,
                          $this->timedLogEntryFactory->recreate($this->logEntry,
                                                                $this->mockLogger
                                                         )
        );
    }
}
