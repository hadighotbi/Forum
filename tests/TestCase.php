<?php

namespace Tests;

use App\Exceptions\Handler;
use App\Models\User;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use DatabaseMigrations;
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();
        $this->disableExceptionHandling();
    }

    protected function signIn ($user = null): TestCase
    {
        $user = $user ?: User::factory()->create();
        $this->actingAs($user);
        return $this;
    }

    protected function disableExceptionHandling()
    {
        $this->oldExceptionHandler = $this->app->make(ExceptionHandler::class);

        $this->app->instance(ExceptionHandler::class, new class extends     Handler {
            public function __construct() {}
            public function report(\Throwable $e) {}
            public function render($request, \Throwable $e) {
                throw $e;
            }
        });
    }
    protected function withExceptionHandling()
    {
        $this->app->instance(ExceptionHandler::class, $this->oldExceptionHandler);
        return $this;
    }
}
