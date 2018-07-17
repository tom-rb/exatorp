<?php

namespace App\SelectionProcess\Controllers;

use App\Members\Events\NewMemberApplied;

use App\Members\Member;
use App\SelectionProcess\SelectionProcess;
use App\SelectionProcess\MemberApplication;
use App\SelectionProcess\HowDidYouHearOption;
use App\Infrastructure\Http\Controller;

use DB;
use Auth;
use Illuminate\Http\Request;

class MemberApplicationController extends Controller
{
    /**
     * MemberApplicationController constructor.
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Form to create a member application.
     */
    public function create(SelectionProcess $process)
    {
        if (!$process->isOpened())
            return redirect()->route('member.welcome');

        if (!$this->candidateHasAgreed())
            return view('selection-process.application.presentation');

        $selectionProcess = $process->load(['areas', 'jobs']);
        $howDidYouHearOptions = HowDidYouHearOption::all();

        return view('selection-process.application.create',
            compact('selectionProcess', 'howDidYouHearOptions'));
    }

    private function candidateHasAgreed()
    {
        if (request()->query('agreed')) {
            session(['selection-process-agreed' => true]);
            return true;
        }

        return session('selection-process-agreed');
    }

    /**
     * Store a new member application for the Selection Process.
     */
    public function store(SelectionProcess $process, Request $request)
    {
        $inputData = $request->all();

        if ($error = $this->validateMemberApplication($process, $inputData))
            return $error;

        event(new NewMemberApplied($candidate = $this->register($process, $inputData)));

        Auth::guard()->login($candidate);

        return redirect()->route('member.home');
    }

    /**
     * Validate all data to create a member application.
     */
    private function validateMemberApplication(SelectionProcess $process, array $data)
    {
        abort_unless($process->isOpened(), 422, "Processo Seletivo fechado.");

        $memberValidator = Member::creationValidator($data);
        $applicationValidator = MemberApplication::validator($process, $data);

        if ($memberValidator->fails() || $applicationValidator->fails()) {
            $allErrors = $memberValidator->errors()->merge($applicationValidator->errors());
            return back()->withErrors($allErrors)->withInput();
        }

        return null;
    }

    /**
     * Register the member and the application.
     */
    private function register(SelectionProcess $process, $validData)
    {
        $candidate = Member::makeCandidate($validData);

        DB::transaction(function() use ($process, $candidate, $validData) {
            $candidate->save();
            MemberApplication::make($process, $candidate, $validData)
                ->save();
        });

        return $candidate;
    }
}