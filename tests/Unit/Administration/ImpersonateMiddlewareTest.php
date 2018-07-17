<?php

namespace Tests\Unit;

use App\Members\Member;
use App\Administration\Impersonation\ImpersonateMiddleware;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ImpersonateMiddlewareTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_impersonates_a_member_when_required_by_the_session()
    {
        $this->signIn($member = create(Member::class));
        $member->setImpersonating($otherMember = create(Member::class));

        $mw = new ImpersonateMiddleware();
        $mw->handle($this->newRequestWithSession(), function($r) use ($otherMember) {
            $this->assertTrue(auth()->user()->is($otherMember));
        });
    }

    /** @test */
    public function it_does_not_impersonates_when_session_does_not_require()
    {
        $this->signIn($member = create(Member::class));

        $mw = new ImpersonateMiddleware();
        $mw->handle($this->newRequestWithSession(), function($r) use ($member) {
            $this->assertTrue(auth()->user()->is($member));
        });
    }
}
