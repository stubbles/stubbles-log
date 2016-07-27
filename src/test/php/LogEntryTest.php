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

use function bovigo\assert\assert;
use function bovigo\assert\assertEmptyArray;
use function bovigo\assert\assertEmptyString;
use function bovigo\assert\predicate\equals;
use function bovigo\callmap\verify;
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
        assert($this->logEntry->target(), equals('testTarget'));
    }

    /**
     * @test
     */
    public function logDataIsInitiallyEmpty()
    {
        assertEmptyString((string) $this->logEntry);
    }

    /**
     * @test
     */
    public function logCallsGivenLogger()
    {
        $this->logEntry->log();
        verify($this->logger, 'log')->received($this->logEntry);
    }

    /**
     * @test
     * @since  1.1.0
     */
    public function logDelayedCallsGivenLogger()
    {
        $this->logEntry->logDelayed();
        verify($this->logger, 'logDelayed')->received($this->logEntry);
    }

    public function logData(): array
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
     * @dataProvider  logData
     */
    public function loggedDataWillBeEscaped($expected, $data)
    {
        assert(
                $this->logEntry->addData($data)->data(),
                equals([$expected])
        );
    }

    /**
     * @param  string  $expected
     * @param  string  $data
     * @test
     * @dataProvider  logData
     */
    public function loggedDataWillBeEscapedInLine($expected, $data)
    {
        assert(
                $this->logEntry->addData('foo', $data, 'bar'),
                equals('foo' . LogEntry::DEFAULT_SEPERATOR . $expected . LogEntry::DEFAULT_SEPERATOR . 'bar')
        );
    }

    /**
     * @param  string  $expected
     * @param  string  $data
     * @since  1.1.0
     * @test
     * @dataProvider  logData
     */
    public function loggedReplacedDataWillBeEscaped($expected, $data)
    {
        assert(
                $this->logEntry->addData("test1")->replaceData(0, $data)->data(),
                equals([$expected])
        );
    }

    /**
     * @param  string  $expected
     * @param  string  $data
     * @since  1.1.0
     * @test
     * @dataProvider  logData
     */
    public function loggedReplacedDataWillBeEscapedInLine($expected, $data)
    {
        assert(
                $this->logEntry->addData('foo', 'baz', 'bar')->replaceData(1, $data),
                equals('foo' . LogEntry::DEFAULT_SEPERATOR . $expected . LogEntry::DEFAULT_SEPERATOR . 'bar')
        );
    }

    /**
     * @test
     */
    public function addDataRemovesDifferentSeperator()
    {
        assert(
                $this->logEntry
                        ->setSeperator(';')
                        ->addData('foo' . LogEntry::DEFAULT_SEPERATOR . 'bar;baz')
                        ->data(),
                equals(['foo' . LogEntry::DEFAULT_SEPERATOR . 'barbaz'])
        );
    }

    /**
     * @since  1.1.0
     * @test
     */
    public function replaceDataRemovesDifferentSeperator()
    {
        assert(
                $this->logEntry
                        ->setSeperator(';')
                        ->addData('test')
                        ->replaceData(0, 'foo' . LogEntry::DEFAULT_SEPERATOR . 'bar;baz')
                        ->data(),
                equals(['foo' . LogEntry::DEFAULT_SEPERATOR . 'barbaz'])
        );
    }

    /**
     * @since  1.1.0
     * @test
     */
    public function replaceDataDoesNothingIfGivenPositionDoesNotExist()
    {
        assertEmptyArray($this->logEntry->replaceData(0, "foo")->data());
    }
}
