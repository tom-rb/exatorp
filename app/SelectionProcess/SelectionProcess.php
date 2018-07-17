<?php

namespace App\SelectionProcess;

use App\Team\Job;
use App\Team\Area;
use App\Model\Entity;

use DB;
use Validator;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

class SelectionProcess extends Entity
{
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['open_date', 'close_date'];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['periodTitle'];

    private $cacheIsFinished = null;

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        // Validate on every save.
        static::saving(function (SelectionProcess $process) {
            return $process->isValid();
        });
    }

    /**
     * Whether the validation rules passes.
     * @return bool
     */
    public function isValid()
    {
        return Validator::make($this->attributesToArray(), [
            'open_date' => 'required|date',
            'close_date' => 'required|date|after:open_date'
        ])->passes();
    }

    /**
     * Scope a query to get the currently opened process
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeCurrentlyOpened(Builder $query)
    {
        $now = Carbon::now();
        return $query->where('open_date','<',$now)
            ->where('close_date','>',$now)
            ->orderBy('id'); // just in case, should be only one.
    }

    /**
     * Whether the selection process is opened.
     * @return bool
     */
    public function isOpened()
    {
        $now = Carbon::now();
        return $now >= $this->open_date && $now < $this->close_date;
    }

    /**
     * Whether the selection process is closed.
     * @return bool
     */
    public function isClosed()
    {
        return ! $this->isOpened();
    }

    /**
     * Whether the selection process is finished, i.e., after all applications are resolved.
     */
    public function isFinished()
    {
        // Must be after the close date to be finished
        if (Carbon::now() < $this->close_date)
            return false;

        if ($this->cacheIsFinished !== null)
            return $this->cacheIsFinished;

        $countUnresolved = $this->relationLoaded('applications')
            ? $this->applications->where('status', null)->count()
            : $this->applications()->whereNull('status')->count();

        $this->cacheIsFinished = ($countUnresolved === 0);
        return $this->cacheIsFinished;
    }

    /**
     * A title for the Selection Process period.
     * @return string
     */
    public function getPeriodTitleAttribute()
    {
        $semester = $this->open_date->month < 7 ? '1º' : '2º';

        return $semester . ' sem. de ' . $this->open_date->year;
    }

    /**
     * Scope a query to get the process with all positions eager loaded
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeWithPositions(Builder $query)
    {
        return $query->with(['areas', 'jobs']);
    }

    /**
     * The member applications made within the selection process.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function applications()
    {
        return $this->hasMany(MemberApplication::class);
    }

    /**
     * Opened areas for application.
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function areas()
    {
        return $this->morphedByMany(Area::class, 'position', 'selection_process_positions');
    }

    /**
     * Opened jobs for application.
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function jobs()
    {
        return $this->morphedByMany(Job::class, 'position', 'selection_process_positions');
    }

    /**
     * Add an area to the selection process. An opened area may not have
     * specific jobs opened for it.
     * @param Area $area
     * @return $this
     */
    public function addArea(Area $area)
    {
        $this->areas()->attach($area);
        // Unloads the areas, so it will be refreshed if needed
        unset($this->areas);
        return $this;
    }

    /**
     * Add a specific job to the selection process.
     * @param Job $job
     * @return $this
     */
    public function addJob(Job $job)
    {
        DB::transaction(function() use ($job) {
            if (! $this->areas->contains($job->area)) {
                $this->addArea($job->area);
            }
            $this->jobs()->attach($job);
        });

        return $this;
    }

    /**
     * The specific job positions for an opened area.
     * @param Area|integer $area
     * @return Collection
     */
    public function jobsForArea($area)
    {
        if ($area instanceof Area)
            $area = $area->id;

        if ($this->relationLoaded('jobs')) {
            return $this->jobs->filter(function ($job) use ($area) {
                return $job->area_id == $area;
            });
        }

        return $this->jobs()->byArea($area)->get();
    }

    /**
     * @param $area
     * @return array
     */
    public function jobsIdsForArea($area)
    {
        return $this->jobsForArea($area)->pluck('id')->toArray();
    }

    /**
     * Remove an area, and all its jobs, from the selection process.
     *
     * @param Area $area
     * @return $this
     */
    public function removeArea(Area $area)
    {
        DB::transaction(function() use ($area) {
            $this->areas()->detach($area);
            $this->jobs()->byArea($area)->each(function ($job) {
                $this->jobs()->detach($job);
            });
        });

        return $this;
    }

    /**
     * Remove an available job from the selection process.
     * @param Job $job
     * @return $this
     */
    public function removeJob(Job $job)
    {
        $this->jobs()->detach($job);
        return $this;
    }

    /**
     * The questions that candidates must answer.
     * @return array
     */
    public function getQuestionsAttribute()
    {
        $questions = [];
        foreach ($this->fromJson($this->attributes['questions']) ?: [] as $key => $question)
            $questions[$key] = Question::fromArray($question);
        return $questions;
    }

    /**
     * Set the questions from an array of Questions.
     *
     * @param array $questions
     * @return $this
     */
    public function setQuestionsAttribute(array $questions)
    {
        $this->attributes['questions'] = collect($questions)->toJson();
        return $this;
    }

    /**
     * Add a question to the selection process.
     * @param $question Question
     * @return $this
     */
    public function addQuestion(Question $question)
    {
        $questions = $this->getQuestionsAttribute();
        array_push($questions, $question);
        $this->setQuestionsAttribute($questions);
        $this->save();

        return $this;
    }

    /**
     * Remove a question from the selection by its index
     * @param $index
     */
    public function removeQuestion($index)
    {
        $questions = $this->getQuestionsAttribute();
        unset($questions[$index]);
        $this->setQuestionsAttribute($questions);
        $this->save();
    }
}
