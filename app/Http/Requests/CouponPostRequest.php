<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CouponPostRequest extends FormRequest
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
            'name' => 'required',
            'coupon_code' => 'required',
            'discount_amount' => 'bail|required|regex:/^[0-9.]+$/',
            'uses' => 'bail|required|integer',
            'max_uses_user' => 'bail|required|regex:/^[0-9.]+$/',
            'discount_amount_from' => 'regex:/^[0-9.]+$/',
            'discount_max' => 'regex:/^[0-9.]+$/',
        ];
    }

    public function messages()
    {
        return [
             'name.required' => 'Vui lòng không được để trống',
             'coupon_code.required' => 'Vui lòng không được để trống',
             'discount_amount.required' => 'Vui lòng không được để trống',
             'discount_amount.integer' => 'Dữ liệu phải dạng số',
             'uses.required' => 'Vui lòng không được để trống',
             'uses.integer' => 'Dữ liệu phải dạng số',
             'max_uses_user.required' => 'Vui lòng không được để trống',
             'max_uses_user.integer' => 'Dữ liệu phải dạng số',
             'discount_amount_from.integer' => 'Dữ liệu phải dạng số',
             'discount_max.integer' => 'Dữ liệu phải dạng số',
        ];
    }
}
