<?php

namespace App;

use App\Infrastructure\Http\Controller;
use App\SelectionProcess\SelectionProcess;

class WelcomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function index()
    {
        $selectionProcess = SelectionProcess::currentlyOpened()->first();

        return view('member.auth.login', compact('selectionProcess'));
    }
}
