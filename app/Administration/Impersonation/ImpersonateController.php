<?php

namespace App\Administration\Impersonation;

use App\Members\Member;

use Auth;
use App\Infrastructure\Http\Controller;

class ImpersonateController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin')->except('stopImpersonate');
    }

    /**
     * Make the current authenticated user impersonate a given member.
     *
     * @param Member $member
     * @return \Illuminate\Http\RedirectResponse
     */
    public function impersonate(Member $member)
    {
        if (! $member->isAdmin()) {
            Auth::user()->setImpersonating($member);
            flash()->warning('Agora você é '.$member->name.', cuidado com o que faz!')
                ->important();
        }
        else {
            flash()->error('Atuar como '.$member->name.' está desabilitado.');
        }

        return back();
    }

    /**
     * Stop the current impersonation.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function stopImpersonate()
    {
        Auth::user()->stopImpersonating();

        flash()->success('Bom te ver de volta!');

        return back();
    }
}
