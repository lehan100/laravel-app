<?php

namespace App\Http\Requests\Default;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Rules\PhoneNumberVnRule;

class ContactPostRequest extends FormRequest
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
            'title' => 'required',
            'phone' => ['required', new PhoneNumberVnRule()],
            'email' => [
                'bail',
                'required',
                'email'
            ],
            'message' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Vui lòng không được để trống',
            'phone.required' => 'Vui lòng không được để trống',
            'title.required' => 'Vui lòng không được để trống',
            'message.required' => 'Vui lòng không được để trống',
            'email.required' => 'Vui lòng không được để trống',
            'email.email' => 'Địa chỉ Email không đúng định dạng',
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
