<?php

namespace Tests\Unit;

use App\SelectionProcess\CandidatesOnHoldList;
use App\Team\Job;
use App\Members\Member;
use App\Members\Events\MemberApproved;
use App\Members\LessonAvailability;

use Hash;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MemberTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_sanitizes_attributes()
    {
        $sanitized = Member::sanitize([
            'name' => 'Mário ',
            'email' => ' a+@PROVIDER.COM',
            'course' => 'ENG.    química',
            'google_account' => 'A+b@GMAIL.com',
            'password' => 'raw password',
            'availability' => ($avail = ['1' => [1,2], '2' => [2],
                'obs' => "Availability as array from front-end format"]),
        ]);

        $this->assertEquals('Mário', $sanitized['name']);
        $this->assertEquals('a+@provider.com', $sanitized['email']);
        $this->assertEquals('Eng. Química', $sanitized['course']);
        $this->assertEquals('a+b@gmail.com', $sanitized['google_account']);
        $this->assertTrue(Hash::check('raw password', $sanitized['password']));
        $this->assertEquals(new LessonAvailability($avail), $sanitized['availability']);
    }

    /** @test */
    public function it_can_be_made_as_candidate()
    {
        $candidate = Member::makeCandidate(['name' => '  Fields must be     sanitized']);

        $this->assertTrue($candidate->isCandidate());
        $this->assertFalse($candidate->isActive());
        $this->assertEquals('Fields Must Be Sanitized', $candidate->name);
    }

    /** @test */
    public function it_can_be_approved()
    {
        $member = makeState(Member::class, 'candidate');

        $this->expectsEvents(MemberApproved::class);

        $member->approve();

        $this->assertTrue($member->isActive());
        $this->assertFalse($member->isCandidate());
    }

    /** @test */
    public function it_can_be_approved_with_a_job()
    {
        $member = makeState(Member::class, 'candidate');

        $this->expectsEvents(MemberApproved::class);

        $member->approve($job = create(Job::class));

        $this->assertTrue($member->isActive());
        $this->assertTrue($member->jobs->first()->is($job));
    }

    /** @test */
    public function a_candidate_cannot_be_dismissed()
    {
        $candidate = createState(Member::class, 'candidate');

        $candidate->dismiss();

        $this->assertTrue($candidate->fresh()->isCandidate(), "Candidate should not be dismissed");
    }

    /** @test */
    public function it_loses_its_jobs_when_dismissed()
    {
        $member = create(Member::class)
            ->addJob(create(Job::class));

        $member->dismiss();

        $this->assertTrue($member->fresh()->isFormer(), "Member should be dismissed");
        $this->assertEmpty($member->jobs);
    }

    /** @test */
    public function it_scopes_query_to_only_active_members()
    {
        $member = create(Member::class);
        $candidate = createState(Member::class, 'candidate');

        $members = Member::active()->pluck('id');

        $this->assertArrayNotHasKey($candidate->id, $members);
        $this->assertEquals($member->id, $members->last());
    }

    /** @test */
    public function it_scopes_query_to_find_members_on_hold_only()
    {
        $member = create(Member::class);
        CandidatesOnHoldList::store($candidate = createState(Member::class, 'candidate'));

        $allOnHold = Member::onHold()->pluck('id');

        $this->assertArrayNotHasKey($member->id, $allOnHold);
        $this->assertEquals($candidate->id, $allOnHold->last());
    }

    /** @test */
    public function it_has_lesson_availability()
    {
        $member = make(Member::class);
        $member->availability = ($avail = new LessonAvailability([2 => [1,2]]));
        $member->save();

        $this->assertEquals($avail, $member->availability);

        // Just to be sure, because mutators
        $loadedMember = Member::find($member->id);
        $this->assertEquals($avail, $loadedMember->availability);
    }
}
