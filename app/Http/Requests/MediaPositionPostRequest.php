<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MediaPositionPostRequest extends FormRequest {
    private $table = 'media_positions';
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules() {
        $id = $this->id;
        return [
            //
            'name' => 'bail|required|min:4',
            'mode' => 'not_in:0',
            'code' => [
                'bail',
                'required',
                "unique:$this->table,code,$id"
            ],
        ];
    }

    public function messages() {
        return [
            'name.required' => 'Vui lòng không được để trống',
            'mode.not_in' => 'Vui lòng không được để trống',
            'code.required' => 'Vui lòng không được để trống',
            'name.min' => 'Chiều dài phải có ít nhất :min ký tự',
            'code.unique'      => 'Code đã tồn tại',
            
        ];
    }

}
