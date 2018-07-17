<?php

namespace Tests\Feature;

use App\Members\Member;
use App\Members\Events\NewMemberApplied;
use App\SelectionProcess\MemberApplication;
use App\SelectionProcess\SelectionProcess;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ApplyForMemberTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function a_guest_cannot_begin_an_application_when_no_selection_process_is_opened()
    {
        $closedProcess = create(SelectionProcess::class);

        $this->get(route('member.welcome'))
            ->assertDontSeeText('Inscreva');

        $this->get(route('selection-process.application.create', $closedProcess))
            ->assertRedirect(route('member.welcome'));

        $this->withExceptionHandling()
            ->post(route('selection-process.application.create', $closedProcess, ['any data']))
            ->assertStatus(422);
    }

    /** @test */
    public function a_guest_can_begin_the_application_when_a_selection_process_is_opened()
    {
        $process = createState(SelectionProcess::class, 'opened');

        $this->get(route('member.welcome'))
            ->assertSeeText($process->periodTitle);

        $this->get(route('selection-process.application.create', $process))
            ->assertStatus(200);
    }

    /** @test */
    public function an_active_member_is_redirected_to_home_when_trying_to_apply()
    {
        $process = createState(SelectionProcess::class, 'opened');

        $this->signIn()
            ->get(route('selection-process.application.create', $process))
            ->assertRedirect(route('member.home'));

        $this->post(route('selection-process.application.create', $process), ['any data'])
            ->assertRedirect(route('member.home'));
    }

    /** @test */
    public function a_guest_must_read_the_agreement_before_the_form_shows_up()
    {
        $process = createState(SelectionProcess::class, 'opened');

        $this->get(route('selection-process.application.create', $process))
            ->assertSeeText('Prezado(a) candidato(a)');

        $this->get(route('selection-process.application.create', ['process' => $process->id, 'agreed' => true]))
            ->assertDontSeeText('Prezado(a) candidato(a)');

        // It remembers the agreement in session
        $this->get(route('selection-process.application.create', $process))
            ->assertDontSeeText('Prezado(a) candidato(a)');
    }

    /** @test */
    public function a_guest_can_apply_for_member()
    {
        $process = createState(SelectionProcess::class, 'opened');
        $data = $this->makeSampleApplicationData($process);

        $this->expectsEvents(NewMemberApplied::class)
            ->post(route('selection-process.application.create', $process), $data);

        $this->assertDatabaseHas('members', ['name' => $data['name']])
            ->assertDatabaseHas('member_applications', ['answers' => je($data['answers'])]);
    }

    /** @test */
    public function the_application_data_is_validated()
    {
        $process = createState(SelectionProcess::class, 'opened');
        $data = $this->makeSampleApplicationData($process);
        unset($data['how_did_you_hear']); // MemberApplication field

        $this->withExceptionHandling()
            ->post(route('selection-process.application.create', $process), $data)
            ->assertSessionHasErrors('how_did_you_hear');
    }

    /** @test */
    public function the_member_data_is_validated()
    {
        $process = createState(SelectionProcess::class, 'opened');
        $data = $this->makeSampleApplicationData($process);
        unset($data['course']); // Member field

        $this->withExceptionHandling()
            ->post(route('selection-process.application.create', $process), $data)
            ->assertSessionHasErrors('course');
    }

    /**
     * Make sample data to fill a member application form.
     * @param SelectionProcess $process
     * @return array
     */
    private function makeSampleApplicationData(SelectionProcess $process)
    {
        // Use factory to create sample data and a password not yet encrypted
        $candidate = makeState(Member::class, 'candidate', ['password' => str_random(10)]);
        $candidate->password_confirmation = $candidate->password;

        // Old front-end format for phone in member registration
        $candidate['phones'] = $candidate->phones[0];

        $application = makeState(MemberApplication::class, 'no_member',
            ['selection_process_id' => $process->id]);

        return array_merge($application->rawAttributestoArray(), $candidate->rawAttributestoArray());
    }

}
