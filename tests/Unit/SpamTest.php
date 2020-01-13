<?php

namespace Tests\Unit;

use App\Inspections\Spam;
use Tests\TestCase;

class SpamTest extends TestCase
{
    public function test_it_checks_for_invalid_keywords()
    {
        $spam = new Spam();

        $this->assertFalse($spam->detect('Innocent reply here'));

        $this->expectException('Exception');

        $spam->detect('xxx');
    }
    
    public function test_it_checks_for_any_key_being_held_down()
    {
        $spam = new Spam();

        $this->expectException('Exception');
        
        $spam->detect('Hello world aaaaaaaaa');
    }
}
