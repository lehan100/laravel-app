<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TierPricePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            //
            //'type' => 'bail|required'
        ];
    }

    public function messages()
    {
        return [
             'type.required' => 'Vui lòng không được để trống'
        ];
    }
}
