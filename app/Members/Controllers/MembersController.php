<?php

namespace App\Members\Controllers;

use App\Members\Member;
use App\Members\Filters\MemberFilters;
use App\Members\ViewModels\ProfileModel;
use App\SelectionProcess\MemberApplication;
use App\Infrastructure\Http\Controller;
use Auth;

class MembersController extends Controller
{
    /**
     * List of members, possibly filtered.
     *
     * @param MemberFilters $filters
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(MemberFilters $filters)
    {
        $this->authorize('index', Member::class);

        $members = Member::filterOrDefault($filters, function ($builder) {
                $builder->active();
            })
            ->with('jobs')
            ->orderBy('name')
            ->paginate(null, ['id', 'name', 'email']);

        return view('member.index', compact('members'));
    }

    /**
     * Show the profile of a member.
     *
     * @param Member $member
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Member $member)
    {
        $this->authorize($member);

        $member->load(['jobs']);

        $application = $this->getApplicationIfCandidate($member);

        return view('member.show', vm(new ProfileModel($member, $application)));
    }

    /**
     * Show a form to edit the profile of a member.
     *
     * @param Member $member
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Member $member)
    {
        if (Auth::user()->cannot('update', $member))
            return redirect(route('member.show', $member));

        $application = $this->getApplicationIfCandidate($member);

        return view('member.edit', compact('member', 'application'));
    }

    /**
     * Update member properties.
     *
     * @param Member $member
     * @return string
     */
    public function update(Member $member)
    {
        $this->authorize('update', $member);

        $member->updateValidator($data = request()->all())
            ->validate();

        $sanitizedData = Member::sanitize($data);

        return tap($member)->update($sanitizedData);
    }

    /**
     * Dismiss the member, leaving him/her as former-member.
     *
     * @param Member $member
     * @return array
     */
    public function dismiss(Member $member)
    {
        $this->authorize('dismiss', $member);

        $member->dismiss();

        return response()->jsonSuccess('Desligado(a) ' . $member->name);
    }

    /**
     * Destroy the member.
     *
     * @param Member $member
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Member $member)
    {
        $this->authorize($member);

        $member->delete();

        return response()->jsonSuccess('Deletado(a)');
    }

    private function getApplicationIfCandidate(Member $member)
    {
        if ($member->isCandidate())
            return MemberApplication::latestFrom($member);
    }
}
