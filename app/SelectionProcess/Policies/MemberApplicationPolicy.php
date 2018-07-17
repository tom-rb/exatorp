<?php

namespace App\SelectionProcess\Policies;

use App\Members\Member;
use Illuminate\Auth\Access\HandlesAuthorization;

class MemberApplicationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the member can list MemberApplications.
     *
     * @param  Member  $member
     * @return mixed
     */
    public function index(Member $member)
    {
        return $member->can('manage-members') || $member->can('approve-candidates');
    }

    /**
     * Determine whether the member can view MemberApplications.
     *
     * @param  Member  $member
     * @return mixed
     */
    public function show(Member $member)
    {
        return $member->can('manage-members') || $member->can('approve-candidates');
    }
}
