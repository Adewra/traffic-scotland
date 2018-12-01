<?php

namespace Adewra\TrafficScotland\Test;

use Adewra\TrafficScotland;

class MigrateDatabaseTest extends TestCase
{
    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate', ['--database' => 'sqlite']);
    }

    /** @test */
    public function migrations_running_successfully()
    {
        $this->assertTrue(true);
    }
}
