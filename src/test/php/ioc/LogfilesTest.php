<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\log
 */
namespace stubbles\log\ioc;
use stubbles\ioc\Binder;
/**
 * Test for stubbles\log\ioc\Logfiles.
 *
 * @group  ioc
 */
class LogfilesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * configures the bindings
     *
     * @param   \stubbles\log\ioc\Logfiles  $logfiles
     * @return  \stubbles\ioc\Injector
     */
    private function configureBindings(Logfiles $logfiles)
    {
        $binder = new Binder();
        $logfiles->configure($binder);
        return $binder->getInjector();
    }

    /**
     * @test
     */
    public function logPathIsIsNotBoundWhenNotGiven()
    {
        $this->assertFalse(
                $this->configureBindings(new Logfiles())
                     ->hasConstant('stubbles.log.path')
        );
    }

    /**
     * @test
     */
    public function logPathIsBoundWhenGiven()
    {
        $this->assertSame(
                __DIR__,
                $this->configureBindings((new Logfiles())->writeTo(__DIR__))
                     ->getConstant('stubbles.log.path')
        );
    }

    /**
     * @test
     */
    public function logEntryFactoryIsBoundAsSingleton()
    {
        $injector = $this->configureBindings(new Logfiles());
        $this->assertSame(
                $injector->getInstance('stubbles\log\entryfactory\LogEntryFactory'),
                $injector->getInstance('stubbles\log\entryfactory\LogEntryFactory')
        );
    }

    /**
     * @test
     */
    public function logEntryFactoryClassIsBoundToTimedLogEntryFactoryByDefault()
    {
        $this->assertInstanceOf(
                'stubbles\log\entryfactory\TimedLogEntryFactory',
                $this->configureBindings(new Logfiles())
                     ->getInstance('stubbles\log\entryfactory\LogEntryFactory')
        );
    }

    /**
     * @test
     */
    public function logEntryFactoryClassIsBoundToConfiguredLogEntryFactoryClass()
    {
        $this->assertInstanceOf(
                'stubbles\log\entryfactory\EmptyLogEntryFactory',
                $this->configureBindings(
                        (new Logfiles())->createEntriesWith('stubbles\log\entryfactory\EmptyLogEntryFactory')
                       )
                     ->getInstance('stubbles\log\entryfactory\LogEntryFactory')
        );
    }

    /**
     * @test
     */
    public function loggerCanBeCreated()
    {
        $this->assertInstanceOf(
                'stubbles\log\Logger',
               $this->configureBindings((new Logfiles())->writeTo(__DIR__))
                    ->getInstance('stubbles\log\Logger')
        );
    }

    /**
     * @test
     */
    public function loggerCanBeCreatedUsingDifferentLoggerProvider()
    {
        $this->assertInstanceOf(
                'stubbles\log\Logger',
                $this->configureBindings(
                        (new Logfiles())->writeTo(__DIR__)->loggerProvidedBy('stubbles\log\ioc\LoggerProvider')
                       )
                     ->getInstance('stubbles\log\Logger')
        );
    }
}
