<?php

namespace Tests\Unit;

use App\Members\Member;

use Session;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CanImpersonateTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_impersonates_a_member_by_recording_it_in_the_session()
    {
        $first = create(Member::class);

        $first->setImpersonating($second = create(Member::class));

        $this->assertTrue($first->isImpersonating());
        $this->assertTrue(Session::has('impersonate'));
    }

    /** @test */
    public function it_stops_impersonating_by_cleaning_the_session()
    {
        $first = create(Member::class);
        $first->setImpersonating($second = create(Member::class));
        $first->stopImpersonating();

        $this->assertFalse($first->isImpersonating());
        $this->assertFalse(Session::has('impersonate'));
    }
}
