<?php

namespace App\SelectionProcess\ViewModels;


use App\Members\Member;
use App\SelectionProcess\HowDidYouHearOption;
use App\SelectionProcess\MemberApplication;
use App\SelectionProcess\SelectionProcess;

class MemberApplicationModel
{
    /**
     * @var SelectionProcess
     */
    public $process;
    /**
     * @var MemberApplication
     */
    public $application;
    /**
     * @var Member
     */
    public $member;

    /**
     * MemberApplicationModel constructor.
     */
    public function __construct(SelectionProcess $process, MemberApplication $application)
    {
        $this->process = $process;
        $this->application = $application;
        $this->member = $application->candidate;
    }

    /**
     * The answers given for "How did you hear about us?"
     */
    public function howDidYouHearAnswers()
    {
        return HowDidYouHearOption::whereKey($this->application->how_did_you_hear)
            ->pluck('description');
    }

    /**
     * The current status of the candidate/application.
     */
    public function statusMessage()
    {
        switch ($this->application->status)
        {
            case MemberApplication::APPROVED:
                return "O(a) candidato(a) foi aprovado(a) para ";

            case MemberApplication::ON_HOLD:
                return "O(a) candidato(a) está em espera de oportunidade.";

            case MemberApplication::REJECTED:
                return "O(a) candidato(a) não foi chamado(a).";

            default:
                return "O(a) candidato(a) está participando do processo seletivo atual.";
        }
    }

    /**
     * The job for which the candidate was approved.
     */
    public function approvedJob()
    {
        if ($this->member->isActive()) {
            // TODO: take job from some history, instead of the current job
            $job = $this->member->jobs->first()->name;
            $area = $this->member->jobs->first()->area_name;
            return "$job ($area)";
        }

        return "";
    }
}