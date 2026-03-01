<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class UserPostRequest extends FormRequest
{
    private $table = 'users';
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
        $id = $this->id;
        return [
            //
            'username' => "bail|required|min:5|unique:$this->table,username,$id",
            'fullname' => 'bail|required',
            'group' => 'not_in:0',
            'email' => [
                'bail',
                'required',
                'email',
                'max:' . config('const.email_length'),
                "unique:$this->table,email,$id"
            ],
            'password' => [
                'nullable',
                'required_with:password_confirmation',
                'confirmed',
                'min:' . config('const.password_length'),
                'max:' . config('const.password_max_length'),
            ]
        ];
    }
    /*public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ( !Hash::check($this->current_password, $this->user()->password) ) {
                $validator->errors()->add('current_password', 'Your current password is incorrect.');
            }
        });
        return;
    }*/

    public function messages()
    {
        return [
            'username.required' => 'Vui lòng không được để trống',
            'username.min'      => 'Chiều dài phải có ít nhất :min ký tứ',
            'username.unique'      => 'Tên đăng nhập đã tồn tại',
            'group.not_in'      => 'Vui lòng không được để trống',
            'fullname.required' => 'Vui lòng không được để trống',
            'email.required' => 'Vui lòng không được để trống',
            'email.max'      => 'Chiều dài tối đa :max ký tứ',
            'email.unique'      => 'Email đã tồn tại',
            'password.confirmed' => 'Mật khẩu không trùng khớp',
            'password.required_with' => 'Mật khẩu không trùng khớp',
            // 'password.required' => 'Vui lòng không được để trống',
            'password.max'      => 'Chiều dài tối đa :max ký tứ',
            'password.min'      => 'Chiều dài phải có ít nhất :min ký tứ',
        ];
    }
}
