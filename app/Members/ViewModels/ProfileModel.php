<?php

namespace App\Members\ViewModels;


use App\Members\Member;
use App\SelectionProcess\MemberApplication;

class ProfileModel
{
    /**
     * @var Member
     */
    public $member;
    /**
     * @var MemberApplication
     */
    private $application;

    /**
     * ProfileModel constructor.
     */
    public function __construct(Member $member, MemberApplication $application = null)
    {
        $this->user = \Auth::user();
        $this->member = $member;
        $this->application = $application;
    }

    /**
     * The current status of the member.
     *
     * @return string
     */
    public function statusMessage()
    {
        if ($this->member->isActive()) {
            $entryDate = $this->member->created_at->formatLocalized('%d de %B, %Y');
            return "Entrou para o Exato em $entryDate.";
        }

        if ($this->member->isCandidate()) {
            if ($this->application->status == MemberApplication::REJECTED)
                return "Candidato(a) não se juntou a equipe .";
            else if ($this->application->status == MemberApplication::ON_HOLD)
                return "Candidato(a) está em espera de outra oportunidade .";
            else
               return "Candidato participando do processo seletivo atual .";
        }

        return "Ex-membro.";
    }
}