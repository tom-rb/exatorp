<?php

namespace Tests\Feature;

use App\SelectionProcess\MemberApplication;
use App\SelectionProcess\SelectionProcess;
use App\Members\Member;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ViewCandidatesTest extends TestCase
{
    use DatabaseTransactions;

    private $process;
    private $candidatesPath;

    protected function setUp()
    {
        parent::setUp();

        $this->process = create(SelectionProcess::class);
        $this->candidatesPath = route('selection-process.application.index', $this->process);
    }

    /** @test */
    public function candidates_cannot_view_other_candidates()
    {
        $this->withExceptionHandling()
            ->signIn($member = createState(Member::class, 'candidate'))
            ->get($this->candidatesPath)
            ->assertStatus(403);
    }

    /** @test */
    public function unauthorized_members_cannot_view_candidates()
    {
        $this->withExceptionHandling()
            ->signIn()
            ->get($this->candidatesPath)
            ->assertStatus(403);
    }

    /** @test */
    public function authorized_members_can_view_candidates()
    {
        $this->signIn($this->globalCoord())->get($this->candidatesPath)
            ->assertStatus(200)
            ->assertJson(['path' => $this->candidatesPath]); // json answer has path

        $this->signIn($this->coord())->get($this->candidatesPath)
            ->assertStatus(200)
            ->assertJson(['path' => $this->candidatesPath]);
    }

    /** @test */
    public function it_lists_the_candidates_for_the_selection_process()
    {
        $applications = $this->createApplications(2);
        $candidates = $applications->map->candidate;

        $response = $this->signIn($this->globalCoord())
            ->get($this->candidatesPath)
            ->assertSeeText(je($candidates[0]->name))
            ->assertSeeText(je($candidates[1]->name));

        // Asserting json representation of paginated result
        $json = $response->json();
        $this->assertCount(2, $json['data']);
        $this->assertEquals(2, $json['total']);
    }

    /** @test */
    public function candidates_can_be_filtered_by_area_of_interest()
    {
        // Factories create one Area for each member application
        $applications = $this->createApplications(2);

        $this->signIn($this->globalCoord())
            ->get(route('selection-process.application.index', [$this->process, 'area' => $applications->first()->first_area_id]))
            ->assertSeeText(je($applications->first()->candidate->name))
            ->assertDontSeeText(je($applications->last()->candidate->name));
    }

    /** @test */
    public function unauthorized_members_cannot_see_a_member_application_profile()
    {
        $application = $this->createApplications(1)->first();

        $this->withExceptionHandling()
            ->signIn()
            ->get(route('selection-process.application.show', [$this->process, $application->candidate]))
            ->assertStatus(403);
    }

    /** @test */
    public function it_shows_the_answers_to_the_member_application_form()
    {
        $application = $this->createApplications(1)->first();

        $this->signIn($this->coord())
            ->get(route('selection-process.application.show', [$this->process, $application->candidate]))
            ->assertSee($application->answers[0]);
    }

    private function createApplications($count)
    {
        return create(MemberApplication::class, $count, [
            'selection_process_id' => $this->process->id
        ]);
    }
}
