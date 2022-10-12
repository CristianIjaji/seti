<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveConsolidadoRequest extends FormRequest
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
            'mes' => date('Y-m-d', strtotime($this->get('mes'))),
            'id_estado_consolidado' => session('id_dominio_consolidado_creado'),
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
            'id_responsable_cliente' => [
                'required',
                'exists:tbl_terceros,id_tercero'
            ],
            'mes' => [
                'required',
                'date'
            ],
            'observacion' => [
                'nullable',
                'max:255'
            ],
            'id_estado_consolidado' => [
                'required',
                'exists:tbl_dominios,id_dominio'
            ],
            'id_actividad' => [
                'required',
                'exists:tbl_actividades,id_actividad'
            ],
            'id_usuareg' => [
                'required'
            ]
        ];
    }

    public function messages()
    {
        return [
            'id_cliente.required' => 'El campo cliente es obligatorio.',
            'id_responsable_cliente.required' => 'El campo encargado cliente es obligatorio.',
            'id_actividad.required' => 'Debe agregar una actividad al consolidado.',
        ];
    }
}
