<?php

namespace App\SelectionProcess\Controllers;

use App\SelectionProcess\Requests\UpdateMemberApplicationStatus;
use App\SelectionProcess\MemberApplication;
use App\SelectionProcess\SelectionProcess;
use App\Infrastructure\Http\Controller;
use App\SelectionProcess\ViewModels\MemberApplicationModel;
use Illuminate\Http\Request;

class CandidatesController extends Controller
{
    /**
     * List the candidates of a Selection Process.
     */
    public function index($processId, Request $request)
    {
        $this->authorize('index', MemberApplication::class);

        $applications = MemberApplication::where('selection_process_id', $processId)
            ->with(['firstArea', 'firstJob', 'secondArea', 'secondJob']);

        if ($request->has('area')) {
            $applications = $applications->forArea($request->query('area'));
        }

        return $applications->paginate(15);
    }

    /**
     * Shows a member's application answers.
     */
    public function show(SelectionProcess $process, $member)
    {
        $this->authorize('show', MemberApplication::class);

        $application = MemberApplication::from($process, $member)
            ->with(['candidate.jobs'])
            ->firstOrFail();

        return view('selection-process.application.show', vm(
            new MemberApplicationModel($process, $application)
        ));
    }

    /**
     * Update the status of a member
     */
    public function update(UpdateMemberApplicationStatus $updateRequest)
    {
        $actionTaken = $updateRequest->apply();

        $messages = [
            'process_finished' => ' - Processo já FINALIZADO!',
            'approve' => ' entrou para a equipe!',
            'hold' => ' ficou em espera de oportunidade.',
            'reject' => ' foi marcado como reprovado.',
            'switch' => ' teve a opção principal trocada.',
            'reset' => ' teve sua decisão desfeita.',
        ];

        return response()->jsonSuccess(
            $updateRequest->memberName() . $messages[$actionTaken],
            ['action' => $actionTaken]
        );
    }
}
