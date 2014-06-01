<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\log
 */
namespace stubbles\log\appender;
use org\bovigo\vfs\vfsStream;
use stubbles\log\LogEntry;
/**
 * Test for stubbles\log\appender\FileLogAppender.
 *
 * @group  appender
 */
class FileLogAppenderTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  FileLogAppender
     */
    private $fileLogAppender;
    /**
     * the logfile
     *
     * @type  string
     */
    private $logFile;
    /**
     * logfile directory
     *
     * @type  org\bovigo\vfs\vfsStreamDirectory
     */
    private $root;

    /**
     * set up the test environment
     */
    public function setUp()
    {
        $this->root            = vfsStream::setup();
        $this->logFile         = vfsStream::url('root/test/foo-' . date('Y-m-d') . '.log');
        $this->fileLogAppender = new FileLogAppender(vfsStream::url('root/test'));
    }

    /**
     * creates log entry
     *
     * @return  stubLogEntry
     */
    protected function createLogEntry()
    {
        $logEntry = new LogEntry('foo',
                                 $this->getMockBuilder('stubbles\log\Logger')
                                      ->disableOriginalConstructor()
                                      ->getMock()
                    );
        return $logEntry->addData('bar')
                        ->addData('baz');
    }

    /**
     * @test
     */
    public function appendWritesLogEntryToLogfile()
    {
        $this->assertSame($this->fileLogAppender,
                          $this->fileLogAppender->append($this->createLogEntry())
                                                ->append($this->createLogEntry())
        );
        $this->assertTrue(file_exists($this->logFile));
        $this->assertEquals("bar|baz\nbar|baz\n", file_get_contents($this->logFile));
    }

    /**
     * @test
     */
    public function createsNonExistingDirectoryWithDefaultFilemode()
    {
        $this->assertSame($this->fileLogAppender,
                          $this->fileLogAppender->append($this->createLogEntry())
        );
        $this->assertEquals(0700, $this->root->getChild('test')->getPermissions());
    }

    /**
     * @test
     */
    public function createsNonExistingDirectoryWithOtherFilemode()
    {
        $this->assertSame($this->fileLogAppender,
                          $this->fileLogAppender->setMode(0644)
                                                ->append($this->createLogEntry())
        );
        $this->assertEquals(0644, $this->root->getChild('test')->getPermissions());
    }

    /**
     * @test
     */
    public function finalizeIsNoOp()
    {
        $this->assertSame($this->fileLogAppender,
                          $this->fileLogAppender->finalize()
        );
    }
}
