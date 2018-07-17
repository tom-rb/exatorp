<?php

namespace App\Members\Controllers;

use App\SelectionProcess\MemberApplication;
use App\Infrastructure\Http\Controller;
use Auth;
use Response;

/**
 * Class HomeController
 * @package App\Infrastructure\Http\Controllers
 */
class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return Response
     */
    public function index()
    {
        $application = Auth::user()->isCandidate()
            ? MemberApplication::latestFrom(Auth::id())
            : [];

        return view('member.home', compact('application'));
    }
}