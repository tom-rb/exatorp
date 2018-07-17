<?php

namespace App\SelectionProcess\Controllers;

use App\SelectionProcess\MemberApplication;
use App\SelectionProcess\SelectionProcess;

use App\Team\Area;
use App\Infrastructure\Http\Controller;

class SelectionProcessController extends Controller
{
    /**
     * Redirect to the latest Selection Process view.
     */
    public function showLatest()
    {
        $this->authorize('show', MemberApplication::class);

        $latestProcess = SelectionProcess::latest('open_date')->first(['id']);

        return redirect(route('selection-process.show', $latestProcess));
    }

    /**
     * Show a selection process and its candidates, possibly filtered.
     */
    public function show($processId)
    {
        $this->authorize('show', MemberApplication::class);

        $process = SelectionProcess::withCount('applications')->findOrFail($processId);
        $areas = Area::with('jobs')->get();

        return view('selection-process.show', compact('process', 'areas'),
            $this->getNextAndPrevProcesses($process)
        );
    }
    
    private function getNextAndPrevProcesses($process)
    {
        $prevProcess = SelectionProcess::latest('open_date')
            ->where('open_date', '<', $process->open_date)
            ->limit(1)->first();

        $nextProcess = SelectionProcess::oldest('open_date')
            ->where('open_date', '>', $process->open_date)
            ->limit(1)->first();

        return compact('prevProcess', 'nextProcess');
    }

    /**
     * Show form to create a SelectionProcess
     */
    public function create()
    {
        $this->authorize('manage-selection-processes');

        //
    }
}
