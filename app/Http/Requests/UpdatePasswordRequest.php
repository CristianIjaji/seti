<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePasswordRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed'
            ]
        ];
    }

    public function messages()
    {
        return [
            'password.required' => 'El campo contraseña es obligatorio.',
            'password.min' => 'La constraseña debe contener más de 8 carácteres.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.'
        ];
    }
}
