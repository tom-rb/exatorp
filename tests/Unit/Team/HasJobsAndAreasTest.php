<?php

namespace Tests\Unit;

use App\Team\Job;
use App\Members\Member;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class HasJobsAndAreasTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var Member
     */
    private $member;

    /**
     * @var Job
     */
    private $job;

    public function setUp()
    {
        parent::setUp();

        $this->member = create(Member::class);
        $this->job = create(Job::class);
    }

    /** @test */
    public function it_can_have_a_job()
    {
        $this->member->addJob($this->job);

        $this->assertTrue(
            $this->member->jobs()->first()->is($this->job)
        );
    }

    /** @test */
    public function it_can_have_multiple_jobs()
    {
        $otherJob = create(Job::class);

        $this->member->addJob($this->job);
        $this->member->addJob($otherJob);

        $this->assertCount(2, $this->member->jobs);
    }

    /** @test */
    public function when_a_job_is_assigned_the_relation_timestamps_are_updated()
    {
        $this->member->addJob($this->job);

        $this->assertNotNull(
            $this->member->jobs->first()->pivot->created_at
        );
    }

    /** @test */
    public function when_a_member_gets_a_job_its_role_is_assigned_to_the_member()
    {
        $this->member->addJob($this->job);

        $this->assertTrue($this->member->isA(
            $this->job->role->name)
        );
    }

    /** @test */
    public function when_a_member_loses_a_job_its_role_is_retracted_from_the_member()
    {
        $this->member->addJob($this->job);
        $this->member->removeJob($this->job);

        $this->assertEmpty($this->member->jobs);
        $this->assertFalse($this->member->isA($this->job->role->name));
    }

    /** @test */
    public function when_all_jobs_are_removed_all_roles_are_retracted_too()
    {
        $this->member->addJob($this->job);
        $this->member->addJob($otherJob = create(Job::class));

        $this->member->removeAllJobs();

        $this->assertEmpty($this->member->jobs);
        $this->assertFalse($this->member->isA($this->job->role->name));
        $this->assertFalse($this->member->isA($otherJob->role->name));
    }

    /** @test */
    public function its_areas_are_determined_according_to_its_jobs()
    {
        $otherJob = create(Job::class);
        $this->member->addJob($this->job);
        $this->member->addJob($otherJob);

        $this->assertTrue($this->member->areas()->first()->is($this->job->area));
        $this->assertTrue($this->member->areas()->last()->is($otherJob->area));
    }
}
