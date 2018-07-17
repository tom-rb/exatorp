<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Model\Entity
 *
 * @mixin \Eloquent
 */
class Entity extends Model
{
    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * Cache for reflected methods
     * @var array
     */
    static protected $staticMethodsReflections = [];

    /**
     * Entity constructor makes sure it has an id attribute.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        if (! array_key_exists($this->primaryKey, $attributes))
            $this->attributes[$this->primaryKey] = null;
    }

    /**
     * Convert the model's attributes to an array ignoring visibility settings
     * (hidden, visible, appends).
     *
     * @return array
     */
    public function rawAttributesToArray()
    {
        $attributes = $this->addDateAttributesToArray($this->attributes);

        $attributes = $this->addMutatedAttributesToArray(
            $attributes, $mutatedAttributes = $this->getMutatedAttributes()
        );

        $attributes = $this->addCastAttributesToArray(
            $attributes, $mutatedAttributes
        );

        return $attributes;
    }

    /**
     * Verifies that an existing static function is actually public before passing it
     * to Eloquent's __callStatic() that would execute it regardless the method's
     * original visibility.
     *
     * @param $method
     * @param $parameters
     * @return mixed
     */
    public static function __callStatic($method, $parameters)
    {
        if (array_has(static::$staticMethodsReflections, $method)) {
            $reflection = static::$staticMethodsReflections[$method];
        }
        else if (method_exists(static::class, $method)) {
            $reflection = new \ReflectionMethod(static::class, $method);
            self::$staticMethodsReflections[$method] = $reflection;
        }

        if (isset($reflection)) {
            if ($reflection->isPrivate())
                throw new \BadMethodCallException("Call to private static method " . static::class . "::$method");
            elseif ($reflection->isProtected())
                throw new \BadMethodCallException("Call to protected static method " . static::class . "::$method");
        }

        return parent::__callStatic($method, $parameters);
    }
}