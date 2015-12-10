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
 * Test for stubbles\log\LogEntry.
 *
 * @group  core
 */
class LogEntryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  \stubbles\log\LogEntry
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
        $this->logger   = NewInstance::stub(Logger::class);
        $this->logEntry = new LogEntry('testTarget', $this->logger);
    }

    /**
     * @test
     */
    public function returnsGivenTarget()
    {
        assertEquals('testTarget', $this->logEntry->target());
    }

    /**
     * @test
     */
    public function logDataIsInitiallyEmpty()
    {
        assertEquals('', (string) $this->logEntry);
    }

    /**
     * @test
     */
    public function logCallsGivenLogger()
    {
        $this->logEntry->log();
        callmap\verify($this->logger, 'log')->received($this->logEntry);
    }

    /**
     * @test
     * @since  1.1.0
     */
    public function logDelayedCallsGivenLogger()
    {
        $this->logEntry->logDelayed();
        callmap\verify($this->logger, 'logDelayed')->received($this->logEntry);
    }

    /**
     * returns quoted data
     *
     * @return  array
     */
    public function getLogData()
    {
        return [['"bar"', '"bar'],
                ['foo"bar', 'foo"bar'],
                ['"baz"', '"baz"'],
                ['', '"'],
                ['foo', "fo\ro"],
                ['ba<nl>r', "ba\nr"],
                ['ba<nl>z', "ba\r\nz"],
                ['foobar;baz', 'foo' . LogEntry::DEFAULT_SEPERATOR . 'bar;baz'],
        ];
    }

    /**
     * @param  string  $expected
     * @param  string  $data
     * @test
     * @dataProvider  getLogData
     */
    public function loggedDataWillBeEscaped($expected, $data)
    {
        assertEquals(
                [$expected],
                $this->logEntry->addData($data)
                               ->data()
        );
    }

    /**
     * @param  string  $expected
     * @param  string  $data
     * @test
     * @dataProvider  getLogData
     */
    public function loggedDataWillBeEscapedInLine($expected, $data)
    {
        assertEquals(
                'foo' . LogEntry::DEFAULT_SEPERATOR . $expected . LogEntry::DEFAULT_SEPERATOR . 'bar',
                $this->logEntry->addData('foo')->addData($data)->addData('bar')
        );
    }

    /**
     * @param  string  $expected
     * @param  string  $data
     * @since  1.1.0
     * @test
     * @dataProvider  getLogData
     */
    public function loggedReplacedDataWillBeEscaped($expected, $data)
    {
        assertEquals(
                [$expected],
                $this->logEntry->addData("test1")->replaceData(0, $data)->data()
        );
    }

    /**
     * @param  string  $expected
     * @param  string  $data
     * @since  1.1.0
     * @test
     * @dataProvider  getLogData
     */
    public function loggedReplacedDataWillBeEscapedInLine($expected, $data)
    {
        assertEquals(
                'foo' . LogEntry::DEFAULT_SEPERATOR . $expected . LogEntry::DEFAULT_SEPERATOR . 'bar',
                $this->logEntry
                        ->addData('foo')
                        ->addData('baz')
                        ->addData('bar')
                        ->replaceData(1, $data)
        );
    }

    /**
     * @test
     */
    public function addDataRemovesDifferentSeperator()
    {
        assertEquals(
                ['foo' . LogEntry::DEFAULT_SEPERATOR . 'barbaz'],
                $this->logEntry
                        ->setSeperator(';')
                        ->addData('foo' . LogEntry::DEFAULT_SEPERATOR . 'bar;baz')
                        ->data()
        );
    }

    /**
     * @since  1.1.0
     * @test
     */
    public function replaceDataRemovesDifferentSeperator()
    {
        assertEquals(
                ['foo' . LogEntry::DEFAULT_SEPERATOR . 'barbaz'],
                $this->logEntry
                        ->setSeperator(';')
                        ->addData('test')
                        ->replaceData(0, 'foo' . LogEntry::DEFAULT_SEPERATOR . 'bar;baz')
                        ->data()
        );
    }

    /**
     * @since  1.1.0
     * @test
     */
    public function replaceDataDoesNothingIfGivenPositionDoesNotExist()
    {
        assertEquals([], $this->logEntry->replaceData(0, "foo")->data());
    }
}
