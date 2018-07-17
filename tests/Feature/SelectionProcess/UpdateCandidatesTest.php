<?php

namespace Tests\Feature;


use App\SelectionProcess\CandidatesOnHoldList;
use App\SelectionProcess\MemberApplication;
use App\SelectionProcess\SelectionProcess;
use App\Team\Job;
use App\Members\Member;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UpdateCandidatesTest extends TestCase
{
    use DatabaseTransactions;

    private $process;
    private $application;
    private $candidate;

    protected function setUp()
    {
        parent::setUp();

        $this->process = create(SelectionProcess::class);

        $this->application = create(MemberApplication::class, [
            'selection_process_id' => $this->process->id
        ]);
        
        $this->candidate = $this->application->candidate;
    }

    /** @test */
    public function non_coordinators_cannot_change_candidate_status()
    {
        $action = ['action' => 'switch'];

        $this->withExceptionHandling()
            ->signIn()
            ->patch(route('selection-process.application.show', [$this->process, $this->candidate]), $action)
            ->assertStatus(403);

        $this->assertTrue($this->application->fresh()->trying_first_option);
    }

    /** @test */
    public function coordinators_can_change_the_current_job_option_of_a_candidate()
    {
        $action = ['action' => 'switch'];

        $this->signIn($this->coord())
            ->patch(route('selection-process.application.show', [$this->process, $this->candidate]), $action)
            ->assertJson($action)
            ->assertSee(je($this->candidate->name));

        $this->assertFalse($this->application->fresh()->trying_first_option);

        // Switch again
        $this->patch(route('selection-process.application.show', [$this->process, $this->candidate]), $action)
            ->assertJson($action)
            ->assertSee(je($this->candidate->name));

        $this->assertTrue($this->application->fresh()->trying_first_option);
    }

    /** @test */
    public function coordinators_can_approve_candidates_to_any_job()
    {
        $job = create(Job::class);

        $this->signIn($this->coord())
            ->patch(route('selection-process.application.show', [$this->process, $this->candidate]), ['job' => $job->id, 'action' => 'approve'])
            ->assertJson(['action' => 'approve'])
            ->assertSee(je($this->candidate->name));

        $this->assertEquals(MemberApplication::APPROVED, $this->application->fresh()->status, 'Application not approved');
        $this->assertTrue($this->candidate->fresh()->isActive(), 'Candidate not approved');
        $this->assertTrue($this->candidate->jobs->contains($job), 'Job not assigned');
    }

    /** @test */
    public function coordinators_can_put_candidates_on_hold_for_next_opportunity()
    {
        $this->signIn($this->coord())
            ->patch(route('selection-process.application.show', [$this->process, $this->candidate]), ['action' => 'hold'])
            ->assertJson(['action' => 'hold'])
            ->assertSee(je($this->candidate->name));

        $this->assertEquals(MemberApplication::ON_HOLD, $this->application->fresh()->status, 'Application not put on hold');
        $this->assertTrue($this->candidate->fresh()->isCandidate(), 'Should still be a candidate');
        $this->assertTrue(CandidatesOnHoldList::contains($this->candidate), 'Candidate not added to on hold list');
    }

    /** @test */
    public function coordinators_can_reject_candidates()
    {
        $this->signIn($this->coord())
            ->patch(route('selection-process.application.show', [$this->process, $this->candidate]), ['action' => 'reject'])
            ->assertJson(['action' => 'reject'])
            ->assertSee(je($this->candidate->name));

        $this->assertEquals(MemberApplication::REJECTED, $this->application->fresh()->status, 'Application not rejected');
        $this->assertTrue($this->candidate->fresh()->isCandidate(), 'Should still be a candidate');
        $this->assertFalse(CandidatesOnHoldList::contains($this->candidate), 'Candidate should not be added to on hold list');
    }

    /** @test */
    public function global_coordinators_can_reset_a_member_application_status()
    {
        // Given a candidate put on hold "or" approved
        CandidatesOnHoldList::store($this->candidate);
        $this->candidate->addJob(create(Job::class));
        $this->candidate->status = Member::ACTIVE;
        $this->candidate->save();

        // When she is reset
        $this->signIn($this->globalCoord())
            ->patch(route('selection-process.application.show', [$this->process, $this->candidate]), ['action' => 'reset'])
            ->assertJson(['action' => 'reset'])
            ->assertSee(je($this->candidate->name));

        // The application statuses and states are reset
        $this->assertNull($this->application->fresh()->status, 'Member application not reset');
        $this->candidate = $this->candidate->fresh();
        $this->assertTrue($this->candidate->isCandidate(), 'Should still be a candidate');
        $this->assertTrue($this->candidate->jobs->isEmpty(), 'Should not have a job assigned');
        $this->assertFalse(CandidatesOnHoldList::contains($this->candidate), 'Should not be added to on hold list');
    }

    /** @test */
    public function coordinators_cannot_change_candidates_status_after_the_process_finishes()
    {
        // Resolve all applications
        $this->application->status = MemberApplication::ON_HOLD;
        $this->application->save();
        // Set date to close the process
        \Carbon\Carbon::setTestNow($this->process->close_date->copy()->addDay());

        $this->signIn($this->coord())
            ->patch(route('selection-process.application.show', [$this->process, $this->candidate]), ['action' => 'reject'])
            ->assertJsonFragment(['action' => 'process_finished']);
    }
}
