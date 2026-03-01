<?php

namespace App\Http\Requests\Default;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Rules\PhoneNumberVnRule;

class CheckoutPostRequest extends FormRequest
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
            'email' => ['required', 'email'],
            'city_id' => 'required',
            'district_id' => 'required',
            'ward_id' => 'required',
            'address' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Vui lòng không được để trống',
            'phone.required' => 'Vui lòng không được để trống',
            'email.required' => 'Vui lòng không được để trống',
            'email.email' => 'Địa chỉ email không hợp lệ',
            'city_id.required' => 'Vui lòng không được để trống',
            'district_id.required' => 'Vui lòng không được để trống',
            'ward_id.required' => 'Vui lòng không được để trống',
            'address.required' => 'Vui lòng không được để trống',
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
