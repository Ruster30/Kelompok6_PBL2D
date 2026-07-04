<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVendorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "nama_vendor"  => "required|string|max:255",
            "jenis_vendor" => "nullable|string|max:100",
            "alamat"       => "nullable|string",
            "deskripsi"    => "nullable|string",
            "email"        => "nullable|email|max:255|unique:vendors,email|required_with:password",
            "password"     => "nullable|string|min:8",
        ];
    }

    public function messages(): array
    {
        return [
            "nama_vendor.required"  => "Nama vendor harus diisi.",
            "email.unique"          => "Email sudah digunakan vendor lain.",
            "email.required_with"   => "Email harus diisi jika password diisi.",
            "password.min"          => "Password minimal 8 karakter.",
        ];
    }
}