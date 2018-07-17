<?php

namespace Tests\Feature;

use App\SelectionProcess\CandidatesOnHoldList;
use App\Members\Member;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ListMembersTest extends TestCase
{
    use DatabaseTransactions;

    private $member;
    private $candidate;

    protected function setUp()
    {
        parent::setUp();

        $this->member = create(Member::class);
        $this->candidate = createState(Member::class, 'candidate');
    }

    /** @test */
    public function an_active_member_can_view_the_member_list()
    {
        $this->signIn()
            ->get(route('member.index'))
            ->assertSeeText($this->member->name);
    }

    /** @test */
    public function a_candidate_cannot_view_the_member_list()
    {
        $this->withExceptionHandling()
            ->signIn($this->candidate)
            ->get(route('member.index'))
            ->assertStatus(403);

        // Don't see the menu entry
        $this->get(route('member.home'))
            ->assertDontSee(quotes(route('member.index')));
    }

    /** @test */
    public function the_member_list_does_not_show_candidates()
    {
        $this->signIn()
            ->get(route('member.index'))
            ->assertDontSeeText($this->candidate->name);
    }

    /** @test */
    public function the_members_can_be_filtered_to_show_the_former_members_only()
    {
        $former = createState(Member::class, 'former');

        $this->signIn()
            ->get(route('member.index', ['status' => 'antigos']))
            ->assertSeeText(e($former->name))
            ->assertDontSeeText($this->member->name)
            ->assertDontSeeText($this->candidate->name);
    }

    /** @test */
    public function the_members_can_be_filtered_to_show_the_candidates_on_hold_only()
    {
        CandidatesOnHoldList::store($onHoldCandidate = createState(Member::class, 'candidate'));

        $this->signIn()
            ->get(route('member.index', ['status' => 'esperando']))
            ->assertSeeText(e($onHoldCandidate->name))
            ->assertDontSeeText(e($this->member->name))
            ->assertDontSeeText(e($this->candidate->name));
    }
}
