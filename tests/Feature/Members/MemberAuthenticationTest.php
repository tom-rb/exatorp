<?php

namespace Tests\Feature;

use App\Members\Member;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MemberAuthenticationTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_warns_when_login_goes_wrong()
    {
        $this->withExceptionHandling()
            ->post(route('member.auth.login'), ['email' => 'invalid@email.com', 'password' => 'wrong'])
            ->assertSessionHasErrors(['email']);

        // Message is shown to the user when redirected back to welcome page
        $this->get(route('member.welcome'))
            ->assertSee('As informações de login não foram encontradas.');
    }

    /** @test */
    public function a_member_is_redirected_home_by_default_after_login()
    {
        $this->post(route('member.auth.login'), $this->validCredentials())
            ->assertRedirect(route('member.home'));
    }

    /** @test */
    public function a_member_is_redirected_to_the_intended_path_after_login()
    {
        $this->withExceptionHandling()
            ->get($intended = route('member.index'))
            ->assertRedirect(route('member.welcome')); // redirected to login page

        $this->post(route('member.auth.login'), $this->validCredentials())
            ->assertRedirect($intended);
    }

    /** @test */
    public function an_authenticated_user_is_redirected_away_from_the_welcome_page_to_home()
    {
        $this->signIn()
            ->get(route('member.welcome'))
            ->assertRedirect(route('member.home'));
    }

    /**
     * Create valid member credentials.
     *
     * @return array
     */
    private static function validCredentials()
    {
        $member = create(Member::class, [
            'password' => bcrypt($pass = 'my-password')
        ]);

        return ['email' => $member->email, 'password' => $pass];
    }
}
