<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use PHPUnit\Framework\TestCase;

class CurrentIncidentsTest extends TestCase
{
    public function setUp()
    {
        // Just overriding the base setup.
    }

    public function testFoo()
    {
        $this->app = $this->refreshApplication();

        //
        $this->assertTrue(true);
    }
}
