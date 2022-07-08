<?php

namespace App\Http\Requests;

use App\Models\TblConfiguracion;
use App\Models\TblTercero;
use Illuminate\Foundation\Http\FormRequest;

class SaveUsuarioRequest extends FormRequest
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

    protected function prepareForValidation()
    {
        if($this->get('id_tercero') > 0) {
            $tercero = TblTercero::findOrFail($this->get('id_tercero'));
            $this->merge([
                'email' => $tercero->correo,
            ]);
        }

        $this->merge([
            'id_usuareg' => (auth()->guest() ? 1 : auth()->id()),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'usuario' => [
                'required',
                'string',
                'max:255',
                'unique:tbl_usuarios,usuario'
            ],
            'id_tercero' => [
                'required',
                'integer',
            ],
            'email' => [
                'required',
                'email',
                'unique:tbl_usuarios,email'
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
            'id_usuareg' => [
                'required',
            ]
        ];
    }

    public function messages()
    {
        return [
            'id_dominio_recibo.required' => 'El recibo es obligatorio.',
            'id_tercero.required' => 'El tercero es obligatorio.',
            'email.required' => 'El tercero seleccionado no tiene un correo registrado.',
            'email.email' => 'El correo del tercero no es valido',
            'password.required' => 'El campo contraseña es obligatorio.',
            'password.min' => 'La constraseña debe contener más de 8 carácteres.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.'
        ];
    }
}
