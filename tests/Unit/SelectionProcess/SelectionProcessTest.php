<?php

namespace Tests\Unit;

use App\Team\Job;
use App\Team\Area;
use App\SelectionProcess\Question;
use App\SelectionProcess\SelectionProcess;
use App\SelectionProcess\MemberApplication;

use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SelectionProcessTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var SelectionProcess
     */
    private $process;

    public function setUp()
    {
        parent::setUp();

        Carbon::setTestNow(); // clear any now() mock datetime
        $this->process = create(SelectionProcess::class);
    }

    private function makeProcess($openDate, $closeDate)
    {
        return SelectionProcess::create([
            'open_date' => $openDate,
            'close_date' => $closeDate
        ]);
    }

    /** @test */
    public function it_must_have_valid_open_and_close_dates()
    {
        $open = Carbon::now()->addDay();
        $close = Carbon::now();

        $process = $this->makeProcess($open, $close);

        $this->assertDatabaseMissing('selection_processes', $process->toArray());
    }

    /** @test */
    public function it_is_closed_if_the_open_date_is_in_the_future()
    {
        $process = $this->makeProcess(
            Carbon::now()->addDay(),
            Carbon::now()->addDays(2)
        );

        $this->assertTrue($process->isClosed());
    }

    /** @test */
    public function it_is_closed_if_the_close_date_is_in_the_past()
    {
        $process = $this->makeProcess(
            Carbon::now()->subDays(2),
            Carbon::now()->subDay()
        );

        $this->assertTrue($process->isClosed());
    }

    /** @test */
    public function it_is_opened_when_between_open_and_close_dates()
    {
        $process = $this->makeProcess(
            Carbon::now()->subDay(),
            Carbon::now()->addDay()
        );

        $this->assertTrue($process->isOpened());
    }

    /** @test */
    public function it_finds_the_currently_opened_process()
    {
        Carbon::setTestNow(Carbon::now()->addDays(15)); // process will be opened

        $this->assertTrue($this->process->is(
            SelectionProcess::currentlyOpened()->first()
        ));
    }

    /** @test */
    public function it_generates_a_title_based_on_the_open_date()
    {
        $open = Carbon::createFromDate(2017, 2, 1); // February
        $process = $this->makeProcess($open, $open->copy()->addWeeks(3));

        $this->assertEquals('1º sem. de 2017', $process->periodTitle);

        $open = Carbon::createFromDate(2017, 7, 1); // July
        $process = $this->makeProcess($open, $open->copy()->addWeeks(3));

        $this->assertEquals('2º sem. de 2017', $process->periodTitle);
    }

    /** @test */
    public function it_can_add_areas_for_application()
    {
        $areaA = create(Area::class);
        $areaB = create(Area::class);

        $this->process->addArea($areaA);
        $this->process->addArea($areaB);

        $this->assertTrue($this->process->areas->contains($areaA));
        $this->assertTrue($this->process->areas->contains($areaB));
    }

    /** @test */
    public function it_can_add_specific_jobs_for_application()
    {
        $job = create(Job::class);
        $this->process->addJob($job);
        $this->assertTrue($this->process->jobsForArea($job->area)->contains($job));
    }

    /** @test */
    public function it_can_add_multiple_jobs_for_the_same_area()
    {
        $area = create(Area::class);
        $jobs = create(Job::class, 2, ['area_id' => $area->id]);

        $this->process->addJob($jobs->pop());
        $this->process->addJob($jobs->pop());

        $this->assertCount(2, $this->process->jobs);
        $this->assertCount(2, $this->process->jobsForArea($area));
    }

    /** @test */
    public function it_removes_all_jobs_when_an_area_is_removed()
    {
        $area = create(Area::class);
        $jobs = create(Job::class, 2, ['area_id' => $area->id]);

        $this->process->addJob($jobs->pop());
        $this->process->addJob($jobs->pop());
        $this->process->removeArea($area);

        $this->assertEmpty($this->process->jobsForArea($area));
    }

    /** @test */
    public function it_removes_jobs()
    {
        $job = create(Job::class);
        $this->process->addJob($job);
        $this->process->removeJob($job);
        $this->assertEmpty($this->process->jobsForArea($job->area));
    }

    /** @test */
    public function it_can_add_additional_questions()
    {
        $process = createState(SelectionProcess::class, 'no_questions');
        $process->addQuestion($q1 = new Question('My question?', 'Answer it however you want.'));
        $process->addQuestion($q2 = new Question('Second question?'));
        $this->assertEquals([$q1, $q2], $process->questions);
    }

    /** @test */
    public function it_can_remove_questions()
    {
        $process = create(SelectionProcess::class, [
            'questions' => [
                new Question('Who?'),
                new Question('When?'),
            ]
        ]);
        $this->assertArrayHasKey(1, $process->questions);

        $process->removeQuestion(1);
        $this->assertArrayNotHasKey(1, $process->questions);
    }

    /** @test */
    public function it_has_many_member_applications()
    {
        $applications = create(MemberApplication::class, 2,
            ['selection_process_id' => $this->process->id]);

        $applications->each(function ($application, $key) {
            $this->assertTrue($this->process->applications[$key]->is($application));
        });
    }

    /** @test */
    public function it_finishes_when_all_applications_are_resolved_after_the_closing_date()
    {
        $applications = create(MemberApplication::class, 2,
            ['selection_process_id' => $this->process->id]);

        $this->assertFalse($this->process->isFinished(), 'Should still be open when applications are unresolved');

        $applications[0]->status = MemberApplication::APPROVED;
        $applications[1]->status = MemberApplication::REJECTED;
        $applications->each->save();

        $this->assertFalse($this->process->fresh()->isFinished(), 'The process is still open, cannot be finished');

        Carbon::setTestNow($this->process->close_date->copy()->addDay());

        $this->assertTrue($this->process->fresh()->isFinished(), 'Now it should be finished already');

        // Testing version with loaded relations
        $this->assertTrue($this->process->fresh()->load('applications')->isFinished(), 'Should be finished.');
    }
}
