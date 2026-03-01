<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PhoneNumberVnRule implements ValidationRule
{

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    private $carriers_number = array(
        '086' => 'Viettel',
        '096' => 'Viettel',
        '097' => 'Viettel',
        '098' => 'Viettel',
        '032' => 'Viettel',
        '033' => 'Viettel',
        '034' => 'Viettel',
        '035' => 'Viettel',
        '036' => 'Viettel',
        '037' => 'Viettel',
        '038' => 'Viettel',
        '039' => 'Viettel',
        '090' => 'Mobifone',
        '093' => 'Mobifone',
        '070' => 'Mobifone',
        '076' => 'Mobifone',
        '077' => 'Mobifone',
        '078' => 'Mobifone',
        '079' => 'Mobifone',
        '091' => 'Vinaphone',
        '094' => 'Vinaphone',
        '081' => 'Vinaphone',
        '082' => 'Vinaphone',
        '083' => 'Vinaphone',
        '084' => 'Vinaphone',
        '085' => 'Vinaphone',
        '088' => 'Vinaphone',
        '0993' => 'Gmobile',
        '0994' => 'Gmobile',
        '0995' => 'Gmobile',
        '0996' => 'Gmobile',
        '0997' => 'Gmobile',
        '059' => 'Gmobile',
        '092' => 'Vietnamobile',
        '052' => 'Vietnamobile',
        '056' => 'Vietnamobile',
        '058' => 'Vietnamobile',
        '095' => 'SFone'
    );

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $number = str_replace(array('-', '.', ' '), '', $value);
        $flag = false;

        // Store all start number in an array to search
        $start_numbers = array_keys($this->carriers_number);

        foreach ($start_numbers as $start_number) {
            if ($this->start_with($start_number, $number)) {
                $flag = true;
            }
        }
        // $number is not a phone number
        if (!preg_match('/\(?([0-9]{3})\)?([ .-]?)([0-9]{3})\2([0-9]{4})/', $number)) {
            $flag = false;
        }
        if (!$flag) {
            $fail('Số điện thoại không hợp lệ');
        }
    }

    public function start_with($needle, $haystack)
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }
}
