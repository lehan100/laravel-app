<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductPostRequest extends FormRequest {

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
        return [
            //
            'name' => 'bail|required|min:5',
//            'alias' => 'bail|required',
            'quantity' => 'bail|required|integer',
            'price' => 'bail|required',
//            'price' => 'bail|required|numeric|gt:-1',
//            'special_price' => "bail|numeric|max:$price",
        ];
    }

    public function messages() {
        return [
            'name.required' => 'Vui lòng không được để trống',
//            'alias.required' => 'Vui lòng không được để trống',
            'quantity.required' => 'Vui lòng không được để trống',
            'price.required' => 'Vui lòng không được để trống',
            'name.min' => 'Chiều dài phải có ít nhất :min ký tự',
            'quantity.integer' => 'Dữ liệu phải dạng số',
            'price.numeric' => 'Dữ liệu phải dạng số',
            'price.gt' => 'Giá sản phẩm phải lớn hơn 0',
//            'special_price.max' => 'Giá giảm phải nhỏ hơn hoặc bằng :max',
        ];
    }

}
