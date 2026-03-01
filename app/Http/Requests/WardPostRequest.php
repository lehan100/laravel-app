<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WardPostRequest extends FormRequest
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
            'name' => 'bail|required|min:1',
            'district_id' => 'not_in:0',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Vui lòng không được để trống',
            'name.min'      => 'Chiều dài phải có ít nhất :min ký tự',
            'district_id.not_in'      => 'Vui lòng không được để trống',
        ];
    }
}
