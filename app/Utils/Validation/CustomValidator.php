<?php

namespace App\Utils\Validation;

use Hash;
use Illuminate\Support\Arr;
use Illuminate\Validation\Validator;

class CustomValidator extends Validator
{
    /**
     * The field under validation must contain only alphabetic chars (including unicode) and spaces.
     * Usage: 'alpha_spaces'
     * Thanks to http://blog.elenakolevska.com/laravel-alpha-validator-that-allows-spaces/
     */
    public function validateAlphaSpaces($attribute, $value, $parameters)
    {
        return preg_match('/^[\pL\s]+$/u', $value);
    }

    /**
     * The field under validation must exist on a given database table filtered
     * by another input value as where clause value.
     * Usage: 'exists_where:table,column,where_column,&other_input'
     *
     * Works just as the Exists rule: the 3rd parameter is the column name for a where
     * clause, the 4th is the value. The difference is that if the 4th parameter has
     * a & prefixing it, it's assumed to be the name of the input data that contains
     * the value (instead of using the hard coded parameter). Ex:
     *
     * [
     *   'area' => 'required',
     *   'job'  => 'required|exists_where:jobs_table,id_column,area_id,&area',
     * ]
     *
     * will check that jobs exists in the jobs_table, at the id_column, where the
     * area_id column has the same value as the area input data.
     */
    public function validateExistsWhere($attribute, $value, $parameters)
    {
        $this->requireParameterCount(4, $parameters, 'exists_where');

        for ($i = 3; $i < count($parameters); $i += 2)
            if (starts_with($parameters[$i], '&'))
                $parameters[$i] = Arr::get($this->data, substr($parameters[$i], 1));

        return $this->validateExists($attribute, $value, $parameters);
    }

    /**
     * The field under validation must have a value equals to its encrypted_match.
     * Usage: 'hash:encrypted_match'
     * Thanks to http://teamnik.org/how-to-update-user-password-in-laravel5/
     */
    public function validateHash($attribute, $value, $parameters)
    {
        return Hash::check($value, $parameters[0]);
    }
}