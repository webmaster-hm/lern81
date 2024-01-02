<?php

namespace Tylercd100\LERN\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Tylercd100\LERN\Factories\MonologHandlerFactory;
use Exception;
use Throwable;
use Illuminate\Support\Facades\Cache;

class TestCase extends Orchestra
{
    /**
     * Setup the test environment.
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->supportedDrivers = [
            'slack',
            'mail',
            'pushover',
            'plivo',
            'twilio',
            'flowdock',
            'fleephook',
            'mailgun'
        ];
    }

    public function tearDown(): void
    {
        Cache::flush();
        parent::tearDown();
    }

    protected function migrate()
    {
        $path = "../../../../tests/migrations";
        $this->artisan('migrate:fresh', [
            '--database' => 'testbench',
            '--path' => $path,
        ]);
    }

    protected function migrateReset()
    {
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getPackageProviders($app)
    {
        return [
            \Tylercd100\LERN\LERNServiceProvider::class
        ];
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getPackageAliases($app)
    {
        return [
            \Tylercd100\LERN\Facades\LERN::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('cache.default', 'file');
        $app['config']->set('lern.ratelimit', 5);

        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        $app['config']->set('lern.record', array_merge($app['config']->get('lern.record'), [
            'table' => 'vendor_tylercd100_lern_exceptions',
            'collect' => [
                'method' => true, //When true it will collect GET, POST, DELETE, PUT, etc...
                'data' => true, //When true it will collect Input data
                'status_code' => true,
                'user_id' => true,
                'url' => true,
            ],
            'excludeKeys' => [
                'password'
            ]
        ]));




        // Copy stuff
        $root = __DIR__ . "/../vendor/orchestra/testbench/fixture/resources/views";
        if (!is_dir($root)) {
            $root = __DIR__ . "/../vendor/orchestra/testbench-core/laravel/resources/views";
            if (!is_dir($root)) {
                throw new Exception("Could not find laravel inside of testbench. Is testbench installed?");
            }
        }

        // Test view
        copy(__DIR__ . "/views/test.blade.php", "{$root}/test.blade.php");

        // Exceptions
        $dir = "{$root}/exceptions";
        if (!is_dir($dir)) {
            mkdir($dir);
        }
        copy(__DIR__ . "/../views/exceptions/default.blade.php", $dir . "/default.blade.php");
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
    public function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
