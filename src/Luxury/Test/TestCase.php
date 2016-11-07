<?php

namespace Luxury\Test;

use Luxury\Foundation\Kernelize;
use Luxury\Support\Facades\Facade;
use Mockery;
use Phalcon\Application;
use Phalcon\Config;
use Phalcon\Config as PhConfig;
use Phalcon\Di\InjectionAwareInterface;
use Phalcon\DiInterface;
use PHPUnit_Framework_TestCase as UnitTestCase;

/**
 * Class UnitTestCase
 *
 * @package Phalcon\Test
 */
abstract class TestCase extends UnitTestCase implements InjectionAwareInterface
{
    /**
     * Holds the configuration variables and other stuff
     * I can use the DI container but for tests like the Translate
     * we do not need the overhead
     *
     * @var Config|null
     */
    protected $config;

    /**
     * @var Application|Kernelize
     */
    protected $app;

    /**
     * @var \Luxury\Foundation\Application
     */
    protected $lxApp;

    /**
     * @var Application|Kernelize
     */
    protected static $kernelClassInstance;

    /**
     * @var \Luxury\Foundation\Application
     */
    protected static $appClassInstance;

    /**
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        global $config;

        parent::setUp();

        $this->checkExtension('phalcon');

        // Creating the application
        $this->lxApp = new \Luxury\Foundation\Application(new PhConfig($config));
        $this->app   = $this->lxApp->make($this->kernel());
    }

    /**
     * @return string
     */
    protected function kernel()
    {
        return static::kernelClassInstance();
    }

    /**
     * @return string
     */
    protected static function kernelClassInstance()
    {
        throw new \RuntimeException("kernelClassInstance not implemented.");
    }

    /**
     * @return \Luxury\Foundation\Kernelize|\Phalcon\Application
     */
    protected static function staticKernel()
    {
        global $config;
        if (self::$appClassInstance == null) {
            self::$appClassInstance = new \Luxury\Foundation\Application(new PhConfig($config));
        }

        if (self::$kernelClassInstance == null) {
            self::$kernelClassInstance = self::$appClassInstance->make(static::kernelClassInstance());

            Facade::clearResolvedInstances();
        }

        return self::$kernelClassInstance;
    }

    protected function tearDown()
    {
        Mockery::close();
        Facade::clearResolvedInstances();
        $this->app->getDI()->reset();
        $this->app = null;

        self::$appClassInstance    = null;
        self::$kernelClassInstance = null;

        parent::tearDown();
    }

    /**
     * Checks if a particular extension is loaded and if not it marks
     * the tests skipped
     *
     * @param mixed $extension
     */
    public function checkExtension($extension)
    {
        $message = function ($ext) {
            sprintf('Warning: %s extension is not loaded', $ext);
        };

        if (is_array($extension)) {
            foreach ($extension as $ext) {
                if (!extension_loaded($ext)) {
                    $this->markTestSkipped($message($ext));
                    break;
                }
            }
        } elseif (!extension_loaded($extension)) {
            $this->markTestSkipped($message($extension));
        }
    }

    /**
     * Returns a unique file name
     *
     * @param  string $prefix A prefix for the file
     * @param  string $suffix A suffix for the file
     *
     * @return string
     */
    protected function getFileName($prefix = '', $suffix = 'log')
    {
        $prefix = ($prefix) ? $prefix . '_' : '';
        $suffix = ($suffix) ? $suffix : 'log';

        return uniqid($prefix, true) . '.' . $suffix;
    }

    /**
     * Removes a file from the system
     *
     * @param string $path
     * @param string $fileName
     */
    protected function cleanFile($path, $fileName)
    {
        $file = (substr($path, -1, 1) != "/") ? ($path . '/') : $path;
        $file .= $fileName;

        $actual = file_exists($file);

        if ($actual) {
            unlink($file);
        }
    }

    /**
     * Sets the Config object.
     *
     * @param Config $config
     *
     * @return $this
     */
    public function setConfig(Config $config)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * Returns the Config object if any.
     *
     * @return null|Config
     */
    public function getConfig()
    {
        if (!$this->config instanceof Config && $this->getDI()->has('config')) {
            return $this->getDI()->getShared('config');
        }

        return $this->config;
    }

    /**
     * Sets the Dependency Injector.
     *
     * @see    Injectable::setDI
     *
     * @param  DiInterface $di
     *
     * @return $this
     */
    public function setDI(DiInterface $di)
    {
        return $this->app->setDI($di);
    }

    /**
     * Returns the internal Dependency Injector.
     *
     * @see    Injectable::getDI
     * @return DiInterface
     */
    public function getDI()
    {
        return $this->app->getDI();
    }

    /**
     * @param string $className
     * @param string $propertyName
     *
     * @return \ReflectionProperty
     */
    public function getPrivateProperty($className, $propertyName)
    {
        $reflector = new \ReflectionClass($className);
        $property  = $reflector->getProperty($propertyName);

        $property->setAccessible(true);

        return $property;
    }

    /**
     * @param string $className
     * @param string $methodName
     *
     * @return \ReflectionMethod
     */
    public function getPrivateMethod($className, $methodName)
    {
        $reflection = new \ReflectionClass($className);
        $method     = $reflection->getMethod($methodName);

        $method->setAccessible(true);

        return $method;
    }

    /**
     * Call protected/private method of a class.
     *
     * @param object &$object    Instantiated object that we will run method on.
     * @param string $methodName Method name to call
     * @param array  $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     */
    public function invokeMethod(&$object, $methodName, array $parameters = [])
    {
        return $this->getPrivateMethod(
            get_class($object),
            $methodName
        )->invokeArgs($object, $parameters);
    }

    /**
     * @param object $object
     * @param string $propertyName
     * @param null   $className
     *
     * @return mixed
     */
    public function valueProperty(&$object, $propertyName, $className = null)
    {
        return $this->getPrivateProperty(
            $className ? $className : get_class($object),
            $propertyName
        )->getValue($object);
    }
}
