<?php

namespace Tests\Unit;

use App\SelectionProcess\CandidatesOnHoldList;
use App\Members\Member;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CandidatesOnHoldListTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var Member
     */
    protected $candidate;

    protected function setUp()
    {
        parent::setUp();

        $this->candidate = createState(Member::class, 'candidate');
    }

    /** @test */
    public function it_references_the_candidate_itself()
    {
        // Tricky: an instance of the list is actually a single Eloquent model.
        // The static methods are designed to make it look like a list (see other tests).
        $listEntry = new CandidatesOnHoldList();

        $listEntry->candidate()
            ->associate($this->candidate)
            ->save();

        $this->assertTrue($listEntry->fresh()->candidate->is($this->candidate));
    }

    /** @test */
    public function it_stores_a_candidate_in_the_list()
    {
        $entry = CandidatesOnHoldList::store($this->candidate);

        $this->assertTrue($entry->fresh()->candidate->is($this->candidate));
    }

    /** @test */
    public function it_checks_if_a_candidate_is_on_the_list()
    {
        CandidatesOnHoldList::store($this->candidate);
        $otherCandidate = createState(Member::class, 'candidate');

        $this->assertTrue(CandidatesOnHoldList::contains($this->candidate));
        $this->assertFalse(CandidatesOnHoldList::contains($otherCandidate));
    }

    /** @test */
    public function it_fetches_all_candidates_on_hold()
    {
        CandidatesOnHoldList::store($this->candidate);
        $otherCandidate = createState(Member::class, 'candidate');

        $all = CandidatesOnHoldList::all();

        $this->assertTrue($all->contains($this->candidate));
        $this->assertFalse($all->contains($otherCandidate));
    }

    /** @test */
    public function it_removes_a_candidate_from_the_list()
    {
        CandidatesOnHoldList::store($this->candidate);

        CandidatesOnHoldList::remove($this->candidate);
        $this->assertFalse(CandidatesOnHoldList::contains($this->candidate), 'Should not be in list anymore');

        CandidatesOnHoldList::store($this->candidate);
        CandidatesOnHoldList::remove($this->candidate->id);
        $this->assertFalse(CandidatesOnHoldList::contains($this->candidate), 'Should be able to remove by id too');
    }
}
