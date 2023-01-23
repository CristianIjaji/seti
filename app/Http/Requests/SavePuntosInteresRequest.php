<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SavePuntosInteresRequest extends FormRequest
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
            'nombre' => mb_strtoupper($this->get('nombre')),
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
            'id_tercero_cliente' => [
                'required',
                'exists:tbl_terceros,id_tercero'
            ],
            'id_dominio_zona' => [
                'required',
                'exists:tbl_dominios,id_dominio',
            ],
            'nombre' => [
                'required',
                'string',
                'max:255',
                Rule::unique('tbl_puntos_interes')->ignore($this->route('site'))
            ],
            'latitud' => [
                'nullable',
                'string',
                'max:255'
            ],
            'longitud' => [
                'nullable',
                'string',
                'max:255'
            ],
            'estado' => [
                'nullable'
            ],
            'descripcion' => [
                'required',
                'string'
            ],
            'id_dominio_tipo_transporte' => [
                'required',
                'exists:tbl_dominios,id_dominio',
            ],
            'id_dominio_tipo_accesso' => [
                'required',
                'exists:tbl_dominios,id_dominio',
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
            'id_tercero_cliente.required' => 'El campo cliente es obligatorio.',
            'id_dominio_zona.required' => 'El campo zona es obligatorio.',
            'id_dominio_tipo_transporte.required' => 'El campo transporte es obligatorio.',
            'id_dominio_tipo_accesso.required' => 'El campo acceso es obligatorio.',
        ];
    }
}
