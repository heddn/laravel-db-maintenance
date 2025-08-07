<?php

namespace FriendsOfCat\Tests\LaravelDbMaintenance;

use FriendsOfCat\LaravelDbMaintenance\Maintenance;
use FriendsOfCat\LaravelDbMaintenance\Provider\DbMaintenanceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(DbMaintenanceProvider::class)]
class ProviderIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function testGlobalMiddleware()
    {
        /** @var Maintenance $maintenance */
        $maintenance = $this->app->make(Maintenance::class);

        $router = $this->app->get('router');
        $router->get('/', fn() => new Response());

        $this->get('/')->assertSuccessful();

        $maintenance->down('Site is down!', 99);

        $this->get('/')
            ->assertStatus(503)
            ->assertHeader('Retry-After', 99);

        $maintenance->up();

        $this->get('/')->assertSuccessful();

        $maintenance->down('Site is down again!', 49);

        $this->get('/')
            ->assertStatus(503)
            ->assertHeader('Retry-After', 49);

        $maintenance->up();

        $this->get('/')->assertSuccessful();
    }
}
