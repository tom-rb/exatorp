<?php

namespace App\Team;

use DB;
use Illuminate\Database\Eloquent\Builder;

trait HasJobsAndAreas
{
    /**
     * Jobs of the member.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function jobs()
    {
        // The job's description is rarely used, but the area_name often is.
        return $this->belongsToMany(Job::class)
            ->select(['jobs.id', 'jobs.name', 'jobs.area_id', 'jobs.role_id'])
            ->withAreaName()
            ->withTimestamps();
    }

    /**
     * Assign a job to the member and assign its role.
     *
     * @param integer|Job $job
     * @return $this
     */
    public function addJob($job)
    {
        if (! ($job instanceof Job))
            $job = Job::findOrFail($job);

        DB::transaction(function () use ($job) {
            $this->jobs()->attach($job);
            $this->assign($job->role);
        });

        return $this->refreshed('jobs');
    }

    /**
     * Remove a job from the member and retract its role.
     *
     * @param integer|Job $job
     * @return $this
     */
    public function removeJob($job)
    {
        if (! ($job instanceof Job))
            $job = Job::findOrFail($job);

        DB::transaction(function () use ($job) {
            $this->jobs()->detach($job);
            $this->retract($job->role);
        });

        return $this->refreshed('jobs');
    }

    /**
     * Remove all jobs from the member.
     * @return $this
     */
    public function removeAllJobs()
    {
        DB::transaction(function () {
            $this->jobs->each(function($job) {
                $this->jobs()->detach($job);
                $this->retract($job->role);
            });
        });

        return $this->refreshed('jobs');
    }

    /**
     * Refresh relation on memory.
     * @return $this
     */
    private function refreshed($relation)
    {
        $this->load($relation);
        return $this;
    }

    /**
     * Retrieves all areas of the member.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function areas()
    {
        return $this->areasQuery()->get();
    }

    /**
     * Query to locate the areas of the member.
     *
     * @return Builder
     */
    public function areasQuery()
    {
        $relationTable = $this->jobs()->getTable();
        $userKeyColumn = $this->jobs()->getQualifiedForeignKeyName();
        $jobKeyColumn = $this->jobs()->getQualifiedRelatedKeyName();

        return Area::join('jobs', 'jobs.area_id', '=', 'areas.id')
            ->join($relationTable, $jobKeyColumn, '=', 'jobs.id')
            ->select('areas.*')
            ->where($userKeyColumn, $this->getKey());
    }
}