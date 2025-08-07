<?php

namespace FriendsOfCat\Tests\LaravelDbMaintenance\Console;

use FriendsOfCat\LaravelDbMaintenance\Console\UpCommand;
use FriendsOfCat\LaravelDbMaintenance\Maintenance;
use FriendsOfCat\Tests\LaravelDbMaintenance\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(UpCommand::class)]
class UpCommandTest extends TestCase
{
    use RefreshDatabase;

    public function testHandle()
    {
        $this->withoutMockingConsoleOutput();

        $maintenance = $this->app->make(Maintenance::class);
        $maintenance->down();

        $this->artisan('up');

        $this->assertTrue($maintenance->isUp());

        $latest = $maintenance->getLatest();

        $this->assertEquals(0, $latest->status);
        $this->assertEquals(60, $latest->retry_after);
        $this->assertSame('', $latest->message);

        $this->assertDatabaseHas('maintenance', ['id' => $latest->id, 'status' => false, 'retry_after' => 60]);
    }
}
