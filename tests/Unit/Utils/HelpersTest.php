<?php

namespace Tests\Unit;

use Tests\TestCase;

class HelpersTest extends TestCase
{
    /** @test */
    public function a_name_can_be_formatted_using_portuguese_style()
    {
        $this->assertEquals(format_pt_name(null), '');

        // Trim multiple spaces, uppercase first letters except for some prepositions.
        $this->assertEquals(format_pt_name(' joÃo DA    siLVA   '), 'João da Silva');
    }

    /** @test */
    public function an_email_can_be_formatted()
    {
        $this->assertEquals(format_email(null), '');

        $this->assertEquals(format_email(' joÃo+DA@siLVA.com   '), 'joão+da@silva.com');
    }
}
