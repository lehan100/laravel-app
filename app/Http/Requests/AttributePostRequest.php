<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttributePostRequest extends FormRequest {

    private $table = 'attribute_sets';

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
            'attribute_set_name' => 'bail|required|min:5',
            'attribute_set_alias' => [
                'bail',
                'required',
                "unique:$this->table,alias,$id"
            ],
        ];
    }

    public function messages() {
        return [
            'attribute_set_name.required' => 'Vui lòng không được để trống',
            'attribute_set_alias.required' => 'Vui lòng không được để trống',
            'attribute_set_name.min' => 'Chiều dài phải có ít nhất :min ký tự',
            'attribute_set_alias.unique' => 'Code đã tồn tại',
        ];
    }

}
