<?php

namespace Tests\Unit;

use Sanitizer;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SanitizerTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_lowercase_strings()
    {
        $this->assertEquals(Sanitizer::sanitizeValue('lowercase', 'Whats Up'), 'whats up');
    }

    /** @test */
    public function it_sanitizes_names()
    {
        $this->assertEquals(Sanitizer::sanitizeValue('name_pt', null), '');

        // Trim multiple spaces, uppercase first letters except for some prepositions.
        $this->assertEquals(Sanitizer::sanitizeValue('name_pt', ' joÃo DA    siLVA   '), 'João da Silva');
    }
}
