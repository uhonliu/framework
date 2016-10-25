<?php
namespace Test\Providers;

use Luxury\Constants\Services;
use Luxury\Providers\Cli\Router;
use Test\Stub\StubKernelCliEmpty;
use Test\TestCase\TestCase;

/**
 * Trait ProvideCliRouterTest
 *
 * @package Test\Providers
 */
class ProvideCliRouterTest extends TestCase
{
    protected function kernel()
    {
        return StubKernelCliEmpty::class;
    }

    public function testRegister()
    {
        $provider = new Router;

        $provider->registering();

        $this->assertTrue($this->getDI()->has(Services::ROUTER));

        $this->assertTrue($this->getDI()->getService(Services::ROUTER)->isShared());

        $this->assertInstanceOf(
            \Phalcon\Cli\Router::class,
            $this->getDI()->getShared(Services::ROUTER)
        );
    }
}