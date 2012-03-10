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
/**
 * Test for net\stubbles\log\ioc\LogBindingModule.
 *
 * @group  ioc
 */
class LogBindingModuleTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * configures the bindings
     *
     * @param   LogBindingModule  $logBindingModule
     * @return  net\stubbles\ioc\Injector
     */
    private function configureBindings(LogBindingModule $logBindingModule)
    {
        $binder = new Binder();
        $logBindingModule->configure($binder);
        return $binder->getInjector();
    }

    /**
     * @test
     */
    public function logPathIsIsNotBoundWhenNotGiven()
    {
        $this->assertFalse($this->configureBindings(LogBindingModule::create())
                                ->hasConstant('net.stubbles.log.path')
        );
    }

    /**
     * @test
     */
    public function logPathIsBoundToProjectPathWhenGiven()
    {

        $this->assertSame(__DIR__ . DIRECTORY_SEPARATOR . 'log',
                          $this->configureBindings(LogBindingModule::createWithProjectPath(__DIR__))
                               ->getConstant('net.stubbles.log.path')
        );
    }

    /**
     * @test
     */
    public function logPathIsBoundWhenGiven()
    {
        $this->assertSame(__DIR__,
                          $this->configureBindings(LogBindingModule::createWithLogPath(__DIR__))
                               ->getConstant('net.stubbles.log.path')
        );
    }

    /**
     * @test
     */
    public function logEntryFactoryIsBoundAsSingleton()
    {
        $injector = $this->configureBindings(new LogBindingModule());
        $this->assertSame($injector->getInstance('net\\stubbles\\log\\entryfactory\\LogEntryFactory'),
                          $injector->getInstance('net\\stubbles\\log\\entryfactory\\LogEntryFactory')
        );
    }

    /**
     * @test
     */
    public function logEntryFactoryClassIsBoundToTimedLogEntryFactoryByDefault()
    {
        $this->assertInstanceOf('net\\stubbles\\log\\entryfactory\\TimedLogEntryFactory',
                                $this->configureBindings(LogBindingModule::create())
                                     ->getInstance('net\\stubbles\\log\\entryfactory\\LogEntryFactory')
        );
    }

    /**
     * @test
     */
    public function logEntryFactoryClassIsBoundToConfiguredLogEntryFactoryClass()
    {
        $this->assertInstanceOf('net\\stubbles\\log\\entryfactory\\EmptyLogEntryFactory',
                                $this->configureBindings(LogBindingModule::create()
                                                                         ->setLogEntryFactory('net\\stubbles\\log\\entryfactory\\EmptyLogEntryFactory')
                                       )
                                     ->getInstance('net\\stubbles\\log\\entryfactory\\LogEntryFactory')
        );
    }

    /**
     * @test
     */
    public function loggerCanBeCreated()
    {
        $this->assertInstanceOf('net\\stubbles\\log\\Logger',
                                $this->configureBindings(LogBindingModule::createWithLogPath(__DIR__))
                                     ->getInstance('net\\stubbles\\log\\Logger')
        );
    }

    /**
     * @test
     */
    public function loggerCanBeCreatedUsingDifferentLoggerProvider()
    {
        $this->assertInstanceOf('net\\stubbles\\log\\Logger',
                                $this->configureBindings(LogBindingModule::createWithLogPath(__DIR__)
                                                                         ->setLoggerProvider('net\\stubbles\\log\\ioc\\LoggerProvider')
                                       )
                                     ->getInstance('net\\stubbles\\log\\Logger')
        );
    }
}
?>