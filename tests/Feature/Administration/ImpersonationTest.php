<?php

namespace Tests\Feature;

use App\Members\Member;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ImpersonationTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function an_admin_can_impersonate_other_member()
    {
        $this->signIn($this->admin())
            ->get( route('impersonate.start', $impersonated = create(Member::class)) );

        $this->assertFlashHas($impersonated->name);

        $this->get(route('member.home'));

        $this->seeIsAuthenticatedAs($impersonated);
    }

    /** @test */
    public function only_admins_can_impersonate()
    {
        $this->signIn($signedMember = create(Member::class))
            ->get( route('impersonate.start', create(Member::class)) )
            ->assertRedirect(route('member.home')); // auth middleware
        $this->get(route('member.home'));

        $this->seeIsAuthenticatedAs($signedMember);
    }

    /** @test */
    public function no_one_can_impersonate_an_admin()
    {
        $this->signIn($adminA = $this->admin())
            ->get( route('impersonate.start', $adminB = $this->admin()) );
        $this->get(route('member.home'));

        $this->seeIsAuthenticatedAs($adminA);
    }
}
