<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveTerceroRequest extends FormRequest
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
            'id_dominio_tipo_documento' => [
                'required',
                'exists:tbl_dominios,id_dominio',
            ],
            'documento' => [
                'required',
                'string',
                'max:255',
                Rule::unique('tbl_terceros')->ignore($this->route('client'))
            ],
            'dv' => [
                'nullable'
            ],
            'razon_social' => [
                'string',
                'max:255',
                'nullable'
            ],
            'nombres' => [
                'required',
                'string',
                'max:255'
            ],
            'apellidos' => [
                'required',
                'string',
                'max:255'
            ],
            'ciudad' => [
                'required',
                'string',
                'max:255'
            ],
            'direccion' => [
                'required',
                'string',
                'max:255'
            ],
            'correo' => [
                'required',
                'email',
                Rule::unique('tbl_terceros')->ignore($this->route('client'))
            ],
            'telefono' => [
                'required',
                'string',
                'max:255'
            ],
            'id_dominio_tipo_tercero' => [
                'required',
                'exists:tbl_dominios,id_dominio'
            ],
            'estado' => [
                'nullable'   
            ],
            'id_usuareg' => [
                'required',
                'exists:tbl_usuarios,id_usuario'
            ]
        ];
    }

    public function messages()
    {
        return [
            'id_dominio_tipo_documento.required' => 'El campo tipo documento es obligatorio.',
            'id_dominio_tipo_tercero.required' => 'El campo tipo tercero es obligatorio.'
        ];
    }
}
