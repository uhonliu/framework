<?php

namespace Test\Cli\Tasks;

use Test\Stub\StubKernelCli;
use Test\TestCase\TestCase;

/**
 * Class ClearCompiledTaskTest
 *
 * @package Test\Cli\Tasks
 */
class ClearCompiledTaskTest extends TestCase
{
    protected static function kernelClassInstance()
    {
        return StubKernelCli::class;
    }

    public function setUp()
    {
        global $config;

        $config['paths']['base'] = __DIR__ . '/../../../';

        mkdir(__DIR__ . '/../../../' . '/bootstrap/compile', 0777, true);

        parent::setUp();
    }

    public function tearDown()
    {
        foreach (glob(__DIR__ . '/../../../' . '/bootstrap/compile/*.*') as $file) {
            @unlink($file);
        }

        @rmdir(__DIR__ . '/../../../' . '/bootstrap/compile');
        @rmdir(__DIR__ . '/../../../' . '/bootstrap');

        parent::tearDown();
    }

    public function testTask()
    {
        file_put_contents(__DIR__ . '/../../../' . '/bootstrap/compile/loader.php', '<?php');

        $this->dispatchCli('luxury clear-compiled -q');

        $this->assertEquals([], glob(__DIR__ . '/../../../' . '/bootstrap/compile/*.*'));
    }
}
