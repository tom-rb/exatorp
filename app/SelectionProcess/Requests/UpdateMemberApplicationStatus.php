<?php

namespace App\SelectionProcess\Requests;

use App\SelectionProcess\MemberApplication;
use App\SelectionProcess\CandidatesOnHoldList;
use App\SelectionProcess\SelectionProcess;
use App\Members\Member;
use Illuminate\Foundation\Http\FormRequest;

class UpdateMemberApplicationStatus extends FormRequest
{
    /**
     * @var Member
     */
    private $candidate;

    /**
     * Determine if the user is authorized to update a member application status.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->candidate = Member::findOrFail($this->route('member'));

        return $this->user()->can('approve-candidates');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'action' => 'required|in:approve,hold,reject,switch,reset',
            'job' => 'required_if:action,approve|exists:jobs,id',
        ];
    }

    /**
     * Update the Member Application status according to request.
     *
     * @return string The action taken.
     */
    public function apply()
    {
        $process = SelectionProcess::findOrFail($this->route('process'));

        if ($process->isFinished())
            return 'process_finished';

        $application = MemberApplication::from($process->id, $this->candidate->id)->first();

        switch ($action = $this->input('action')) {
            case 'approve':
                $application->status = MemberApplication::APPROVED;
                $this->candidate->approve($this->input('job'));
                break;
            case 'hold':
                $application->status = MemberApplication::ON_HOLD;
                CandidatesOnHoldList::store($this->candidate);
                break;
            case 'reject':
                $application->status = MemberApplication::REJECTED;
                break;
            case 'switch':
                $application->trying_first_option = !$application->trying_first_option;
                break;
            case 'reset':
                $application->status = null;
                CandidatesOnHoldList::remove($this->candidate->id);
                $this->candidate->status = Member::CANDIDATE;
                $this->candidate->jobs()->detach();
                $this->candidate->save();
                break;
        }
        $application->save();

        return $action;
    }

    public function memberName()
    {
        return $this->candidate->name;
    }
}
