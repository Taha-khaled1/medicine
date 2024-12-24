<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use libphonenumber\PhoneNumberUtil;

class ValidPhoneNumber implements Rule
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
        $phoneUtil = PhoneNumberUtil::getInstance();
        try {
            $phoneNumber = $phoneUtil->parse($value, 'ZZ');
            return $phoneUtil->isValidNumber($phoneNumber);
        } catch (\libphonenumber\NumberParseException $e) {
            return false;
        }
    }

    public function message()
    {
        return 'The :attribute format is invalid.';
    }
}
