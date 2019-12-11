<?php

namespace BYanelli\SelfValidatingModels\Tests;

use Illuminate\Config\Repository;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\EventServiceProvider;
use Illuminate\Filesystem\FilesystemServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Queue\QueueServiceProvider;
use Illuminate\Translation\TranslationServiceProvider;
use Illuminate\Validation\ValidationServiceProvider;
use PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
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

        (new TranslationServiceProvider($app))->register();
        (new ValidationServiceProvider($app))->register();
        (new FilesystemServiceProvider($app))->register();
        (new QueueServiceProvider($app))->register();
        (new EventServiceProvider($app))->register();

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

        collect([
            'create table posts (id integer primary key, title text, created_at timestamp , updated_at timestamp );',
            'create table comments (id integer primary key, body text, created_at timestamp , updated_at timestamp );',
            'create table users (id integer primary key, email text, created_at timestamp , updated_at timestamp );',
        ])
            ->each(function (string $statement) use ($capsule) {
                $capsule->getConnection()->statement($statement);
            });
    }
}
