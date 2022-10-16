<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SaveActividadRequest extends FormRequest
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
            'ot' => mb_strtoupper($this->get('ot')),
            'id_usuareg' => (Auth::id() === null ? 1 : Auth::id()),
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
            'ot' => [
                'string',
                'nullable',
                'max:255',
                Rule::unique('tbl_actividades')->ignore($this->route('activity'))
            ],
            'id_tipo_actividad' => [
                'required',
                'exists:tbl_dominios,id_dominio'
            ],
            'id_subsistema' => [
                'required',
                'nullable',
                'exists:tbl_dominios,id_dominio'
            ],
            'descripcion' => [
                'required',
                'string',
            ],
            'id_encargado_cliente' => [
                'required',
                'exists:tbl_terceros,id_tercero'
            ],
            'id_resposable_contratista' => [
                'required',
                'exists:tbl_terceros,id_tercero'
            ],
            'id_estacion' => [
                'required',
                'exists:tbl_puntos_interes,id_punto_interes'
            ],
            'permiso_acceso' => [
                'string',
                'max:255',
                'nullable'
            ],
            'fecha_solicitud' => [
                'required',
                'date'
            ],
            'fecha_programacion' => [
                'nullable',
                'date'
            ],
            'fecha_reprogramacion' => [
                'nullable',
                'date'
            ],
            'fecha_ejecucion' => [
                'nullable',
                'date'
            ],
            'id_estado_actividad' => [
                'required',
                'exists:tbl_dominios,id_dominio'
            ],
            'id_cotizacion' => [
                'required',
                'nullable',
                'exists:tbl_cotizaciones,id_cotizacion'
            ],
            'id_orden_compra' => [
                'nullable',
                'exists:tbl_ordenes_compra,id_orden_compra'
            ],
            'id_informe' => [
                'nullable',
                'exists:tbl_informes_actividades,id_informe_actividad'
            ],
            'fecha_liquidado' => [
                'nullable',
                'date'
            ],
            'liquidado' => [
                'nullable',
                'boolean'
            ],
            'id_mes_consolidado' => [
                'nullable',
                'exists:tbl_dominios,id_dominio'
            ],
            'valor' => [
                'required',
            ],
            'observaciones' => [
                'string'
            ],
            'id_usuareg' => [
                'required',
                'exists:tbl_usuarios,id_usuario'
            ]
        ];
    }
}
