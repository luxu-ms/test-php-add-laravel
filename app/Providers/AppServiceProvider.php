<?php

declare(strict_types=1);

namespace App\Providers;

use Database;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use PDO;
use TaskRepository;

final class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PDO::class, fn (): PDO => Database::connect());
        $this->app->bind(
            TaskRepository::class,
            fn (Application $app): TaskRepository => new TaskRepository($app->make(PDO::class)),
        );
    }
}
