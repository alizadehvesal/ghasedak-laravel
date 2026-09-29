<?php

namespace Vestra\Ghasedak\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Vestra\Ghasedak\GhasedakServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [GhasedakServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('ghasedak.api_key', 'test-api-key');
        $app['config']->set('ghasedak.base_url', 'http://api.ghasedaksms.com/v2');
        $app['config']->set('ghasedak.templates', [
            'verification' => 'login_code',
            'invoice' => 'invoice_code',
        ]);
    }
}
