<?php

namespace Tests\Feature;

use App\SelectionProcess\MemberApplication;
use App\Members\Member;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ViewProfileTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var Member
     */
    private $member;

    /**
     * Setup the test environment.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->member = create(Member::class);
    }

    /** @test */
    public function an_unauthenticated_user_cannot_see_a_profile()
    {
        $this->withExceptionHandling()
            ->visitProfile()
            ->assertRedirect(route('member.welcome'));
    }

    /** @test */
    public function a_member_can_see_her_profile()
    {
        $this->signIn($this->member)
            ->visitProfile()
            ->assertSeeText(e($this->member->name))
            ->assertSeeText(e($this->member->course));
    }

    /** @test */
    public function a_candidate_can_see_her_own_profile()
    {
        $candidate = create(MemberApplication::class)->candidate;

        $this->signIn($candidate)
            ->visitProfile($candidate)
            ->assertSeeText(e($candidate->name))
            ->assertSeeText(e($candidate->course));
    }

    /** @test */
    public function a_candidate_cannot_see_others_profile()
    {
        $candidate = createState(Member::class, 'candidate');

        $this->withExceptionHandling()
            ->signIn($candidate)
            ->visitProfile()
            ->assertStatus(403);
    }

    /**
     * @param null $member
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    private function visitProfile($member = null)
    {
        if (is_null($member)) $member = $this->member;

        return $this->get(route('member.show', $member));
    }
}
