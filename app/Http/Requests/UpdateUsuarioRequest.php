<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUsuarioRequest extends FormRequest
{
    protected $rules = [];
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if(trim($this->get('password')) === ''){
            $this->rules = [
                'usuario' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('tbl_usuarios')->ignore($this->route('user')),
                ],
                'id_tercero' => [
                    'nullable',
                    'integer',
                ],
                'id_dominio_recibo' => [
                    'required'
                ],
                'servicios' => [
                    'required'
                ],
                'logo' => [
                    'nullable',
                    'image'
                ],
                'estado' => 'required'
            ];
        } else {
            $this->rules = [
                'usuario' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('tbl_usuarios')->ignore($this->route('user')),
                ],
                'id_tercero' => [
                    'nullable',
                    'integer',
                ],
                'password' => [
                    'required',
                    'string',
                    'min:8',
                    'confirmed'
                ],
                'id_dominio_recibo' => [
                    'required'
                ],
                'servicios' => [
                    'required'
                ],
                'logo' => [
                    'nullable',
                    'image'
                ],
                'estado' => 'required'
            ];
        }
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return $this->rules;
    }

    public function messages()
    {
        return [
            'id_dominio_recibo.required' => 'El recibo es obligatorio.',
            'email.required' => 'El campo correo es obligatorio.',
            'email.email' => 'Correo no válido.',
            'password.required' => 'El campo contraseña es obligatorio.',
            'password.min' => 'La constraseña debe contener más de 8 carácteres.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.'
        ];
    }
}
