<?php

namespace Tests\utilities;

use App\Members\Member;

trait CreatesMembers
{
    private $rolesNotSeeded = true;

    /**
     * @param null|Member $member
     * @return static
     */
    protected function signIn($member = null)
    {
        $member = $member ?: create(Member::class);

        return tap($this)->actingAs($member);
    }

    /**
     * @return Member
     */
    protected function admin()
    {
        $this->seedMemberRoles();

        return create(Member::class)->assign('admin');
    }

    /**
     * @return Member
     */
    protected function globalCoord()
    {
        $this->seedMemberRoles();

        // The job doesn't matter; give role only
        return create(Member::class)->assign('coord-geral');
    }

    /**
     * @return Member
     */
    protected function coord()
    {
        $this->seedMemberRoles();

        $coordJob = create(\App\Team\Job::class, [
            'role_id' => \Silber\Bouncer\Database\Role::whereName('coord')->first()->id
        ]);

        return create(Member::class)->addJob($coordJob);
    }

    /**
     * @return Member
     */
    protected function newMemberCan($ability)
    {
        $this->seedMemberRoles();

        return create(Member::class)->allow($ability);
    }

    /**
     * @return static
     */
    protected function seedMemberRoles()
    {
        if ($this->rolesNotSeeded) {
            (new \RolesAndAbilitiesSeeder)->run();
            $this->rolesNotSeeded = false;
        }

        return $this;
    }
}