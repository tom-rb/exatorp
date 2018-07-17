<?php

namespace App\Members;

use Iterator;
use JsonSerializable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;

class LessonAvailability implements Arrayable, Jsonable, JsonSerializable, Iterator
{
    private $availability = [];

    /**
     * @var string
     */
    private $observations;

    /**
     * Slot descriptions
     * @var array
     */
    private static $description = [
        1 => '19h15 às 20h45',
        2 => '21h00 às 22h30',
    ];

    /**
     * Return an example of an availability with all possible slots
     * @return static
     */
    public static function all()
    {
        for ($i = 1; $i <= 5; $i++) {
            $all[$i] = [1, 2]; // first and second slot each day
        }
        return new static($all);
    }

    /**
     * A human readable description of a slot
     * @param $day
     * @param $slot
     * @return string
     */
    public static function slotDescription($slot)
    {
        return static::$description[$slot];
    }

    /**
     * WeeklyAvailability constructor.
     * @param array $attributes
     */
    public function __construct($attributes = null)
    {
        if (is_null($attributes)) $attributes = [];

        $availability = array_only($attributes, [1,2,3,4,5]);

        // Each availability value is an array with a 1 and/or 2 that indicates
        // "I am available" on the first and/or second time slot.
        foreach ($availability as $day => $slots) {
            if (in_array(1, $slots) || array_has($slots, 1)) $this->availability[$day][] = 1;
            if (in_array(2, $slots) || array_has($slots, 2)) $this->availability[$day][] = 2;
        }

        $this->observations = array_get($attributes, 'obs', '');
    }

    /**
     * Get the availability for a day of the week.
     *
     * @param $weekday integer From 1 (Monday) to 5 (Friday)
     * @return array Containing numbers {1,2} to indicate that first or second
     * time slot is available for that weekday.
     */
    public function availabilityFor($weekday)
    {
        if (array_has($this->availability, $weekday))
            return $this->availability[$weekday];
        return [];
    }

    /**
     * Check if the given slot is present.
     *
     * @param $weekday
     * @param $slot
     * @return bool
     */
    public function isAvailable($weekday, $slot)
    {
        return array_has($this->availability, $weekday) ? in_array($slot, $this->availability[$weekday]) : false;
    }

    /**
     * Number of days in a week that it's available.
     *
     * @return int
     */
    public function daysAvailable()
    {
        return count($this->availability);
    }

    /**
     * Some observation about the availability
     * @return string
     */
    public function observations()
    {
        return $this->observations;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        $availabilities = $this->availability;
        $availabilities['obs'] = $this->observations;
        return $availabilities;
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param  int $options
     * @return string
     */
    public function toJson($options = 0)
    {
        json_encode($this->jsonSerialize(), $options);
    }

    /**
     * Convert the object into something JSON serializable.
     *
     * @return array
     */
    function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * Return the current element
     */
    public function current()
    {
        return current($this->availability);
    }

    /**
     * Move forward to next element
     */
    public function next()
    {
        return next($this->availability);
    }

    /**
     * Return the key of the current element
     */
    public function key()
    {
        return key($this->availability);
    }

    /**
     * Checks if current position is valid
     */
    public function valid()
    {
        return key($this->availability) !== null;
    }

    /**
     * Rewind the Iterator to the first element
     */
    public function rewind()
    {
        reset($this->availability);
    }
}