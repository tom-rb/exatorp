<?php

namespace App\Members\Policies;

use App\Members\Member;

class MemberPolicy
{
    /**
     * Check if user can view a given member's profile
     * @param Member $user
     * @param Member $member
     * @return bool
     */
    public function view(Member $user, Member $member)
    {
        return $user->isActive() || $user->is($member);
    }

    /**
     * Check if user can view the list of members
     * @return bool
     */
    public function index(Member $user)
    {
        return $user->isActive();
    }

    /**
     * Check if user can edit a given member
     * @return bool
     */
    public function update(Member $user, Member $member)
    {
        return $user->is($member) || $user->can('administrate-members');
    }

    /**
     * Check if user can dismiss a given member
     * @return bool
     */
    public function dismiss(Member $user, Member $member)
    {
        return $member->isActive()
            && $user->isNot($member)
            && ($user->can('manage-members') ||
                $user->can('administrate-members'));
    }

    /**
     * Check if user can delete a given member
     * @return bool
     */
    public function delete(Member $user)
    {
        return $user->can('administrate-members');
    }

    /**
     * Check if user can impersonate another member
     */
    public function impersonate(Member $user, Member $member)
    {
        return $user->isAdmin() && $user->isNot($member);
    }
}
