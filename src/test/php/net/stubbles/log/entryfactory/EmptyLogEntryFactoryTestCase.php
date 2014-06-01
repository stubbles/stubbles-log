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
 * Test for stubbles\log\entryfactory\EmptyLogEntryFactory.
 *
 * @group  entryfactory
 */
class EmptyLogEntryFactoryTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  EmptyLogEntryFactory
     */
    private $emptyLogEntryFactory;
    /**
     * created instance
     *
     * @type  LogEntry
     */
    private $logEntry;
    /**
     * mocked logger instance
     *
     * @type  PHPUnit_Framework_MockObject_MockObject
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
        $this->emptyLogEntryFactory = new EmptyLogEntryFactory();
        $this->logEntry             = $this->emptyLogEntryFactory->create('testTarget', $this->mockLogger);
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
    public function createdLogEntryIsEmpty()
    {
        $this->assertEquals('', $this->logEntry->get());
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
                          $this->emptyLogEntryFactory->recreate($this->logEntry,
                                                                $this->mockLogger
                                                       )
        );
    }
}
