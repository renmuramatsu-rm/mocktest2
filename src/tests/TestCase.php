<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->disableMiddlewareForAllTests();
    }

    protected function disableMiddlewareForAllTests(): void
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);
    }
}
