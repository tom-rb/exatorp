<?php

namespace Tests\Unit;

use App\SelectionProcess\HowDidYouHearOption;
use App\Team\Job;
use App\Team\Area;
use App\Members\Member;
use App\SelectionProcess\Question;
use App\SelectionProcess\SelectionProcess;
use App\SelectionProcess\MemberApplication;

use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MemberApplicationTest extends TestCase
{
    use DatabaseTransactions;

    /** @var SelectionProcess */
    private $process;

    /** @var array */
    private $data;

    /** @var Member */
    private $candidate;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        // Make application just to grab the created process and data
        $application = make(MemberApplication::class);
        $this->process = $application->selectionProcess;
        $this->data = $application->rawAttributesToArray();
        $this->candidate = $application->candidate;
    }

    /**
     * Calls validation of given data by the MemberApplication.
     */
    private function validateMemberApplication()
    {
        MemberApplication::validator($this->process, $this->data)->validate();
    }

    /**
     * @return MemberApplication with current data.
     */
    private function makeMemberApplication()
    {
        return MemberApplication::make($this->process, $this->candidate, $this->data);
    }

    /** @test */
    public function it_is_created_from_a_selection_process_with_a_member()
    {
        // Validation works for default data, this is essential to the other tests!
        $this->validateMemberApplication();

        $this->makeMemberApplication()->save();
    }

    /**
     * @test
     * @expectedException \Illuminate\Validation\ValidationException
     */
    public function it_must_have_an_existing_selection_process()
    {
        $this->process->id = 99;

        $this->validateMemberApplication();
    }

    /** @test */
    public function it_has_a_first_area_and_job_option()
    {
        $this->process->addJob($job = create(Job::class));
        $this->data['first_area_id'] = $job->area_id;
        $this->data['first_area_job'] = $job->id;

        $application = $this->makeMemberApplication();

        $this->assertTrue($job->area->is($application->firstArea));
        $this->assertTrue($job->is($application->firstJob));
    }

    /** @test */
    public function it_has_a_second_area_and_job_option()
    {
        $this->process->addJob($job = create(Job::class));
        $this->data['second_area_id'] = $job->area_id;
        $this->data['second_area_job'] = $job->id;

        $application = $this->makeMemberApplication();

        $this->assertTrue($job->area->is($application->secondArea));
        $this->assertTrue($job->is($application->secondJob));
    }

    /**
     * @test
     * @expectedException \Illuminate\Validation\ValidationException
     */
    public function it_must_have_an_available_first_area_option()
    {
        // Existing area, but not opened for application in the current process.
        $this->data['first_area_id'] = create(Area::class)->id;

        $this->validateMemberApplication();
    }

    /**
     * @test
     * @expectedException \Illuminate\Validation\ValidationException
     */
    public function it_must_have_a_job_option_if_the_area_requires_it()
    {
        $this->process->addJob($openedJob = create(Job::class));
        $closedJob = create(Job::class, ['area_id' => $openedJob->area->id]);

        // Area is from an existing, and opened, job position. (it's valid)
        $this->data['first_area_id'] = $openedJob->area_id;

        // Job is of the same area, but not opened for application.
        $this->data['first_area_job'] = $closedJob->id;
        $this->validateMemberApplication();
    }

    /** @test */
    public function it_does_not_require_a_job_if_the_area_does_not_specify_one()
    {
        $this->process->addArea($area = create(Area::class));
        $this->data['second_area_id'] = $area->id;
        unset($this->data['second_area_job']);

        $this->validateMemberApplication();
    }

    /** @test */
    public function it_does_not_require_a_second_option()
    {
        unset($this->data['second_area_id']); // simple but deadly

        $this->makeMemberApplication();
    }

    /**
     * @test
     * @expectedException \Illuminate\Validation\ValidationException
     */
    public function it_checks_if_the_selected_job_belongs_to_the_corresponding_area()
    {
        $this->process->addJob($availableJob = create(Job::class));
        $unavailableJob = create(Job::class);

        $this->data['first_area_id'] = $availableJob->area_id;
        $this->data['first_area_job'] = $unavailableJob->id;

        $this->validateMemberApplication();
    }

    /** @test */
    public function it_cleans_the_job_choice_if_not_applicable()
    {
        $job = create(Job::class);
        $this->process->addArea($job->area); // add area only

        $this->data['first_area_id'] = $job->area_id;
        $this->data['first_area_job'] = $job->id;      // not needed for the area
        $this->data['second_area_id'] = $job->area_id; // does not care if second choice is the same
        $this->data['second_area_job'] = $job->id;     // not needed too

        $this->validateMemberApplication();
        $application = $this->makeMemberApplication();

        $this->assertNull($application->first_area_job);
        $this->assertNull($application->second_area_job);
    }

    /** @test */
    public function it_requires_answers_if_the_selection_process_have_questions()
    {
        $questionsCount = count($this->process->questions);
        $this->process->addQuestion(new Question('Why would you?'));

        // No answers given for new question, should fail
        $this->assertValidationFails(function () {
            $this->validateMemberApplication();
        });

        // Now it's ok
        $this->data['answers'][$questionsCount] = 'A valid answer';
        $this->validateMemberApplication();
        $application = $this->makeMemberApplication();

        $this->assertEquals($this->data['answers'], $application->answers);
    }

    /** @test */
    public function it_can_hold_multiple_how_did_you_hear_answers()
    {
        $howDids = create(HowDidYouHearOption::class, 2)->pluck('id')->toArray();
        $this->data['how_did_you_hear'] = $howDids;

        $this->validateMemberApplication();
        $application = $this->makeMemberApplication();

        $this->assertEquals($howDids, $application->how_did_you_hear);
    }

    /** @test */
    public function it_accept_how_did_you_hear_options_by_array_keys_too()
    {
        $idA = create(HowDidYouHearOption::class)->id;
        $idB = create(HowDidYouHearOption::class)->id;
        $this->data['how_did_you_hear'] = [
            $idA => 'true',
            $idB => 'or anything',
        ];

        $this->validateMemberApplication();
        $application = $this->makeMemberApplication();

        $this->assertEquals([$idA, $idB], $application->how_did_you_hear);
    }

    /**
     * @test
     * @expectedException \Illuminate\Validation\ValidationException
     */
    public function it_checks_for_valid_how_did_you_hear_options()
    {
        $this->data['how_did_you_hear'] = [99];

        $this->validateMemberApplication();
    }

    /** @test */
    public function it_can_filter_applications_for_area()
    {
        $appA = createState(MemberApplication::class, 'with_second_job', ['selection_process_id' => $this->process->id]);
        $appB = createState(MemberApplication::class, 'with_second_job', ['selection_process_id' => $this->process->id]);

        // First area ...
        $filteredA = MemberApplication::forArea($appA->firstArea)->get();
        $this->assertTrue($filteredA->contains($appA), 'Cannot find filtered first area');
        $this->assertFalse($filteredA->contains($appB), 'Found unfiltered member application (1st)');

        // ... or second area are considered by the filter
        $filteredB = MemberApplication::forArea($appB->secondArea)->get();
        $this->assertTrue($filteredB->contains($appB), 'Cannot find filtered second area');
        $this->assertFalse($filteredB->contains($appA), 'Found unfiltered member application (2nd)');

        // It also works for slugs
        $filteredA = MemberApplication::forArea($appA->secondArea->slug)->get();
        $this->assertTrue($filteredA->contains($appA), 'Cannot find filtered area by slug');
        $this->assertFalse($filteredA->contains($appB), 'Found unfiltered member application (3rd)');

        // Or simply ids
        $filteredB = MemberApplication::forArea($appB->firstArea->id)->get();
        $this->assertTrue($filteredB->contains($appB), 'Cannot find filtered area by id');
        $this->assertFalse($filteredB->contains($appA), 'Found unfiltered member application (4th)');
    }

    /** @test */
    public function it_can_find_an_application_given_a_process_and_a_member()
    {
        $app = create(MemberApplication::class);

        $found = MemberApplication::from($app->selectionProcess, $app->candidate)->first();
        $this->assertTrue($app->is($found), 'Not found by objects');

        $found = MemberApplication::from($app->selectionProcess->id, $app->candidate->id)->first();
        $this->assertTrue($app->is($found), 'Not found by ids');
    }

    /** @test */
    public function it_finds_the_latest_application_of_a_member()
    {
        $oldest = create(MemberApplication::class);
        $newest = create(MemberApplication::class, [
            'member_id' => $oldest->candidate->id,
            'created_at' => Carbon::now()->addYear(),
        ]);

        $found = MemberApplication::latestFrom($oldest->candidate);
        $this->assertTrue($newest->is($found), 'Not found by objects');

        $found = MemberApplication::latestFrom($oldest->candidate->id);
        $this->assertTrue($newest->is($found), 'Not found by ids');
    }

    /** @test */
    public function it_pretty_prints_the_current_and_other_options()
    {
        $app = makeState(MemberApplication::class, ['with_second_job']);

        $this->assertContains($app->firstArea->name, $app->current_option);
        $this->assertContains($app->secondJob->name, $app->other_option);
        $this->assertContains($app->secondJob->name, $app->other_option);

        $app->trying_first_option = false;

        $this->assertContains($app->secondJob->name, $app->current_option);
        $this->assertContains($app->secondJob->name, $app->current_option);
        $this->assertContains($app->firstArea->name, $app->other_option);
    }
}
