<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class SaveCotizacionRequest extends FormRequest
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
            'ot_trabajo' => [
                'string',
                'nullable',
                'max:255',
                Rule::unique('tbl_cotizaciones')->ignore($this->route('quotes'))
            ],
            'id_cliente' => [
                'required',
                'exists:tbl_terceros,id_tercero'
            ],
            'id_estacion' => [
                'required',
                'exists:tbl_puntos_interes,id_punto_interes'
            ],
            'descripcion' => [
                'required',
                'max:255'
            ],
            'fecha_solicitud' => [
                'required',
                'date'
            ],
            'fecha_envio' => [
                'nullable',
                'date'
            ],
            'id_prioridad' => [
                'required',
                'exists:tbl_dominios,id_dominio'
            ],
            'id_proceso' => [
                'required',
                'exists:tbl_dominios,id_dominio'
            ],
            'id_responsable_cliente' => [
                'required',
                'exists:tbl_terceros,id_tercero'
            ],
            'valor' => [
                'nullable'
            ],
            'iva' => [
                'required',
                'exists:tbl_dominios,id_dominio'
            ],
            'observaciones' => [
                'string'
            ],
            'valor_reasignado' => [
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
            'id_cliente.required' => 'El campo cliente es obligatorio.',
            'id_estacion.required' => 'El campo punto de interés es obligatorio.',
            'descripcion.max' => 'El campo tipo de trabajo no puede ser mayor a 255 carácteres.',
            'id_prioridad.required' => 'El campo prioridad es obligatorio.',
            'id_proceso.required' => 'El campo proceso es obligatorio.',
            'id_responsable_cliente.required' => 'El campo contratista es obligatorio.',
        ];
    }
}
