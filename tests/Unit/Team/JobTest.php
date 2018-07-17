<?php

namespace Tests\Unit;

use App\Team\Job;
use App\Team\Area;

use Silber\Bouncer\Database\Role;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class JobTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var Job
     */
    private $job;

    public function setUp()
    {
        parent::setUp();

        $this->job = make(Job::class);
    }

    /** @test */
    public function it_belongs_to_an_area()
    {
        $this->assertInstanceOf(Area::class, $this->job->area);
    }

    /** @test */
    public function it_has_a_role()
    {
        $this->assertInstanceOf(Role::class, $this->job->role);
    }

    /** @test */
    public function it_can_fetch_the_area_name_directly()
    {
        $this->job->save();

        $job = Job::withAreaName()->find($this->job->id);

        $this->assertEquals($this->job->area->name, $job->area_name);
        $this->assertEquals($this->job->name, $job->name); // sanity check
    }

    /** @test */
    public function it_can_fetch_the_area_name_without_interfering_with_previous_selects()
    {
        $this->job->save();

        $job = Job::select(['id', 'name'])->withAreaName()->find($this->job->id);

        $this->assertEquals($this->job->area->name, $job->area_name);
        $this->assertEquals($this->job->name, $job->name);
        $this->assertFalse(isset($job->description), 'Description column should not be loaded');
    }

    /** @test */
    public function it_can_fetch_the_area_name_while_being_a_relationship()
    {
        $this->job->save();

        $area = Area::with(['jobs' => function ($builder) {
            $builder->select(['id','name','area_id'])->withAreaName();
        }])->find($this->job->area_id);

        $this->assertTrue($this->job->area->is($area), 'Wrong instance');
        $this->assertFalse(isset($area->jobs->first()->description), 'The select was not respected');
    }
}
