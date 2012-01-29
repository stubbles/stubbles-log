<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\log
 */
namespace net\stubbles\log;
/**
 * Test for net\stubbles\log\LogEntry.
 *
 * @group  log
 * @group  log_core
 */
class LogEntryTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  LogEntry
     */
    protected $logEntry;
    /**
     * mocked logger instance
     *
     * @type  \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockLogger;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockLogger = $this->getMock('net\\stubbles\\log\\Logger', array(), array(), '', false);
        $this->logEntry   = new LogEntry('testTarget', $this->mockLogger);
    }

    /**
     * @test
     */
    public function returnsGivenTarget()
    {
        $this->assertEquals('testTarget', $this->logEntry->getTarget());
    }

    /**
     * @test
     */
    public function logDataIsInitiallyEmpty()
    {
        $this->assertEquals('', $this->logEntry->get());
    }

    /**
     * @test
     */
    public function logCallsGivenLogger()
    {
        $this->mockLogger->expects($this->once())
                         ->method('log')
                         ->with($this->logEntry);
        $this->logEntry->log();
    }

    /**
     * @test
     * @since  1.1.0
     */
    public function logDelayedCallsGivenLogger()
    {
        $this->mockLogger->expects($this->once())
                         ->method('logDelayed')
                         ->with($this->logEntry);
        $this->logEntry->logDelayed();
    }

    /**
     * returns quoted data
     *
     * @return  array
     */
    public function getLogData()
    {
        return array(array('"bar"', '"bar'),
                     array('foo"bar', 'foo"bar'),
                     array('"baz"', '"baz"'),
                     array('', '"'),
                     array('foo', "fo\ro"),
                     array('ba<nl>r', "ba\nr"),
                     array('ba<nl>z', "ba\r\nz"),
                     array('foobar;baz', 'foo' . LogEntry::DEFAULT_SEPERATOR . 'bar;baz'),
        );
    }

    /**
     * @param  string  $excepted
     * @param  string  $data
     * @test
     * @dataProvider  getLogData
     */
    public function loggedDataWillBeEscaped($expected, $data)
    {
        $this->assertEquals(array($expected),
                            $this->logEntry->addData($data)
                                           ->getData()
        );
    }

    /**
     * @param  string  $excepted
     * @param  string  $data
     * @test
     * @dataProvider  getLogData
     */
    public function loggedDataWillBeEscapedInLine($expected, $data)
    {
        $this->assertEquals('foo' . LogEntry::DEFAULT_SEPERATOR . $expected . LogEntry::DEFAULT_SEPERATOR . 'bar',
                            $this->logEntry->addData('foo')
                                           ->addData($data)
                                           ->addData('bar')
                                           ->get()
        );
    }

    /**
     * @param  string  $excepted
     * @param  string  $data
     * @since  1.1.0
     * @test
     * @dataProvider  getLogData
     */
    public function loggedReplacedDataWillBeEscaped($expected, $data)
    {
        $this->assertEquals(array($expected),
                            $this->logEntry->addData("test1")
                                           ->replaceData(0, $data)
                                           ->getData()
        );
    }

    /**
     * @param  string  $excepted
     * @param  string  $data
     * @since  1.1.0
     * @test
     * @dataProvider  getLogData
     */
    public function loggedReplacedDataWillBeEscapedInLine($expected, $data)
    {
        $this->assertEquals('foo' . LogEntry::DEFAULT_SEPERATOR . $expected . LogEntry::DEFAULT_SEPERATOR . 'bar',
                            $this->logEntry->addData('foo')
                                           ->addData('baz')
                                           ->addData('bar')
                                           ->replaceData(1, $data)
                                           ->get()
        );
    }

    /**
     * @test
     */
    public function addDataRemovesDifferentSeperator()
    {
        $this->assertEquals(array('foo' . LogEntry::DEFAULT_SEPERATOR . 'barbaz'),
                            $this->logEntry->setSeperator(';')
                                           ->addData('foo' . LogEntry::DEFAULT_SEPERATOR . 'bar;baz')
                                           ->getData()
        );
    }

    /**
     * @since  1.1.0
     * @test
     */
    public function replaceDataRemovesDifferentSeperator()
    {
        $this->assertEquals(array('foo' . LogEntry::DEFAULT_SEPERATOR . 'barbaz'),
                            $this->logEntry->setSeperator(';')
                                           ->addData('test')
                                           ->replaceData(0, 'foo' . LogEntry::DEFAULT_SEPERATOR . 'bar;baz')
                                           ->getData()
        );
    }

    /**
     * @since  1.1.0
     * @test
     */
    public function replaceDataDoesNothingIfGivenPositionDoesNotExist()
    {
        $this->assertEquals(array(),
                            $this->logEntry->replaceData(0, "foo")
                                           ->getData()
        );
    }
}
?>