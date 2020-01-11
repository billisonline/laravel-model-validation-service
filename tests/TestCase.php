<?php

namespace BYanelli\SelfValidatingModels\Tests;

use BYanelli\SelfValidatingModels\Tests\Support\ExpectsCustomExceptions;
use BYanelli\SelfValidatingModels\Tests\Support\ExpectsValidationExceptions;
use BYanelli\SelfValidatingModels\Tests\TestApp\ModelValidationServiceProvider;
use Illuminate\Config\Repository;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\EventServiceProvider;
use Illuminate\Filesystem\FilesystemServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Queue\QueueServiceProvider;
use Illuminate\Support\ServiceProvider;
use Illuminate\Translation\TranslationServiceProvider;
use Illuminate\Validation\ValidationServiceProvider;
use PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    use ExpectsCustomExceptions, ExpectsValidationExceptions;

    /**
     * @var Application|null
     */
    private static $app;

    public function ensureAppConfigured()
    {
        if (isset(self::$app)) {
            return self::$app;
        }

        $app = Application::getInstance();

        $app->instance('config', $config = new Repository([]));
        $app->instance('path.lang', __DIR__.'/resources/lang');

        $this->registerAndBootServiceProviders($app, [
            TranslationServiceProvider::class,
            ValidationServiceProvider::class,
            FilesystemServiceProvider::class,
            QueueServiceProvider::class,
            EventServiceProvider::class,
        ]);

        return (self::$app = $app);
    }

    public function setUp()
    {
        $app = $this->ensureAppConfigured();

        $this->setupDatabase($app);
    }

    /**
     * @param Application|null $app
     */
    private function setupDatabase(?Application $app): void
    {
        $capsule = new Capsule($app);

        $capsule->addConnection([
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $capsule->setAsGlobal();

        $capsule->bootEloquent();

        $this->registerAndBootServiceProviders($app, [ModelValidationServiceProvider::class]);

        collect([
            'create table posts (id integer primary key, title text, body text, unpublish_reason text, protected integer, published integer, created_at timestamp , updated_at timestamp );',
        ])
            ->each(function (string $statement) use ($capsule) {
                $capsule->getConnection()->statement($statement);
            });
    }

    /**
     * @param Application $app
     * @param string[]|ServiceProvider[]|array $providers
     */
    private function registerAndBootServiceProviders(Application $app, array $providers): void
    {
        collect($providers)
            ->map(function (string $provider) use ($app): ServiceProvider {
                return new $provider($app);
            })
            ->each(function (ServiceProvider $provider) {
                $provider->register();
            })
            ->each(function (ServiceProvider $provider) {
                if (method_exists($provider, 'boot')) {
                    $provider->boot();
                }
            });
    }
}
