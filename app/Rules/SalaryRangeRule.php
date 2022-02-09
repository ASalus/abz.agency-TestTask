<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class SalaryRangeRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $checkValue = str_replace([',','$'],'',$value);
        if (($checkValue >= 0)&&($checkValue<=500000)){
            return true;
        }
        else {
            return false;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Salary should in range from $0 to $500,000.';
    }
}
