<?php

namespace App\SelectionProcess;

use App\Model\Entity;
use App\Members\Member;

class CandidatesOnHoldList extends Entity
{
    protected $table = 'candidates_on_hold';
    protected $primaryKey = 'member_id';
    public $incrementing = false;

    /**
     * Store a new 'on hold' entry for the given candidate.
     *
     * @param $candidate
     * @return static
     */
    public static function store($candidate)
    {
        $entry = (new static)
            ->candidate()
            ->associate($candidate);

        return tap($entry)->save();
    }

    /**
     * Removes a candidate from the list.
     *
     * @param $candidate
     * @return bool
     */
    public static function remove($candidate)
    {
        $id = self::getId($candidate);

        $success = (new static)
            ->whereKey($id)
            ->delete();

        return $success;
    }

    /**
     * Checks whether the candidate exists in the list.
     *
     * @param $candidate
     * @return bool
     */
    public static function contains($candidate)
    {
        $id = self::getId($candidate);

        return static::whereKey($id)->exists();
    }

    /**
     * Retrieve all candidates on hold list.
     *
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function all($columns = ['*'])
    {
        // Make columns refer to Members table only
        $columns = array_map(function ($col) { return 'members.'.$col; },
            is_array($columns) ? $columns : func_get_args()
        );

        return Member::select($columns)
            ->join('candidates_on_hold', 'members.id', '=', 'candidates_on_hold.member_id')
            ->get();
    }

    /**
     * Reference to the candidate model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function candidate()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    /**
     * @param $candidate
     * @return mixed
     */
    private static function getId($candidate)
    {
        if ($candidate instanceof Member)
            $candidate = $candidate->id;

        return $candidate;
    }
}