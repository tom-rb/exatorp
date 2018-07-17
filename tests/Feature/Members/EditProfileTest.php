<?php

namespace Tests\Feature;


use App\Members\Member;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class EditProfileTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var Member
     */
    private $member;

    public function setUp()
    {
        parent::setUp();

        $this->member = create(Member::class);
        $this->signIn($this->member);
    }

    /** @test */
    public function a_member_cannot_edit_other_members_profile()
    {
        // Can't see link to edit on other's profile page
        $this->get(route('member.show', $otherMember = create(Member::class)))
            ->assertDontSee(route('member.edit', $otherMember));

        // Can't open the edit form (is redirected to profile)
        $this->get(route('member.edit', $otherMember))
            ->assertRedirect(route('member.show', $otherMember));

        // Can't post to update the profile
        $this->withExceptionHandling()
            ->patch(route('member.update', $otherMember), [])
            ->assertStatus(403);
    }

    /** @test */
    public function a_member_can_get_access_to_edit_his_own_profile()
    {
        // See the edit link
        $this->get(route('member.show', $this->member))
            ->assertSee(route('member.edit', $this->member));

        // Open the edit form
        $this->get(route('member.edit', $this->member))
            ->assertSeeText($this->member->name);
    }

    /** @test */
    public function a_member_can_update_her_own_profile()
    {
        $this->patch($this->member->path, [
            'name' => 'Changed e name',
            'ra'     => '654321',
            'course' => 'My new course',
            'admission_year' => '3012',
        ]);

        tap($this->member->fresh(), function ($member) {
            $this->assertEquals('Changed e Name', $member->name);
            $this->assertEquals('654321', $member->ra);
            $this->assertEquals('My New Course', $member->course);
            $this->assertEquals('3012', $member->admission_year);
        });
    }

    /** @test */
    public function a_member_can_update_her_email_as_long_as_it_remains_unique()
    {
        $otherMember = create(Member::class);

        $this->patch($this->member->path, [
            'email'  => $this->member->email, // same email, no problem
        ])->assertStatus(200);

        $this->assertValidationFails(function () use ($otherMember) {
            $this->patch($this->member->path, [
                'email'  => $otherMember->email, // oops
            ]);
        });
    }

    /** @test */
    public function a_member_can_change_his_phones()
    {
        $this->patch($this->member->path, $data = [
            'phones' => ['1234 5678', '9876 5432']
        ]);

        $this->assertEquals($data['phones'], $this->member->fresh()->phones);
    }

    /** @test */
    public function the_phones_are_validated_when_editing_the_profile()
    {
        $this->withExceptionHandling()
            ->patch($this->member->path, $data = [
                'phones' => null
            ])->assertSessionHasErrors('phones');

        $this->withExceptionHandling()
            ->patch($this->member->path, $data = [
                'phones' => []
            ])->assertSessionHasErrors('phones');

        $this->withExceptionHandling()
            ->patch($this->member->path, $data = [
                'phones' => ['']
            ])->assertSessionHasErrors('phones.0');

        $this->withExceptionHandling()
            ->patch($this->member->path, $data = [
                'phones' => ['12345678', 'not valid']
            ])->assertSessionHasErrors('phones.1');

        $this->withExceptionHandling()
            ->patch($this->member->path, $data = [
                'phones' => ['12345678', '12345678'] // duplicated
            ])->assertSessionHasErrors('phones.0')
            ->assertSessionHasErrors('phones.1');
    }

    /** @test */
    public function a_member_can_update_his_authentication_password()
    {
        tap($this->member, function($member) {
            $member->password = bcrypt('correct_password');
        })->save();

        $this->patch($this->member->path, [
            'password'  => 'new_password',
            'current_password'  => 'correct_password',
        ])->assertStatus(200);

        $this->assertTrue(\Hash::check('new_password', $this->member->fresh()->password));
    }

    /** @test */
    public function the_password_is_validated_when_editing_the_profile()
    {
        tap($this->member, function($member) {
            $member->password = bcrypt('correct_password');
        })->save();

        $this->withExceptionHandling()
            ->patch($this->member->path, [
                'current_password'  => 'wrong_password',
                'password'  => 'new_password',
            ])->assertSessionHasErrors('current_password', "Current password must be right");

        $this->flushSession()
            ->patch($this->member->path, [
                'current_password'  => '',
                'password'  => 'new_password',
        ])->assertSessionHasErrors('current_password', "Current password cannot be empty");

        $this->flushSession()
            ->patch($this->member->path, [
                'password'  => 'new_password',
            ])->assertSessionHasErrors('current_password', "Current password must be present");

        $this->flushSession()
            ->patch($this->member->path, [
                'current_password'  => 'correct_password',
            ])->assertSessionHasErrors('password', "New password must not be empty if current is given");

        $this->flushSession()
            ->patch($this->member->path, [
                'current_password'  => 'correct_password',
                'password'  => 'short',
            ])->assertSessionHasErrors('password', "Password must not be short");

        $this->flushSession()
            ->patch($this->member->path, [
                'current_password'  => 'correct_password',
                'password'  => 'correct_password',
            ])->assertSessionHasErrors('password', "Password must be different then the current one");
    }

    /** @test */
    public function the_password_its_not_erased_when_empty_fields_are_provided()
    {
        $this->patch($this->member->path, [
            'password'  => '',
            'current_password'  => '',
        ])->assertStatus(200);

        $this->assertNotEmpty($this->member->fresh()->password);
    }
}
