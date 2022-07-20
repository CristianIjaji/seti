<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'id_cliente' => [
                'required',
                'exists:tbl_terceros,id_tercero'
            ],
            'id_zona' => [
                'required',
                'exists:tbl_dominios,id_dominio',
            ],
            'nombre' => [
                'required',
                'string',
                'max:255'
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
                'max:255'
            ],
            'id_tipo_transporte' => [
                'required',
                'exists:tbl_dominios,id_dominio',
            ],
            'id_tipo_accesso' => [
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
            'id_cliente.required' => 'El campo cliente es obligatorio.',
            'id_zona.required' => 'El campo zona es obligatorio.',
            'id_tipo_transporte.required' => 'El campo transporte es obligatorio.',
            'id_tipo_accesso.required' => 'El campo acceso es obligatorio.',
        ];
    }
}
