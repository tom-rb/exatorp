<?php

namespace App\Members;

use App\Administration\Impersonation\CanImpersonate;
use App\Members\Events\MemberApproved;
use App\Members\Notifications\ResetMemberPasswordNotification;
use App\Model\CanBeFiltered;
use App\Model\Entity;
use App\Team\HasJobsAndAreas;

use Silber\Bouncer\Database\HasRolesAndAbilities;
use Illuminate\Auth\Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Validator;
use Sanitizer;

class Member extends Entity implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword, Notifiable;

    use HasRolesAndAbilities, HasJobsAndAreas;
    use CanBeFiltered, CanImpersonate;

    /**
     * Member Status
     */
    const CANDIDATE = 0;
    const ACTIVE = 1;
    const FORMER_MEMBER = 2;

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'phones' => 'array'
    ];

    /**
     * The attributes that should be hidden for arrays.
     */
    protected $hidden = [
        'google_account', 'availability', 'password', 'remember_token', 'updated_at'
    ];

    /**
     * The accessors to append to the model's array form.
     */
    protected $appends = ['path'];

    /**
     * Sanitizes data used to create/update a member
     *
     * @param array $data
     */
    public static function sanitize(array $data)
    {
        $filtered = array_only_clean($data, [
            'name',
            'email',
            'ra',
            'course',
            'admission_year',
            'phones',
            'google_account',
            'password',
        ]);

        $sanitized = Sanitizer::sanitize([
            'name' => 'strip_spaces|name_pt',
            'email' => 'strip_spaces|lowercase',
            'course' => 'strip_spaces|name_pt',
            'phones' => 'array',
            'google_account' => 'strip_spaces|lowercase',
            'password' => 'bcrypt',
        ], $filtered);

        if (isset($data['availability']))
            $sanitized['availability'] = new LessonAvailability($data['availability']);

        return $sanitized;
    }

    /**
     * Make a validator to assure the given data is valid to create a Member.
     *
     * @param array $data
     * @return \Illuminate\Validation\Validator
     */
    public static function creationValidator(array $data)
    {
        $rules = [
            'name'   => 'required|alpha_spaces|max:255',
            'email'  => 'required|email|max:255|unique:members',
            'ra'     => 'required|digits:6',
            'course' => 'required|alpha_spaces|max:255',
            'admission_year' => 'required|digits:4',
            'phones'  => 'required|regex:/^[\d ]{8,14}$/', // old front-end format
            'availability' => 'required|array|min:2',
            'password' => 'required|min:6|confirmed',
        ];

        return Validator::make($data, $rules);
    }

    /**
     * Make a validator to assure the given data is valid to update a Member.
     *
     * @param $data
     * @return \Illuminate\Validation\Validator
     */
    public function updateValidator($data)
    {
        $rules = [
            'name'   => 'alpha_spaces|max:255',
            'email'  => 'email|max:255|unique:members,email,'.$this->id,
            'ra'     => 'digits:6',
            'course' => 'alpha_spaces|max:255',
            'admission_year' => 'digits:4',
            'phones'  => 'array|min:1',
            'phones.*' => ['required', 'regex:/^[\d ]{8,14}$/', 'distinct'],
            'current_password' => 'nullable|required_with:password|hash:'.$this->password,
            'password' => 'nullable|required_with:current_password|different:current_password|min:6',
        ];

        return Validator::make($data, $rules);
    }

    /**
     * Make a Member with Candidate status.
     *
     * @param array $data Valid data to create member
     * @return Member
     */
    public static function makeCandidate($data)
    {
        $sanitized = Member::sanitize($data);
        $sanitized['status'] = static::CANDIDATE;

        return new Member($sanitized);
    }


    /**
     * Scope a query to only include active members.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeActive(Builder $query)
    {
        return $query->where('status', static::ACTIVE);
    }

    /**
     * Scope a query to only include former members.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeFormer(Builder $query)
    {
        return $query->where('status', static::FORMER_MEMBER);
    }

    /**
     * Scope a query to only include members on hold.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeOnHold(Builder $query)
    {
        // Using an alias to members table, so table prefixes are applied automatically.
        return $query->from($this->getTable().' as m')
            ->whereExists(function ($query) {
                $query->select(\DB::raw(1))
                    ->from('candidates_on_hold as h')
                    ->whereColumn('h.member_id', 'm.id');
        });
    }

    /**
     * Approve a member to use the system. A job can be assigned in the process.
     */
    public function approve($job = null)
    {
        $this->status = static::ACTIVE;
        $this->save();

        if (! is_null($job))
            $this->addJob($job);

        event(new MemberApproved($this));
    }

    /**
     * Dismiss a member, leaving him/her as a former member.
     */
    public function dismiss()
    {
        if (!$this->isActive())
            return;

        $this->status = static::FORMER_MEMBER;
        $this->save();

        $this->removeAllJobs();
    }

    /**
     * A candidate member is someone not yet evaluated in a selection process.
     *
     * @return bool True if the member is still a candidate, false otherwise.
     */
    public function isCandidate()
    {
        return $this->status == static::CANDIDATE;
    }

    /**
     * @return bool True if the member is currently active, false otherwise.
     */
    public function isActive()
    {
        return $this->status == static::ACTIVE;
    }

    /**
     * @return bool True if the member is a former member, false otherwise.
     */
    public function isFormer()
    {
        return $this->status == static::FORMER_MEMBER;
    }

    /**
     * Whether the user is an admin.
     * @return bool
     */
    public function isAdmin()
    {
        return $this->isAn('admin');
    }

    /**
     * Path to the member resource.
     */
    public function getPathAttribute()
    {
        return route('member.show', $this);
    }

    /**
     * Get availability as a LessonAvailability.
     *
     * @return LessonAvailability
     */
    public function getAvailabilityAttribute()
    {
        return new LessonAvailability($this->fromJson($this->attributes['availability']));
    }

    /**
     * Set availability from a LessonAvailability.
     *
     * @param LessonAvailability $availability
     * @return $this
     */
    public function setAvailabilityAttribute(LessonAvailability $availability)
    {
        $this->attributes['availability'] = $this->asJson($availability);
        return $this;
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetMemberPasswordNotification($token));
    }
}
