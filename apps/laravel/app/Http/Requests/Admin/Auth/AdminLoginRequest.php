<?php

namespace App\Http\Requests\Admin\Auth;

use Illuminate\Foundation\Http\FormRequest;

class AdminLoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'identifier' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'identifier.required' => 'Vui lòng nhập tên đăng nhập, email hoặc số điện thoại.',
            'identifier.max' => 'Thông tin đăng nhập không được vượt quá 255 ký tự.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
        ];
    }
}
