<?php

namespace Tests\Feature;

use App\Students\Student;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class StudentAuthenticationTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function a_student_is_redirected_home_by_default_after_login()
    {
        $this->post(route('student.auth.login'), $this->validCredentials())
            ->assertRedirect(route('student.home'));
    }

    /** @test */
    public function a_student_is_redirected_to_the_intended_path_after_login()
    {
        $this->markTestSkipped('Test pages are yet to be defined for students.');

        $this->withExceptionHandling()
            ->get($intended = route('student.index'))
            ->assertRedirect(route('student.welcome')); // redirected to login page

        $this->post(route('student.auth.login'), $this->validCredentials())
            ->assertRedirect($intended);
    }

    /**
     * Create valid student credentials.
     *
     * @return array
     */
    private static function validCredentials()
    {
        $member = create(Student::class, [
            'password' => bcrypt($pass = 'my-password')
        ]);

        return ['email' => $member->email, 'password' => $pass];
    }
}
