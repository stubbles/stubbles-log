<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\log
 */
namespace net\stubbles\log\ioc;
use net\stubbles\ioc\Binder;
use net\stubbles\ioc\Injector;
/**
 * Test for net\stubbles\log\ioc\LogBindingModule.
 *
 * @group  ioc
 */
class LogBindingModuleTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  LogBindingModule
     */
    private $logBindingModule;
    /**
     * mocked log entry factory
     *
     * @type  Injector
     */
    private $injector;

    /**
     * set up the test environment
     */
    public function setUp()
    {
        $this->injector         = new Injector();
        $this->logBindingModule = LogBindingModule::create(__DIR__);
        $this->logBindingModule->configure(new Binder($this->injector));
    }

    /**
     * @test
     */
    public function logPathIsIsNotBoundWhenNotGiven()
    {
        $injector               = new Injector();
        $this->logBindingModule = new LogBindingModule();
        $this->logBindingModule->configure(new Binder($injector));
        $this->assertFalse($injector->hasConstant('net.stubbles.log.path'));
    }

    /**
     * @test
     */
    public function logPathIsBoundToProjectPathWhenGiven()
    {
        LogBindingModule::create(__DIR__)
                        ->configure(new Binder($this->injector));
        $this->assertSame(__DIR__ . DIRECTORY_SEPARATOR . 'log',
                          $this->injector->getConstant('net.stubbles.log.path')
        );
    }

    /**
     * @test
     */
    public function logPathIsBoundWhenGiven()
    {
        LogBindingModule::createWithLogPath(__DIR__)
                        ->configure(new Binder($this->injector));
        $this->assertSame(__DIR__,
                          $this->injector->getConstant('net.stubbles.log.path')
        );
    }

    /**
     * @test
     */
    public function logEntryFactoryIsBoundAsSingleton()
    {
        $this->assertSame($this->injector->getInstance('net\\stubbles\\log\\entryfactory\\LogEntryFactory'),
                          $this->injector->getInstance('net\\stubbles\\log\\entryfactory\\LogEntryFactory')
        );
    }

    /**
     * @test
     */
    public function logEntryFactoryClassIsBoundToTimedLogEntryFactoryByDefault()
    {
        $this->assertInstanceOf('net\\stubbles\\log\\entryfactory\\TimedLogEntryFactory',
                                $this->injector->getInstance('net\\stubbles\\log\\entryfactory\\LogEntryFactory')
        );
    }

    /**
     * @test
     */
    public function logEntryFactoryClassIsBoundToConfiguredLogEntryFactoryClass()
    {
        $this->logBindingModule->setLogEntryFactory('net\\stubbles\\log\\entryfactory\\EmptyLogEntryFactory')
                               ->configure(new Binder($this->injector));
        $this->assertInstanceOf('net\\stubbles\\log\\entryfactory\\EmptyLogEntryFactory',
                                $this->injector->getInstance('net\\stubbles\\log\\entryfactory\\LogEntryFactory')
        );
    }

    /**
     * @test
     */
    public function loggerCanBeCreated()
    {
        $this->assertInstanceOf('net\\stubbles\\log\\Logger',
                                $this->injector->getInstance('net\\stubbles\\log\\Logger')
        );
    }

    /**
     * @test
     */
    public function loggerCanBeCreatedUsingDifferentLoggerProvider()
    {
        $this->logBindingModule->setLoggerProvider('net\\stubbles\\log\\ioc\\LoggerProvider')
                               ->configure(new Binder($this->injector));
        $this->assertInstanceOf('net\\stubbles\\log\\Logger',
                                $this->injector->getInstance('net\\stubbles\\log\\Logger')
        );
    }
}
?>