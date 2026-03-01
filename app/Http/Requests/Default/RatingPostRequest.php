<?php

namespace App\Http\Requests\Default;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Rules\PhoneNumberVnRule;

class RatingPostRequest extends FormRequest
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
            'name' => 'required',
            'phone' => ['required', new PhoneNumberVnRule()],
            'product_id' => 'not_in:0',
            'content' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Vui lòng không được để trống',
            'phone.required' => 'Vui lòng không được để trống',
            'content.required' => 'Vui lòng không được để trống',
            'product_id.not_in'  => 'Vui lòng không được để trống',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors(),
            'status' => false
        ], 200));
    }
}
