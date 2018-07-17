<?php

namespace App\SelectionProcess;

use JsonSerializable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

class Question implements Arrayable, Jsonable, JsonSerializable
{
    /** @var string */
    private $question;

    /** @var string|null */
    private $helpDescription;

    /**
     * Question constructor.
     */
    public function __construct($question, $helpDescription = null)
    {
        $this->question = $question;
        $this->helpDescription = $helpDescription;
    }

    /**
     * Create a question from an array representation
     * @param $attributes
     * @return static
     */
    public static function fromArray($attributes)
    {
        return new static($attributes['question'], $attributes['helpDescription']);
    }

    /**
     * @return string
     */
    public function question()
    {
        return $this->question;
    }

    /**
     * @return null|string
     */
    public function helpDescription()
    {
        return $this->helpDescription;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'question' => $this->question,
            'helpDescription' => $this->helpDescription,
        ];
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
}