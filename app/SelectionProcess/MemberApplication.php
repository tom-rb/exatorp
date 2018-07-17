<?php

namespace App\SelectionProcess;

use App\Team\Job;
use App\Team\Area;
use App\Members\Member;
use App\Model\Entity;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use Validator;

class MemberApplication extends Entity
{
    /**
     * Member Application Status.
     * These constants must be updated in the JS side too.
     */
    const APPROVED = 1;
    const ON_HOLD = 2;
    const REJECTED = 3;

    protected $casts = [
        'answers' => 'array',
        'how_did_you_hear' => 'array',
        'status' => 'integer',
        'trying_first_option' => 'boolean',
    ];

    protected $with = ['candidate'];

    /**
     * Includes formatted data about options to the Json representation.
     */
    protected $appends = ['path', 'current_option', 'other_option', 'current_area_slug'];

    /**
     * Choose which data is visible in Array and Json representations.
     * Must include $appends and desired relationships.
     */
    protected $visible = ['path', 'current_option', 'other_option', 'current_area_slug',
        'candidate', 'answers', 'how_did_you_hear', 'trying_first_option', 'status'];

    /**
     * Make a MemberApplication without persisting it.
     *
     * @param SelectionProcess $process
     * @param Member $candidate
     * @param array $answers
     * @return static
     */
    public static function make(SelectionProcess $process, Member $candidate, array $answers)
    {
        $data = array_only($answers, [
            'first_area_id',
            'first_area_job',
            'second_area_id',
            'second_area_job',
            'answers',
            'how_did_you_hear',
        ]);
        $data['selection_process_id'] = $process->id;
        $data['member_id'] = $candidate->id;

        // This field might come as an associative array, where the keys matter, or an
        // indexed array, where the values matter.
        $data['how_did_you_hear'] = array_keys_if_associative($data['how_did_you_hear']);

        /* The validation can't remove the trash out of the jobs choice, let's do it here.
         * The trash may come from the UI using radio buttons that don't let the user deselect a job option. */
        if ($process->jobsForArea(array_get($data, 'first_area_id'))->isEmpty())
            $data['first_area_job'] = null;
        if ($process->jobsForArea(array_get($data, 'second_area_id'))->isEmpty())
            $data['second_area_job'] = null;

        return new static($data);
    }

    /**
     * Scope a query to return only applications for the given area.
     *
     * @param Builder $builder
     * @param $area
     * @return Builder
     */
    public function scopeForArea(Builder $builder, $area)
    {
        if ($area instanceof Area) $area = $area->id;

        // May be a slug
        if (is_string($area) && !is_numeric($area)) {
            $area = Area::where('slug', $area)->first();
            $area = $area ? $area->id : 0;
        }

        $builder->where('first_area_id', $area)->orWhere('second_area_id', $area);

        /**
         * This ordering prioritise the applications for the given area to appear first.
         * For each ORDER BY case, it has to check what is the current active option.
         * The reasoning behind the clauses is to have the following ordering:
         *   1. Applications whose current choice is the given area
         *   2. Applications trying for the given area as first choice
         *   3. For the same area and job, prioritise those who has chosen them as first option
         *   4. As last priority ordering, use the non-active area and job option
         */
        return $builder->orderByRaw(
            "CASE WHEN trying_first_option THEN first_area_id <> $area ELSE second_area_id <> $area END,
            trying_first_option DESC,
            CASE WHEN trying_first_option THEN first_area_id ELSE second_area_id END,
            CASE WHEN trying_first_option THEN first_area_job ELSE second_area_job END,
            CASE WHEN trying_first_option THEN second_area_id ELSE first_area_id END,
            CASE WHEN trying_first_option THEN second_area_job ELSE first_area_job END");
    }

    /**
     * Find a MemberApplication given the process and member who applied.
     *
     * @param $process
     * @param $member
     * @return MemberApplication|null
     */
    public function scopeFrom($builder, $process, $member)
    {
        if ($process instanceof SelectionProcess)
            $process = $process->id;

        if ($member instanceof Member)
            $member = $member->id;

        return $builder->where('selection_process_id', $process)
            ->where('member_id', $member);
    }

    /**
     * Find the latest MemberApplication of a member.
     */
    static function latestFrom($member)
    {
        if ($member instanceof Member)
            $member = $member->id;

        return (new static)->where('member_id', $member)->latest()->first();
    }

    /**
     * Make a validator to validate data used to create an instance.
     *
     * @param SelectionProcess $process
     * @param array $data
     * @return \Illuminate\Validation\Validator
     */
    public static function validator(SelectionProcess $process, array $data)
    {
        $data['selection_process_id'] = $process->id;
        if (isset($data['how_did_you_hear']))
            $data['how_did_you_hear'] = array_keys_if_associative($data['how_did_you_hear']);

        $validator = Validator::make($data, static::validationRules($process));
        static::addJobRule($validator, 'first', $process);
        static::addJobRule($validator, 'second', $process);

        return $validator;
    }

    /**
     * Validation rules
     *
     * @return array
     */
    private static function validationRules($process)
    {
        $rules = [
            'selection_process_id' => 'required|exists:selection_processes,id',
            'first_area_id' => [
                'required',
                'exists_where:selection_process_positions,position_id,selection_process_id,&selection_process_id,position_type,Area'
            ],
            'second_area_id' => [
                'nullable',
                'exists_where:selection_process_positions,position_id,selection_process_id,&selection_process_id,position_type,Area'
            ],
            'first_area_job' => 'nullable',
            'second_area_job' => 'nullable',
            'how_did_you_hear' => 'required|array|min:1',
            'how_did_you_hear.*' => 'exists:how_did_you_hear_options,id',
        ];

        // Must have the same number of answers as questions in the selection process
        if (($size = count($process->questions)) > 0) {
            $rules['answers'] = "required|array|size:{$size}";
            $rules['answers.*'] = "required|min:10";
        }

        return $rules;
    }

    /**
     * Adds a rule to the validator to verify specific jobs for areas, if applicable.
     *
     * @param $validator
     * @param string $ordinal first or second
     */
    private static function addJobRule(&$validator, $ordinal, $process)
    {
        $jobs = $process->jobsIdsForArea(array_get($validator->attributes(), "{$ordinal}_area_id"));

        $validator->sometimes("{$ordinal}_area_job",
            // The given job must be an available job for the area ...
            ['required', Rule::in($jobs)],
            // ... only if a list of jobs is specified for that area.
            function () use ($jobs) {
                return !empty($jobs);
            }
        );
    }

    public function getPathAttribute()
    {
        return route('selection-process.application.show', [
            'process' => $this->selection_process_id,
            'member_id' => $this->member_id,
        ], false);
    }

    public function getCurrentAreaSlugAttribute()
    {
        return $this->trying_first_option ? $this->firstArea->slug : $this->secondArea->slug;
    }

    public function getCurrentOptionAttribute()
    {
        return $this->trying_first_option ? $this->getFirstOptionAttribute() : $this->getSecondOptionAttribute();
    }

    public function getOtherOptionAttribute()
    {
        return (!$this->trying_first_option) ? $this->getFirstOptionAttribute() : $this->getSecondOptionAttribute();
    }

    public function getFirstOptionAttribute()
    {
        if ($this->firstArea)
            return $this->firstArea->name . ($this->firstJob ? ' - '.$this->firstJob->name : '');
        return null;
    }

    public function getSecondOptionAttribute()
    {
        if ($this->secondArea)
            return $this->secondArea->name . ($this->secondJob ? ' - '.$this->secondJob->name : '');
        return null;
    }

    /**
     * The selection process in which the application was filled.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function selectionProcess()
    {
        return $this->belongsTo(SelectionProcess::class, 'selection_process_id');
    }

    /**
     * The candidate that has filled the application.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function candidate()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    /**
     * The first Area that the member has chosen
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function firstArea()
    {
        return $this->belongsTo(Area::class, 'first_area_id');
    }

    /**
     * The first Job that the member has chosen (if applicable)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function firstJob()
    {
        return $this->belongsTo(Job::class, 'first_area_job');
    }

    /**
     * The second Area that the member has chosen
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function secondArea()
    {
        return $this->belongsTo(Area::class, 'second_area_id');
    }

    /**
     * The second Job that the member has chosen (if applicable)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function secondJob()
    {
        return $this->belongsTo(Job::class, 'second_area_job');
    }
}
