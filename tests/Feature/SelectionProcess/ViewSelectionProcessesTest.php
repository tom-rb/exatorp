<?php

namespace Tests\Feature;

use App\Members\Member;
use App\SelectionProcess\MemberApplication;
use App\SelectionProcess\SelectionProcess;

use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ViewSelectionProcessesTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var SelectionProcess
     */
    private $process;

    protected function setUp()
    {
        parent::setUp();

        // Some future, closed process.
        $this->process = create(SelectionProcess::class, [
            'open_date' => Carbon::now()->addMonths(6),
            'close_date' => Carbon::now()->addMonths(7),
        ]);
    }

    /** @test */
    public function candidates_cannot_view_selection_processes()
    {
        $this->withExceptionHandling()
            ->signIn($member = createState(Member::class, 'candidate'))
            ->get(route('selection-process.index'))
            ->assertStatus(403);

        // Side menu link
        $this->get(route('member.home'))
            ->assertDontSee(quotes(route('selection-process.index')));
    }

    /** @test */
    public function unauthorized_members_cannot_view_selection_processes()
    {
        $this->withExceptionHandling()
            ->signIn()
            ->get(route('selection-process.index'))
            ->assertStatus(403);

        // Side menu link
        $this->get(route('member.home'))
            ->assertDontSee(quotes(route('selection-process.index')));
    }

    /** @test */
    public function authorized_members_can_view_selection_processes()
    {
        $this->signIn($this->globalCoord())
            ->get(route('selection-process.show', $this->process))
            ->assertSeeText($this->process->periodTitle)
            ->assertSee(quotes(route('selection-process.index')));

        $this->signIn($this->coord())
            ->get(route('selection-process.show', $this->process))
            ->assertSeeText($this->process->periodTitle)
            ->assertSee(quotes(route('selection-process.index')));
    }

    /** @test */
    public function selection_processes_are_shown_by_most_recent_first()
    {
        $olderProcess = create(SelectionProcess::class, ['open_date' => Carbon::now()->subYear()]);
        $recentProcess = $this->process;

        $response = $this->signIn($this->globalCoord())
            ->get(route('selection-process.index'))
            ->assertRedirect()
            ->baseResponse;

        $viewData = $this->follow($response)
            ->baseResponse->getOriginalContent()->getData();

        $this->assertTrue($recentProcess->is($viewData['process']));
    }

    /** @test */
    public function it_fetches_the_previous_and_next_process()
    {
        $oldest = create(SelectionProcess::class, ['open_date' => Carbon::now()->subYear(2)]);
        $previous = create(SelectionProcess::class, ['open_date' => Carbon::now()->subYear()]);
        $next = create(SelectionProcess::class, ['open_date' => Carbon::now()->addYear()]);
        $latest = create(SelectionProcess::class, ['open_date' => Carbon::now()->addYears(2)]);

        $viewData = $this->signIn($this->globalCoord())
            ->get(route('selection-process.show', $this->process))
            ->baseResponse->getOriginalContent()->getData();

        $this->assertTrue($previous->is($viewData['prevProcess']), 'Previous process is wrong');
        $this->assertTrue($next->is($viewData['nextProcess']), 'Next process is wrong');
    }

    /** @test */
    public function it_shows_the_total_number_of_candidates_for_the_process()
    {
        $applications = create(MemberApplication::class, 3, [
            'selection_process_id' => $this->process->id
        ]);

        $this->signIn($this->globalCoord())
            ->get(route('selection-process.show', $this->process))
            ->assertSeeText(': '.$applications->count());
    }

    /** @test */
    public function it_outputs_a_csv_containing_candidates_data_in_iso_format()
    {
        $application = create(MemberApplication::class, [
            'selection_process_id' => $this->process->id
        ]);

        $response = $this->signIn($this->globalCoord())
            ->get(route('selection-process.csv', $this->process));

        $response->assertHeader('Content-Type', 'text/plain; charset=ISO-8859-1')
            ->assertSee(utf8_decode($application->candidate->name));
    }

    /** @test */
    public function it_outputs_a_csv_containing_candidates_data_filtered_by_area()
    {
        $applications = create(MemberApplication::class, 2, [
            'selection_process_id' => $this->process->id
        ]);

        $response = $this->signIn($this->globalCoord())
            ->get(route('selection-process.csv', [
                'process' => $this->process,
                'area' => $applications->first()->first_area_id,
            ]));

        $response->assertHeader('Content-Type', 'text/plain; charset=ISO-8859-1')
            ->assertSee(utf8_decode($applications->first()->candidate->name))
            ->assertDontSee(utf8_decode($applications->last()->candidate->name));
    }
}
