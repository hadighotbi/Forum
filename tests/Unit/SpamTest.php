<?php

namespace Tests\Unit;

use App\Inspections\Spam;
use PHPUnit\Framework\TestCase;

class SpamTest extends TestCase
{
    function test_it_checks_for_invalid_keywords ()
    {
        $spam = new Spam();

        $this->assertFalse($spam->detect('Innocent reply here'));

        $this->expectException('Exception');

        $spam->detect('yahoo customer support');
    }

    function test_it_checks_for_any_key_being_held_down ()
    {
        $spam = new Spam();

        $this->expectException('Exception');

        $spam->detect('helooooooo');

    }
}
